<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Helpers.php';

Auth::requireLogin();

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

$adminTitle = 'Formular-Einsendungen';
$activeNav = 'anfragen';
require_once __DIR__ . '/includes/admin_header.php';

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
      Übersicht aller online eingereichten Kontaktformulare und Schnupperdienst-Anmeldungen. Klicke auf eine Anfrage, um alle Details im Pop-Up zu öffnen.
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
            <th class="py-4 px-6">Nachricht / Anliegen</th>
            <th class="py-4 px-6 text-center">Status</th>
            <th class="py-4 px-6 text-right">Aktionen</th>
          </tr>
        </thead>
        <tbody class="divide-y divide-slate-100 text-slate-700">
          <?php if (empty($anfragen)): ?>
            <tr>
              <td colspan="6" class="py-12 text-center text-slate-400">Keine Anfragen im System vorhanden.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($anfragen as $a): ?>
              <?php 
                $submissionJson = htmlspecialchars(json_encode([
                    'id' => $a['id'],
                    'type' => $a['type'],
                    'type_label' => ($a['type'] === 'schnupperdienst' ? '🔥 Schnupperdienst-Anmeldung' : '✉️ Kontaktanfrage'),
                    'name' => $a['name'],
                    'age' => $a['age'] ? ((int)$a['age'] . ' Jahre alt') : 'Nicht angegeben',
                    'email' => $a['email'],
                    'phone' => $a['phone'] ?: 'Nicht angegeben',
                    'message' => $a['message'],
                    'status' => $a['status'],
                    'date_formatted' => formatDateTimeGerman($a['created_at'])
                ], JSON_UNESCAPED_UNICODE), ENT_QUOTES, 'UTF-8');
              ?>
              <tr class="hover:bg-slate-50/80 transition cursor-pointer row-anfrage" data-anfrage='<?= $submissionJson ?>'>
                <td class="py-4 px-6 whitespace-nowrap">
                  <?php if ($a['type'] === 'schnupperdienst'): ?>
                    <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-300 mb-1">🔥 Schnupperdienst</span>
                  <?php else: ?>
                    <span class="inline-block px-2.5 py-0.5 rounded text-[10px] font-bold uppercase bg-blue-100 text-blue-900 border border-blue-200 mb-1">✉️ Kontakt</span>
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
                  <a href="mailto:<?= e($a['email']) ?>" onclick="event.stopPropagation();" class="text-navy hover:underline block font-semibold">
                    <?= e($a['email']) ?>
                  </a>
                  <?php if (!empty($a['phone'])): ?>
                    <a href="tel:<?= e($a['phone']) ?>" onclick="event.stopPropagation();" class="text-xs text-slate-500 hover:text-navy block mt-0.5">
                      <?= e($a['phone']) ?>
                    </a>
                  <?php endif; ?>
                </td>

                <td class="py-4 px-6 max-w-sm">
                  <div class="text-xs text-slate-600 line-clamp-2 whitespace-pre-line font-light">
                    <?= nl2br(e($a['message'])) ?>
                  </div>
                </td>

                <td class="py-4 px-6 text-center whitespace-nowrap" onclick="event.stopPropagation();">
                  <div class="inline-flex rounded-lg p-1 bg-slate-100 border border-slate-200 text-xs">
                    <a href="/admin/anfragen.php?set_status=<?= $a['id'] ?>&status=neu&token=<?= $csrf ?>" class="px-2 py-1 rounded transition <?= ($a['status'] === 'neu') ? 'bg-red-600 text-white font-bold' : 'text-slate-600 hover:text-navy' ?>">
                      Neu
                    </a>
                    <a href="/admin/anfragen.php?set_status=<?= $a['id'] ?>&status=in_bearbeitung&token=<?= $csrf ?>" class="px-2 py-1 rounded transition <?= ($a['status'] === 'in_bearbeitung') ? 'bg-amber-500 text-white font-bold' : 'text-slate-600 hover:text-navy' ?>">
                      In Arbeit
                    </a>
                    <a href="/admin/anfragen.php?set_status=<?= $a['id'] ?>&status=erledigt&token=<?= $csrf ?>" class="px-2 py-1 rounded transition <?= ($a['status'] === 'erledigt') ? 'bg-emerald-600 text-white font-bold' : 'text-slate-600 hover:text-navy' ?>">
                      Erledigt
                    </a>
                  </div>
                </td>

                <td class="py-4 px-6 text-right whitespace-nowrap" onclick="event.stopPropagation();">
                  <div class="inline-flex items-center gap-2">
                    <button type="button" class="btn-open-modal px-3 py-1.5 rounded-lg bg-navy hover:bg-navy-dark text-white font-bold text-xs uppercase tracking-wider transition shadow-2xs flex items-center gap-1">
                      <span>👁️</span> Pop-Up
                    </button>
                    <a href="/admin/anfragen.php?delete=<?= $a['id'] ?>&token=<?= $csrf ?>" onclick="return confirm('Eintrag wirklich löschen?');" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold text-xs uppercase transition border border-red-200" title="Löschen">
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

