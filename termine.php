<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Helpers.php';

$db = Database::getConnection();
$seo = getPageSeo('termine');

$filterCategory = $_GET['cat'] ?? 'all';

$sql = 'SELECT * FROM termine WHERE is_public = 1';
$params = [];

if ($filterCategory !== 'all') {
    $sql .= ' AND category = ?';
    $params[] = $filterCategory;
}

$sql .= ' ORDER BY start_datetime ASC';
$stmt = $db->prepare($sql);
$stmt->execute($params);
$termine = $stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/banner.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

  <!-- Filter Bar (Hell) -->
  <div class="flex flex-wrap items-center justify-between gap-4 mb-10 pb-6 border-b border-slate-200">
    <div class="flex flex-wrap gap-2">
      <a href="/termine.php?cat=all" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition <?= ($filterCategory === 'all') ? 'bg-navy text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
        Alle Termine
      </a>
      <a href="/termine.php?cat=dienst" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition <?= ($filterCategory === 'dienst') ? 'bg-navy text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
        🚒 Einsatzabteilung
      </a>
      <a href="/termine.php?cat=jugend" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition <?= ($filterCategory === 'jugend') ? 'bg-navy text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
        👦 Jugendfeuerwehr
      </a>
      <a href="/termine.php?cat=oeffentlich" class="px-4 py-2 rounded-xl text-xs font-bold uppercase tracking-wider transition <?= ($filterCategory === 'oeffentlich') ? 'bg-navy text-white shadow-sm' : 'bg-white border border-slate-200 text-slate-700 hover:bg-slate-50' ?>">
        🎉 Öffentlich & Feste
      </a>
    </div>

    <div class="text-xs text-slate-500 font-medium">
      <?= count($termine) ?> Termine eingetragen
    </div>
  </div>

  <!-- Termine Grid (Helle Kacheln) -->
  <?php if (empty($termine)): ?>
    <div class="light-tile rounded-3xl p-12 text-center max-w-md mx-auto">
      <p class="text-slate-500">Zu dieser Kategorie stehen derzeit keine Termine an.</p>
    </div>
  <?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
      <?php foreach ($termine as $t): 
        $start = strtotime($t['start_datetime']);
        $day = date('d', $start);
        $monthShort = strftime('%b', $start) ?: date('M', $start);
        $year = date('Y', $start);
        $timeString = date('H:i', $start);
        if (!empty($t['end_datetime'])) {
            $timeString .= ' - ' . date('H:i', strtotime($t['end_datetime']));
        }
        $timeString .= ' Uhr';
      ?>
        <article class="light-tile rounded-2xl p-6 flex flex-col sm:flex-row gap-6 items-start group">
          <!-- Datum-Badge Box -->
          <div class="flex-shrink-0 w-full sm:w-24 rounded-xl bg-slate-50 border border-slate-200 p-3 text-center shadow-sm group-hover:border-sand transition">
            <span class="block text-2xl sm:text-3xl font-extrabold text-navy"><?= $day ?></span>
            <span class="block text-xs font-bold uppercase tracking-widest text-sand"><?= strtoupper($monthShort) ?></span>
            <span class="block text-[10px] text-slate-400 font-medium"><?= $year ?></span>
          </div>

          <!-- Inhalt -->
          <div class="flex-1">
            <div class="flex items-center gap-2 mb-2">
              <span class="px-2.5 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-100 text-slate-700 border border-slate-200">
                <?= e($t['category']) ?>
              </span>
              <span class="text-xs text-slate-500 font-medium flex items-center gap-1">
                <svg class="w-3.5 h-3.5 text-sand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                <?= $timeString ?>
              </span>
            </div>

            <h3 class="text-lg font-bold text-navy uppercase group-hover:text-sand-dark transition mb-2">
              <?= e($t['title']) ?>
            </h3>

            <?php if (!empty($t['description'])): ?>
              <p class="text-sm text-slate-600 leading-relaxed font-light mb-3">
                <?= e($t['description']) ?>
              </p>
            <?php endif; ?>

            <div class="flex items-center gap-1.5 text-xs text-slate-500 font-medium pt-2 border-t border-slate-100">
              <svg class="w-3.5 h-3.5 text-sand" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
              <span>Ort: <?= e($t['location']) ?></span>
            </div>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
