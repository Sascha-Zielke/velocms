# VeloCMS — RESUME.md
> Stand: 2026-05-27 | Letzte Session: Maxiworx Refactor (Phase 25b) abgeschlossen

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
| 24 | Booking-App (7 Phasen: Foundation, AvailabilityEngine, Admin-UI, Templates, API, Mail, Tests) | ✅ DONE |
| 25 | Maxiworx Website (CSS-Theme, Layout, 9 Views, Module+Controller, Booking-Overlay) | ✅ DONE |
| 25b | Maxiworx Refactor: DB-Content, Gold-Admin-Theme, Visual Editor Nav | ✅ DONE |

## Maxiworx Tenant — Vollständiger Stand (2026-05-27)

### Technisch
- Domain: maxiworx.webzite-newmedia.com (Ionos DNS → 95.217.185.113)
- DB: velocms_maxiworx (Schema-Kopie von velocms_site_a)
- Nginx: /etc/nginx/sites-available/maxiworx (SSL via Let's Encrypt, gültig bis 2026-08-25)
- Basic Auth: /etc/nginx/.htpasswd_maxiworx (Preview-Schutz)
- Admin-User: s.zielke84@gmail.com

### Code-Architektur (nach Phase 25b)
- modules/Maxiworx/MaxiworxModule.php — Tenant-Guard via Tenant::domain(), routes, Visual Editor Nav-Item
- modules/Maxiworx/Controllers/MaxiworxController.php — lädt jede Page aus DB via PagesModel; graceful [] fallback
- modules/Maxiworx/views/ — 9 Views (home + 8 Unterseiten); DB-Content mit hardcoded Fallbacks
- public/assets/css/maxiworx.css — Frontend-Theme (Gold #C9A227, Dark #0D0D0D, Barlow-Fonts)
- public/assets/css/maxiworx-admin.css — Admin-Theme (CSS-Variablen-Override: Gold/Dark)
- views/layouts/admin.php — lädt maxiworx-admin.css per Tenant::domain()-Check; zeigt "Maxiworx" als Logo
- views/layouts/maxiworx.php — Custom Frontend-Layout (Header, Footer, Booking-Overlay)
- scripts/mw_seed_pages.php — Einmalig ausgeführt 2026-05-27; erstellt alle 9 Pages in DB

### DB-Content (velocms_maxiworx nach Seed)
- 9 Pages in velocms_pages (slug: home, equipment, service-preise, specials, referenzen, kontakt, impressum, datenschutz, agb)
- Alle mit Sections + Rows + Boxes befüllt
- home: 5 Sektionen (hero, portfolio, hardware, services, cta), 13 Boxes
- settings: homepage_slug=home, site_name=Maxiworx

### Admin-UI
- Visual Editor (→ /admin/pages) zwischen Dashboard und Seiten im Sidebar
- Sidebar-Logo zeigt Maxiworx (gold)
- Sidebar-Hintergrund: #111111, Accent: #C9A227

## Nächste Schritte (Prio-Reihenfolge)

1. **Logo** — public/assets/images/maxiworx/logo.png platzieren (User hat das Logo)
2. **Bilder** — Hero-Foto (hero-studio.jpg), Hardware-Grid-Bilder, Portfolio-Artwork
3. **Rechtstexte** — Impressum, Datenschutz, AGB via Visual Editor befüllen (/admin/pages)
4. **Backoffice-Website-Workflow.md** — Section 7 mit Learnings aus Phase 25+25b befüllen
5. **Domain-Wechsel** — maxiworx.de wenn Kunde ready; DNS → Nginx vhost anpassen

## Wichtige Server-Hinweise
- PHP-Error-Log: /var/log/fpm-php.www.log — jetzt dauerhaft aktiv
- git pull als root schlägt fehl → immer sudo -u velocms git pull
- Seed-Script: /var/www/velocms/scripts/mw_seed_pages.php — sicher wiederholt ausführbar (SKIP wenn vorhanden)
- Nach manuellem root-git-Befehl: chown -R velocms:velocms /var/www/velocms/

## Wichtige Pfade & Credentials
- Webroot: /var/www/velocms/public/
- DB User: velocms / VeloCMS_DB_Secure_2026!
- DB Namen: velocms_site_a | velocms_master | velocms_maxiworx
- Superadmin: s.zielke84@gmail.com
- GitHub: https://github.com/Sascha-Zielke/velocms

## CI-Pipeline — Wichtige Hinweise
- PHPUnit: colors=true ist korrekt (xs:boolean, NICHT enum)
- Kein --no-interaction bei PHPUnit (nur bei Composer)
- Deploy-User: velocms — .git muss ihm gehören
- Nach manuellem root-git-Befehl: chown -R velocms:velocms /var/www/velocms/
