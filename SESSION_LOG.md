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
