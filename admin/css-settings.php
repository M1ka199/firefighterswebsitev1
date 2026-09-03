<?php
declare(strict_types=1);

$adminTitle = 'Globale CSS-Einstellungen';
$activeNav = 'css-settings';
require_once __DIR__ . '/includes/admin_header.php';
Auth::requireAdmin();

$db = Database::getConnection();

// CSS speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
        die('Ungültiger CSRF-Token');
    }

    $customCss = (string)($_POST['custom_css'] ?? '');
    
    $stmtUp = $db->prepare('INSERT OR REPLACE INTO system_settings (setting_key, setting_value, updated_at) VALUES (?, ?, CURRENT_TIMESTAMP)');
    $stmtUp->execute(['custom_css', $customCss]);

    setFlash('success', 'Globale CSS-Einstellungen erfolgreich gespeichert. Die Änderungen sind sofort auf allen Seiten aktiv!');
    header('Location: /admin/css-settings.php');
    exit;
}

// Aktuelles CSS laden
$stmt = $db->prepare('SELECT setting_value FROM system_settings WHERE setting_key = ?');
$stmt->execute(['custom_css']);
$currentCss = (string)($stmt->fetchColumn() ?: '');

$csrf = Auth::csrfToken();
?>

<div class="max-w-6xl mx-auto space-y-8">

  <!-- Kopfbereich -->
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white">
    <div>
      <span class="text-xs font-bold text-sand uppercase tracking-widest block">Design & Layout</span>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-1">
        Globale CSS-Einstellungen
      </h1>
      <p class="text-slate-600 text-xs sm:text-sm mt-1 font-light">
        Hier können Sie global gültige CSS-Regeln für die gesamte Website definieren. Änderungen werden unmittelbar im Header aller öffentlichen Unterseiten injiziert.
      </p>
    </div>

    <a href="/index.php" target="_blank" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-navy hover:text-white border border-slate-300 text-navy font-extrabold uppercase text-xs tracking-wider transition shadow-sm flex items-center gap-2 self-start sm:self-auto">
      <span>👁️</span> Live-Website ansehen
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Haupt-Editor (2 Spalten) -->
    <div class="lg:col-span-2 space-y-6">
      <form action="/admin/css-settings.php" method="POST" class="space-y-6">
        <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

        <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-4">
          
          <div class="flex items-center justify-between border-b border-slate-100 pb-4">
            <div>
              <h3 class="text-base font-bold text-navy uppercase flex items-center gap-2">
                <span class="w-2.5 h-2.5 bg-sand rounded-sm"></span>
                Custom CSS Code-Editor
              </h3>
              <p class="text-xs text-slate-500 mt-0.5">
                Schreiben Sie Standard-CSS oder überschreiben Sie Klassen und Variablen.
              </p>
            </div>
            
            <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 border border-slate-200 text-slate-600">
              Header Injection: &lt;style&gt;
            </span>
          </div>

          <div class="relative">
            <textarea 
              id="css-editor"
              name="custom_css" 
              rows="18" 
              placeholder="/* Globale CSS-Regeln hier eintragen */&#10;:root {&#10;  /* Variablen überschreiben */&#10;}&#10;&#10;.meine-klasse {&#10;  color: #002b66;&#10;}"
              class="w-full rounded-2xl p-5 font-mono text-xs sm:text-sm bg-slate-900 text-emerald-400 border border-slate-800 shadow-inner focus:outline-none focus:ring-2 focus:ring-sand focus:border-transparent leading-relaxed resize-y"
              spellcheck="false"
            ><?= e($currentCss) ?></textarea>
          </div>

          <div class="flex flex-wrap items-center justify-between gap-4 pt-4 border-t border-slate-100">
            <button type="submit" class="px-8 py-3.5 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm flex items-center gap-2">
              <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>
              <span>CSS-Einstellungen speichern</span>
            </button>

            <button type="button" id="btn-clear-css" class="px-4 py-2.5 rounded-xl bg-slate-100 hover:bg-red-50 hover:text-red-600 text-slate-600 font-bold text-xs uppercase tracking-wider transition border border-slate-200">
              Zurücksetzen / Leeren
            </button>
          </div>

        </div>
      </form>
    </div>

    <!-- Sidebar: CSS-Referenzen & Schnipsel (1 Spalte) -->
    <div class="space-y-6">
      
      <!-- System Variablen -->
      <div class="light-panel rounded-3xl p-6 border border-slate-200 shadow-sm bg-white space-y-4">
        <h3 class="text-sm font-bold text-navy uppercase flex items-center gap-2">
          <span class="text-base">🎨</span>
          Design-System Variablen
        </h3>
        <p class="text-xs text-slate-500 leading-relaxed font-light">
          Diese CSS-Variablen sind global im Theme hinterlegt und können direkt angesprochen oder überschrieben werden:
        </p>

        <div class="space-y-2 text-xs font-mono">
          <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200">
            <span class="text-slate-700">--color-navy</span>
            <div class="flex items-center gap-1.5">
              <span class="w-3.5 h-3.5 rounded-full bg-[#002b66] border border-slate-300"></span>
              <span class="text-[11px] text-slate-500">#002b66</span>
            </div>
          </div>

          <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200">
            <span class="text-slate-700">--color-sand</span>
            <div class="flex items-center gap-1.5">
              <span class="w-3.5 h-3.5 rounded-full bg-[#997c33] border border-slate-300"></span>
              <span class="text-[11px] text-slate-500">#997c33</span>
            </div>
          </div>

          <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200">
            <span class="text-slate-700">--color-alarm</span>
            <div class="flex items-center gap-1.5">
              <span class="w-3.5 h-3.5 rounded-full bg-[#dc2626] border border-slate-300"></span>
              <span class="text-[11px] text-slate-500">#dc2626</span>
            </div>
          </div>

          <div class="flex items-center justify-between p-2 rounded-lg bg-slate-50 border border-slate-200">
            <span class="text-slate-700">--font-sans</span>
            <span class="text-[11px] text-slate-500">Helvetica Neue</span>
          </div>
        </div>
      </div>

      <!-- Quick Snippets / Vorlagen zum Einfügen -->
      <div class="light-panel rounded-3xl p-6 border border-slate-200 shadow-sm bg-white space-y-4">
        <h3 class="text-sm font-bold text-navy uppercase flex items-center gap-2">
          <span class="text-base">⚡</span>
          Vorlagen zum Einfügen
        </h3>
        <p class="text-xs text-slate-500">
          Klicken Sie auf eine Vorlage, um das Snippet in den Editor zu übernehmen:
        </p>

        <div class="space-y-2.5">
          <button type="button" class="btn-insert-snippet w-full text-left p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition text-xs group" data-snippet="/* Überschriften-Schmuck */&#10;h1, h2, h3 {&#10;  letter-spacing: 0.04em;&#10;}">
            <strong class="text-navy block font-bold group-hover:text-sand-dark">Überschriften-Laufweite</strong>
            <span class="text-[11px] text-slate-500">Buchstabenabstand aller Titel optimieren</span>
          </button>

          <button type="button" class="btn-insert-snippet w-full text-left p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition text-xs group" data-snippet="/* Kacheln stärker betonen */&#10;.light-tile {&#10;  border-radius: 1.5rem;&#10;  box-shadow: 0 10px 25px -5px rgba(0, 43, 102, 0.08);&#10;}">
            <strong class="text-navy block font-bold group-hover:text-sand-dark">Kachel-Schatten & Radius</strong>
            <span class="text-[11px] text-slate-500">Plastischere Kacheln mit weichem Schatten</span>
          </button>

          <button type="button" class="btn-insert-snippet w-full text-left p-3 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 transition text-xs group" data-snippet="/* Goldener Hover-Effekt auf Schaltflächen */&#10;.bg-navy:hover {&#10;  background-color: #001f4d !important;&#10;  box-shadow: 0 4px 12px rgba(0, 43, 102, 0.25);&#10;}">
            <strong class="text-navy block font-bold group-hover:text-sand-dark">Button Hover Glow</strong>
            <span class="text-[11px] text-slate-500">Edler Hover-Effekt auf Navy-Buttons</span>
          </button>
        </div>
      </div>

    </div>

  </div>

</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const editor = document.getElementById('css-editor');
  const btnClear = document.getElementById('btn-clear-css');
  const snippetButtons = document.querySelectorAll('.btn-insert-snippet');

  if (btnClear && editor) {
    btnClear.addEventListener('click', () => {
      if (confirm('Möchten Sie den gesamten CSS-Code wirklich leeren?')) {
        editor.value = '';
        editor.focus();
      }
    });
  }

  snippetButtons.forEach(btn => {
    btn.addEventListener('click', () => {
      const snippet = btn.getAttribute('data-snippet');
      if (snippet && editor) {
        if (editor.value.trim().length > 0) {
          editor.value += "\n\n" + snippet;
        } else {
          editor.value = snippet;
        }
        editor.focus();
      }
    });
  });
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
