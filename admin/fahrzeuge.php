<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Helpers.php';

Auth::requireLogin();

$db = Database::getConnection();

// Löschen
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if (Auth::validateCsrf($_GET['token'] ?? '')) {
        $stmtDel = $db->prepare('DELETE FROM fahrzeuge WHERE id = ?');
        $stmtDel->execute([$delId]);
        setFlash('success', 'Fahrzeug erfolgreich gelöscht.');
        header('Location: /admin/fahrzeuge.php');
        exit;
    }
}

// Toggle Aktiv-Status
if (isset($_GET['toggle_active'])) {
    $toggleId = (int)$_GET['toggle_active'];
    if (Auth::validateCsrf($_GET['token'] ?? '')) {
        $stmtT = $db->prepare('UPDATE fahrzeuge SET is_active = 1 - is_active WHERE id = ?');
        $stmtT->execute([$toggleId]);
        setFlash('success', 'Fahrzeug-Status erfolgreich aktualisiert.');
        header('Location: /admin/fahrzeuge.php');
        exit;
    }
}

$adminTitle = 'Fuhrpark & Fahrzeuge';
$activeNav = 'fahrzeuge';
require_once __DIR__ . '/includes/admin_header.php';

$stmt = $db->query('SELECT * FROM fahrzeuge ORDER BY sort_order ASC, name ASC');
$fahrzeuge = $stmt->fetchAll();
$csrf = Auth::csrfToken();
?>

<div class="max-w-7xl mx-auto space-y-8">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white">
    <div>
      <span class="text-xs font-bold text-sand uppercase tracking-widest block">CMS Modul</span>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-1">
        Fuhrpark & Einsatzfahrzeuge
      </h1>
      <p class="text-slate-600 text-xs sm:text-sm mt-1 font-light">
        Verwalten Sie die Fahrzeuge, Funkrufnamen, taktischen Rollen und technischen Ausstattungen für die Über-uns-Seite.
      </p>
    </div>

    <a href="/admin/fahrzeug-edit.php" class="px-5 py-3 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase text-xs tracking-wider transition shadow-sm flex items-center gap-2 self-start sm:self-auto">
      <span>+</span> Neues Fahrzeug anlegen
    </a>
  </div>

  <div class="light-panel rounded-3xl overflow-hidden border border-slate-200 shadow-sm bg-white">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs sm:text-sm">
        <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider text-[11px] border-b border-slate-200">
          <tr>
            <th class="py-4 px-6">Foto</th>
            <th class="py-4 px-6">Fahrzeug & Bezeichnung</th>
            <th class="py-4 px-6">Funkrufname & Zuständigkeit</th>
            <th class="py-4 px-6 text-center">Sortierung</th>
            <th class="py-4 px-6 text-center">Status</th>
            <th class="py-4 px-6 text-right">Aktionen</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          <?php if (empty($fahrzeuge)): ?>
            <tr>
              <td colspan="6" class="py-12 text-center text-slate-400 font-medium">
                Keine Fahrzeuge im System eingetragen.
              </td>
            </tr>
          <?php else: ?>
            <?php foreach ($fahrzeuge as $f): ?>
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-4 px-6">
                  <div class="w-20 h-14 rounded-xl overflow-hidden bg-slate-100 border border-slate-300 flex-shrink-0">
                    <?php if (!empty($f['photo_url'])): ?>
                      <img src="<?= e($f['photo_url']) ?>" alt="<?= e($f['name']) ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                      <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">Kein Bild</div>
                    <?php endif; ?>
                  </div>
                </td>
                <td class="py-4 px-6">
                  <strong class="text-navy block font-bold text-base"><?= e($f['name']) ?></strong>
                  <span class="text-xs text-slate-500 font-medium"><?= e($f['bezeichnung']) ?></span>
                </td>
                <td class="py-4 px-6">
                  <?php if (!empty($f['callsign'])): ?>
                    <div class="text-xs font-mono font-bold text-sand-dark">
                      <?= e($f['callsign']) ?>
                    </div>
                  <?php else: ?>
                    <span class="text-slate-400 text-xs">–</span>
                  <?php endif; ?>
                  <?php if (!empty($f['responsible_person'])): ?>
                    <div class="text-[11px] text-navy font-semibold mt-1 flex items-center gap-1">
                      <span>👤</span> <?= e($f['responsible_person']) ?>
                    </div>
                  <?php endif; ?>
                </td>
                <td class="py-4 px-6 text-center whitespace-nowrap">
                  <span class="px-2.5 py-1 rounded-md text-xs font-bold bg-slate-100 text-slate-600 border border-slate-200">
                    Pos. <?= (int)$f['sort_order'] ?>
                  </span>
                </td>
                <td class="py-4 px-6 text-center whitespace-nowrap">
                  <a href="/admin/fahrzeuge.php?toggle_active=<?= $f['id'] ?>&token=<?= $csrf ?>" class="px-3 py-1 rounded-full text-xs font-bold transition <?= ($f['is_active'] == 1) ? 'bg-emerald-50 text-emerald-700 border border-emerald-300 hover:bg-emerald-100' : 'bg-slate-100 text-slate-500 border border-slate-200 hover:bg-slate-200' ?>">
                    <?= ($f['is_active'] == 1) ? '✓ Aktiv' : '– Deaktiviert' ?>
                  </a>
                </td>
                <td class="py-4 px-6 text-right whitespace-nowrap">
                  <div class="inline-flex items-center gap-2">
                    <a href="/admin/fahrzeug-edit.php?id=<?= $f['id'] ?>" class="px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-navy hover:text-white text-navy font-bold text-xs uppercase tracking-wider transition border border-slate-200">
                      Bearbeiten
                    </a>
                    <a href="/admin/fahrzeuge.php?delete=<?= $f['id'] ?>&token=<?= $csrf ?>" onclick="return confirm('Fahrzeug wirklich löschen?');" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold text-xs uppercase transition border border-red-200" title="Löschen">
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
