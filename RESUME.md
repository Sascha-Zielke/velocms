# VeloCMS — Aktueller Stand

## Status
Auth-Modul fertig — bereit für MySQL-Datenbank + Nginx-Setup

## Was erledigt ist
- Core Framework vollständig (Session 1)
- **Auth-Modul vollständig gebaut und auditiert:**
  - `modules/Auth/AuthModule.php` — Routen: GET/POST /admin/login, POST /admin/logout, GET /admin
  - `modules/Auth/Controllers/AuthController.php` — showLogin/login/logout (logout POST-only + CSRF)
  - `modules/Auth/Controllers/DashboardController.php` — requireAuth + Dashboard
  - `modules/Auth/Models/UserModel.php` — getByEmail, create (role allowlist, password_hash), updateLastLogin
  - `modules/Auth/views/admin/login.php` — standalone Login-View (declare + lang whitelist)
  - `modules/Auth/views/admin/dashboard.php` — Layout-View
  - `tests/Unit/Modules/Auth/UserModelTest.php` — 6 Tests inkl. Hashing + Role-Guard
- Alle Views: `declare(strict_types=1)` + `$_COOKIE['vcms_lang']` Whitelist nachgerüstet
- Logout als POST (CSRF-Schutz gegen Forced-Logout-Angriff)
- 16 Unit-Tests, alle grün ✅

## Nächster Schritt
**MySQL Datenbank einrichten:**
1. Master-DB `velocms_master` anlegen
2. Site-DB `velocms_site_a` (oder Name nach Wunsch) anlegen
3. MySQL-User `velocms` mit Rechten auf beide DBs
4. `.env` befüllen
5. `php velocms migrate` ausführen → velocms_sites + velocms_users anlegen
6. Ersten Admin-User per Seed-Script anlegen

**Danach:**
- Nginx Rewrite-Config: alle Requests → public/index.php
- SSL mit Certbot
- GitHub Actions CI/CD

## Offene TODOs
- [x] Core Framework
- [x] Auth-Modul
- [ ] MySQL Datenbank einrichten + .env befüllen
- [ ] Nginx Rewrite-Config
- [ ] SSL mit Certbot
- [ ] Seed-Script: ersten Admin-User anlegen
- [ ] Pages-Modul (Visual Editor Grid)
- [ ] GitHub Actions CI/CD
