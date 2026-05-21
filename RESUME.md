# VeloCMS — Aktueller Stand

## Status
Core Framework fertig — bereit für erstes Modul

## Was erledigt ist
- Server: Ubuntu 22.04, PHP 8.2, MySQL, Nginx, Node.js
- GitHub Repo: https://github.com/Sascha-Zielke/velocms
- velocms-skills installiert in .claude/
- **Core Framework vollständig gebaut:**
  - `core/Router.php` — URL-Routing mit Named Segments
  - `core/Controller.php` — Base Controller mit View-Integration, Flash, Auth-Guards
  - `core/View.php` — Layout-Vererbung (extend/section/endSection/yield)
  - `core/Model.php` — Base Model mit find/getAll/softDelete
  - `core/Database.php` — PDO Singleton + Testverbindung
  - `core/Auth.php` — Login/Logout/CSRF-Verifikation
  - `core/Module.php` — Abstrakte Modul-Basis mit RouterProxy
  - `core/AdminMenu.php` — Statische Admin-Menüregistrierung
  - `core/ModuleLoader.php` — Scannt und bootet alle Module
  - `core/Migration.php` — Abstrakte Migration-Basis
  - `core/MigrationRunner.php` — CLI-Migrationsverwaltung mit Batches
  - `core/Services/TranslationService.php` — DeepL + Anthropic Fallback
  - `core/functions.php` — e(), t(), localized(), csrf_field(), safe_html()
  - `bootstrap/App.php` — Bootstrap: .env → DB → Module → Router
  - `public/index.php` — Einstiegspunkt
  - `lang/de.php` + `lang/en.php` — UI-Strings (Layer 1 i18n)
  - `views/layouts/admin.php` + `views/layouts/frontend.php`
  - `views/admin/login.php` — Login-Seite (standalone, kein Layout)
  - `migrations/001_create_sites_table.php`
  - `migrations/002_create_users_table.php`
  - `tests/bootstrap.php` + `phpunit.xml`
  - `tests/Unit/Core/RouterTest.php` + `AuthTest.php` — 10 Tests ✅
  - `velocms` CLI — `php velocms migrate|rollback|status`
  - `.env.example`

## Nächster Schritt
Erstes Modul bauen: **Auth-Modul** (Login-Controller + UserModel)

- `modules/Auth/AuthModule.php` — Routen registrieren (/admin/login, /admin/logout)
- `modules/Auth/Controllers/AuthController.php` — GET/POST login, logout
- `modules/Auth/Models/UserModel.php` — getByEmail(), create()
- `modules/Auth/migrations/001_create_users_table.php` (ggf. global Migration nutzen)
- MySQL-Datenbank anlegen: `velocms_master` + Site-DB
- `.env` befüllen

## Offene TODOs
- [x] Core Framework
- [ ] Auth-Modul (Login/Logout/UserModel)
- [ ] MySQL Datenbank einrichten + .env befüllen
- [ ] Nginx konfigurieren + .htaccess/Rewrite
- [ ] SSL mit Certbot
- [ ] Erstes Content-Modul (Pages)
- [ ] GitHub Actions CI/CD
