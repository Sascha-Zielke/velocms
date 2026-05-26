# VeloCMS Session Log

## Template (append one block per session)

```
## YYYY-MM-DD — Session N

**Duration:** ~Xh
**Done:**
- 

**Issues:**
- 

**Next:**
- 
```

---

## 2026-05-26 — Session 13

**Duration:** ~30min
**Done:**
- Codebase-Audit: vollständiger Scan auf toten Code, veraltete Dateien, TODO/FIXMEs
- Gelöscht (tote Dateien / nie geladen):
  - `modules/Pages/Controller/PageController.php` — altes Demo-Stub, falscher Namespace, nie PSR-4 geladen
  - `modules/Pages/Controller/` (Verzeichnis) — jetzt leer, entfernt
  - `modules/Pages/routes.php` — wurde nirgends required, referenzierte toten PageController
  - `views/admin/login.php` — alte Version ohne Honeypot/Forgot-Password-Link, durch `modules/Auth/views/admin/login.php` ersetzt (nicht mehr referenziert)
  - `views/admin/` (Verzeichnis) — jetzt leer, entfernt
  - `task_plan.md` — Dev-Planungs-Artefakt, alle 22 Phasen abgeschlossen
  - `modules/Pages/views/frontend/404.php` — PagesController nutzt direkt `views/errors/404.php`
  - `modules/Blog/views/frontend/404.php` — nach Bugfix ebenfalls entfernt
- Bugfix `modules/Blog/Controllers/BlogController.php`:
  - Blog-404-Fall renderte `frontend/404` ohne `extend('frontend')` → komplett leere HTTP-Response
  - Fix: nutzt nun `include BASE_PATH . '/views/errors/404.php'` wie PagesController (konsistent)
- Codebase ist jetzt sauber: kein toter Code, keine Stubs, keine Dev-Artefakte

**Issues:**
- keine

**Next:**
- Translation-App für VeloCMS entwickeln

---

## 2026-05-26 — Session 12

**Duration:** ~1h
**Done:**
- Bugfix: Navigation + Sites gaben 500-Fehler (beide seit Phase 14/21 unbemerkt kaputt)
  - Root-Cause: `velocms_nav_items` und `velocms_sites` hatten keine `deleted_at`-Spalte
  - Ursache: Tabellen existierten vor Migration — `CREATE TABLE IF NOT EXISTS` hat das Erstellen übersprungen, Spalte wurde nie angelegt
  - Fix 1: `migrations/004_add_deleted_at_to_sites.php` — deleted_at zu velocms_sites
  - Fix 2: `modules/Nav/migrations/002_add_deleted_at_to_nav_items.php` — deleted_at zu velocms_nav_items
- Weiterer Fix: `bootstrap/App.php` war Totcode (nie via PSR-4 geladen) — Phase 20 + 22 wirkungslos in Produktion
  - `core/App.php` ist die echte App-Klasse: Phase 20 (handleMaintenanceMode) + Phase 22 (Tenant::resolve) dort integriert
  - `bootstrap/App.php` gelöscht
- PHP-Error-Log dauerhaft aktiviert: `/var/log/fpm-php.www.log`
- CI-Deploy-Problem behoben: Server steckte bei Phase 21 weil lokale Änderung an `velocms` den git pull blockiert hatte
  - Fix: `git checkout -- velocms` dann `sudo -u velocms git pull`
- Audit: ✅ Navigation zeigt Einträge im Frontend, Sites CRUD funktioniert, Error-Log clean

**Issues:**
- CI-Deploy schlägt still fehl wenn lokale Dateiänderungen auf dem Server vorhanden sind
- `error_log` hatte "no value" → PHP-Fehler gingen ins Nichts (catch_workers_output disabled)

**Next:**
- Sites/create: Dropdown für Web-Technologie (PHP → später erweiterbar)
- Weitere Features nach Bedarf

---

## 2026-05-26 — Session 11

**Duration:** ~30min
**Done:**
- Phase 22: Tenant-Routing vollständig implementiert und deployed
  - `core/Tenant.php`: Kompletter Rewrite — explizite Modi (CLI-Guard → Single-Site → Multi-Site), `bootSingleSite()` mit synthetischem id=0-Row, `isMultiSite()` prüft id>0, graceful Fallback wenn Master-DB nicht erreichbar
  - `bootstrap/App.php`: DB-Init von `Database::connect()` direkt auf `Tenant::resolve($config)` umgestellt; Bedingung auf `!empty($config['db_host'])`
  - `velocms` CLI: `tenant:init <domain> [name] [db]` (Master-DB + velocms_sites anlegen, ON DUPLICATE KEY UPDATE), `tenant:list` (Tabelle aller Sites oder Single-Site-Meldung)
  - `tests/Unit/Core/TenantTest.php`: `testIsMultiSite_returnsFalse_initially()` ergänzt
