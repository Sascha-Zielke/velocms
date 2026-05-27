# VeloCMS — Booking-App RESUME
> Produkt: veloSolution → veloCMS-PHP → App: Booking
> Gestartet: 2026-05-27 | Status: ✅ Phase 2 abgeschlossen — Phase 3 offen

---

## Server & Repo
- VPS: velocms@95.217.185.113:22 | Webroot: /var/www/velocms/public
- GitHub: https://github.com/Sascha-Zielke/velocms (main → auto-deploy)
- PHP Error-Log: /var/log/fpm-php.www.log
- Stack: PHP 8.2.31 · Nginx 1.18 · MySQL 8.0.45

---

## Arbeitsregeln für den AI-Agent

| Regel | Detail |
|---|---|
| **/compact** | Bei 60% Kontext-Auslastung sofort `/compact` durchführen |
| **Audits** | Nach jeder Phase: Audit 1 (Code-Review) + Audit 2 (Live-Verify) |
| **RESUME** | RESUME_BOOKING_APP.md nach jeder Phase aktualisieren |
| **Nächste Phase** | Erst starten wenn beide Audits ✅ grün sind |
| **Commits** | Session-End-Protokoll aus git-workflow.md einhalten |

---

## Architektur-Entscheidungen (ADR)

### ADR-001: Universal Resource Scheduling (URS)
- **Entscheidung:** Alles Buchbare ist eine generische `Resource` mit einem `type`-Feld (human, room, asset). Keine spezialisierten Tabellen für Tische, Mitarbeiter, Räume.
- **Konsequenz:** Hohe Abstraktion in PHP. Branchenspezifische Parameter leben in einem `metadata`-JSON-Feld (MySQL JSON-Column).

### ADR-002: Temporal Integrity via MySQL SELECT FOR UPDATE
- **Kontext:** PostgreSQL GiST-Exclusion-Constraints stehen nicht zur Verfügung (VPS läuft MySQL 8.0.45).
- **Entscheidung:** Double-Booking-Schutz via Datenbank-Transaktion + `SELECT ... FOR UPDATE` auf der Ressource, gefolgt von App-Level-Overlap-Check innerhalb der Transaktion. Kein Booking wird geschrieben, wenn `start_at < existing.end_at AND end_at > existing.start_at` für dieselbe Resource zutrifft.
- **Konsequenz:** Konsistenz wird durch DB-Lock garantiert, nicht durch einen Unique-Constraint — funktioniert zuverlässig auch unter hoher Last.

### ADR-003: UTC-Speicherung + Timezone-Handling in der View
- **Entscheidung:** Alle Timestamps in MySQL als `DATETIME` in UTC. Timezone-Umrechnung ausschließlich im View-Layer (PHP `DateTimeImmutable` + `DateTimeZone`).
- **Konsequenz:** Sichere DB-Indizierung, keine Timezone-Bugs in Cronjobs.

### ADR-004: Modulstruktur folgt VeloCMS-Konventionen
- **Entscheidung:** `modules/Booking/` statt separatem Root-Verzeichnis. `BookingModule::boot()` statt `manifest.json`-Parser. Migrations via bestehendes `php velocms migrate`-System.
- **Konsequenz:** Keine neue Infrastruktur, kein zweites Composer-Paket, kein Manifest-Parser.

### ADR-005: Extension-System via PHP-Interface
- **Entscheidung:** Kein globales WordPress-artiges Hook-System im Core. Branchenspezifische Templates implementieren ein `BookingTemplateInterface` und werden per Setting aktiviert. Action/Filter-Hooks bleiben scoped innerhalb des Booking-Moduls.
- **Konsequenz:** Erweiterbar ohne Core-Änderungen, aber keine systemweite Hook-API nötig.

### ADR-006: REST-API via bestehendem Controller-Pattern
- **Entscheidung:** Kein GraphQL. JSON-Endpoints via `$this->json()` (VeloCMS-Controller-Pattern). Public-Endpoints für das Frontend-Widget, Admin-Endpoints hinter `requireAuth()`.
- **Konsequenz:** Keine neue API-Schicht, keine neue Abhängigkeit.

### ADR-007: Kein Redis, kein S3
- **Entscheidung:** PHP-native Sessions (bereits im VeloCMS vorhanden). Datei-Uploads via bestehendem Media-Modul. Redis und S3 sind auf dem VPS nicht vorhanden und werden nicht benötigt.

---

## Verzeichnisstruktur

