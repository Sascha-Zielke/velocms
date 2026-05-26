# VeloCMS — RESUME.md
> Stand: 2026-05-26 | Letzte Session: Phasen 12–16 deployed + CI/CD repariert

## Server
- IP: 95.217.185.113 | SSH Port: 22 | User: velocms
- PHP 8.2.31 · Nginx 1.18 · MySQL 8.0.45 · Composer 2.9.8

## Abgeschlossene Phasen

| Phase | Inhalt | Status |
|-------|--------|--------|
| 0 | Server Provisioning (PHP, Nginx, MySQL, Composer) | ✅ DONE |
| 1 | Core Framework (Router, Controller, Model, View) | ✅ DONE |
| 2 | Auth-Modul (Login, CSRF, Rollen, 16 Tests grün) | ✅ DONE |
| 3 | PHP 8 Fixes, DB-Connection | ✅ DONE |
| 5 | CI/CD: GitHub Actions (auto-deploy auf push→main) | ✅ DONE |
| 6–10 | Pages, Visual Editor, Media, Blog (CRUD + Frontend) | ✅ DONE |
| 11 | PHPUnit CI — 30 Tests, CI+Deploy Workflow | ✅ DONE |
| 12 | User-Management (CRUD, Rollen, Passwort-Reset) | ✅ DONE |
| 13 | Settings-Modul (17 Keys, setting()-Helper, Exception-Handler) | ✅ DONE |
| 14 | Navigation Builder (CRUD, Up/Down, nav()-Helper, Frontend) | ✅ DONE |
| 15 | Custom 404/403/500 Error-Pages + Homepage-Route | ✅ DONE |
| 16 | SEO-Basics (Sitemap, robots.txt, Canonical, OG-Tags) | ✅ DONE |

## Aktueller Stand (2026-05-26)

- CI/CD: GitHub Actions Run #9 — **✅ success** (30 Tests grün, Deploy OK)
- Deploy-Pipeline: Push → Test → SSH-Deploy → migrate → php-fpm reload
- Letzter Deploy-Commit: `2ab1cab` (chore: .git permission fix trigger)
- Server-Stand: Alle Phasen 12–16 live auf 95.217.185.113

### Was live ist:
- ✅ Admin: /admin (Login, Dashboard, Blog, Pages, Media, Nav, Settings, Users)
- ✅ Frontend: Pages mit Visual Editor, Blog-Liste + Einzelpost
- ✅ Navigation: DB-basiert via nav()-Helper, Admin-verwaltbar
- ✅ Settings: 17 Keys (Site, Branding, SEO, Social, Footer)
- ✅ SEO: /sitemap.xml, /robots.txt, Canonical-URLs, OG-Tags
- ✅ Error-Pages: 404/403/500 mit eigenem Design
- ✅ User-Management: CRUD, editor/admin/superadmin, Passwort-Reset

### CI/CD-Fixes dieser Session:
- `--no-interaction` aus PHPUnit-Befehl entfernt (via GitHub UI)
- `colors="true"` bleibt korrekt (xs:boolean laut PHPUnit-XSD)
- Server-Git-Permissions: `chown -R velocms:velocms /var/www/velocms/.git`
- Remote auf SSH zurückgesetzt nach HTTPS-Fetch

## Nächste Phase

**Phase 17 — Kontaktformular (empfohlen)**
- DSGVO-konform, Honeypot-Spam-Schutz, Rate-Limit
- SMTP-Versand via PHP mail() oder externer Dienst
- Admin-Benachrichtigung per E-Mail

**Phase 18 — Tenant-Provisioning (Superadmin-UI)**
- Neue Sites über Admin anlegen
- DB-Isolation per Tenant

**Phase 19 — Frontend-Theme**
- Projekt-spezifisches Design
- CSS-Variablen, Dark Mode

## Wichtige Pfade & Credentials
- Webroot: /var/www/velocms/public/
- DB User: velocms / VeloCMS_DB_Secure_2026!
- DB Name: velocms_site_a | Master: velocms_master
- Superadmin: s.zielke84@gmail.com
- GitHub: https://github.com/Sascha-Zielke/velocms

## CI-Pipeline — Wichtige Hinweise
- PHPUnit: `colors="true"` ist korrekt (xs:boolean, NICHT enum)
- Kein `--no-interaction` bei PHPUnit (nur bei Composer)
- Deploy-User: velocms — `.git` muss ihm gehören
- Nach manuellem root-git-Befehl: `chown -R velocms:velocms /var/www/velocms/.git`
