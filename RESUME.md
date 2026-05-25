# VeloCMS — RESUME.md
> Stand: 2026-05-25 | Letzte Session: Phase 0+5 Server-Provisioning & CI/CD

## Server
- IP: 95.217.185.113 | SSH Port: 22 | User: velocms
- PHP 8.2.31 · Nginx 1.18 · MySQL 8.0.45 · Composer 2.9.8

## Abgeschlossene Phasen

| Phase | Inhalt | Status |
|-------|--------|--------|
| 0 | Server Provisioning (PHP, Nginx, MySQL, Composer) | ✅ DONE 2026-05-25 |
| 1 | Core Framework (Router, Controller, Model, View) | ✅ DONE |
| 2 | Auth-Modul (Login, CSRF, Rollen, 16 Tests grün) | ✅ DONE |
| 3 | PHP 8 Fixes, DB-Connection | ✅ DONE |
| 5 | CI/CD: GitHub Actions (auto-deploy auf push→main) | ✅ DONE 2026-05-25 |

## Aktueller Stand

- Repo geklont: /var/www/velocms ✅
- .env konfiguriert ✅
- DB: velocms_site_a (velocms_users, velocms_sites, velocms_migrations) ✅
- Superadmin: s.zielke84@gmail.com / superadminPower-Pracht-Lachs!#123 ✅
- GitHub Actions: run #11 success ✅
- PSR-4: 19 Klassen, kein Fehler ✅
- Admin erreichbar: http://95.217.185.113/admin (HTTP 200) ✅
- Homepage: 404 (kein Frontend-Controller implementiert, erwartet)

## Nächste Phase: 6 — Pages-Modul + Visual Editor

Aufgaben:
1. SSL via certbot (Let's Encrypt) für webzite-newmedia.com
2. Pages-Modul: CRUD (Admin + Frontend)
3. Visual Editor: Page → Sections → Rows → Boxes (JSON)
4. Frontend-Renderer für Sections/Rows/Boxes
5. Media-Upload-Modul (EXIF-Strip, WebP)

## Wichtige Pfade
- Webroot: /var/www/velocms/public/
- Storage: /var/www/velocms/storage/tenants/
- DB User: velocms / VeloCMS_DB_Secure_2026!
- DB Name: velocms_site_a
- Master DB: velocms_master
