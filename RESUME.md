# VeloCMS — RESUME.md
> Stand: 2026-05-27 | Letzte Session: Translation-App vollständig abgeschlossen (Phase 23)

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
| 17 | Kontaktformular (DSGVO, Honeypot, Rate-Limit, Admin-Inbox) | ✅ DONE |
| 18 | Frontend-Theme (Design-Tokens, Responsive Nav, Dark Mode, JS) | ✅ DONE |
| 19 | Passwort-Reset (Token, SHA-256, Single-Use, 1h TTL, PHP mail()) | ✅ DONE |
| 20 | Wartungsmodus (App::handleMaintenanceMode, 503, Admin-Bypass) | ✅ DONE |
| 21 | Tenant-Provisioning Superadmin-UI (Sites-CRUD, DB-Provisioning) | ✅ DONE |
| 22 | Tenant-Routing (App::boot → Tenant::resolve, Single+Multi-Site, CLI-Guard) | ✅ DONE |
| 23 | Translation-App (7 Phasen: DB, Engine, Switcher, Dashboard, Glossar, CSV, Tests) | ✅ DONE |

## Aktueller Stand (2026-05-27)

- CI/CD: Letzter Deploy `d258263` (Translation-App Final-Cleanup)
- Deploy-Pipeline: Push → Test → SSH-Deploy → migrate → php-fpm reload
- Server-Stand: Alle Phasen 12–23 live auf 95.217.185.113

### Was live ist:
- ✅ Admin: /admin (Login, Dashboard, Blog, Pages, Media, Nav, Settings, Users, Kontakt)
- ✅ Frontend: Pages mit Visual Editor, Blog-Liste + Einzelpost
- ✅ Navigation: DB-basiert via nav()-Helper, Admin-verwaltbar
- ✅ Settings: Keys inkl. Contact-Settings
- ✅ SEO: /sitemap.xml, /robots.txt, Canonical-URLs, OG-Tags
- ✅ Error-Pages: 404/403/500 mit eigenem Design
- ✅ User-Management: CRUD, editor/admin/superadmin, Passwort-Reset
- ✅ Kontaktformular: /kontakt — DSGVO, Honeypot, Rate-Limit, PHP mail(), Admin-Inbox
- ✅ Frontend-Theme: CSS Design-Tokens, Sticky Header, Responsive Nav (Hamburger), Dark Mode, Print, Scroll-Reveal
- ✅ Passwort-Reset: /admin/password/reset — Token (SHA-256, 1h TTL, Single-Use), PHP mail(), kein User-Enumeration
- ✅ Wartungsmodus: maintenance_mode=1 → 503 für alle außer Admin+; /admin immer erreichbar
- ✅ Sites-Verwaltung: /admin/sites (Superadmin) — CRUD, Status-Management, DB-Provisioning
- ✅ Tenant-Routing: App::boot() → Tenant::resolve(); Single-Site (kein MASTER_DB) und Multi-Site (MASTER_DB), CLI-Guard, graceful Fallback
- ✅ Translation-App: velocms_translations, DeepL+Anthropic, Glossar, CSV, Admin-Dashboard, 73 Tests

## Nächste Phase

Offen — wird in der nächsten Session definiert.

## Wichtige Server-Hinweise (neu)
- PHP-Error-Log: `/var/log/fpm-php.www.log` — jetzt dauerhaft aktiv
- `git pull` als root schlägt fehl (kein SSH-Key) → immer `sudo -u velocms git pull`
- Lokale Änderungen auf dem Server blockieren CI-Deploy → nach manuellem Server-Edit immer `git checkout -- <datei>` vorher

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
- Nach manuellem root-git-Befehl: `chown -R velocms:velocms /var/www/velocms/` (ganzes Verzeichnis, nicht nur .git!)
- `git reset --hard` als root erzeugt 600-Dateien (root umask) → danach immer `find /var/www/velocms -not -path '*/.git/*' -type f -exec chmod 644 {} \;` und `-type d -exec chmod 755 {} \;`
- Nginx realpath() braucht 755 auf `/var/www/velocms/` und `/var/www/velocms/public/`
