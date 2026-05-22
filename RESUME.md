# VeloCMS — Aktueller Stand

## Status
App läuft — Admin-Login erreichbar. Noch kein Superadmin-User vorhanden, SSL fehlt noch.

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
**Superadmin anlegen (interaktiv im Terminal):**
```bash
php scripts/seed-admin.php s.zielke84@gmail.com "Sascha Zielke"
```

**Danach:**
- SSL mit Certbot (`apt install certbot python3-certbot-nginx && certbot --nginx -d webzite-newmedia.com`)
- GitHub Actions CI/CD
- Pages-Modul (Visual Editor Grid)

## Offene TODOs
- [x] Core Framework
- [x] Auth-Modul
- [x] MySQL Datenbank einrichten + .env befüllen
- [x] Nginx Rewrite-Config
- [x] Migrations ausgeführt (velocms_sites + velocms_users)
- [ ] Seed-Script: ersten Admin-User anlegen
- [ ] SSL mit Certbot
- [ ] GitHub Actions CI/CD
- [ ] Pages-Modul (Visual Editor Grid)
