# VeloCMS — Backoffice Website-Workflow

> Dieses Dokument beschreibt den **manuellen Prozess** zum Aufbau einer neuen Website auf Basis von veloCMS.
> Es dient als Spezifikationsgrundlage für den späteren automatisierten Backoffice-Flow:
> **veloSolution → Neue Website → Web-Engineering → Mockup hochladen → Site generieren**

---

## 1. Inputs (Was braucht der Workflow?)

### 1.1 Pflicht-Inputs
| Input | Beschreibung | Später im Backoffice |
|---|---|---|
| **Mockup** | Screenshot, Figma-Export, Skizze oder Foto | Upload-Feld (PNG/PDF/Figma-Link) |
| **Branche** | Restaurant, Handwerker, Agentur, … | Dropdown / Template-Auswahl |
| **Domainname** | Ziel-Domain der Website | DNS-Feld + Tenant-Provisioning |
| **Primärfarbe** | Hauptfarbe aus dem Mockup | Colorpicker |
| **Logo** | Datei oder Platzhalter | Datei-Upload |
| **Sprachen** | Welche Sprachen soll die Site haben? | Mehrfachauswahl |

### 1.2 Optionale Inputs
| Input | Beschreibung | Standard |
|---|---|---|
| Sekundärfarbe | Akzentfarbe | Wird aus Primärfarbe abgeleitet |
| Schriftart | Google Fonts Name | System-Font-Stack |
| Slogan / Tagline | Wird im Hero verwendet | — |
| Kontakt-E-Mail | Für Kontaktformular-Routing | site_email aus Settings |
| Social-Media-Links | Footer-Icons | — |

---

## 2. Analyse-Phase (Was erkennt der Agent aus dem Mockup?)

Der Agent liest das Mockup und extrahiert:

### 2.1 Layout-Struktur
- Wie viele **Sektionen** hat die Startseite? (Header, Hero, Features, CTA, Footer …)
- Gibt es eine **Navigation** (sticky, transparent, sidebar)?
- Gibt es ein **Blog**-Bereich?
- Gibt es ein **Kontaktformular**?
- Gibt es eine **Buchungs-Funktion**?

### 2.2 Visuelle Tokens
- Primär- und Sekundärfarbe (hex)
- Border-Radius (eckig / abgerundet / rund)
- Schriftart-Stil (serif / sans-serif / monospace)
- Spacing-Dichte (kompakt / normal / großzügig)
- Dark Mode vorhanden? (ja / nein)

### 2.3 Content-Slots
Für jede Sektion: Welche Felder braucht sie?

| Sektion | Felder |
|---|---|
| Hero | Überschrift, Unterzeile, CTA-Text, CTA-URL, Hintergrundbild |
| Features | Titel + Icon + Text (wiederholt, 3–6×) |
| Über uns | Überschrift, Fließtext, Bild |
| Leistungen | Liste mit Titel + Beschreibung |
| Testimonials | Name + Zitat + Foto (wiederholt) |
| CTA-Banner | Überschrift, Button-Text, Button-URL |
| Kontakt | Formular (Name, E-Mail, Nachricht) + optionale Karte |
| Footer | Logo, Links, Copyright, Social |

---

## 3. Entscheidungen (Was legt der Agent fest?)

Vor dem Bauen werden diese Entscheidungen explizit dokumentiert:

```
Primärfarbe:     #______
Sekundärfarbe:   #______
Schriftart:      ______
Border-Radius:   ______px
Sektionen:       [Hero, Features, Über uns, Kontakt, Footer]
Booking-Widget:  ja / nein
Blog:            ja / nein
Mehrsprachig:    [de, en]
Template-Key:    generic / restaurant / handwerker / studio
```

Im späteren Backoffice: **Vorschau dieser Entscheidungen vor dem Generieren** (Confirmation-Step).

---

## 4. Build-Phase (Was wird gebaut?)

### 4.1 CSS / Design-Tokens
```
/public/css/sites/{domain}/theme.css
  — CSS-Variablen: --color-primary, --color-secondary, --font-family, --radius-base …
  — Wird auf Layout-CSS aus VeloCMS aufgesetzt (kein Reset des Core-CSS)
```

### 4.2 Views / Templates
```
/modules/Pages/views/frontend/{domain}/
  ├── home.php          (Startseite mit allen Sektionen)
  ├── sections/
  │   ├── hero.php
  │   ├── features.php
  │   ├── about.php
  │   ├── cta.php
  │   └── contact.php
  └── layout.php        (Header + Footer für diese Site)
```

Alternativ: Sektionen werden im **Visual Editor** (Pages → Sections → Rows → Boxes) angelegt, sodass sie über das CMS bearbeitbar sind.

### 4.3 Seiten-Einträge im CMS
- Startseite wird als Page angelegt (slug: `/`)
- Navigation wird in `velocms_nav_items` eingetragen
- Kontaktformular-Route wird aktiviert
- Booking-Widget wird auf der gewünschten Seite eingebunden (falls vorhanden)

### 4.4 Settings
```
site_name       = "…"
site_email      = "…"
default_language = "de"
active_languages = ["de","en"]
```

---

## 5. Qualitätssicherung

| Check | Beschreibung |
|---|---|
| Responsive | Mobile (375px), Tablet (768px), Desktop (1280px) |
| Dark Mode | Sofern im Mockup vorhanden |
| Kontrastprüfung | Primärfarbe auf Weiß/Schwarz (WCAG AA) |
| Formulare | CSRF, Honeypot, Validierung |
| Performance | Keine externen Fonts ohne Fallback, keine uncompressed images |
| Booking | Slot-Berechnung, Doppelbuchungsschutz, E-Mail |
| SEO | Title, Description, OG-Tags, sitemap.xml |

---

## 6. Übergabe / Abschluss

- Git-Commit mit `feat({domain}): initial site build`
- Backup-Tag: `backup/{domain}-v1.0`
- RESUME.md aktualisieren

---

## 7. Erkenntnisse für den späteren Backoffice-Flow

*(Wird nach dem ersten manuellen Durchlauf ausgefüllt)*

### Was war aufwändig und sollte automatisiert werden?
- [ ] …

### Was hat der Agent aus dem Mockup **nicht** erkannt / nachfragen müssen?
- [ ] …

### Welche Inputs waren unklar und brauchen ein besseres UI?
- [ ] …

### Welche Entscheidungen trifft der Agent gut alleine?
- [ ] …

### Welche Entscheidungen brauchen zwingend menschliche Bestätigung?
- [ ] …

---

*Erstellt: 2026-05-27 | Erster Durchlauf: ausstehend*
