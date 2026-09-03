# Freiwillige Feuerwehr Wulften am Harz – Website & Custom CMS

Moderne, minimalistische und professionelle Website inklusive maßgeschneidertem Admin-CMS für die **Freiwillige Feuerwehr Wulften am Harz**.

---

## 🚒 Highlights & Features

1. **Design-System & UI/UX:**
   - **Farbschema:** Tiefblau (`#002b66`) und edles Sand/Messing (`#997c33`) mit dezenten Feuer- und Alarm-Akzenten.
   - **Typografie:** Eurostile Next Pro (mit optimierten Fallbacks: Eurostile, Rajdhani, Chakra Petch).
   - **Kachel-Design mit Glassmorphismus:** Mehrstufige Unschärfe-Filter (`backdrop-blur`), zarte Randreflexionen und dynamische Tiefenwirkung.
   - **Globaler Unterseiten-Banner:** Einheitlicher Banner mit Titel, Breadcrumb und Einleitungstext auf allen Unterseiten.

2. **Frontend-Seiten:**
   - **Startseite (`index.php`):** Hero-Bereich mit dynamischem Bild und Call-to-Action, Letzte Einsätze in Glass-Kacheln, Ansprechpartner der Wehr, "Lust auf einen Schnupperdienst" Banner.
   - **Einsätze (`einsaetze.php`):** Visuelle Jahresstatistik mit Countern und Prozentbalken, interaktiver Live-Filter (Jahr, Einsatzart Brand/TH/BMA, Freitextsuche).
   - **Einsatz-Detail (`einsatz-detail.php`):** Detailansicht mit Lagebericht, Fakten-Box, Fahrzeugliste und Datenschutz-Hinweisen.
   - **Ortskommando (`kommando.php`):** Führungskräfte strukturiert nach 4 Hierarchiestufen (Wehrleitung, Stellvertretung, Gruppenführer/Fachwarte, Erweitertes Kommando).
   - **Dienstplan & Termine (`termine.php`):** Interaktive Datums-Kacheln mit Monats- und Kategoriefilter.
   - **Über Uns (`ueber-uns.php`):** Historie, Einsatzabteilung, Jugendfeuerwehr und Fuhrpark (LF 10, MTW).
   - **Kontakt & Schnupperdienst (`kontakt.php`, `schnupperdienst.php`):** Spezieller Schnupperdienst-Funnel für Quereinsteiger mit AJAX-Absendung und Notruf-112-Sicherheitshinweis.
   - **Rechtliches:** Impressum (`impressum.php` nach DDG) & Datenschutz (`datenschutz.php` nach DSGVO).

3. **Backend & Custom CMS (`/admin/`):**
   - **Interner Bereich:** Geschützt durch Session-Auth und gehashte Passwörter (`password_hash` mit Argon2id/Bcrypt).
   - **Einsatz-Verwaltung:** Vollständige CRUD-Operationen mit automatischem Dateiupload (JPEG/PNG/WebP) und Jahresnummerierung.
   - **Mitglieder-Verwaltung:** Verwaltung der Führungskräfte mit Dienstgraden, Hierarchiestufen und Startseiten-Toggle.
   - **Termin-Verwaltung:** Termine für Dienstpläne erstellen und löschen.
   - **Formular-Einsendungen:** Verwaltung aller Kontakt- und Schnupperdienst-Meldungen mit Status-Workflow (Neu &rarr; In Bearbeitung &rarr; Erledigt).
   - **Seiten- & SEO-Verwaltung:** Anpassung von `<title>`, Meta-Descriptions, Keywords und den Bannern aller Unterseiten.
   - **Hero-Slider-Verwaltung:** Bearbeitung des Startseiten-Headers (Titel, Untertitel, Bild-Upload, Button-Link).
   - **System-Einstellungen:** 
     - Globaler Custom-CSS Editor.
     - **E-Mail SMTP-Konfiguration mit Live-Status-Prüfung:** Echtzeit Socket-Handshake (EHLO, STARTTLS, AUTH) ohne Neuladen der Seite.

4. **Datenbank:**
   - Lokale SQLite 3 Datenbank (`database/feuerwehr.sqlite`).
   - Automatische Initialisierung und Seeding beim ersten Seitenaufruf.
   - Geschützt vor direktem HTTP-Download durch `.htaccess`.

---

## 🚀 Schnellstart & Lokale Ausführung

### Option A: Mit PHP Built-In Server
Führen Sie im Projektverzeichnis folgenden Befehl aus:
```bash
php -S localhost:8000
```
Öffnen Sie anschließend im Browser:
* **Website:** [http://localhost:8000](http://localhost:8000)
* **CMS Admin-Panel:** [http://localhost:8000/admin/login.php](http://localhost:8000/admin/login.php)

### Option B: Mit XAMPP, Laragon oder Webhosting
Kopieren Sie den Ordner einfach in Ihr Webverzeichnis (z.B. `C:\xampp\htdocs\firefighterswebsitev1` oder `/var/www/html/`). PHP 7.4 oder 8.x mit aktivierter SQLite3/PDO-Erweiterung genügt.

---

## 🔐 Standard-Zugangsdaten (CMS)

* **Benutzername:** `admin`
* **Passwort:** `admin123`

*(Das Passwort kann im CMS oder per `password_hash()` in der Datenbank geändert werden)*

---

## 📁 Projektstruktur

```
firefighterswebsitev1/
├── database/
│   ├── .htaccess             # Schutz vor direktem Download
│   ├── feuerwehr.sqlite       # Lokale SQLite-Datenbank
│   └── schema.sql            # Referenz-Schema (DDG/SQL)
├── src/
│   ├── Database.php          # Singleton PDO & Auto-Migration/Seeder
│   ├── Auth.php              # Session, Login, Logout, CSRF
│   └── Helpers.php           # SEO, Badges, Datumsformatierung, Flash
├── assets/
│   ├── css/custom.css        # Eurostile Next Pro & Glassmorphismus
│   ├── js/main.js            # Sticky Header & Navigation
│   ├── js/filter-einsaetze.js# Live-Filter nach Jahr, Art & Suchtext
│   ├── js/forms.js           # Asynchrone AJAX Formularverarbeitung
│   └── img/                  # Einsatzfotos, Mannschaft & Portraits
├── includes/
│   ├── header.php            # Globaler Sticky Header & Navigation
│   ├── banner.php            # Globaler Unterseiten-Banner
│   └── footer.php            # 3-Spalten Footer & Notruf 112
├── uploads/                  # Verzeichnis für CMS Uploads
├── api/
│   └── submit_form.php       # Formular Backend-Handler
├── admin/                    # Maßgeschneidertes Admin-Dashboard
│   ├── index.php             # Dashboard-Übersicht
│   ├── login.php / logout.php# Auth-Seiten
│   ├── einsaetze.php / einsatz-edit.php
│   ├── mitglieder.php / mitglied-edit.php
│   ├── termine.php
│   ├── anfragen.php
│   ├── seiten.php
│   ├── hero.php
│   ├── settings.php          # SMTP-Check & Global CSS
│   └── api/test_smtp.php     # Live-Socket-Test
├── index.php                 # Startseite
├── ueber-uns.php
├── einsaetze.php / einsatz-detail.php
├── kommando.php
├── termine.php
├── kontakt.php
├── schnupperdienst.php
├── impressum.php
├── datenschutz.php
└── init_db.php               # Standalone Setup-Skript
```
