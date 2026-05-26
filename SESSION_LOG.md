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
