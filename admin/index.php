<?php
declare(strict_types=1);

$adminTitle = 'Dashboard';
$activeNav = 'index';
require_once __DIR__ . '/includes/admin_header.php';

$db = Database::getConnection();
$currentYear = (int)date('Y');

// Statistiken abfragen
$cntEinsaetze = $db->query("SELECT COUNT(*) FROM einsaetze WHERE year = {$currentYear}")->fetchColumn();
$cntMitglieder = $db->query("SELECT COUNT(*) FROM mitglieder")->fetchColumn();
$cntFahrzeuge = $db->query("SELECT COUNT(*) FROM fahrzeuge")->fetchColumn();
$cntTermine = $db->query("SELECT COUNT(*) FROM termine WHERE start_datetime >= date('now')")->fetchColumn();
$cntAnfragenNeu = $db->query("SELECT COUNT(*) FROM form_submissions WHERE status = 'neu'")->fetchColumn();

// Neueste Einsätze
$recentEinsaetze = $db->query("SELECT * FROM einsaetze ORDER BY date DESC, time DESC LIMIT 5")->fetchAll();

// Neueste Anfragen
$recentAnfragen = $db->query("SELECT * FROM form_submissions ORDER BY created_at DESC LIMIT 5")->fetchAll();
?>

<div class="max-w-7xl mx-auto space-y-8">
  
  <!-- Begrüßung & Schnellaktionen (Helle Card) -->
  <div class="flex flex-col md:flex-row md:items-center justify-between gap-4 light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white">
    <div>
      <span class="text-xs font-bold text-sand uppercase tracking-widest block">Übersicht & Verwaltung</span>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-1">
        Willkommen, <?= e($user['full_name']) ?>!
      </h1>
      <p class="text-slate-600 text-xs sm:text-sm mt-1 font-light">
        Hier verwalten Sie Einsätze, Kommandomitglieder, Fuhrpark, Termine und Website-Inhalte der Freiwilligen Feuerwehr Wulften.
      </p>
    </div>

    <!-- Quick Action Buttons -->
    <div class="flex flex-wrap items-center gap-3">
      <a href="/admin/einsatz-edit.php" class="px-4 py-2.5 rounded-xl bg-navy hover:bg-navy-dark text-white text-xs font-bold uppercase tracking-wider transition shadow-sm flex items-center gap-1.5">
        <span>+</span> Neuer Einsatz
      </a>
      <a href="/admin/fahrzeug-edit.php" class="px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-navy hover:bg-slate-50 text-xs font-bold uppercase tracking-wider transition flex items-center gap-1.5">
        <span>+</span> Neues Fahrzeug
      </a>
      <?php if (($user['role'] ?? '') === 'admin'): ?>
        <a href="/admin/css-settings.php" class="px-4 py-2.5 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 text-xs font-bold uppercase tracking-wider transition">
          🎨 Globale CSS
        </a>
      <?php endif; ?>
    </div>
  </div>

  <!-- Statistik Kacheln (Hell) -->
  <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-6">
    <div class="light-tile rounded-2xl p-6 border-l-4 border-sand bg-white">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Einsätze <?= $currentYear ?></span>
        <span class="text-xl">🚨</span>
      </div>
      <span class="text-3xl font-extrabold text-navy block"><?= $cntEinsaetze ?></span>
      <a href="/admin/einsaetze.php" class="text-xs text-sand hover:underline font-bold mt-2 inline-block">Einsätze &rarr;</a>
    </div>

    <div class="light-tile rounded-2xl p-6 border-l-4 border-blue-600 bg-white">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Ortskommando</span>
        <span class="text-xl">👨‍🚒</span>
      </div>
      <span class="text-3xl font-extrabold text-navy block"><?= $cntMitglieder ?></span>
      <a href="/admin/mitglieder.php" class="text-xs text-sand hover:underline font-bold mt-2 inline-block">Kommando &rarr;</a>
    </div>

    <div class="light-tile rounded-2xl p-6 border-l-4 border-orange-500 bg-white">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Fuhrpark</span>
        <span class="text-xl">🚒</span>
      </div>
      <span class="text-3xl font-extrabold text-navy block"><?= $cntFahrzeuge ?></span>
      <a href="/admin/fahrzeuge.php" class="text-xs text-sand hover:underline font-bold mt-2 inline-block">Fahrzeuge &rarr;</a>
    </div>

    <div class="light-tile rounded-2xl p-6 border-l-4 border-emerald-600 bg-white">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Termine</span>
        <span class="text-xl">📅</span>
      </div>
      <span class="text-3xl font-extrabold text-navy block"><?= $cntTermine ?></span>
      <a href="/admin/termine.php" class="text-xs text-sand hover:underline font-bold mt-2 inline-block">Dienstplan &rarr;</a>
    </div>

    <div class="light-tile rounded-2xl p-6 border-l-4 border-red-600 bg-white">
      <div class="flex items-center justify-between mb-2">
        <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Neue Anfragen</span>
        <span class="text-xl">✉️</span>
      </div>
      <span class="text-3xl font-extrabold text-red-600 block"><?= $cntAnfragenNeu ?></span>
      <a href="/admin/anfragen.php" class="text-xs text-sand hover:underline font-bold mt-2 inline-block">Anfragen &rarr;</a>
    </div>
  </div>

  <!-- Zwei Spalten: Neueste Einsätze & Neueste Anfragen -->
  <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
    
    <!-- Linke Spalte: Neueste Einsätze -->
    <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white">
      <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
        <h3 class="text-lg font-bold uppercase text-navy tracking-wider flex items-center gap-2">
          <span class="w-2.5 h-2.5 bg-red-600 rounded-sm"></span>
          Zuletzt erfasste Einsätze
        </h3>
        <a href="/admin/einsaetze.php" class="text-xs font-bold text-sand hover:underline uppercase">Alle anzeigen</a>
      </div>

      <?php if (empty($recentEinsaetze)): ?>
        <p class="text-slate-400 text-xs py-4">Noch keine Einsätze erfasst.</p>
      <?php else: ?>
        <div class="space-y-3">
          <?php foreach ($recentEinsaetze as $e): ?>
            <div class="p-3.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-between gap-3 transition">
              <div class="truncate">
                <div class="flex items-center gap-2 text-xs mb-1">
                  <span class="font-bold text-navy">#<?= $e['incident_number'] ?>/<?= $e['year'] ?></span>
                  <span class="text-slate-500">• <?= formatDateGerman($e['date']) ?></span>
                  <span class="uppercase text-[10px] px-1.5 py-0.5 rounded bg-white border border-slate-200 text-slate-700 font-semibold"><?= e($e['category']) ?></span>
                </div>
                <h4 class="text-sm font-bold text-navy truncate"><?= e($e['title']) ?></h4>
              </div>
              <a href="/admin/einsatz-edit.php?id=<?= $e['id'] ?>" class="px-3 py-1.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-navy text-xs font-bold uppercase transition flex-shrink-0">
                Bearbeiten
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

    <!-- Rechte Spalte: Neueste Formular-Meldungen -->
    <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white">
      <div class="flex items-center justify-between mb-6 pb-4 border-b border-slate-100">
        <h3 class="text-lg font-bold uppercase text-navy tracking-wider flex items-center gap-2">
          <span class="w-2.5 h-2.5 bg-sand rounded-sm"></span>
          Eingegangene Anfragen
        </h3>
        <a href="/admin/anfragen.php" class="text-xs font-bold text-sand hover:underline uppercase">Alle anzeigen</a>
      </div>

      <?php if (empty($recentAnfragen)): ?>
        <p class="text-slate-400 text-xs py-4">Keine offenen Anfragen vorhanden.</p>
      <?php else: ?>
        <div class="space-y-3">
          <?php foreach ($recentAnfragen as $a): ?>
            <div class="p-3.5 rounded-xl bg-slate-50 hover:bg-slate-100 border border-slate-200 flex items-center justify-between gap-3 transition">
              <div class="truncate">
                <div class="flex items-center gap-2 text-xs mb-1">
                  <?php if ($a['type'] === 'schnupperdienst'): ?>
                    <span class="px-2 py-0.5 rounded text-[10px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-300">🔥 Schnupperdienst</span>
                  <?php else: ?>
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-900 border border-blue-200">Kontakt</span>
                  <?php endif; ?>
                  <span class="text-slate-500 text-[11px]"><?= formatDateGerman($a['created_at']) ?></span>
                </div>
                <h4 class="text-sm font-bold text-navy truncate"><?= e($a['name']) ?> (<?= e($a['email']) ?>)</h4>
              </div>
              <a href="/admin/anfragen.php" class="px-3 py-1.5 rounded-lg bg-white border border-slate-300 hover:bg-slate-50 text-navy text-xs font-bold uppercase transition flex-shrink-0">
                Details
              </a>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