<!-- Pop-Up Modal für Detailansicht einer Anfrage -->
<div id="anfrage-detail-modal" class="fixed inset-0 z-50 hidden bg-slate-900/80 backdrop-blur-sm flex items-center justify-center p-4 overflow-y-auto">
  <div class="bg-white w-full max-w-2xl rounded-3xl shadow-2xl border border-slate-200 overflow-hidden flex flex-col my-auto max-h-[92vh]">
    
    <!-- Modal Header -->
    <div class="px-6 py-5 border-b border-slate-200 flex items-center justify-between bg-slate-50/80">
      <div>
        <div class="flex items-center gap-2 mb-1">
          <span id="modal-type-badge" class="px-2.5 py-0.5 rounded text-[11px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-300"></span>
          <span id="modal-date" class="text-xs text-slate-500 font-medium"></span>
        </div>
        <h3 id="modal-name" class="text-xl font-extrabold text-navy uppercase tracking-tight"></h3>
      </div>
      <button type="button" id="modal-btn-close" class="w-9 h-9 rounded-xl bg-slate-200/80 hover:bg-slate-300 text-slate-700 flex items-center justify-center font-bold text-base transition">
        ✕
      </button>
    </div>

    <!-- Modal Body -->
    <div class="p-6 space-y-6 overflow-y-auto flex-1">
      
      <!-- Kontaktdaten Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-3 gap-4 p-4 rounded-2xl bg-slate-50 border border-slate-200">
        <div>
          <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">E-Mail-Adresse</span>
          <a id="modal-email" href="" class="text-navy font-bold text-xs sm:text-sm hover:underline break-all"></a>
        </div>
        <div>
          <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Telefonnummer</span>
          <a id="modal-phone" href="" class="text-slate-800 font-bold text-xs sm:text-sm hover:underline"></a>
        </div>
        <div>
          <span class="block text-[10px] font-bold text-slate-500 uppercase tracking-wider mb-0.5">Alter</span>
          <span id="modal-age" class="text-slate-800 font-bold text-xs sm:text-sm"></span>
        </div>
      </div>

      <!-- Vollständige Nachricht -->
      <div>
        <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
          Vollständige Nachricht / Inhalt der Anfrage:
        </span>
        <div class="bg-white border border-slate-200 rounded-2xl p-5 text-sm text-slate-700 leading-relaxed whitespace-pre-wrap font-normal max-h-64 overflow-y-auto shadow-inner" id="modal-message">
        </div>
      </div>

      <!-- Status-Steuerung im Modal -->
      <div class="pt-4 border-t border-slate-200 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <span class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1.5">
            Bearbeitungsstatus direkt anpassen:
          </span>
          <div class="inline-flex rounded-xl p-1 bg-slate-100 border border-slate-200 text-xs gap-1">
            <a id="modal-status-neu" href="" class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
              Neu
            </a>
            <a id="modal-status-arbeit" href="" class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
              In Arbeit
            </a>
            <a id="modal-status-erledigt" href="" class="px-3 py-1.5 rounded-lg text-xs font-bold transition">
              Erledigt
            </a>
          </div>
        </div>

        <a id="modal-reply-btn" href="" class="px-5 py-2.5 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase text-xs tracking-wider transition shadow-sm flex items-center justify-center gap-2">
          <span>✉️</span> E-Mail antworten
        </a>
      </div>

    </div>

    <!-- Modal Footer -->
    <div class="px-6 py-4 bg-slate-50/80 border-t border-slate-200 flex items-center justify-between">
      <a id="modal-delete-btn" href="" onclick="return confirm('Eintrag wirklich unwiderruflich löschen?');" class="text-xs font-bold text-red-600 hover:text-red-700 uppercase tracking-wider">
        ✕ Einsendung löschen
      </a>
      <button type="button" id="modal-btn-dismiss" class="px-6 py-2.5 rounded-xl bg-slate-200 hover:bg-slate-300 text-slate-800 font-bold uppercase text-xs tracking-wider transition">
        Schließen
      </button>
    </div>

  </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const modal = document.getElementById('anfrage-detail-modal');
  const closeBtn = document.getElementById('modal-btn-close');
  const dismissBtn = document.getElementById('modal-btn-dismiss');
  const csrfToken = <?= json_encode($csrf) ?>;

  function closeModal() {
    modal.classList.add('hidden');
  }

  if (closeBtn) closeBtn.addEventListener('click', closeModal);
  if (dismissBtn) dismissBtn.addEventListener('click', closeModal);
  modal.addEventListener('click', (e) => {
    if (e.target === modal) closeModal();
  });
  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && !modal.classList.contains('hidden')) closeModal();
  });

  function openAnfrageModal(data) {
    document.getElementById('modal-name').textContent = data.name || 'Unbekannt';
    document.getElementById('modal-date').textContent = data.date_formatted || '';
    
    // Typ Badge
    const typeBadge = document.getElementById('modal-type-badge');
    typeBadge.textContent = data.type_label;
    if (data.type === 'schnupperdienst') {
      typeBadge.className = 'px-2.5 py-0.5 rounded text-[11px] font-extrabold uppercase bg-amber-100 text-amber-900 border border-amber-300';
    } else {
      typeBadge.className = 'px-2.5 py-0.5 rounded text-[11px] font-bold uppercase bg-blue-100 text-blue-900 border border-blue-200';
    }

    // Kontaktdaten
    const emailEl = document.getElementById('modal-email');
    emailEl.textContent = data.email || '–';
    emailEl.href = data.email ? ('mailto:' + data.email) : '#';

    const phoneEl = document.getElementById('modal-phone');
    phoneEl.textContent = data.phone || '–';
    phoneEl.href = (data.phone && data.phone !== 'Nicht angegeben') ? ('tel:' + data.phone) : '#';

    document.getElementById('modal-age').textContent = data.age || '–';
    document.getElementById('modal-message').textContent = data.message || '(Keine Nachricht eingegeben)';

    // Antwort Button
    const replyBtn = document.getElementById('modal-reply-btn');
    const subject = encodeURIComponent('Ihre Anfrage bei der Freiwilligen Feuerwehr Wulften: ' + (data.type === 'schnupperdienst' ? 'Schnupperdienst' : 'Kontakt'));
    replyBtn.href = data.email ? `mailto:${data.email}?subject=${subject}` : '#';

    // Status Buttons
    const btnNeu = document.getElementById('modal-status-neu');
    const btnArbeit = document.getElementById('modal-status-arbeit');
    const btnErledigt = document.getElementById('modal-status-erledigt');

    btnNeu.href = `/admin/anfragen.php?set_status=${data.id}&status=neu&token=${csrfToken}`;
    btnArbeit.href = `/admin/anfragen.php?set_status=${data.id}&status=in_bearbeitung&token=${csrfToken}`;
    btnErledigt.href = `/admin/anfragen.php?set_status=${data.id}&status=erledigt&token=${csrfToken}`;

    btnNeu.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition ' + 
      (data.status === 'neu' ? 'bg-red-600 text-white shadow-sm' : 'text-slate-600 hover:text-navy hover:bg-white');
    btnArbeit.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition ' + 
      (data.status === 'in_bearbeitung' ? 'bg-amber-500 text-white shadow-sm' : 'text-slate-600 hover:text-navy hover:bg-white');
    btnErledigt.className = 'px-3 py-1.5 rounded-lg text-xs font-bold transition ' + 
      (data.status === 'erledigt' ? 'bg-emerald-600 text-white shadow-sm' : 'text-slate-600 hover:text-navy hover:bg-white');

    // Lösch Button
    document.getElementById('modal-delete-btn').href = `/admin/anfragen.php?delete=${data.id}&token=${csrfToken}`;

    modal.classList.remove('hidden');
  }

  // Event Listener für alle Zeilen und Pop-Up Buttons
  document.querySelectorAll('.row-anfrage').forEach(row => {
    row.addEventListener('click', () => {
      try {
        const data = JSON.parse(row.getAttribute('data-anfrage'));
        openAnfrageModal(data);
      } catch (err) {
        console.error('Fehler beim Parsen der Anfrage-Daten:', err);
      }
    });

    const popBtn = row.querySelector('.btn-open-modal');
    if (popBtn) {
      popBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        try {
          const data = JSON.parse(row.getAttribute('data-anfrage'));
          openAnfrageModal(data);
        } catch (err) {
          console.error('Fehler beim Parsen der Anfrage-Daten:', err);
        }
      });
    }
  });
});
</script>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
