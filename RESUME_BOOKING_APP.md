# VeloCMS — Booking-App RESUME
> Produkt: veloSolution → veloCMS-PHP → App: Booking
> Gestartet: — | Status: 🔲 Entwicklungsplan ausstehend

---

## Server & Repo
- VPS: velocms@95.217.185.113:22 | Webroot: /var/www/velocms/public
- GitHub: https://github.com/Sascha-Zielke/velocms (main → auto-deploy)
- PHP Error-Log: /var/log/fpm-php.www.log

---

## Entwicklungsplan

> Wird in der nächsten Session eingetragen.

---

## Architektur-Entscheidungen

> Wird nach Eingang des Entwicklungsplans definiert.

---

## Phasen-Übersicht

> Wird nach Eingang des Entwicklungsplans definiert.

---

## Wichtige Hinweise für Folge-Sessions

- `sudo -u velocms git pull` — nie als root
- `php velocms migrate` nach jedem Deploy mit neuen Migrations
- `sudo systemctl reload php8.2-fpm` nach Änderungen an Config/Core
