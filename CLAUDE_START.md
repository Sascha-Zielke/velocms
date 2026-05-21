# VeloCMS — Session Start

## Projekt
- **Name:** VeloCMS
- **Beschreibung:** Modulares PHP 8.2+ CMS, eigenes MVC-Framework
- **Domain:** https://webzite-newmedia.com
- **Admin:** https://webzite-newmedia.com/admin

## Server
- **VPS:** Hetzner, Ubuntu 22.04, IP: 95.217.185.113
- **SSH:** velocms@95.217.185.113 -p 22
- **Webroot:** /var/www/velocms/public
- **PHP:** 8.2, Nginx, MySQL 8.0

## Repository
- **GitHub:** https://github.com/Sascha-Zielke/velocms
- **Skills:** https://github.com/Sascha-Zielke/velocms-skills
- **Branch:** main → auto-deploy via GitHub Actions

## Pflichtlektüre
1. Lies diese Datei
2. Lies RESUME.md
3. Dann erst Code schreiben

## Wichtige Regeln
- Kein Laravel/Symfony — eigenes MVC
- Layer 1 i18n: lang/de.php + lang/en.php
- Layer 2 i18n: _en Spalten in DB — NIEMALS AJAX-basiert
- CSRF auf jedem POST
- e() auf jeder Ausgabe
- PDO Prepared Statements überall
