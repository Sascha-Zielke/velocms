# VeloCMS — Multi-Phase Task Plan
**Generated:** 2026-05-25  
**Server:** 95.217.185.113 (Hetzner, fresh/ABSOLUTE ZERO)  
**Target Domain:** webzite-newmedia.com  
**Repo:** https://github.com/Sascha-Zielke/velocms  
**Architecture:** PHP 8.2, Nginx, MySQL 8.0, own MVC (no framework)

---

## Current Baseline
| Area | Status |
|------|--------|
| Core Framework | ✅ Complete |
| Auth Module | ✅ Complete (16 tests green) |
| Server (Hetzner) | ❌ ABSOLUTE ZERO |
| SSL | ❌ Not configured |
| CI/CD | ❌ Not configured |
| Pages Module | ❌ Not started |
| Media Module | ❌ Not started |
| Multi-tenancy hardening | ❌ Partial (DB design done) |

---

## DOUBLE-AUDIT GATE (applies to every phase)
1. **Internal Dry-Run:** Review against architecture rules (multi-tenancy, relative paths, DSGVO, no framework).
2. **Live Verification:** Run a specific shell command on the server to prove the phase is complete. Output goes into RESUME.md before the next phase begins.

---

## PHASE 0 — Server Foundation
**Goal:** Go from ABSOLUTE ZERO to a secured, package-complete Linux base.  
**Estimated time:** 30 min

### Steps
- [ ] 0.1 SSH connection verified (root@95.217.185.113)
- [ ] 0.2 APT update + upgrade
- [ ] 0.3 Install: `nginx`, `php8.2-fpm`, `php8.2-{cli,mysql,mbstring,xml,curl,zip,intl,gd}`, `mysql-server`, `git`, `composer`, `certbot`, `python3-certbot-nginx`, `unzip`, `curl`
- [ ] 0.4 Create system user `velocms` (no login shell for security; home = /var/www/velocms)
- [ ] 0.5 Create directory structure:
  ```
  /var/www/velocms/
  /var/www/velocms/public/
  /storage/tenants/          (tenant-isolated uploads, outside webroot)
  /var/log/velocms/
  ```
- [ ] 0.6 Set correct ownership: `chown -R velocms:www-data /var/www/velocms`

**Audit Command:**
```bash
php8.2 -v && nginx -v && mysql --version && git --version
```

---

## PHASE 1 — Database Layer
**Goal:** MySQL hardened, master registry DB + first tenant DB created, least-privilege user.  
**Estimated time:** 15 min

### Steps
- [ ] 1.1 Run `mysql_secure_installation`
- [ ] 1.2 Create master registry DB: `velocms_master`
  - Table: `vcms_sites` (id, tenant_id UUID, domain, db_name, name, active, created_at)
- [ ] 1.3 Create first site DB: `velocms_site_1`
- [ ] 1.4 Create DB user `velocms_app` with GRANT on both DBs only
- [ ] 1.5 Write credentials into server-side `/var/www/velocms/.env` (not in repo)

**Architecture constraints:**
- Tenant routing MUST use `tenant_id` (UUID), never the domain string
- All queries use PDO prepared statements
- No DB root password in .env

**Audit Command:**
```bash
mysql -u velocms_app -p -e "SHOW DATABASES;"
```

---

## PHASE 2 — Application Deployment
**Goal:** Repo cloned, dependencies installed, migrations run, first admin seeded.  
**Estimated time:** 20 min

### Steps
- [ ] 2.1 Deploy SSH key for GitHub read access (deploy key, read-only)
- [ ] 2.2 `git clone git@github.com:Sascha-Zielke/velocms.git /var/www/velocms`
- [ ] 2.3 `cd /var/www/velocms && composer install --no-dev --optimize-autoloader`
- [ ] 2.4 Copy `.env.example` → `.env`, fill with Phase 1 DB credentials + APP_URL
- [ ] 2.5 `php velocms migrate` — run all pending migrations
- [ ] 2.6 `php scripts/seed-admin.php s.zielke84@gmail.com "Sascha Zielke"` — create superadmin
- [ ] 2.7 Verify: `php velocms migrate:status` shows all migrations applied

**Architecture constraints:**
- `.env` stays on server, never committed
- Seed script uses `password_hash(PASSWORD_DEFAULT)` internally
- No hardcoded credentials

**Audit Command:**
```bash
php velocms migrate:status
```

---

## PHASE 3 — Web Server Configuration
**Goal:** Nginx serving VeloCMS on port 80, PHP-FPM running as velocms user, front controller active.  
**Estimated time:** 15 min

