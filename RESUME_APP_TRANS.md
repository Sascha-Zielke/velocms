# VeloCMS — Translation-App RESUME
> Produkt: veloSolution → veloCMS-PHP → App: Translation
> Gestartet: 2026-05-26 | Status: 🔲 Phase 1 ausstehend

---

## Server & Repo
- VPS: velocms@95.217.185.113:22 | Webroot: /var/www/velocms/public
- GitHub: https://github.com/Sascha-Zielke/velocms (main → auto-deploy)
- PHP Error-Log: /var/log/fpm-php.www.log

## Architektur-Entscheidungen (unveränderlich)

| Entscheidung | Wahl |
|---|---|
| Speicherung | Zentrale `velocms_translations`-Tabelle — keine bestehende Tabelle wird verändert |
| Live-Sprachwechsel | JS fetch + DOM-Swap des Content-Bereichs — kein Reload |
| KI-Provider | DeepL (primär) → Anthropic (Fallback) |
| Sprach-Cookie | `vcms_lang` (bereits vorhanden) |
| Apps-Menü | `type: section`-Header in AdminMenu bei Position 95 |
| Neue Sprache | 0 Migrationen nötig — einfach in Settings aktivieren |

## Phasen-Übersicht

| Phase | Titel | Status |
|-------|-------|--------|
| 1 | Foundation: DB, TranslationService, Apps-Menü | 🔲 Offen |
| 2 | Sprachumschalter Frontend + Admin (kein Reload) | 🔲 Offen |
| 3 | Auto-Translation Engine + Trigger | 🔲 Offen |
| 4 | Admin-Dashboard + manueller Editor | 🔲 Offen |
| 5 | Content-Abdeckung: Blog, Pages, Nav, SEO | 🔲 Offen |
| 6 | Erweiterte Features: Glossar, Export/Import | 🔲 Offen |
| 7 | Tests + Final-Audit | 🔲 Offen |

---

## Phase 1 — Foundation
**Status:** 🔲 Offen

### Geplante Dateien
```
core/Services/TranslationService.php        (Rebuild — Bugs behoben)
core/AdminMenu.php                          (type:'section' Support)
views/layouts/admin.php                     (Section-Header-Rendering)
modules/Translation/
├── TranslationModule.php
├── migrations/
│   ├── 001_create_translations_table.php
│   └── 002_add_translation_settings.php
lang/de.php                                 (apps.* + nav.* Keys)
lang/en.php                                 (apps.* + nav.* Keys)
RESUME_APP_TRANS.md                         (diese Datei — bereits erstellt)
```

**Audit 1 (Code-Review):** 🔲  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Phase 2 — Sprachumschalter
**Status:** 🔲 Offen

### Geplante Dateien
```
views/layouts/frontend.php                  (Switcher Toggle/Dropdown)
views/layouts/admin.php                     (Switcher Header)
public/assets/js/frontend.js                (fetch + DOM-Swap)
public/assets/css/frontend.css             (Switcher-Styles)
public/assets/css/admin.css                (Switcher-Styles Admin)
core/functions.php                          (localized() → translations-Tabelle)
```

**Audit 1 (Code-Review):** 🔲  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Phase 3 — Auto-Translation Engine
**Status:** 🔲 Offen

### Geplante Dateien
```
modules/Translation/
├── Models/TranslationModel.php
├── Services/TranslationEngine.php
modules/Blog/Controllers/AdminBlogController.php   (auto-trigger Hook)
modules/Nav/Controllers/AdminNavController.php     (auto-trigger Hook)
```

**Audit 1 (Code-Review):** 🔲  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Phase 4 — Admin-Dashboard + Editor
**Status:** 🔲 Offen

### Geplante Dateien
```
modules/Translation/
├── Controllers/AdminTranslationController.php
├── views/admin/translation/
│   ├── dashboard.php
│   ├── editor.php
│   └── settings.php
modules/Blog/views/admin/index.php         (Inline-Badges)
modules/Nav/views/admin/nav/index.php      (Inline-Badges)
```

**Audit 1 (Code-Review):** 🔲  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Phase 5 — Content-Abdeckung
**Status:** 🔲 Offen

### Geplante Dateien
```
modules/Translation/Services/TranslationEngine.php  (JSON-Traversal Pages)
modules/Pages/Controllers/AdminPagesController.php   (auto-trigger Hook)
modules/Settings/Controllers/AdminSettingsController.php (Hook)
views/layouts/frontend.php                           (hreflang-Tags)
```

**Audit 1 (Code-Review):** 🔲  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Phase 6 — Erweiterte Features
**Status:** 🔲 Offen

### Geplante Dateien
```
modules/Translation/
├── migrations/003_create_glossary_table.php
├── Controllers/AdminGlossaryController.php
├── views/admin/translation/glossary.php
├── Controllers/AdminTranslationExportController.php
```

**Audit 1 (Code-Review):** 🔲  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Phase 7 — Tests + Final-Audit
**Status:** 🔲 Offen

### Geplante Dateien
```
tests/Unit/Core/Services/TranslationServiceTest.php
tests/Unit/Modules/Translation/TranslationModelTest.php
tests/Unit/Modules/Translation/TranslationEngineTest.php
RESUME.md                                            (Update)
SESSION_LOG.md                                       (Eintrag)
RESUME_APP_TRANS.md                                  (Final-Update)
```

**Audit 1 (Code-Review):** 🔲  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Wichtige Hinweise für Folge-Sessions

- `sudo -u velocms git pull` — nie als root
- `php velocms migrate` nach jedem Deploy mit neuen Migrations
- `sudo systemctl reload php8.2-fpm` nach Änderungen an Config/Core
- Keine bestehende Tabelle anpassen — alles läuft über `velocms_translations`
- Neue Sprache: nur in `active_languages`-Setting eintragen, 0 Migrationen
