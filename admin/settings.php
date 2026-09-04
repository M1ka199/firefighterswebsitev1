<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Helpers.php';

Auth::requireAdmin();

$db = Database::getConnection();

// Einstellungen speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
        die('Ungültiger CSRF-Token');
    }

    $keys = [
        'site_name', 'contact_email', 'phone', 'address', 'instagram_url',
        'smtp_host', 'smtp_port', 'smtp_user', 'smtp_pass', 'smtp_encryption',
        'custom_css'
    ];

    $stmtUp = $db->prepare('INSERT OR REPLACE INTO system_settings (setting_key, setting_value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP)');
    
    foreach ($keys as $key) {
        if (isset($_POST[$key])) {
            $val = (string)$_POST[$key];
            // Wenn Passwort leer gelassen wurde, altes Passwort behalten
            if ($key === 'smtp_pass' && empty($val)) {
                continue;
            }
            $stmtUp->execute([$key, $val]);
        }
    }

    setFlash('success', 'System-Einstellungen erfolgreich gespeichert.');
    header('Location: /admin/settings.php');
    exit;
}

$adminTitle = 'System-Einstellungen';
$activeNav = 'settings';
require_once __DIR__ . '/includes/admin_header.php';

// Aktuelle Einstellungen laden
$settings = [];
$stmt = $db->query('SELECT setting_key, setting_value FROM system_settings');
while ($row = $stmt->fetch()) {
    $settings[$row['setting_key']] = $row['setting_value'];
}

$csrf = Auth::csrfToken();
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
  
  <div class="light-panel rounded-3xl p-6 border border-slate-200 shadow-sm bg-white">
    <span class="text-xs font-bold text-sand uppercase tracking-widest block">CMS Modul</span>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-1">
      System-Einstellungen
    </h1>
    <p class="text-slate-600 text-xs mt-1 font-light">
      Konfigurieren Sie SMTP-Mailversand, globales Custom-CSS und Stammdaten der Wehr.
    </p>
  </div>

  <form action="/admin/settings.php" method="POST" class="space-y-8">
    <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

    <!-- 1. E-Mail SMTP-Konfiguration & Live-Status-Check -->
    <div class="light-panel rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm bg-white space-y-6">
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-100 pb-4">
        <div>
          <h3 class="text-lg font-bold text-navy uppercase flex items-center gap-2">
            <span class="w-2.5 h-2.5 bg-sand rounded-sm"></span>
            SMTP-Konfiguration für Formular-Benachrichtigungen
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">
            Für den automatischen Versand von Kontaktanfragen und Schnupperdienst-Meldungen.
          </p>
        </div>

        <!-- Live Status Test Button -->
        <button type="button" id="btn-test-smtp" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-navy hover:text-white border border-slate-300 text-navy text-xs font-bold uppercase tracking-wider transition shadow-sm flex items-center gap-2 self-start sm:self-auto">
          <svg class="w-4 h-4 text-sand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          <span>Verbindung jetzt live testen</span>
        </button>
      </div>

      <!-- Live Test Feedback Box -->
      <div id="smtp-test-result" class="hidden p-4 rounded-xl text-xs font-semibold"></div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div class="sm:col-span-2">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            SMTP Server Host
          </label>
          <input type="text" id="smtp_host" name="smtp_host" value="<?= e($settings['smtp_host'] ?? '') ?>" placeholder="z.B. mail.ihredomain.de oder smtp.ionos.de" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Port
          </label>
          <input type="number" id="smtp_port" name="smtp_port" value="<?= e($settings['smtp_port'] ?? '587') ?>" placeholder="587 oder 465" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Verschlüsselung
          </label>
          <select id="smtp_encryption" name="smtp_encryption" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
            <option value="tls" <?= (($settings['smtp_encryption'] ?? '') === 'tls') ? 'selected' : '' ?>>TLS (STARTTLS - empfohlen für Port 587)</option>
            <option value="ssl" <?= (($settings['smtp_encryption'] ?? '') === 'ssl') ? 'selected' : '' ?>>SSL (Port 465)</option>
            <option value="none" <?= (($settings['smtp_encryption'] ?? '') === 'none') ? 'selected' : '' ?>>Keine Verschlüsselung (Port 25)</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            SMTP Benutzername
          </label>
          <input type="text" id="smtp_user" name="smtp_user" value="<?= e($settings['smtp_user'] ?? '') ?>" placeholder="postmaster@feuerwehr-wulften.de" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            SMTP Passwort
          </label>
          <input type="password" id="smtp_pass" name="smtp_pass" placeholder="•••••••• (leer lassen um altes zu behalten)" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>
      </div>
    </div>


    <!-- 2. Eingabemaske für globales CSS -->
    <div class="light-panel rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm bg-white space-y-4">
      <div class="flex items-center justify-between border-b border-slate-100 pb-4">
        <div>
          <h3 class="text-lg font-bold text-navy uppercase flex items-center gap-2">
            <span class="w-2.5 h-2.5 bg-sand rounded-sm"></span>
            Globales Custom CSS
          </h3>
          <p class="text-xs text-slate-500 mt-0.5">
            Wird automatisch im Header aller Unterseiten der Website eingebunden.
          </p>
        </div>
        <a href="/admin/css-settings.php" class="px-3.5 py-1.5 rounded-xl bg-slate-100 hover:bg-navy hover:text-white text-navy text-xs font-bold uppercase tracking-wider transition border border-slate-300">
          🎨 Zum erweiterten CSS-Editor
        </a>
      </div>

      <div>
        <textarea name="custom_css" rows="6" placeholder="/* Benutzerdefinierte CSS-Regeln */&#10;.custom-highlight { color: #997c33; }" class="light-input w-full rounded-xl p-4 text-xs font-mono resize-y leading-relaxed bg-slate-50"><?= e($settings['custom_css'] ?? '') ?></textarea>
      </div>
    </div>


    <!-- 3. Stammdaten & Allgemeine Angaben -->
    <div class="light-panel rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm bg-white space-y-6">
      <div class="border-b border-slate-100 pb-4">
        <h3 class="text-lg font-bold text-navy uppercase flex items-center gap-2">
          <span class="w-2.5 h-2.5 bg-sand rounded-sm"></span>
          Allgemeine Stammdaten
        </h3>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Offizieller Name der Wehr
          </label>
          <input type="text" name="site_name" value="<?= e($settings['site_name'] ?? 'Freiwillige Feuerwehr Wulften am Harz') ?>" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Zentrale Kontakt-E-Mail
          </label>
          <input type="email" name="contact_email" value="<?= e($settings['contact_email'] ?? 'info@feuerwehr-wulften.de') ?>" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Telefonnummer Feuerwehrhaus
          </label>
          <input type="text" name="phone" value="<?= e($settings['phone'] ?? '+49 5556 112') ?>" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Instagram Profil-URL
          </label>
          <input type="url" name="instagram_url" value="<?= e($settings['instagram_url'] ?? 'https://www.instagram.com') ?>" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
          Anschrift
        </label>
        <input type="text" name="address" value="<?= e($settings['address'] ?? 'Steinstraße 1, 37199 Wulften am Harz') ?>" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
      </div>
    </div>

    <!-- Speichern Button -->
    <div class="pt-2">
      <button type="submit" class="px-10 py-4 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm">
        Alle Einstellungen speichern
      </button>
    </div>

  </form>