### Nginx Virtual Host (`/etc/nginx/sites-available/velocms`)
Key directives:
- `root /var/www/velocms/public;`
- `try_files $uri $uri/ /index.php?$query_string;`
- `location ~ \.php$` → proxy to PHP-FPM sock
- `location /storage/` → 403 (storage outside webroot anyway)
- Security headers block

### PHP-FPM Pool (`/etc/php/8.2/fpm/pool.d/velocms.conf`)
- `user = velocms`, `group = www-data`
- `listen = /run/php/php8.2-velocms.sock`
- `pm = dynamic`, `pm.max_children = 20`

**Steps**
- [ ] 3.1 Write Nginx vhost config
- [ ] 3.2 Write PHP-FPM pool config
- [ ] 3.3 `nginx -t && systemctl reload nginx`
- [ ] 3.4 Test: `curl -I http://webzite-newmedia.com/admin/login`

**Audit Command:**
```bash
curl -s -o /dev/null -w "%{http_code}" http://95.217.185.113/admin/login
```
Expected: `200`

---

## PHASE 4 — SSL & Security Hardening
**Goal:** HTTPS with Let's Encrypt, security headers, PHP hardened.  
**Estimated time:** 15 min

### Steps
- [ ] 4.1 `certbot --nginx -d webzite-newmedia.com -d www.webzite-newmedia.com`
- [ ] 4.2 Verify auto-renewal: `certbot renew --dry-run`
- [ ] 4.3 Add Nginx security headers:
  - `Strict-Transport-Security: max-age=31536000; includeSubDomains`
  - `X-Frame-Options: SAMEORIGIN`
  - `X-Content-Type-Options: nosniff`
  - `Referrer-Policy: strict-origin-when-cross-origin`
  - `Content-Security-Policy: default-src 'self'; font-src 'self'; img-src 'self' data:; script-src 'self'; style-src 'self' 'unsafe-inline'`
  - (Note: `'unsafe-inline'` for styles only — DSGVO: NO external CDNs)
- [ ] 4.4 PHP-FPM: `expose_php = Off`, `display_errors = Off`, `upload_max_filesize = 10M`
- [ ] 4.5 UFW firewall: allow 22, 80, 443; deny all else

**Audit Command:**
```bash
curl -s -o /dev/null -w "%{http_code}" https://webzite-newmedia.com/admin/login
```
Expected: `200`

---

## PHASE 5 — CI/CD Pipeline (GitHub Actions)
**Goal:** Push to `main` auto-deploys to VPS in < 60 seconds.  
**Estimated time:** 20 min

### Workflow (`.github/workflows/deploy.yml`)
Trigger: `push` to `main`
Steps:
1. SSH to VPS
2. `cd /var/www/velocms && git pull --ff-only origin main`
3. `composer install --no-dev --optimize-autoloader`
4. `php velocms migrate`
5. `systemctl reload php8.2-fpm`

### GitHub Secrets Required
- `VPS_HOST` = 95.217.185.113
- `VPS_USER` = velocms
- `VPS_SSH_KEY` = private deploy key (write access to server)
- `VPS_PORT` = 22

### Steps
- [ ] 5.1 Generate deploy key pair on server: `ssh-keygen -t ed25519 -C "github-actions-deploy"`
- [ ] 5.2 Add public key to `~/.ssh/authorized_keys` on VPS
- [ ] 5.3 Add private key to GitHub Secrets as `VPS_SSH_KEY`
- [ ] 5.4 Create `.github/workflows/deploy.yml`
- [ ] 5.5 Push and verify Actions run

**Audit:** GitHub Actions tab — green checkmark on latest commit to `main`

---

## PHASE 6 — Pages Module (Visual Editor Grid)
**Goal:** Full Page → Sections → Rows → Boxes admin CRUD with JSON-first data engine.  
**Estimated time:** 3-4 hours  
**Architecture:** Modular, `modules/Pages/`

### Data Model
```sql
-- velocms_pages
id, site_id (FK), slug, title, title_en, status(draft|published), layout(JSON), 
created_at, updated_at, deleted_at

-- layout JSON structure:
{
  "sections": [
    {
      "id": "uuid",
      "bg_color": "#fff",
      "overlay": 0,          -- 0-100%
      "padding": "md",
      "rows": [
        {
          "id": "uuid",
          "cols": 2,
          "gap": "md",
          "boxes": [
            {
              "id": "uuid",
              "type": "text|image|video|button|spacer",
              "data": { ... type-specific ... }
            }
          ]
        }
      ]
    }
  ]
}
```

### Box Type Schemas
| Type | Required data keys |
|------|-------------------|
| text | `content` (HTML, sanitized), `content_en` |
| image | `src` (relative path), `alt`, `alt_en`, `link` |
| video | `provider` (youtube/vimeo), `id`, `poster` (relative), `2click: true` |
| button | `label`, `label_en`, `href` (relative), `style` |
| spacer | `height` (px or rem string) |

