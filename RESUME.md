# VeloCMS — Aktueller Stand

## Status
**2026-05-25 — Neuer Hetzner-Server (ABSOLUTE ZERO). Provisioning steht an.**  
Codebase lokal vollständig (Core + Auth). Server ist neu deployed, keinerlei Pakete installiert.

## Was erledigt ist
- Core Framework vollständig (Session 1)
- Auth-Modul vollständig gebaut und auditiert:
  - `modules/Auth/AuthModule.php` — Routen: GET/POST /admin/login, POST /admin/logout, GET /admin
  - `modules/Auth/Controllers/AuthController.php` — showLogin/login/logout (logout POST-only + CSRF)
  - `modules/Auth/Controllers/DashboardController.php` — requireAuth + Dashboard
  - `modules/Auth/Models/UserModel.php` — getByEmail, create (role allowlist, password_hash), updateLastLogin
  - `modules/Auth/views/admin/login.php` — standalone Login-View
  - `modules/Auth/views/admin/dashboard.php` — Layout-View
  - `tests/Unit/Modules/Auth/UserModelTest.php` — 6 Tests inkl. Hashing + Role-Guard
- 16 Unit-Tests, alle grün ✅
- `task_plan.md` generiert (9 Phasen, vollständig)

## Server-Stand (2026-05-25)
| Was | Status |
|-----|--------|
| Hetzner VPS 95.217.185.113 | ✅ Deployed |
| Nginx | ✅ Installiert |
| PHP 8.2.31 | ✅ Installiert |
| MySQL 8.0 | ✅ Installiert |
| Git / Composer 2.9.8 | ✅ Installiert |
| System-User `velocms` | ✅ Angelegt |
| Verzeichnisstruktur | ✅ `/var/www/velocms`, `/storage/tenants` |
| App deployed | ❌ Nicht geklont |
| Migrations | ❌ Nicht ausgeführt |
| SSL | ❌ Nicht konfiguriert |
| CI/CD | ❌ Nicht konfiguriert |

## Nächster Schritt — PHASE 5 (GitHub Actions CI/CD)
Deploy-Key, Sudo-Regel, Workflow-File, Secrets in GitHub.

## Offene TODOs (priorisiert)
- [x] **PHASE 0:** Server provisionieren — ✅ 2026-05-25 (PHP 8.2.31, Composer 2.9.8, Nginx, MySQL, Git)
- [x] **PHASE 1:** MySQL einrichten — ✅ 2026-05-25 (velocms_master, velocms_site_1, vcms_sites mit UUID b2847a9e-913a-4f46-9cf1-0617bc93214c)
- [x] **PHASE 2:** App deployen — ✅ 2026-05-25 (2 Migrations, superadmin s.zielke84@gmail.com angelegt)
- [x] **PHASE 3:** Nginx + PHP-FPM — ✅ 2026-05-25 (HTTP 200, socket active, nginx -t OK)
- [x] **PHASE 4:** SSL + Security Hardening — ✅ 2026-05-25 (HTTPS 200, HSTS+CSP+X-Frame+X-Content-Type je einmal, certbot dry-run OK)
- [ ] **PHASE 5:** GitHub Actions CI/CD
- [ ] **PHASE 6:** Pages-Modul (Visual Editor Grid)
- [ ] **PHASE 7:** Media-Modul (EXIF-Stripping, Tenant-Storage)
- [ ] **PHASE 8:** Multi-Tenancy Hardening
- [ ] **PHASE 9:** DSGVO Compliance Layer

## Double-Audit Gate — Letztes bestandenes Audit
- **Phase:** Phase 4 — SSL + Security Hardening
- **Datum:** 2026-05-25
- **Ergebnis:** HTTPS 200 ✅, HSTS/CSP/X-Frame/X-Content-Type je einmal ✅, certbot --dry-run ✅
