# VeloCMS Architecture Rules

## Mandatory Reading on Session Start

At the beginning of EVERY Claude Code session on this project:

1. Read `/var/www/velocms/CLAUDE_START.md`
2. Read `/var/www/velocms/RESUME.md`

Do not skip this. These files contain the current state, open TODOs, and context that makes you useful.

## Core Principles

1. **Modular first.** All functionality lives in `modules/{Name}/`. Nothing goes in `core/` unless it's truly framework-level and needed by 3+ modules.

2. **No framework dependencies.** VeloCMS uses its own MVC. Do not suggest or introduce Laravel, Symfony, or other PHP frameworks.

3. **Multi-site by default.** Every query must respect the current site's DB connection. Never assume a single-site setup.

4. **Visual Editor Grid is sacred.** The Page → Sections → Rows → Boxes hierarchy must not be broken. Box data is JSON-serialized in the `data` column.

## Architecture Decision Rules

| Decision | Rule |
|----------|------|
| New feature | → New module in `modules/` |
| Shared utility (3+ modules need it) | → `core/` |
| Frontend logic | → Vanilla JS only, no frameworks |
| Styling | → Project CSS, no Bootstrap/Tailwind |
| Third-party API | → Wrap in a Service class in `core/Services/` |
| Multi-site data | → Always use site-specific DB connection |

## File Placement

```
New module:        modules/{Name}/
New core util:     core/{ClassName}.php
New service:       core/Services/{Name}Service.php
New migration:     modules/{Name}/migrations/NNN_....php
New translation:   lang/de.php AND lang/en.php
New test:          tests/Unit/Modules/{Name}/ or tests/Integration/
```