```
modules/Booking/
├── BookingModule.php                        (boot, install, Routen, Menü)
├── migrations/
│   ├── 001_create_booking_resources.php
│   ├── 002_create_booking_slots.php
│   ├── 003_create_bookings.php
│   ├── 004_create_booking_requirements.php
│   └── 005_create_booking_templates.php
├── Core/
│   ├── Entities/
│   │   ├── Resource.php                     (generische buchbare Ressource)
│   │   ├── Booking.php
│   │   └── TimeSlot.php
│   ├── ValueObjects/
│   │   ├── DateTimeRange.php
│   │   ├── BookingStatus.php
│   │   └── ResourceType.php
│   ├── Services/
│   │   ├── AvailabilityEngine.php           (Slot-Berechnung, Conflict-Check)
│   │   └── BookingService.php               (CRUD, SELECT FOR UPDATE)
│   └── Contracts/
│       └── BookingTemplateInterface.php
├── Extensions/
│   ├── Restaurant/
│   │   └── RestaurantTemplate.php
│   ├── Handwerker/
│   │   └── HandwerkerTemplate.php
│   └── Studio/
│       └── StudioTemplate.php
├── Models/
│   ├── ResourceModel.php
│   ├── BookingModel.php
│   └── SlotModel.php
├── Controllers/
│   ├── Admin/
│   │   ├── AdminBookingController.php
│   │   ├── AdminResourceController.php
│   │   └── AdminTemplateController.php
│   └── Api/
│       ├── ApiAvailabilityController.php    (public)
│       └── ApiBookingController.php         (public + auth)
└── views/
    ├── admin/
    │   ├── booking/
    │   │   ├── dashboard.php
    │   │   ├── index.php
    │   │   └── detail.php
    │   ├── resource/
    │   │   ├── index.php
    │   │   └── form.php
    │   └── template/
    │       └── settings.php
    └── widget/
        ├── booking-form.php
        └── calendar.php
```

---

## DB-Tabellenstruktur (MySQL, Prefix: velocms_booking_)

```sql
-- Generische buchbare Ressourcen (Tisch, Mitarbeiter, Raum, Asset)
velocms_booking_resources   (id, name, type ENUM(human,room,asset), template_key, metadata JSON, is_active, created_at, updated_at)

-- Verfügbarkeitsfenster pro Ressource (Öffnungszeiten / Schichten)
velocms_booking_slots       (id, resource_id, weekday TINYINT, start_time TIME, end_time TIME, is_active)

-- Buchungen
velocms_bookings            (id, resource_id, customer_name, customer_email, customer_phone, start_at DATETIME, end_at DATETIME, status ENUM(pending,confirmed,canceled), notes TEXT, metadata JSON, created_at, updated_at, canceled_at)

-- Anforderungen / Abhängigkeiten zwischen Ressourcen
velocms_booking_requirements (id, booking_id, resource_id, quantity TINYINT)

-- Branchenspezifische Template-Konfigurationen
velocms_booking_templates   (id, template_key VARCHAR(50), config JSON, created_at, updated_at)
```

---

## Phasen-Übersicht

| Phase | Titel | Status |
|-------|-------|--------|
| 1 | Foundation: Modulstruktur, Migrations, Core-Entities | ✅ Abgeschlossen |
| 2 | AvailabilityEngine + BookingService (SELECT FOR UPDATE) | ✅ Abgeschlossen |
| 3 | Admin-UI: Dashboard, Ressourcen, Buchungsübersicht | 🔲 Offen |
| 4 | Extension-System: BookingTemplateInterface + 3 Branchen-Templates | 🔲 Offen |
| 5 | REST-API + Frontend-Buchungswidget | 🔲 Offen |
| 6 | E-Mail-Benachrichtigungen + Bestätigungen | 🔲 Offen |
| 7 | Tests + Final-Audit | 🔲 Offen |

---

## Phase 1 — Foundation
**Status:** ✅ Abgeschlossen

### Erstellte Dateien
```
modules/Booking/BookingModule.php
modules/Booking/migrations/001_create_booking_resources.php
modules/Booking/migrations/002_create_booking_slots.php
modules/Booking/migrations/003_create_bookings.php
modules/Booking/migrations/004_create_booking_requirements.php
modules/Booking/migrations/005_create_booking_templates.php
modules/Booking/Core/Entities/Resource.php
modules/Booking/Core/Entities/Booking.php
modules/Booking/Core/Entities/TimeSlot.php
modules/Booking/Core/ValueObjects/DateTimeRange.php
modules/Booking/Core/ValueObjects/BookingStatus.php
modules/Booking/Core/ValueObjects/ResourceType.php
modules/Booking/Core/Contracts/BookingTemplateInterface.php
lang/de.php                                                  (booking.* Keys)
lang/en.php                                                  (booking.* Keys)
```