### Constraints
- NO inline HTML/CSS in JSON
- All video embeds: 2-click privacy solution (Vanilla JS)
- All image paths: relative (`/uploads/...`)
- Styling parameters only through allowed schema keys
- Admin UI: Vanilla JS drag-and-drop, no external JS libs

### Files to Create
```
modules/Pages/
├── PagesModule.php
├── Controllers/
│   ├── PageController.php       (admin CRUD)
│   └── FrontendPageController.php
├── Models/
│   └── PageModel.php
├── migrations/
│   └── 001_create_pages_table.php
├── views/
│   ├── admin/
│   │   ├── index.php            (page list)
│   │   ├── edit.php             (visual editor)
│   │   └── _box_editor.php      (partial)
│   └── frontend/
│       └── page.php             (render engine)
└── assets/
    ├── editor.js                (Vanilla JS drag-drop)
    └── editor.css
```

**Audit:**
- PHPUnit: `vendor/bin/phpunit --filter Pages`
- Manual: Create a page with Text + Image boxes, publish, verify frontend render
- JSON audit: Confirm no domain-absolute paths in saved data

---

## PHASE 7 — Media Module
**Goal:** Upload pipeline with EXIF stripping, tenant-isolated storage, relative paths.  
**Estimated time:** 1-2 hours

### Upload Pipeline
1. Receive file → validate MIME whitelist (`image/jpeg`, `image/png`, `image/webp`, `image/gif`, `application/pdf`)
2. Strip EXIF: `imagecreatefromjpeg()` + `imagejpeg()` (re-encode to strip metadata)
3. Generate safe filename: `{uuid}.{ext}` — never use original filename
4. Save to: `/storage/tenants/{tenant_id}/uploads/{YYYY/MM}/`
5. Store relative path in DB: `/storage/tenants/{tenant_id}/uploads/2026/05/{uuid}.jpg`
6. Serve via Nginx alias (map `/storage/` → `/storage/` outside webroot)

### Constraints
- DSGVO: EXIF stripped before disk write
- No domain in stored paths
- MIME whitelist — never trust `$_FILES['type']`; use `finfo_file()`
- File size limit: 10MB (enforced in PHP + Nginx)

---

## PHASE 8 — Multi-Tenancy Hardening
**Goal:** Master Registry routing fully enforced; tenant isolation airtight.  
**Estimated time:** 2 hours

### Key Work
- [ ] 8.1 Bootstrap reads `vcms_sites` from master DB by request domain → resolves `tenant_id`
- [ ] 8.2 All subsequent DB connections use tenant-specific database
- [ ] 8.3 Storage paths always use `tenant_id` UUID (never domain string)
- [ ] 8.4 Session namespace: `vcms_{tenant_id}_` prefix on all keys
- [ ] 8.5 Add `site_id` FK + index to all content tables

---

## PHASE 9 — DSGVO Compliance Layer
**Goal:** All DSGVO-mandated behaviors automated and enforced.  
**Estimated time:** 1 hour

### Checklist
- [ ] 9.1 External asset calls: zero (audit with CSP report-only first)
- [ ] 9.2 Fonts: local copies only (no Google Fonts CDN calls)
- [ ] 9.3 Video: 2-click embed wrapper (Vanilla JS, no external script)
- [ ] 9.4 Forms: CSRF + honeypot field (hidden, must be empty on submit)
- [ ] 9.5 Data retention: cron script deletes soft-deleted rows after configurable period
- [ ] 9.6 EXIF stripping: verified in Media Module pipeline
- [ ] 9.7 Cookie consent: own implementation, no third-party consent SaaS

---

## Phase Dependencies

```
PHASE 0
  └── PHASE 1
        └── PHASE 2
              ├── PHASE 3
              │     └── PHASE 4
              │           └── PHASE 5 (CI/CD)
              ├── PHASE 6 (Pages — can start after Phase 2)
              ├── PHASE 7 (Media — can start after Phase 2)
              └── PHASE 8 (Multi-tenancy — after Phases 6+7)
                    └── PHASE 9 (DSGVO — final layer)
```

---

## Risk Register

| Risk | Mitigation |
|------|-----------|
| Hetzner firewall blocks port 80/443 | Check Hetzner Cloud firewall rules in addition to UFW |
| Domain DNS not pointing to 95.217.185.113 | Verify `dig webzite-newmedia.com` before Certbot |
| composer install fails (memory) | `php -d memory_limit=512M /usr/bin/composer install` |
| MySQL 8.0 auth plugin mismatch | Use `caching_sha2_password` or force `mysql_native_password` in DB user GRANT |
| Git pull fails (no deploy key) | Generate key first, add to GitHub before attempting pull |