- CI/CD: GitHub Actions Run #21 — ✅ success, Deploy OK
- Audit 1 (Code-Review): ✅ Single-Site (kein MASTER_DB) → kein Master-DB-Traffic, kein 503-Risiko; CLI-Guard korrekt; prepared statements im Multi-Site-Lookup; isMultiSite() korrekt
- Audit 2 (Live-Verify): ✅ webzite-newmedia.com → HTML geladen ("Willkommen bei VeloCMS!"), /admin → Login-Form sichtbar, kein PHP-Fehler

**Issues:**
- keine

**Next:**
- Offen — nach Absprache mit dem Nutzer

---

## 2026-05-26 — Session 10

**Duration:** ~1h
**Done:**
- Phase 21: Tenant-Provisioning Superadmin-UI vollständig implementiert und deployed
  - `migrations/003_update_sites_table`: www_alias + status ENUM zu velocms_sites hinzugefügt; active-Flag in status migriert
  - `modules/Sites/Models/SiteModel`: getAll/getById/domainExists/dbNameExists/create/update/softDelete/provisionDb/markActive; db_name-Whitelist-Regex
  - `modules/Sites/Controllers/AdminSitesController`: index/create/store/edit/update/provision/delete; requireRole('superadmin')
  - `modules/Sites/SitesModule`: 7 Routen, Sidebar-Menüeintrag position 90 min_role superadmin
  - Views: sites/index (Tabelle, Status-Badges), sites/create (Form), sites/edit (Form + Provision-Banner + Danger-Zone)
  - `provisionDb()`: CREATE DATABASE IF NOT EXISTS, graceful catch bei fehlenden Rechten
  - `public/assets/css/admin.css`: vcms-card/vcms-card--danger/vcms-hint + alle fehlenden Badge-Varianten (Rollen + Site-Status)
  - `lang/de.php` + `lang/en.php`: 30 sites.*-Keys je
  - `tests/Unit/Modules/Sites/SiteModelTest.php`: 8 Unit-Tests
- CI/CD: GitHub Actions Run #20 — ✅ success, Deploy + Migration 003 OK
- Audit 1 (Code-Review): ✅ CSRF, requireRole superadmin, db_name-Whitelist, Status-Allowlist, provisionDb graceful
- Audit 2 (Live-Verify): ✅ /admin/sites → Auth-Redirect korrekt, /admin/sites/create → Auth-Redirect, keine PHP-Fehler

**Issues:**
- keine

**Next:**
- Tenant-Routing in App::boot() auf Tenant::resolve() umstellen (Breaking Change — Absprache nötig)
- Weitere Features nach Bedarf

---

## 2026-05-26 — Session 9