**Audit 1 (Code-Review):** ✅ Fix: ResourceType::label() nutzt jetzt t() statt hardcoded German  
**Audit 2 (Live-Verify):** ✅ 5 Migrations (Batch 13), PHP-Syntax ok, 73/73 Tests grün  
**Commits:** b15db42 (Phase 1), f9e2fdd (ResourceType fix)

---

## Phase 2 — AvailabilityEngine + BookingService
**Status:** ✅ Abgeschlossen

### Erstellte Dateien
```
modules/Booking/Core/Services/AvailabilityEngine.php
modules/Booking/Core/Services/BookingService.php
modules/Booking/Core/Services/BookingConflictException.php
modules/Booking/Core/Services/BookingOutsideSlotsException.php
modules/Booking/Models/ResourceModel.php
modules/Booking/Models/BookingModel.php
modules/Booking/Models/SlotModel.php
```

**Audit 1 (Code-Review):** ✅ Fix: DateTimeImmutable::modify() false-Guard in isWithinSlot()  
**Audit 2 (Live-Verify):** ✅ PHP-Syntax ok, 73/73 Tests grün  
**Commit:** 945a9a9

---

## Phase 3 — Admin-UI
**Status:** 🔲 Offen

### Geplante Dateien
```
modules/Booking/Controllers/Admin/AdminBookingController.php
modules/Booking/Controllers/Admin/AdminResourceController.php
modules/Booking/Controllers/Admin/AdminTemplateController.php
modules/Booking/views/admin/booking/dashboard.php
modules/Booking/views/admin/booking/index.php
modules/Booking/views/admin/booking/detail.php
modules/Booking/views/admin/resource/index.php
modules/Booking/views/admin/resource/form.php
modules/Booking/views/admin/template/settings.php
```

**Audit 1 (Code-Review):** 🔲  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Phase 4 — Extension-System + Branchen-Templates
**Status:** 🔲 Offen

### Geplante Dateien
```
modules/Booking/Extensions/Restaurant/RestaurantTemplate.php
modules/Booking/Extensions/Handwerker/HandwerkerTemplate.php
modules/Booking/Extensions/Studio/StudioTemplate.php
```

**Audit 1 (Code-Review):** 🔲  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Phase 5 — REST-API + Frontend-Widget
**Status:** 🔲 Offen

### Geplante Dateien
```
modules/Booking/Controllers/Api/ApiAvailabilityController.php
modules/Booking/Controllers/Api/ApiBookingController.php
modules/Booking/views/widget/booking-form.php
modules/Booking/views/widget/calendar.php
public/assets/js/booking-widget.js
public/assets/css/booking-widget.css
```

**Audit 1 (Code-Review):** 🔲  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Phase 6 — E-Mail-Benachrichtigungen
**Status:** 🔲 Offen

### Geplante Dateien
```
modules/Booking/Core/Services/BookingMailer.php
modules/Booking/views/mail/confirmation.php
modules/Booking/views/mail/cancellation.php
```

**Audit 1 (Code-Review):** 🔲  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Phase 7 — Tests + Final-Audit
**Status:** 🔲 Offen

### Geplante Dateien
```
tests/Unit/Modules/Booking/AvailabilityEngineTest.php
tests/Unit/Modules/Booking/BookingServiceTest.php
tests/Unit/Modules/Booking/DateTimeRangeTest.php
RESUME.md                       (Update — Phase 24)
RESUME_BOOKING_APP.md           (Final-Update)
```

**Audit 1 (Code-Review):** 🔲  
**Audit 2 (Live-Verify):** 🔲  
**Commit:** —

---

## Wichtige Hinweise für Folge-Sessions

- `sudo -u velocms git pull` — nie als root
- `php velocms migrate` nach jedem Deploy mit neuen Migrations
- `sudo systemctl reload php8.2-fpm` nach Änderungen an Config/Core
- Double-Booking-Schutz: immer via `BookingService` — nie direkt in Controller schreiben
- Timestamps: immer UTC speichern, Timezone-Umrechnung nur im View-Layer
- Neue Branchen-Templates: `BookingTemplateInterface` implementieren, per Setting aktivieren — keine Core-Änderung nötig
