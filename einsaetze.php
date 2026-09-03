<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Helpers.php';

$db = Database::getConnection();
$seo = getPageSeo('einsaetze');

// 1. Einsätze laden
$stmt = $db->query('SELECT * FROM einsaetze WHERE is_published = 1 ORDER BY date DESC, time DESC');
$alleEinsaetze = $stmt->fetchAll();

// 2. Verfügbare Jahre ermitteln
$stmtYears = $db->query('SELECT DISTINCT year FROM einsaetze WHERE is_published = 1 ORDER BY year DESC');
$jahre = $stmtYears->fetchAll(PDO::FETCH_COLUMN);
$currentYear = (int)date('Y');

// 3. Jahresstatistik für das aktuelle/neueste Jahr
$latestYear = !empty($jahre) ? (int)$jahre[0] : $currentYear;
$statsYear = (int)($_GET['year'] ?? $latestYear);

$stmtStats = $db->prepare('
    SELECT 
        COUNT(*) as total,
        SUM(CASE WHEN category = "brand" THEN 1 ELSE 0 END) as count_brand,
        SUM(CASE WHEN category = "th" THEN 1 ELSE 0 END) as count_th,
        SUM(CASE WHEN category = "bma" THEN 1 ELSE 0 END) as count_bma,
        SUM(CASE WHEN category = "sonstige" THEN 1 ELSE 0 END) as count_sonstige
    FROM einsaetze 
    WHERE is_published = 1 AND year = ?
');
$stmtStats->execute([$statsYear]);
$stats = $stmtStats->fetch() ?: ['total' => 0, 'count_brand' => 0, 'count_th' => 0, 'count_bma' => 0, 'count_sonstige' => 0];

$extraScripts = ['/assets/js/filter-einsaetze.js'];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/banner.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">

  <!-- Visuelle Jahresstatistik (Hell & Aufgeräumt) -->
  <section class="light-panel rounded-3xl p-6 sm:p-8 mb-12 border border-slate-200 shadow-sm">
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 border-b border-slate-200 pb-5 mb-6">
      <div>
        <span class="text-xs font-bold text-sand uppercase tracking-widest">Offizielle Jahresauswertung</span>
        <h2 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-0.5">
          Einsatzstatistik <?= $statsYear ?>
        </h2>
      </div>
      <div class="text-xs text-slate-500 font-medium">
        Stand: <?= date('d.m.Y') ?> • FF Wulften am Harz
      </div>
    </div>

    <!-- Statistik Counter Grid -->
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4 sm:gap-6">
      <!-- Gesamt -->
      <div class="bg-slate-50 rounded-2xl p-5 text-center border-l-4 border-sand border border-slate-200">
        <span class="block text-3xl sm:text-4xl font-extrabold text-navy mb-1 font-eurostile">
          <?= (int)$stats['total'] ?>
        </span>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Gesamteinsätze</span>
      </div>

      <!-- Brände -->
      <div class="bg-red-50/50 rounded-2xl p-5 text-center border-l-4 border-red-600 border border-red-100">
        <span class="block text-3xl sm:text-4xl font-extrabold text-red-600 mb-1 font-eurostile">
          <?= (int)$stats['count_brand'] ?>
        </span>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Brandeinsätze</span>
      </div>

      <!-- Hilfeleistungen -->
      <div class="bg-blue-50/50 rounded-2xl p-5 text-center border-l-4 border-blue-600 border border-blue-100">
        <span class="block text-3xl sm:text-4xl font-extrabold text-blue-700 mb-1 font-eurostile">
          <?= (int)$stats['count_th'] ?>
        </span>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">Hilfeleistungen (TH)</span>
      </div>

      <!-- BMA & Sonstige -->
      <div class="bg-amber-50/50 rounded-2xl p-5 text-center border-l-4 border-amber-500 border border-amber-100">
        <span class="block text-3xl sm:text-4xl font-extrabold text-amber-700 mb-1 font-eurostile">
          <?= (int)$stats['count_bma'] + (int)$stats['count_sonstige'] ?>
        </span>
        <span class="text-xs font-bold uppercase tracking-wider text-slate-600">BMA / Sonstige</span>
      </div>
    </div>

    <!-- Visueller Verteilungs-Balken -->
    <?php if ($stats['total'] > 0): 
      $pctBrand = round(($stats['count_brand'] / $stats['total']) * 100);
      $pctTh = round(($stats['count_th'] / $stats['total']) * 100);
      $pctOther = 100 - $pctBrand - $pctTh;
    ?>
      <div class="mt-6 pt-6 border-t border-slate-100">
        <div class="flex justify-between text-xs text-slate-500 mb-2">
          <span>Verteilung: Brand (<?= $pctBrand ?>%) • TH (<?= $pctTh ?>%) • BMA/Sonstiges (<?= $pctOther ?>%)</span>
          <span class="font-bold text-navy"><?= $stats['total'] ?> Einsätze im Kalenderjahr</span>
        </div>
        <div class="w-full h-3 bg-slate-200 rounded-full overflow-hidden flex">
          <div class="bg-red-600 h-full" style="width: <?= $pctBrand ?>%" title="Brände: <?= $pctBrand ?>%"></div>
          <div class="bg-sand h-full" style="width: <?= $pctTh ?>%" title="TH: <?= $pctTh ?>%"></div>
          <div class="bg-amber-500 h-full" style="width: <?= $pctOther ?>%" title="Sonstige: <?= $pctOther ?>%"></div>
        </div>
      </div>
    <?php endif; ?>
  </section>


  <!-- Filter- und Suchleiste (Hell) -->
  <div class="bg-white rounded-2xl p-4 sm:p-5 mb-10 border border-slate-200 shadow-sm flex flex-col md:flex-row md:items-center justify-between gap-4">
    
    <!-- Kategorie Buttons -->
    <div class="flex flex-wrap items-center gap-2">
      <button data-filter-cat="all" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider bg-navy text-white transition shadow-sm">
        Alle Arten
      </button>
      <button data-filter-cat="brand" class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-navy transition">
        🔥 Brände
      </button>
      <button data-filter-cat="th" class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-navy transition">
        🛠️ TH (Hilfeleistung)
      </button>
      <button data-filter-cat="bma" class="px-4 py-2 rounded-xl text-xs font-semibold uppercase tracking-wider bg-slate-100 text-slate-700 hover:bg-slate-200 hover:text-navy transition">
        🔔 BMA
      </button>
    </div>

    <!-- Jahr & Suche -->
    <div class="flex flex-wrap items-center gap-3">
      <!-- Jahr Filter -->
      <div class="relative">
        <select id="filter-year" class="light-input rounded-xl px-4 py-2 text-xs font-bold uppercase tracking-wider appearance-none pr-8 cursor-pointer">
          <option value="all">Alle Jahre</option>
          <?php foreach ($jahre as $j): ?>
            <option value="<?= $j ?>" <?= ($j === $statsYear) ? 'selected' : '' ?>>Jahr <?= $j ?></option>
          <?php endforeach; ?>
        </select>
        <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2.5 text-slate-500">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
        </div>
      </div>

      <!-- Live-Suche -->
      <div class="relative flex-1 sm:w-64">
        <input type="text" id="filter-search" placeholder="Stichwort, Ort suchen..." class="light-input w-full rounded-xl pl-9 pr-4 py-2 text-xs font-medium placeholder-slate-400">
        <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none text-slate-400">
          <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
        </div>
      </div>
    </div>

  </div>

  <!-- Status / Trefferanzeige -->
  <div class="flex items-center justify-between text-xs text-slate-500 mb-6 px-1">
    <span id="einsaetze-count" class="font-bold text-navy">
      Zeige <?= count($alleEinsaetze) ?> von <?= count($alleEinsaetze) ?> Einsätzen
    </span>
  </div>


  <!-- Einsatzübersicht (Helle Kacheln) -->
  <div id="einsaetze-grid" class="tile-grid">
    <?php foreach ($alleEinsaetze as $einsatz): ?>
      <article 
        class="einsatz-card light-tile rounded-2xl overflow-hidden flex flex-col justify-between group transition-all duration-300"
        data-cat="<?= e(strtolower($einsatz['category'])) ?>"
        data-year="<?= (int)$einsatz['year'] ?>"
      >
        <!-- Kachelbild & Badges -->
        <div class="relative h-48 w-full overflow-hidden bg-slate-100">
          <?php if (!empty($einsatz['image_url'])): ?>
            <img src="<?= e($einsatz['image_url']) ?>" alt="<?= e($einsatz['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
          <?php else: ?>
            <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
              <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
            </div>
          <?php endif; ?>

          <!-- Kategorie-Tag -->
          <div class="absolute top-3.5 left-3.5">
            <?= getCategoryBadge($einsatz['category']) ?>
          </div>

          <!-- Nummer & Datum -->
          <div class="absolute top-3.5 right-3.5 text-xs font-semibold text-slate-800 bg-white/95 px-2.5 py-1 rounded-md shadow-sm border border-slate-200">
            #<?= (int)$einsatz['incident_number'] ?> • <?= formatDateGerman($einsatz['date']) ?>
          </div>
        </div>

        <!-- Inhalt -->
        <div class="p-6 flex-1 flex flex-col justify-between">
          <div>
            <!-- Ort & Uhrzeit -->
            <div class="flex items-center justify-between text-xs font-semibold text-sand mb-2 uppercase tracking-wide">
              <span class="truncate flex items-center gap-1">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                <?= e($einsatz['location']) ?>
              </span>
              <span class="text-slate-400 font-normal"><?= formatTimeGerman($einsatz['time']) ?></span>
            </div>

            <!-- Stichwort & Titel -->
            <h3 class="text-lg font-bold text-navy mb-2.5 group-hover:text-sand-dark transition line-clamp-2 leading-snug">
              <?= e($einsatz['title']) ?>
            </h3>

            <!-- Teaser-Text -->
            <p class="text-sm text-slate-600 line-clamp-3 leading-relaxed mb-4 font-light">
              <?= e($einsatz['description']) ?>
            </p>
          </div>

          <!-- Footer der Kachel -->
          <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
            <span class="text-slate-500 truncate max-w-[65%] font-medium">
              <?= !empty($einsatz['vehicles']) ? e($einsatz['vehicles']) : 'FF Wulften' ?>
            </span>
            <a href="/einsatz-detail.php?id=<?= $einsatz['id'] ?>" class="text-navy font-bold hover:text-sand inline-flex items-center gap-1 group-hover:translate-x-1 transition flex-shrink-0">
              Details <span>&rarr;</span>
            </a>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <!-- Leerer Filter-Zustand -->
  <div id="einsaetze-empty" class="hidden text-center py-16 light-tile rounded-3xl mt-6">
    <svg class="w-16 h-16 text-slate-400 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.172 16.172a4 4 0 015.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
    <h3 class="text-xl font-bold text-navy mb-2 uppercase">Keine Einsätze gefunden</h3>
    <p class="text-slate-500 text-sm max-w-md mx-auto">
      Zu Ihren aktuellen Filterkriterien liegen keine Berichte vor. Bitte passen Sie das Jahr oder die Einsatzkategorie an.
    </p>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
