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
| 1 | Foundation: DB, TranslationService, Apps-Menü | ✅ Abgeschlossen |
| 2 | Sprachumschalter Frontend + Admin (kein Reload) | ✅ Abgeschlossen |
| 3 | Auto-Translation Engine + Trigger | ✅ Abgeschlossen |
| 4 | Admin-Dashboard + manueller Editor | ✅ Abgeschlossen |
| 5 | Content-Abdeckung: Blog, Pages, Nav, SEO | ✅ Abgeschlossen |
| 6 | Erweiterte Features: Glossar, Export/Import | 🔲 Offen |
| 7 | Tests + Final-Audit | 🔲 Offen |

---

## Phase 1 — Foundation
**Status:** ✅ Abgeschlossen

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

**Audit 1 (Code-Review):** ✅  
**Audit 2 (Live-Verify):** ✅ Route matcht, Controller antwortet (OPcache-Bug via `systemctl restart` behoben)  
**Commit:** 584c841 + 19a407e

---

## Phase 2 — Sprachumschalter
**Status:** ✅ Abgeschlossen

### Geplante Dateien
```
views/layouts/frontend.php                  (Switcher Toggle/Dropdown)
views/layouts/admin.php                     (Switcher Header)
public/assets/js/frontend.js                (fetch + DOM-Swap)
public/assets/css/frontend.css             (Switcher-Styles)
public/assets/css/admin.css                (Switcher-Styles Admin)
core/functions.php                          (localized() → translations-Tabelle)
```

**Audit 1 (Code-Review):** ✅ Sicher, Event-Delegation, Cookie sanitiert  
**Audit 2 (Live-Verify):** ✅ Cookie-Switch DE↔EN serverseitig bestätigt  
**Commit:** 15c92a1

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

**Audit 1 (Code-Review):** ✅ session_write_close() vor fastcgi_finish_request(), manual-source-Guard, Hash-Dedup  
**Audit 2 (Live-Verify):** ✅ Engine feuert im Hintergrund, Fehler korrekt geloggt, kein 500 für User  
**Commit:** 71283c6 (+ fix BlogModel author_id)  
**Hinweis:** DEEPL_API_KEY + ANTHROPIC_API_KEY müssen in .env eingetragen werden

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

**Audit 1 (Code-Review):** ✅ Dead Code entfernt, FQN-Imports bereinigt, hardcodierte Strings ersetzt  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Phase 5 — Content-Abdeckung
**Status:** ✅ Abgeschlossen

### Umgesetzte Dateien
```
core/Controller.php                                  (jsonWithBackground() hinzugefügt)
modules/Pages/Controllers/AdminPagesController.php   (auto-trigger: title + saveBox text)
modules/Pages/views/frontend/page.php                (localized() mit Tabellenkontext)
views/layouts/frontend.php                           (hreflang-Tags im <head>)
```

**Ansatz Textboxen:** box.data['text'] → velocms_translations(table='velocms_boxes', row_id=box.id, field='text')
**hreflang:** Cookie-basiertes System → alle Sprachen zeigen auf dieselbe URL

**Audit 1 (Code-Review):** ✅  
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
