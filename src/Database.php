<?php
declare(strict_types=1);

class Database {
    private static ?PDO $instance = null;
    private static string $dbFile = __DIR__ . '/../database/feuerwehr.sqlite';

    public static function getConnection(): PDO {
        if (self::$instance === null) {
            $isNew = !file_exists(self::$dbFile);
            
            $dbDir = dirname(self::$dbFile);
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }

            self::$instance = new PDO('sqlite:' . self::$dbFile);
            self::$instance->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            self::$instance->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
            self::$instance->exec('PRAGMA foreign_keys = ON;');

            if ($isNew) {
                self::initSchema(self::$instance);
                self::seedInitialData(self::$instance);
            } else {
                self::initSchema(self::$instance);
                self::ensureFahrzeugeData(self::$instance);
                self::ensureHierarchienData(self::$instance);
            }
        }
        return self::$instance;
    }

    public static function initSchema(PDO $db): void {
        $schema = <<<SQL
        PRAGMA foreign_keys = ON;

        CREATE TABLE IF NOT EXISTS users (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            username VARCHAR(50) NOT NULL UNIQUE,
            password_hash VARCHAR(255) NOT NULL,
            full_name VARCHAR(100) NOT NULL,
            role VARCHAR(20) DEFAULT 'admin',
            last_login DATETIME,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

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

        CREATE TABLE IF NOT EXISTS mitglieder (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            name VARCHAR(100) NOT NULL,
            rank VARCHAR(80) NOT NULL,
            role_title VARCHAR(100) NOT NULL,
            hierarchy_level INTEGER NOT NULL DEFAULT 3, -- 1: Wehrfuehrung, 2: Stellvertreter, 3: Fachwarte, 4: Erweitertes Kommando
            photo_url VARCHAR(255),
            email VARCHAR(100),
            phone VARCHAR(50),
            show_on_homepage INTEGER DEFAULT 0,
            sort_order INTEGER DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

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

        CREATE TABLE IF NOT EXISTS hero_slides (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            title VARCHAR(150) NOT NULL,
            subtitle TEXT,
            bg_image_url VARCHAR(255) NOT NULL,
            button_text VARCHAR(50) DEFAULT 'Ueber uns',
            button_link VARCHAR(255) DEFAULT '/ueber-uns.php',
            sort_order INTEGER DEFAULT 0,
            is_active INTEGER DEFAULT 1
        );

        CREATE TABLE IF NOT EXISTS system_settings (
            setting_key VARCHAR(50) PRIMARY KEY,
            setting_value TEXT NOT NULL,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP
        );

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

        CREATE TABLE IF NOT EXISTS kommando_hierarchien (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            level INTEGER NOT NULL UNIQUE,
            title VARCHAR(150) NOT NULL,
            description TEXT,
            sort_order INTEGER DEFAULT 0
        );
SQL;
        $db->exec($schema);
    }

    public static function seedInitialData(PDO $db): void {
        // 1. Admin-Nutzer anlegen (Passwort: admin123)
        $adminHash = password_hash('admin123', PASSWORD_DEFAULT);
        $stmtUser = $db->prepare('INSERT OR IGNORE INTO users (id, username, password_hash, full_name, role) VALUES (1, ?, ?, ?, ?)');
        $stmtUser->execute(['admin', $adminHash, 'Administrator FF Wulften', 'admin']);

        // 2. Standard-Einstellungen
        $settings = [
            'site_name' => 'Freiwillige Feuerwehr Wulften am Harz',
            'contact_email' => 'info@feuerwehr-wulften.de',
            'phone' => '+49 5556 112',
            'address' => 'Steinstrasse 1, 37199 Wulften am Harz',
            'instagram_url' => 'https://www.instagram.com',
            'smtp_host' => 'smtp.example.com',
            'smtp_port' => '587',
            'smtp_user' => 'postmaster@feuerwehr-wulften.de',
            'smtp_pass' => '',
            'smtp_encryption' => 'tls',
            'custom_css' => '/* Hier koennen Sie benutzerdefiniertes globales CSS eintragen */'
        ];
        $stmtSet = $db->prepare('INSERT OR REPLACE INTO system_settings (setting_key, setting_value) VALUES (?, ?)');
        foreach ($settings as $k => $v) {
            $stmtSet->execute([$k, $v]);
        }

        // 3. Hero Slide
        $stmtHero = $db->prepare('
            INSERT OR IGNORE INTO hero_slides (id, title, subtitle, bg_image_url, button_text, button_link, is_active)
            VALUES (1, ?, ?, ?, ?, ?, 1)
        ');
        $stmtHero->execute([
            'Gemeinschaft. Einsatz. Ehrensache.',
            'Seit ueber einem Jahrhundert rund um die Uhr fuer den Schutz und die Sicherheit der Buergerinnen und Buerger in Wulften am Harz und Umgebung im Einsatz.',
            '/uploads/hero/hero-firefighters.jpg',
            'Ueber uns',
            '/ueber-uns.php'
        ]);

        // 4. Seiten & SEO mit Bannern
        $pages = [
            [
                'slug' => 'startseite',
                'page_title' => 'Freiwillige Feuerwehr Wulften am Harz | 24/7 Einsatzbereit',
                'meta_description' => 'Offizieller Webauftritt der Freiwilligen Feuerwehr Wulften am Harz. Aktuelle Einsaetze, Termine, Schnupperdienst und Ortskommando.',
                'keywords' => 'Feuerwehr Wulften am Harz, Freiwillige Feuerwehr, Einsaetze Wulften, Notruf 112, Brandschutz, Jugendfeuerwehr',
                'banner_title' => 'Willkommen bei der FF Wulften',
                'banner_intro' => 'Ihre Freiwillige Feuerwehr im Harzvorland.'
            ],
            [
                'slug' => 'ueber-uns',
                'page_title' => 'Ueber Uns | Freiwillige Feuerwehr Wulften am Harz',
                'meta_description' => 'Erfahren Sie mehr ueber unsere Geschichte, unsere Ausruestung, Fahrzeuge und die starke Gemeinschaft in Wulften am Harz.',
                'keywords' => 'Ueber uns Feuerwehr Wulften, LF 10, Fuhrpark Feuerwehr, Geschichte Feuerwehr Wulften',
                'banner_title' => 'Ueber unsere Feuerwehr',
                'banner_intro' => 'Tradition, modernste Technik und ehrenamtliches Engagement fuer unsere Gemeinde seit Generationen.'
            ],
            [
                'slug' => 'einsaetze',
                'page_title' => 'Einsaetze & Alarmierungen | FF Wulften am Harz',
                'meta_description' => 'Uebersicht ueber alle Brand- und Hilfeleistungseinsaetze der Freiwilligen Feuerwehr Wulften am Harz.',
                'keywords' => 'Einsaetze Wulften, Einsatzberichte, Brand, Technische Hilfeleistung, Notfaelle',
                'banner_title' => 'Einsatzberichte & Statistiken',
                'banner_intro' => 'Aktuelle und transparente Einsatzberichte unserer Wehr, geordnet nach Einsatzart und Jahrgang.'
            ],
            [
                'slug' => 'kommando',
                'page_title' => 'Ortskommando & Fuehrung | FF Wulften am Harz',
                'meta_description' => 'Die Fuehrungskraefte und Funktionstraeger der Freiwilligen Feuerwehr Wulften am Harz im Ueberblick.',
                'keywords' => 'Ortsbrandmeister Wulften, Fuehrung Feuerwehr, Ortskommando, Dienstgrade',
                'banner_title' => 'Das Ortskommando',
                'banner_intro' => 'Unsere Fuehrungsmannschaft traegt Verantwortung fuer Organisation, Ausbildung und Einsatzbereitschaft.'
            ],
            [
                'slug' => 'termine',
                'page_title' => 'Dienstplan & Termine | FF Wulften am Harz',
                'meta_description' => 'Uebersicht aller Ausbildungsdienste, Uebungen und oeffentlichen Veranstaltungen der FF Wulften.',
                'keywords' => 'Feuerwehr Termine Wulften, Ausbildungsdienst, Jugendfeuerwehr Termine',
                'banner_title' => 'Dienstplan & Termine',
                'banner_intro' => 'Alle anstehenden Uebungsdienste, Veranstaltungen und Termine der Jugend- und Einsatzabteilung.'
            ],
            [
                'slug' => 'kontakt',
                'page_title' => 'Kontakt | Freiwillige Feuerwehr Wulften am Harz',
                'meta_description' => 'Nehmen Sie Kontakt mit der Feuerwehr Wulften auf. Fuer Notfaelle waehlen Sie immer die 112.',
                'keywords' => 'Kontakt Feuerwehr Wulften, Adresse Feuerwehrhaus, Ansprechpartner',
                'banner_title' => 'Kontakt & Ansprechpartner',
                'banner_intro' => 'Wir freuen uns ueber Ihre Nachricht. Im akuten Notfall waehlen Sie bitte unverzueglich den Notruf 112!'
            ],
            [
                'slug' => 'schnupperdienst',
                'page_title' => 'Schnupperdienst & Mitmachen | FF Wulften am Harz',
                'meta_description' => 'Werde Teil unseres Teams! Melde dich unverbindlich zum Schnupperdienst bei der Feuerwehr Wulften an.',
                'keywords' => 'Schnupperdienst Feuerwehr, Mitmachen Feuerwehr Wulften, Ehrenamt, Quereinsteiger',
                'banner_title' => 'Lust auf einen Schnupperdienst?',
                'banner_intro' => 'Erlebe Feuerwehr hautnah: Mach mit in unserem starken Team und schuetze deine Heimat!'
            ],
            [
                'slug' => 'impressum',
                'page_title' => 'Impressum | FF Wulften am Harz',
                'meta_description' => 'Rechtliche Angaben und Impressum der Freiwilligen Feuerwehr Wulften am Harz.',
                'keywords' => 'Impressum Feuerwehr Wulften',
                'banner_title' => 'Impressum',
                'banner_intro' => 'Rechtliche Hinweise und Angaben gemaess Digitale-Dienste-Gesetz (DDG).'
            ],
            [
                'slug' => 'datenschutz',
                'page_title' => 'Datenschutz | FF Wulften am Harz',
                'meta_description' => 'Datenschutzerklaerung gemaess DSGVO der Freiwilligen Feuerwehr Wulften am Harz.',
                'keywords' => 'Datenschutz Feuerwehr Wulften',
                'banner_title' => 'Datenschutzerklaerung',
                'banner_intro' => 'Informationen ueber die Erhebung und Verarbeitung personenbezogener Daten nach DSGVO.'
            ]
        ];

        $stmtPage = $db->prepare('
            INSERT OR IGNORE INTO seiten_seo (slug, page_title, meta_description, keywords, banner_title, banner_intro)
            VALUES (?, ?, ?, ?, ?, ?)
        ');
        foreach ($pages as $p) {
            $stmtPage->execute([$p['slug'], $p['page_title'], $p['meta_description'], $p['keywords'], $p['banner_title'], $p['banner_intro']]);
        }

        // 5. Mitglieder (Ortskommando Wulften)
        $mitglieder = [
            [
                'name' => 'Michael Müller',
                'rank' => 'Erster Hauptbrandmeister',
                'role_title' => 'Ortsbrandmeister',
                'hierarchy_level' => 1,
                'photo_url' => '/uploads/mitglieder/portrait-chef.jpg',
                'email' => 'ortsbrandmeister@feuerwehr-wulften.de',
                'phone' => '+49 5556 112-10',
                'show_on_homepage' => 1,
                'sort_order' => 1
            ],
            [
                'name' => 'Sarah Lindemann',
                'rank' => 'Oberbrandmeisterin',
                'role_title' => 'Stellv. Ortsbrandmeisterin',
                'hierarchy_level' => 2,
                'photo_url' => '/uploads/mitglieder/portrait-vize.jpg',
                'email' => 'vize@feuerwehr-wulften.de',
                'phone' => '+49 5556 112-11',
                'show_on_homepage' => 1,
                'sort_order' => 2
            ],
            [
                'name' => 'Tobias Becker',
                'rank' => 'Hauptlöschmeister',
                'role_title' => 'Gruppenführer Gruppe 1',
                'hierarchy_level' => 3,
                'photo_url' => null,
                'email' => 'gruppe1@feuerwehr-wulften.de',
                'phone' => null,
                'show_on_homepage' => 1,
                'sort_order' => 3
            ],
            [
                'name' => 'Janina Kruse',
                'rank' => 'Löschmeisterin',
                'role_title' => 'Jugendfeuerwehrwartin',
                'hierarchy_level' => 3,
                'photo_url' => null,
                'email' => 'jugend@feuerwehr-wulften.de',
                'phone' => null,
                'show_on_homepage' => 1,
                'sort_order' => 4
            ],
            [
                'name' => 'Florian Hartung',
                'rank' => 'Oberfeuerwehrmann',
                'role_title' => 'Gerätewart & Atemschutz',
                'hierarchy_level' => 4,
                'photo_url' => null,
                'email' => 'geraete@feuerwehr-wulften.de',
                'phone' => null,
                'show_on_homepage' => 0,
                'sort_order' => 5
            ]
        ];

        $stmtM = $db->prepare('
            INSERT OR IGNORE INTO mitglieder 
            (name, rank, role_title, hierarchy_level, photo_url, email, phone, show_on_homepage, sort_order)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        foreach ($mitglieder as $m) {
            $stmtM->execute([
                $m['name'], $m['rank'], $m['role_title'], $m['hierarchy_level'],
                $m['photo_url'], $m['email'], $m['phone'], $m['show_on_homepage'], $m['sort_order']
            ]);
        }

        // 6. Einsätze Demo-Daten
        $einsaetze = [
            [
                'year' => 2026,
                'incident_number' => 18,
                'title' => 'B 2 – Zimmerbrand im Wohngebiet',
                'keyword' => 'B 2 - Zimmerbrand',
                'category' => 'brand',
                'date' => '2026-08-28',
                'time' => '22:15',
                'location' => 'Lindenweg, Wulften am Harz',
                'description' => 'Alarmierung zu einem Zimmerbrand im ersten Obergeschoss eines Einfamilienhauses. Ein Atemschutztrupp ging mit einem C-Rohr in das Gebaeude vor und loeschte den Entstehungsbrand in der Kueche schnell ab. Eine Person wurde mit Verdacht auf Rauchgasinhalation an den Rettungsdienst uebergeben. Anschliessend erfolgten Belueftungsmassnahmen mit dem Druckluefter.',
                'image_url' => '/uploads/einsaetze/einsatz_brand.jpg',
                'vehicles' => 'LF 10, MTW, Rettungsdienst, Polizei'
            ],
            [
                'year' => 2026,
                'incident_number' => 17,
                'title' => 'TH 2 – Verkehrsunfall mit eingeklemmter Person',
                'keyword' => 'TH 2 - VU mit P-Klemmt',
                'category' => 'th',
                'date' => '2026-08-14',
                'time' => '17:42',
                'location' => 'B243 Richtung Hattorf am Harz',
                'description' => 'Schwerer Verkehrsunfall mit zwei beteiligten Fahrzeugen. In enger Zusammenarbeit mit dem Rettungsdienst wurde die technische Rettung eingeleitet. Mittels hydraulischem Rettungssatz (Schere und Spreizer) wurde das Fahrzeugdach entfernt, um eine schonende Rettung der verletzten Person zu gewaehrleisten. Parallel wurde der Brandschutz sichergestellt.',
                'image_url' => '/uploads/einsaetze/einsatz_th.jpg',
                'vehicles' => 'LF 10, MTW, Notarzt, RTW, Rettungshubschrauber Christoph 44'
            ],
            [
                'year' => 2026,
                'incident_number' => 16,
                'title' => 'TH 1 – Umgestürzter Baum blockiert Fahrbahn',
                'keyword' => 'TH 1 - Unwetter',
                'category' => 'th',
                'date' => '2026-07-22',
                'time' => '06:30',
                'location' => 'Kreisstrasse K7, Wulften Richtung Dorste',
                'description' => 'Aufgrund einer sommerlichen Gewitterfront stuerzte ein ausgewachsener Baum quer ueber die Fahrbahn. Die Einsatzstelle wurde abgesichert, der Baum mittels Motorsaege zerkleinert und das Holz sicher an den Strassenrand verbracht. Die Fahrbahn wurde gereinigt.',
                'image_url' => '/uploads/einsaetze/einsatz_th.jpg',
                'vehicles' => 'LF 10'
            ],
            [
                'year' => 2026,
                'incident_number' => 15,
                'title' => 'BMA – Auslösung Brandmeldeanlage Industriebetrieb',
                'keyword' => 'BMA 1 - Einlauf',
                'category' => 'bma',
                'date' => '2026-06-11',
                'time' => '11:05',
                'location' => 'Gewerbegebiet Wulften',
                'description' => 'Die Brandmeldeanlage eines Gewerbebetriebs meldete Feueralarm. Nach Erkundung des zustaendigen Melders konnte Entwarnung gegeben werden: Es handelte sich um eine Taeuschung durch Schweissarbeiten in einer Werkhalle. Anlage zurueckgestellt.',
                'image_url' => null,
                'vehicles' => 'LF 10, MTW'
            ],
            [
                'year' => 2025,
                'incident_number' => 31,
                'title' => 'B 3 – Flaechenbrand Getreidefeld',
                'keyword' => 'B 3 - Flaechenbrand',
                'category' => 'brand',
                'date' => '2025-07-19',
                'time' => '15:10',
                'location' => 'Feldmark Wulften am Harz',
                'description' => 'Flaechenbrand auf ca. 2.000 qm abgeerntetem Getreidefeld bei hochsommerlichen Temperaturen. Durch raschen Loeschangriff mit mehreren D- und C-Rohren und Unterstuetzung oertlicher Landwirte mit Grubbern konnte ein Uebergreifen auf den angrenzenden Wald verhindert werden.',
                'image_url' => '/uploads/einsaetze/einsatz_brand.jpg',
                'vehicles' => 'LF 10, MTW, TLF Nachbarwehren'
            ]
        ];

        $stmtE = $db->prepare('
            INSERT OR IGNORE INTO einsaetze
            (year, incident_number, title, keyword, category, date, time, location, description, image_url, vehicles)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        foreach ($einsaetze as $e) {
            $stmtE->execute([
                $e['year'], $e['incident_number'], $e['title'], $e['keyword'],
                $e['category'], $e['date'], $e['time'], $e['location'],
                $e['description'], $e['image_url'], $e['vehicles']
            ]);
        }

        // 7. Termine Demo-Daten
        $termine = [
            [
                'title' => 'Praktischer Ausbildungsdienst: Technische Hilfeleistung & Hebekissen',
                'category' => 'dienst',
                'start_datetime' => '2026-09-11 19:00:00',
                'end_datetime' => '2026-09-11 21:30:00',
                'location' => 'Feuerwehrhaus Wulften',
                'description' => 'Schwerpunkt: Fahrzeugstabilisierung und sicheres Arbeiten mit Hoch- und Niederdruck-Hebekissen.'
            ],
            [
                'title' => 'Jugendfeuerwehr Dienst: Fahrzeug- & Geraetekunde',
                'category' => 'jugend',
                'start_datetime' => '2026-09-15 17:30:00',
                'end_datetime' => '2026-09-15 19:00:00',
                'location' => 'Feuerwehrhaus Wulften',
                'description' => 'Kennenlernen der Geraetschaften auf dem LF 10 und spielerische Knotenkunde.'
            ],
            [
                'title' => 'Atemschutz-Belastungsuebung (FTZ)',
                'category' => 'dienst',
                'start_datetime' => '2026-09-26 08:30:00',
                'end_datetime' => '2026-09-26 13:00:00',
                'location' => 'Feuerwehrtechnische Zentrale (FTZ)',
                'description' => 'Jaehrliche Streckendurchgangsuebung fuer alle tauglichen Atemschutzgeraetetraeger.'
            ],
            [
                'title' => 'Tag der offenen Tuer & Schnuppertag 2026',
                'category' => 'oeffentlich',
                'start_datetime' => '2026-10-04 11:00:00',
                'end_datetime' => '2026-10-04 17:00:00',
                'location' => 'Feuerwehrhaus Steinstrasse 1',
                'description' => 'Grosses Rahmenprogramm fuer Gross und Klein: Schauuebungen, Fuehrungen, Huepfburg, Grillstation und Schnupperdienst-Station!'
            ]
        ];

        $stmtT = $db->prepare('
            INSERT OR IGNORE INTO termine (title, category, start_datetime, end_datetime, location, description, is_public)
            VALUES (?, ?, ?, ?, ?, ?, 1)
        ');
        foreach ($termine as $t) {
            $stmtT->execute([$t['title'], $t['category'], $t['start_datetime'], $t['end_datetime'], $t['location'], $t['description']]);
        }

        self::ensureFahrzeugeData($db);
        self::ensureHierarchienData($db);
    }

    public static function ensureFahrzeugeData(PDO $db): void {
        // Migration: ensure responsible_person column exists
        try {
            $db->exec("ALTER TABLE fahrzeuge ADD COLUMN responsible_person VARCHAR(150)");
        } catch (Throwable $e) {}

        $cnt = (int)$db->query("SELECT COUNT(*) FROM fahrzeuge")->fetchColumn();
        if ($cnt === 0) {
            $stmtIns = $db->prepare('
                INSERT INTO fahrzeuge 
                (name, bezeichnung, tactical_role, callsign, responsible_person, description, technical_data, photo_url, sort_order, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
            ');

            $stmtIns->execute([
                'LF 10',
                'Löschgruppenfahrzeug LF 10',
                'Erstangreifer & Brandbekämpfung',
                'Florian Göttingen 14-45-1',
                'Gerätewart: Tobias Bornemann',
                'Das Löschgruppenfahrzeug LF 10 ist das erstausrückende Einsatzfahrzeug der Feuerwehr Wulften für Brandeinsätze und technische Hilfeleistungen. Ausgestattet mit einem 1.200 Liter Wassertank und moderner Ausrüstung zur schnellen Menschenrettung.',
                "Fahrgestell: MAN / Aufbau: Schlingmann\nBesatzung: 1/8 (Gruppe)\nLöschwasser: 1.200 Liter\nPumpe: FPN 10-2000 (2.000 l/min bei 10 bar)\nBesonderheiten: 4 Atemschutzgeräte (2 im Mannschaftsraum), Schnellangriff, Stromerzeuger, Beleuchtungssatz, hydraulischer Rettungssatz",
                '/assets/img/einsatz-th.jpg',
                1
            ]);

            $stmtIns->execute([
                'MTW',
                'Mannschaftstransportwagen',
                'Mannschaftstransport & Führung',
                'Florian Göttingen 14-17-1',
                'Fahrzeugpate: Sarah Lindemann',
                'Der Mannschaftstransportwagen dient dem sicheren Transport weiterer Einsatzkräfte zur Einsatzstelle, als Führungsfahrzeug bei Flächenlagen sowie für die Fahrten der Jugend- und Kinderfeuerwehr zu Wettbewerben und Lagern.',
                "Fahrgestell: Volkswagen Crafter / Transporter\nBesatzung: 1/7 (8 Sitzplätze)\nAusstattung: Mobiles Funkgerät, Handlampen, Absicherungsmaterial, Verkehrsleitkegel, Notfallrucksack, Anhängerkupplung",
                '/assets/img/hero-firefighters.jpg',
                2
            ]);
        }

        // Aktualisiere bestehende Fahrzeuge mit zuständiger Person falls noch leer
        $db->exec("UPDATE fahrzeuge SET responsible_person = 'Gerätewart: Tobias Bornemann' WHERE name = 'LF 10' AND (responsible_person IS NULL OR responsible_person = '')");
        $db->exec("UPDATE fahrzeuge SET responsible_person = 'Fahrzeugpate: Sarah Lindemann' WHERE name = 'MTW' AND (responsible_person IS NULL OR responsible_person = '')");

        // Ergänze weitere Fahrzeuge falls weniger als 4 vorhanden sind
        $cntNow = (int)$db->query("SELECT COUNT(*) FROM fahrzeuge")->fetchColumn();
        if ($cntNow < 4) {
            $stmtMore = $db->prepare('
                INSERT INTO fahrzeuge 
                (name, bezeichnung, tactical_role, callsign, responsible_person, description, technical_data, photo_url, sort_order, is_active)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
            ');

            $stmtMore->execute([
                'TLF 3000',
                'Tanklöschfahrzeug TLF 3000',
                'Vegetationsbrandbekämpfung & Wassertransport',
                'Florian Göttingen 14-24-1',
                'Gerätewart: Christian Meier',
                'Spezialisiert auf die zügige Bereitstellung großer Löschwassermengen bei Vegetations- und Waldbränden im Harzvorland sowie auf Bundesstraßen. Mit Geländeallrad und Dachwerfer ideal für anspruchsvolles Terrain.',
                "Fahrgestell: MAN TGM 4x4 Allrad\nBesatzung: 1/2 (Trupp)\nLöschwasser: 3.000 Liter\nSchaummittel: 200 Liter\nPumpe: FPN 10-2000 (2.000 l/min bei 10 bar)\nBesonderheiten: Dachwerfer (Monitor), Waldbrand-D-Schläuche, Selbstschutzdüsen, Allradantrieb",
                '/assets/img/einsatz-brand.jpg',
                3
            ]);

            $stmtMore->execute([
                'GW-L',
                'Gerätewagen Logistik / Transport',
                'Logistik, Materialtransport & Sonderlagen',
                'Florian Göttingen 14-64-1',
                'Zeugwart: Sven Hoffmann',
                'Flexibles Transportfahrzeug für Sonderausrüstung wie Hochwasser-Tauchpumpen, zusätzliche B-Schläuche, Bindemittel und Verpflegung bei langanhaltenden Großschadenslagen und Unwettern.',
                "Ladefläche mit hydraulischer Dautel-Ladebordwand (1.000 kg)\nBesatzung: 1/5 (Staffel)\nRollcontainer-System für Hochwasser, Strom, Beleuchtung und 1.000m B-Schlauch\nAusstattung: LED-Umfeldbeleuchtung, Heckwarnsystem",
                '/assets/img/einsatz-sonstige.jpg',
                4
            ]);
        }
    }

    public static function ensureHierarchienData(PDO $db): void {
        $cnt = (int)$db->query("SELECT COUNT(*) FROM kommando_hierarchien")->fetchColumn();
        if ($cnt > 0) return;

        $defaults = [
            [1, 'Ortsbrandmeister (Wehrleitung)', 'Führung der Ortsfeuerwehr und Dienstaufsicht', 1],
            [2, 'Stellvertretende Wehrleitung', 'Unterstützung und Stellvertretung des Ortsbrandmeisters', 2],
            [3, 'Gruppenführer & Fachwarte', 'Führung von taktischen Einheiten sowie Fachbereiche wie Atemschutz, Funk, Ausbildung', 3],
            [4, 'Erweitertes Ortskommando & Gerätewarte', 'Gerätewarte, Sicherheitsbeauftragte, Schriftführer und Kassenwarte', 4],
        ];

        $stmt = $db->prepare('INSERT INTO kommando_hierarchien (level, title, description, sort_order) VALUES (?, ?, ?, ?)');
        foreach ($defaults as $d) {
            $stmt->execute($d);
        }
    }
}
