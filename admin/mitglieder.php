<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Helpers.php';

Auth::requireLogin();

$db = Database::getConnection();

// Löschen
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if (Auth::validateCsrf($_GET['token'] ?? '')) {
        $stmtDel = $db->prepare('DELETE FROM mitglieder WHERE id = ?');
        $stmtDel->execute([$delId]);
        setFlash('success', 'Mitglied erfolgreich gelöscht.');
        header('Location: /admin/mitglieder.php');
        exit;
    }
}

// Toggle Startseite
if (isset($_GET['toggle_home'])) {
    $toggleId = (int)$_GET['toggle_home'];
    if (Auth::validateCsrf($_GET['token'] ?? '')) {
        $stmtT = $db->prepare('UPDATE mitglieder SET show_on_homepage = 1 - show_on_homepage WHERE id = ?');
        $stmtT->execute([$toggleId]);
        setFlash('success', 'Startseiten-Status aktualisiert.');
        header('Location: /admin/mitglieder.php');
        exit;
    }
}

$adminTitle = 'Ortskommando Verwaltung';
$activeNav = 'mitglieder';
require_once __DIR__ . '/includes/admin_header.php';

$stmt = $db->query('SELECT * FROM mitglieder ORDER BY hierarchy_level ASC, sort_order ASC');
$mitglieder = $stmt->fetchAll();

$hierarchienMap = [];
try {
    $stmtH = $db->query('SELECT level, title FROM kommando_hierarchien ORDER BY level ASC');
    while ($rowH = $stmtH->fetch()) {
        $hierarchienMap[(int)$rowH['level']] = $rowH['title'];
    }
} catch (Throwable $e) {}

$csrf = Auth::csrfToken();
?>

<div class="max-w-7xl mx-auto space-y-8">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white">
    <div>
      <span class="text-xs font-bold text-sand uppercase tracking-widest block">CMS Modul</span>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-1">
        Ortskommando & Führungskräfte
      </h1>
      <p class="text-slate-600 text-xs sm:text-sm mt-1 font-light">
        Hier verwalten Sie Führungskräfte, eigene Hierarchiestufen, Dienstgrade und die Anzeige auf der Startseite.
      </p>
    </div>

    <div class="flex flex-wrap items-center gap-3 self-start sm:self-auto">
      <a href="/admin/hierarchien.php" class="px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold uppercase text-xs tracking-wider transition border border-slate-300 flex items-center gap-1.5 shadow-sm">
        <span>⚙️</span> Eigene Hierarchiestufen
      </a>
      <a href="/admin/mitglied-edit.php" class="px-5 py-3 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase text-xs tracking-wider transition shadow-sm flex items-center gap-2">
        <span>+</span> Neues Mitglied anlegen
      </a>
    </div>
  </div>

  <div class="light-panel rounded-3xl overflow-hidden border border-slate-200 shadow-sm bg-white">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs sm:text-sm">
        <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider text-[11px] border-b border-slate-200">
          <tr>
            <th class="py-4 px-6">Foto</th>
            <th class="py-4 px-6">Name & Dienstgrad</th>
            <th class="py-4 px-6">Position / Funktion</th>
            <th class="py-4 px-6">Hierarchiestufe</th>
            <th class="py-4 px-6 text-center">Startseite?</th>
            <th class="py-4 px-6 text-right">Aktionen</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          <?php if (empty($mitglieder)): ?>
            <tr>
              <td colspan="6" class="py-8 text-center text-slate-400">Keine Mitglieder angelegt.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($mitglieder as $m): ?>
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-4 px-6">
                  <div class="w-12 h-12 rounded-full overflow-hidden bg-slate-100 border border-slate-300">
                    <?php if (!empty($m['photo_url'])): ?>
                      <img src="<?= e($m['photo_url']) ?>" alt="<?= e($m['name']) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                      <div class="w-full h-full flex items-center justify-center text-slate-500 text-xs font-bold">FF</div>
                    <?php endif; ?>
                  </div>
                </td>
                <td class="py-4 px-6">
                  <strong class="text-navy block font-bold text-sm"><?= e($m['name']) ?></strong>
                  <span class="text-xs text-slate-500"><?= e($m['rank']) ?></span>
                </td>
                <td class="py-4 px-6 text-sand font-semibold">
                  <?= e($m['role_title']) ?>
                </td>
                <td class="py-4 px-6 whitespace-nowrap">
                  <span class="px-2.5 py-1 rounded-md text-[11px] font-bold bg-slate-100 border border-slate-200 text-navy">
                    Stufe <?= $m['hierarchy_level'] ?>: <?= e($hierarchienMap[(int)$m['hierarchy_level']] ?? getHierarchyName((int)$m['hierarchy_level'])) ?>
                  </span>
                </td>
                <td class="py-4 px-6 text-center whitespace-nowrap">
                  <a href="/admin/mitglieder.php?toggle_home=<?= $m['id'] ?>&token=<?= $csrf ?>" class="px-2.5 py-1 rounded-full text-xs font-bold transition <?= ($m['show_on_homepage'] == 1) ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' ?>">
                    <?= ($m['show_on_homepage'] == 1) ? '✓ Auf Startseite' : '– Nicht angezeigt' ?>
                  </a>
                </td>
                <td class="py-4 px-6 text-right whitespace-nowrap">
                  <div class="inline-flex items-center gap-2">
                    <a href="/admin/mitglied-edit.php?id=<?= $m['id'] ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-navy hover:text-white text-navy font-bold text-xs uppercase tracking-wider transition border border-slate-200">
                      Bearbeiten
                    </a>
                    <a href="/admin/mitglieder.php?delete=<?= $m['id'] ?>&token=<?= $csrf ?>" onclick="return confirm('Mitglied wirklich löschen?');" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold text-xs uppercase transition border border-red-200" title="Löschen">
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
