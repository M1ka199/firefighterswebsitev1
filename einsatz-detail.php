<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Helpers.php';

$id = (int)($_GET['id'] ?? 0);
$db = Database::getConnection();

$stmt = $db->prepare('SELECT * FROM einsaetze WHERE id = ? AND is_published = 1 LIMIT 1');
$stmt->execute([$id]);
$einsatz = $stmt->fetch();

if (!$einsatz) {
    header('Location: /einsaetze.php');
    exit;
}

$seo = [
    'page_title' => $einsatz['title'] . ' | FF Wulften am Harz',
    'meta_description' => substr(strip_tags($einsatz['description']), 0, 160),
    'keywords' => 'Einsatzbericht, Feuerwehr Wulften, ' . $einsatz['keyword'],
    'banner_title' => 'Einsatzbericht #' . $einsatz['incident_number'] . ' / ' . $einsatz['year'],
    'banner_intro' => $einsatz['title']
];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/banner.php';
?>

<div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

  <div class="mb-6">
    <a href="/einsaetze.php" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-navy hover:text-sand transition">
      <span>&larr;</span> Zurück zur Einsatzübersicht
    </a>
  </div>

  <article class="light-panel rounded-3xl overflow-hidden border border-slate-200 shadow-sm">
    
    <!-- Bildbereich falls vorhanden -->
    <?php if (!empty($einsatz['image_url'])): ?>
      <div class="relative w-full h-72 sm:h-96 md:h-[420px] bg-slate-100 overflow-hidden">
        <img src="<?= e($einsatz['image_url']) ?>" alt="<?= e($einsatz['title']) ?>" class="w-full h-full object-cover">
        <div class="absolute bottom-4 left-4 right-4 flex items-center justify-between">
          <div><?= getCategoryBadge($einsatz['category']) ?></div>
          <span class="text-xs text-slate-700 bg-white/95 px-3 py-1.5 rounded-lg border border-slate-200 shadow-sm font-medium">
            Einsatzfoto © FF Wulften
          </span>
        </div>
      </div>
    <?php endif; ?>

    <div class="p-6 sm:p-10">
      
      <!-- Fakten-Box (Grid) -->
      <div class="grid grid-cols-2 md:grid-cols-4 gap-4 p-5 rounded-2xl bg-slate-50 border border-slate-200 mb-8">
        <div>
          <span class="block text-[11px] font-bold text-sand uppercase tracking-wider">Datum</span>
          <span class="text-sm sm:text-base font-bold text-navy"><?= formatDateGerman($einsatz['date']) ?></span>
        </div>
        <div>
          <span class="block text-[11px] font-bold text-sand uppercase tracking-wider">Alarmzeit</span>
          <span class="text-sm sm:text-base font-bold text-navy"><?= formatTimeGerman($einsatz['time']) ?></span>
        </div>
        <div>
          <span class="block text-[11px] font-bold text-sand uppercase tracking-wider">Stichwort</span>
          <span class="text-sm sm:text-base font-bold text-navy truncate block"><?= e($einsatz['keyword']) ?></span>
        </div>
        <div>
          <span class="block text-[11px] font-bold text-sand uppercase tracking-wider">Einsatzort</span>
          <span class="text-sm sm:text-base font-bold text-navy truncate block"><?= e($einsatz['location']) ?></span>
        </div>
      </div>

      <!-- Überschrift -->
      <h1 class="text-2xl sm:text-3xl lg:text-4xl font-extrabold uppercase text-navy mb-6 leading-tight">
        <?= e($einsatz['title']) ?>
      </h1>

      <!-- Lagebericht -->
      <div class="prose max-w-none text-slate-700 text-base sm:text-lg leading-relaxed space-y-4 mb-8">
        <h2 class="text-lg font-bold uppercase tracking-wider text-sand border-b border-slate-200 pb-2">
          Einsatzbericht
        </h2>
        <p class="whitespace-pre-line font-light">
          <?= nl2br(e($einsatz['description'])) ?>
        </p>
      </div>

      <!-- Eingesetzte Kräfte & Fahrzeuge -->
      <?php if (!empty($einsatz['vehicles'])): ?>
        <div class="p-5 rounded-2xl bg-amber-50/70 border border-amber-200">
          <span class="block text-xs font-bold text-amber-800 uppercase tracking-widest mb-1">
            Eingesetzte Einheiten & Rettungsmittel:
          </span>
          <p class="text-sm sm:text-base text-navy font-semibold">
            <?= e($einsatz['vehicles']) ?>
          </p>
        </div>
      <?php endif; ?>

      <!-- Datenschutz-Hinweis -->
      <div class="mt-8 pt-6 border-t border-slate-200 text-xs text-slate-500 flex items-center gap-2">
        <svg class="w-4 h-4 text-sand flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
        <span>Hinweis: Einsatzberichte dienen ausschließlich der sachlichen Information der Bevölkerung. Personenbezogene Daten und Kennzeichen werden geschützt.</span>
      </div>

    </div>

  </article>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
