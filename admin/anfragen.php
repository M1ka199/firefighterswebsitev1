<?php
declare(strict_types=1);

$adminTitle = 'Formular-Einsendungen';
$activeNav = 'anfragen';
require_once __DIR__ . '/includes/admin_header.php';

$db = Database::getConnection();

// Status ändern
if (isset($_GET['set_status'])) {
    $subId = (int)$_GET['set_status'];
    $newStatus = in_array($_GET['status'], ['neu', 'in_bearbeitung', 'erledigt']) ? $_GET['status'] : 'neu';
    if (Auth::validateCsrf($_GET['token'] ?? '')) {
        $stmtS = $db->prepare('UPDATE form_submissions SET status = ? WHERE id = ?');
        $stmtS->execute([$newStatus, $subId]);
        setFlash('success', 'Status aktualisiert.');
        header('Location: /admin/anfragen.php');
        exit;
    }
}

// Löschen
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if (Auth::validateCsrf($_GET['token'] ?? '')) {
        $stmtD = $db->prepare('DELETE FROM form_submissions WHERE id = ?');
        $stmtD->execute([$delId]);
        setFlash('success', 'Einsendung gelöscht.');
        header('Location: /admin/anfragen.php');
        exit;
    }
}

$stmt = $db->query('SELECT * FROM form_submissions ORDER BY created_at DESC');
$anfragen = $stmt->fetchAll();
$csrf = Auth::csrfToken();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
  
  <div class="light-panel rounded-3xl p-6 border border-slate-200 shadow-sm bg-white">
    <span class="text-xs font-bold text-sand uppercase tracking-widest block">CMS Modul</span>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-1">
      Eingegangene Anfragen & Schnupperdienst
    </h1>
    <p class="text-slate-600 text-xs mt-1 font-light">
      Übersicht aller online eingereichten Kontaktformulare und Schnupperdienst-Anmeldungen.
    </p>
  </div>

  <div class="light-panel rounded-3xl overflow-hidden border border-slate-200 shadow-sm bg-white">
    <div class="overflow-x-auto">
      <table class="w-full text-left text-xs sm:text-sm">
        <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider text-[11px] border-b border-slate-200">
          <tr>
            <th class="py-4 px-6">Typ & Datum</th>
            <th class="py-4 px-6">Name & Alter</th>
            <th class="py-4 px-6">Kontakt (E-Mail / Tel)</th>
            <th class="py-4 px-6">Inhalt / Interesse</th>
            <th class="py-4 px-6 text-center">Status</th>
            <th class="py-4 px-6 text-right">Aktionen</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          <?php if (empty($anfragen)): ?>
            <tr>
              <td colspan="6" class="py-8 text-center text-slate-400">Keine Anfragen vorhanden.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($anfragen as $a): ?>
              <tr class="hover:bg-slate-50/80 transition">
                <td class="py-4 px-6 whitespace-nowrap">
                  <?php if ($a['type'] === 'schnupperdienst'): ?>
                    <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-300 mb-1">🔥 Schnupperdienst</span>
                  <?php else: ?>
                    <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-900 border border-blue-200 mb-1">Kontakt</span>
                  <?php endif; ?>
                  <span class="block text-xs text-slate-500"><?= formatDateTimeGerman($a['created_at']) ?></span>
                </td>

                <td class="py-4 px-6">
                  <strong class="text-navy block font-bold text-sm"><?= e($a['name']) ?></strong>
                  <?php if (!empty($a['age'])): ?>
                    <span class="text-xs text-slate-500"><?= (int)$a['age'] ?> Jahre alt</span>
                  <?php endif; ?>
                </td>

                <td class="py-4 px-6 whitespace-nowrap">
                  <a href="mailto:<?= e($a['email']) ?>" class="text-navy hover:underline block font-semibold">
                    <?= e($a['email']) ?>
                  </a>
                  <?php if (!empty($a['phone'])): ?>
                    <a href="tel:<?= e($a['phone']) ?>" class="text-xs text-slate-500 hover:text-navy block mt-0.5">
                      <?= e($a['phone']) ?>
                    </a>
                  <?php endif; ?>
                </td>

                <td class="py-4 px-6 max-w-sm">
                  <div class="text-xs text-slate-600 line-clamp-3 whitespace-pre-line font-light">
                    <?= nl2br(e($a['message'])) ?>
                  </div>
                </td>

                <td class="py-4 px-6 text-center whitespace-nowrap">
                  <div class="inline-flex rounded-lg p-1 bg-slate-100 border border-slate-200 text-xs">
                    <a href="/admin/anfragen.php?set_status=<?= $a['id'] ?>&status=neu&token=<?= $csrf ?>" class="px-2 py-1 rounded <?= ($a['status'] === 'neu') ? 'bg-red-600 text-white font-bold' : 'text-slate-600 hover:text-navy' ?>">
                      Neu
                    </a>
                    <a href="/admin/anfragen.php?set_status=<?= $a['id'] ?>&status=in_bearbeitung&token=<?= $csrf ?>" class="px-2 py-1 rounded <?= ($a['status'] === 'in_bearbeitung') ? 'bg-amber-500 text-white font-bold' : 'text-slate-600 hover:text-navy' ?>">
                      In Arbeit
                    </a>
                    <a href="/admin/anfragen.php?set_status=<?= $a['id'] ?>&status=erledigt&token=<?= $csrf ?>" class="px-2 py-1 rounded <?= ($a['status'] === 'erledigt') ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600 hover:text-navy' ?>">
                      Erledigt
                    </a>
                  </div>
                </td>

                <td class="py-4 px-6 text-right whitespace-nowrap">
                  <a href="/admin/anfragen.php?delete=<?= $a['id'] ?>&token=<?= $csrf ?>" onclick="return confirm('Eintrag wirklich löschen?');" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold text-xs uppercase transition border border-red-200">
                    ✕ Löschen
                  </a>
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
