-- =============================================================================
-- SQLite Datenbankschema fuer Freiwillige Feuerwehr Wulften am Harz
-- =============================================================================

PRAGMA foreign_keys = ON;

-- 1. Benutzer (Admin / Redakteure)
CREATE TABLE IF NOT EXISTS users (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    username VARCHAR(50) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    full_name VARCHAR(100) NOT NULL,
    role VARCHAR(20) DEFAULT 'admin',
    last_login DATETIME,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 2. Einsätze
CREATE TABLE IF NOT EXISTS einsaetze (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    year INTEGER NOT NULL,
    incident_number INTEGER NOT NULL,
    title VARCHAR(150) NOT NULL,
    keyword VARCHAR(50) NOT NULL,
    category VARCHAR(20) NOT NULL, -- 'brand', 'th', 'sonstige', 'bma'
    date DATE NOT NULL,
    time TIME NOT NULL,
    location VARCHAR(150) NOT NULL,
    description TEXT NOT NULL,
    image_url VARCHAR(255),
    vehicles VARCHAR(255),
    is_published INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);
CREATE INDEX IF NOT EXISTS idx_einsaetze_year ON einsaetze(year);
CREATE INDEX IF NOT EXISTS idx_einsaetze_category ON einsaetze(category);

-- 3. Mitglieder (Ortskommando & Führung)
CREATE TABLE IF NOT EXISTS mitglieder (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    rank VARCHAR(80) NOT NULL,
    role_title VARCHAR(100) NOT NULL,
    hierarchy_level INTEGER NOT NULL DEFAULT 3, -- 1: Wehrleitung, 2: Stellvertreter, 3: Fachwarte, 4: Erweitertes Kommando
    photo_url VARCHAR(255),
    email VARCHAR(100),
    phone VARCHAR(50),
    show_on_homepage INTEGER DEFAULT 0,
    sort_order INTEGER DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 4. Termine & Dienstplan
CREATE TABLE IF NOT EXISTS termine (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(150) NOT NULL,
    category VARCHAR(30) NOT NULL, -- 'dienst', 'jugend', 'kinder', 'oeffentlich', 'kommando'
    start_datetime DATETIME NOT NULL,
    end_datetime DATETIME,
    location VARCHAR(150) DEFAULT 'Feuerwehrhaus Wulften',
    description TEXT,
    is_public INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 5. CMS-Seiten & SEO-Konfiguration
CREATE TABLE IF NOT EXISTS seiten_seo (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    slug VARCHAR(50) NOT NULL UNIQUE,
    page_title VARCHAR(150) NOT NULL,
    meta_description VARCHAR(255) NOT NULL,
    keywords VARCHAR(255),
    banner_title VARCHAR(150),
    banner_intro TEXT,
    og_image VARCHAR(255),
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 6. Hero-Slider Startseite
CREATE TABLE IF NOT EXISTS hero_slides (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    title VARCHAR(150) NOT NULL,
    subtitle TEXT,
    bg_image_url VARCHAR(255) NOT NULL,
    button_text VARCHAR(50) DEFAULT 'Über uns',
    button_link VARCHAR(255) DEFAULT '/ueber-uns.php',
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1
);

-- 7. Globale Systemeinstellungen
CREATE TABLE IF NOT EXISTS system_settings (
    setting_key VARCHAR(50) PRIMARY KEY,
    setting_value TEXT NOT NULL,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 8. Kontakt- & Schnupperdienst-Anmeldungen
CREATE TABLE IF NOT EXISTS form_submissions (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    type VARCHAR(30) NOT NULL, -- 'kontakt', 'schnupperdienst'
    name VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL,
    phone VARCHAR(50),
    age INTEGER,
    message TEXT,
    status VARCHAR(20) DEFAULT 'neu', -- 'neu', 'in_bearbeitung', 'erledigt'
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 9. Fahrzeuge (Fuhrpark & Technik)
CREATE TABLE IF NOT EXISTS fahrzeuge (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    name VARCHAR(100) NOT NULL,
    bezeichnung VARCHAR(150) NOT NULL,
    tactical_role VARCHAR(100),
    callsign VARCHAR(100),
    responsible_person VARCHAR(150),
    description TEXT NOT NULL,
    technical_data TEXT,
    photo_url VARCHAR(255),
    sort_order INTEGER DEFAULT 0,
    is_active INTEGER DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP
);

-- 10. Eigene Hierarchiestufen für Ortskommando
CREATE TABLE IF NOT EXISTS kommando_hierarchien (
    id INTEGER PRIMARY KEY AUTOINCREMENT,
    level INTEGER NOT NULL UNIQUE,
    title VARCHAR(150) NOT NULL,
    description TEXT,
    sort_order INTEGER DEFAULT 0
);