</div>

<!-- JavaScript für den SMTP-Live-Status-Check -->
<script>
document.addEventListener('DOMContentLoaded', () => {
  const btn = document.getElementById('btn-test-smtp');
  const resultBox = document.getElementById('smtp-test-result');

  if (!btn || !resultBox) return;

  btn.addEventListener('click', async () => {
    btn.disabled = true;
    const oldText = btn.innerHTML;
    btn.innerHTML = `
      <svg class="animate-spin -ml-1 mr-2 h-4 w-4 text-navy inline-block" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
      </svg>
      Verbindung wird geprüft...
    `;

    resultBox.classList.remove('hidden');
    resultBox.className = 'p-4 rounded-xl text-xs font-semibold bg-slate-100 border border-slate-300 text-slate-700';
    resultBox.innerHTML = 'Prüfe TCP/Socket-Verbindung, EHLO-Banner und STARTTLS...';

    const payload = {
      smtp_host: document.getElementById('smtp_host').value,
      smtp_port: document.getElementById('smtp_port').value,
      smtp_encryption: document.getElementById('smtp_encryption').value,
      smtp_user: document.getElementById('smtp_user').value,
      smtp_pass: document.getElementById('smtp_pass').value
    };

    try {
      const resp = await fetch('/admin/api/test_smtp.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify(payload)
      });

      const data = await resp.json();

      if (data.success) {
        resultBox.className = 'p-4 rounded-xl text-xs font-semibold bg-emerald-50 border border-emerald-300 text-emerald-800 shadow-sm';
        resultBox.innerHTML = `
          <div class="flex items-center gap-2">
            <span class="text-base">✅</span>
            <div>
              <strong class="text-emerald-900 block font-bold">SMTP-Server erreichbar!</strong>
              <span>${data.message}</span>
            </div>
          </div>
        `;
      } else {
        resultBox.className = 'p-4 rounded-xl text-xs font-semibold bg-red-50 border border-red-300 text-red-800 shadow-sm';
        resultBox.innerHTML = `
          <div class="flex items-center gap-2">
            <span class="text-base">❌</span>
            <div>
              <strong class="text-red-900 block font-bold">Verbindungstest fehlgeschlagen</strong>
              <span>${data.message || 'Der Server konnte nicht erreicht werden.'}</span>
            </div>
          </div>
        `;
      }
    } catch (err) {
      resultBox.className = 'p-4 rounded-xl text-xs font-semibold bg-red-50 border border-red-300 text-red-800 shadow-sm';
      resultBox.innerHTML = `Fehler beim Ausführen des Verbindungstests: ${err.message}`;
    } finally {
      btn.disabled = false;
      btn.innerHTML = oldText;
    }
  });
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
