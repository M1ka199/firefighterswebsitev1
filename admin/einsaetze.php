<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Helpers.php';

Auth::requireLogin();

$db = Database::getConnection();

// Einsatz löschen
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $delToken = $_GET['token'] ?? '';
    if (Auth::validateCsrf($delToken)) {
        $stmtDel = $db->prepare('DELETE FROM einsaetze WHERE id = ?');
        $stmtDel->execute([$delId]);
        setFlash('success', 'Einsatz erfolgreich gelöscht.');
        header('Location: /admin/einsaetze.php');
        exit;
    }
}

$adminTitle = 'Einsatz-Verwaltung';
$activeNav = 'einsaetze';
require_once __DIR__ . '/includes/admin_header.php';

$stmt = $db->query('SELECT * FROM einsaetze ORDER BY year DESC, incident_number DESC, date DESC');
$einsaetze = $stmt->fetchAll();
$csrf = Auth::csrfToken();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 light-panel rounded-3xl p-6 border border-slate-200 shadow-sm bg-white">
    <div>
      <span class="text-xs font-bold text-sand uppercase tracking-widest block">CMS Modul</span>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-1">
        Einsatz-Verwaltung
      </h1>
      <p class="text-slate-600 text-xs mt-1 font-light">
        Einsatzberichte erstellen, bearbeiten, bebildern und veröffentlichen.
      </p>
    </div>

    <a href="/admin/einsatz-edit.php" class="px-5 py-3 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase text-xs tracking-wider transition shadow-sm flex items-center gap-2 self-start sm:self-auto">
      <span>+</span> Neuer Einsatz anlegen
    </a>
  </div>

  <!-- Einsätze Tabelle (Hell) -->
  <div class="light-panel rounded-3xl overflow-hidden border border-slate-200 shadow-sm bg-white">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs sm:text-sm">
        <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider text-[11px] border-b border-slate-200">
          <tr>
            <th class="py-4 px-6">Nr. / Jahr</th>
            <th class="py-4 px-6">Kategorie</th>
            <th class="py-4 px-6">Datum & Zeit</th>
            <th class="py-4 px-6">Stichwort & Titel</th>
            <th class="py-4 px-6">Ort</th>
            <th class="py-4 px-6 text-right">Aktionen</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          <?php if (empty($einsaetze)): ?>
            <tr>
              <td colspan="6" class="py-8 text-center text-slate-400">Keine Einsätze vorhanden.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($einsaetze as $e): ?>
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-4 px-6 font-bold text-navy whitespace-nowrap">
                  #<?= $e['incident_number'] ?> / <?= $e['year'] ?>
                </td>
                <td class="py-4 px-6 whitespace-nowrap">
                  <?= getCategoryBadge($e['category']) ?>
                </td>
                <td class="py-4 px-6 whitespace-nowrap text-slate-600">
                  <?= formatDateGerman($e['date']) ?><br>
                  <span class="text-[11px] text-slate-400 font-medium"><?= formatTimeGerman($e['time']) ?></span>
                </td>
                <td class="py-4 px-6 max-w-xs truncate">
                  <span class="font-bold text-navy block truncate"><?= e($e['title']) ?></span>
                  <span class="text-xs text-sand block truncate"><?= e($e['keyword']) ?></span>
                </td>
                <td class="py-4 px-6 text-slate-600 max-w-[160px] truncate">
                  <?= e($e['location']) ?>
                </td>
                <td class="py-4 px-6 text-right whitespace-nowrap">
                  <div class="inline-flex items-center gap-2">
                    <a href="/admin/einsatz-edit.php?id=<?= $e['id'] ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-navy hover:text-white text-navy font-bold text-xs uppercase tracking-wider transition border border-slate-200">
                      Bearbeiten
                    </a>
                    <a href="/admin/einsaetze.php?delete=<?= $e['id'] ?>&token=<?= $csrf ?>" onclick="return confirm('Einsatz wirklich unwiderruflich löschen?');" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold text-xs uppercase transition border border-red-200" title="Löschen">
                      ✕
                    </a>
                  </div>
                </td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