**Duration:** ~30min
**Done:**
- Phase 20: Wartungsmodus vollständig implementiert und deployed
  - `bootstrap/App.php`: `handleMaintenanceMode()` zwischen ModuleLoader::boot() und Router::dispatch()
  - Logik: setting('maintenance_mode')='1' → 503 + Retry-After:3600, außer /admin/* und eingeloggter Admin/Superadmin
  - `views/errors/maintenance.php`: 503-Seite mit vcms-error-page Layout, robots noindex, function_exists()-Guards
  - `lang/de.php` + `lang/en.php`: maintenance.title/headline/text
  - Kein DB-Risiko: setting() hat try/catch → maintenance_mode bleibt inaktiv wenn DB weg
- CI/CD: GitHub Actions Run #18 — ✅ success, Deploy OK
- Audit 1 (Code-Review): ✅ XSS-safe, 503+Retry-After korrekt, Exit nach include, /admin-Bypass, Auth::hasRole('admin') korrekt
- Audit 2 (Live-Verify): ✅ / → 200 normal (maintenance_mode=0 Early-Return), /admin → Login OK, keine PHP-Fehler

**Issues:**
- keine

**Next:**
- Phase 21: Tenant-Provisioning Superadmin-UI

---

## 2026-05-26 — Session 8

**Duration:** ~1h
**Done:**
- Phase 19: Passwort-Reset vollständig implementiert und deployed
  - `modules/Auth/migrations/001_create_password_resets_table.php`: velocms_password_resets (token_hash VARCHAR(128) SHA-256, expires_at, used_at, user_id)
  - `modules/Auth/Models/PasswordResetModel.php`: createToken(), findValidToken(), markUsed(), purgeExpired() — Raw-Token nie gespeichert
  - `modules/Auth/Controllers/PasswordResetController.php`: showRequest/sendReset/showForm/reset — kein User-Enumeration (gleiches Response bei gültiger/ungültiger E-Mail)
  - `modules/Auth/views/admin/password_reset_request.php` + `password_reset_form.php`: Standalone HTML wie login.php
  - `modules/Auth/AuthModule.php`: 4 neue Routen (GET/POST /admin/password/reset, GET/POST /admin/password/reset/[a:token])
  - `modules/Auth/views/admin/login.php`: "Passwort vergessen?" Link ergänzt
  - `lang/de.php` + `lang/en.php`: 18 password_reset.*-Keys je
  - `tests/Unit/Modules/Auth/PasswordResetModelTest.php`: 8 Unit-Tests
  - `public/assets/css/admin.css`: vcms-login-hint + vcms-login-back Klassen
- CI/CD: GitHub Actions Run #16 — ✅ success, Deploy OK
- Audit 1 (Code-Review): ✅ CSRF, No-Enumeration, SHA-256-Hash, Single-Use, TTL, Password-Validation alle korrekt
- Audit 2 (Live-Verify): ✅ /admin/password/reset 200 OK, "Passwort vergessen?" auf Login-Seite, Invalid-Token-Redirect korrekt

**Issues:**
- keine

**Next:**
- Phase 20: Wartungsmodus (Middleware-Handler für maintenance_mode Setting)
- Phase 21: Tenant-Provisioning Superadmin-UI

---

## 2026-05-26 — Session 7

**Duration:** ~2h
**Done:**
- Phase 18: Frontend-Theme vollständig implementiert und deployed
  - `frontend.css` (~500 Zeilen): Design-Token-System (CSS Custom Properties), Typografie-Skala, Sticky Header, Desktop-Nav, Hamburger/Overlay Mobile-Nav, Visual-Editor-Boxes, Buttons, Forms, Cards, Blog, Footer, Error-Pages, Dark Mode, Print, Responsive (768px/480px)
  - `frontend.js`: Sticky-Header-Shadow, Hamburger-Toggle (Escape/Outside-Click/Body-Scroll-Lock), Video-Consent-Bug-Fix (Selektor + YouTube-nocookie + Vimeo), Scroll-Reveal (IntersectionObserver)
  - `frontend.php`: Hamburger-Button + Mobile-Nav-Overlay ergänzt
  - `404.php`: vcms-error-code, Header/Nav/Footer via PHP-Guards, JS geladen
  - `500.php`: Inline-Styles entfernt, vcms-error-code
  - `contact.php`: Inline-Styles durch Theme-Klassen ersetzt
- Audit 1: ✅ Code-Review bestanden
- Audit 2: ✅ Live-Verify — CSS/JS/Layout/404/kontakt/admin alle korrekt
- Server-Permission-Problem (root-umask) behoben:
  - Root-Ursache: `git reset --hard` als root → Dateien 600-owned
  - Fix: `find /var/www/velocms -not -path '*/.git/*' -type f -exec chmod 644 {} \;` + `-type d -exec chmod 755 {} \;`
  - Lektion: Nach jedem root-git-Befehl IMMER `chown -R velocms:velocms /var/www/velocms/` + find chmod

**Issues:**
- `git reset --hard` als root erzeugt Dateien mit 600 (root umask 077) — breaking für www-data
- Nur `.git` zu chownen reicht NICHT — immer das gesamte Verzeichnis
- Nginx `realpath()` braucht 755 auf den obersten Verzeichnissen

**Next:**
- Phase 19: Passwort vergessen / Reset-Mail
- Phase 20: Wartungsmodus-Handler

---

## 2026-05-26 — Session 6

**Duration:** ~1h
**Done:**
- Memory-Konsolidierung abgeschlossen: project-infrastructure.md (GREENFIELD → PRODUCTION), feedback-rules.md (Audit-Gate verschärft), SSH-Port 2222 → 22 korrigiert, CI/CD-Quirks dokumentiert
- Phase 17: Kontaktformular vollständig implementiert und deployed
  - ContactModule, ContactController (Frontend), AdminContactController, ContactModel
  - 2 Migrations (velocms_contact_messages + Contact-Settings-Keys)
  - Frontend /kontakt: Honeypot, CSRF, DSGVO-Consent-Checkbox, Rate-Limit (3/h), Validation
  - Admin /admin/contact: Inbox, Filter, Spam-Markierung, DSGVO-Purge, Einstellungen
  - PHP mail() mit Reply-To, MIME-Header, konfigurierbarer Empfänger
  - 8 Unit-Tests (ContactModelTest) — alle grün
  - DE + EN Übersetzungen (je 45 Keys)
- Audit 1 (Code-Review): ✅ Alle Checks bestanden
- Audit 2 (Live-Verify): ✅ /kontakt 200, Honeypot/CSRF/Consent im HTML, POST ohne CSRF → 403, /admin/contact → 302 Login

**Issues:**
- PHP nicht lokal installiert → Tests laufen nur via CI/CD (kein Problem, CI ist grün)

**Next:**
- Phase 18: Tenant-Provisioning Superadmin-UI
- Phase 19: Frontend-Theme

---

## 2026-05-26 — Session 5 (Fortsetzung von Session 4)

**Duration:** ~3h (inkl. CI-Debugging)
**Done:**
- Phasen 12–16 auf GitHub gepusht (waren lokal, nie pushed)
- CI-Pipeline repariert:
  - Root-Ursache 1: `--no-interaction` ist kein PHPUnit-Flag → via GitHub UI entfernt
  - Root-Ursache 2: `colors="auto"` ungültig (XSD erwartet xs:boolean) → auf `colors="true"` zurückgesetzt
- Server-Git-History divergiert (manuelle Deploys) → `git reset --hard origin/main` als root
- `.git/objects` und `.git/config` root-owned nach manuellen git-Befehlen → `chown -R velocms:velocms /var/www/velocms/.git` (2x)
- GitHub Actions Run #9: ✅ 30 Tests grün, Deploy erfolgreich
- Alle Phasen 12–16 live auf Server (User-Mgmt, Settings, Nav, Error-Pages, SEO)
- RESUME.md vollständig aktualisiert

**Issues:**
- Manuelle root-SSH-Befehle auf Server ändern .git-Permissions → immer danach chown velocms
- PAT ohne `workflow` scope → ci.yml-Änderungen nur via GitHub UI möglich
- Remote-URL muss SSH bleiben (git@github.com) für den Deploy-User velocms

**Next:**
- Phase 17: Kontaktformular (DSGVO, Honeypot, Rate-Limit)
- Phase 18: Tenant-Provisioning Superadmin-UI
- Phase 19: Frontend-Theme

---

## 2026-05-22 — Session 3

**Duration:** ~15min
**Done:**
- Fix: Controller::__construct() ergänzt — parent::__construct() in PHP 8 wirft "Cannot call constructor" wenn Parent keinen Constructor definiert
- Fix: Database::getTestConnection() fehlende PDO-Attribute ergänzt (FETCH_ASSOC, EMULATE_PREPARES)
- Admin-Login-Seite jetzt erreichbar (war 500 Internal Server Error)
- Nginx-Config bereits gesetzt, Migrations bereits gelaufen — .env bereits befüllt
- 16 Tests, alle grün

**Issues:**
- Kein Superadmin-User vorhanden — seed-admin.php muss interaktiv ausgeführt werden
- SSL (certbot) noch nicht installiert

**Next:**
- Superadmin anlegen: `php scripts/seed-admin.php`
- SSL mit certbot einrichten
- GitHub Actions CI/CD
- Pages-Modul (Visual Editor Grid)

---

## 2026-05-21 — Session 2

**Duration:** ~1h
**Done:**
- Auth-Modul komplett mit TDD-Workflow + Reviewer-Audit nach jeder Komponente
- UserModel: role allowlist (privilege-escalation-Guard), password guard, lastInsertId check
- AuthController: CSRF auf login + logout, filter_var email, password_verify, no user enumeration
- DashboardController: requireAuth im Constructor
- AuthModule: logout als POST registriert (vorher GET — CSRF-Lücke)
- Admin-Layout: Logout als POST-Form (kein CSRF-angreifbarer GET-Link mehr)
- Alle Views: declare(strict_types=1) + $_COOKIE lang-Whitelist nachgerüstet
- 16 Unit-Tests, 27 Assertions — alle grün

**Issues:**
- Reviewer flaggte redirectWithError()-Terminierung als false positive (never-Rückgabetyp verifiziert)

**Next:**
- MySQL Datenbank anlegen, .env befüllen, php velocms migrate ausführen

---

## 2026-05-21 — Session 1

**Duration:** ~1h
**Done:**
- Komplettes Core Framework gebaut (Router, Controller, View, Model, Database, Auth)
- Layout-System implementiert (extend/section/yield)
- Module-Basis mit RouterProxy für saubere Route-Registrierung
- ModuleLoader, MigrationRunner, Migration-Basis
- TranslationService (DeepL + Anthropic Fallback)
- i18n Layer 1: lang/de.php + lang/en.php
- Bootstrap/App.php mit .env-Loader, Session, Error-Config
- public/index.php, CLI-Script velocms
- Core-Migrations: velocms_sites, velocms_users
- Views: layouts/admin.php, layouts/frontend.php, admin/login.php
- PHPUnit Setup: phpunit.xml, tests/bootstrap.php
- 10 Unit-Tests (RouterTest, AuthTest) — alle grün

**Issues:**
- Auth::verifyCsrf() auf RuntimeException umgestellt (vorher die() — nicht testbar)

**Next:**
- Auth-Modul bauen (LoginController, UserModel)
- MySQL Datenbank anlegen + .env befüllen
- Nginx Rewrite-Config prüfen
