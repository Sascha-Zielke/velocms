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
