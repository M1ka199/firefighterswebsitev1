<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Helpers.php';

Auth::requireLogin();

$db = Database::getConnection();

// Löschen einer Stufe
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    if (Auth::validateCsrf($_GET['token'] ?? '')) {
        $stmtDel = $db->prepare('DELETE FROM kommando_hierarchien WHERE id = ?');
        $stmtDel->execute([$delId]);
        setFlash('success', 'Hierarchiestufe erfolgreich gelöscht.');
        header('Location: /admin/hierarchien.php');
        exit;
    }
}

// Neuanlage / Bearbeitung speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
        die('Ungültiger CSRF-Token');
    }

    $editId      = (int)($_POST['id'] ?? 0);
    $level       = (int)($_POST['level'] ?? 1);
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $sortOrder   = (int)($_POST['sort_order'] ?? 0);

    if (empty($title)) {
        setFlash('error', 'Bitte eine Bezeichnung für die Hierarchiestufe angeben.');
    } else {
        if ($editId > 0) {
            $stmtUp = $db->prepare('UPDATE kommando_hierarchien SET level = ?, title = ?, description = ?, sort_order = ? WHERE id = ?');
            $stmtUp->execute([$level, $title, $description, $sortOrder, $editId]);
            setFlash('success', 'Hierarchiestufe aktualisiert.');
        } else {
            $stmtIns = $db->prepare('INSERT INTO kommando_hierarchien (level, title, description, sort_order) VALUES (?, ?, ?, ?)');
            $stmtIns->execute([$level, $title, $description, $sortOrder]);
            setFlash('success', 'Neue Hierarchiestufe angelegt.');
        }
        header('Location: /admin/hierarchien.php');
        exit;
    }
}

$adminTitle = 'Hierarchiestufen verwalten';
$activeNav = 'mitglieder';
require_once __DIR__ . '/includes/admin_header.php';

// Stufen abrufen
$stmt = $db->query('SELECT * FROM kommando_hierarchien ORDER BY sort_order ASC, level ASC');
$hierarchien = $stmt->fetchAll();

// Prüfen ob Bearbeitungsmodus
$editItem = null;
if (isset($_GET['edit'])) {
    $editId = (int)$_GET['edit'];
    $stmtE = $db->prepare('SELECT * FROM kommando_hierarchien WHERE id = ? LIMIT 1');
    $stmtE->execute([$editId]);
    $editItem = $stmtE->fetch();
}

$csrf = Auth::csrfToken();
?>

<div class="max-w-6xl mx-auto space-y-8">
  
  <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white">
    <div>
      <span class="text-xs font-bold text-sand uppercase tracking-widest block">CMS Modul • Ortskommando</span>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-1">
        Eigene Hierarchiestufen
      </h1>
      <p class="text-slate-600 text-xs sm:text-sm mt-1 font-light">
        Erstellen und verwalten Sie individuelle Führungs- und Hierarchiestufen für die Kommando-Übersicht.
      </p>
    </div>

    <a href="/admin/mitglieder.php" class="px-5 py-3 rounded-xl bg-slate-100 hover:bg-navy hover:text-white border border-slate-300 text-navy font-extrabold uppercase text-xs tracking-wider transition shadow-sm flex items-center gap-2 self-start sm:self-auto">
      <span>&larr;</span> Zurück zu den Mitgliedern
    </a>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Formular: Stufe anlegen / bearbeiten -->
    <div class="lg:col-span-1">
      <div class="light-panel rounded-3xl p-6 border border-slate-200 shadow-sm bg-white space-y-4">
        <h3 class="text-base font-bold text-navy uppercase border-b border-slate-100 pb-3">
          <?= $editItem ? 'Stufe bearbeiten' : 'Neue Stufe erstellen' ?>
        </h3>

        <form action="/admin/hierarchien.php" method="POST" class="space-y-4">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
          <input type="hidden" name="id" value="<?= $editItem ? (int)$editItem['id'] : 0 ?>">

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
              Stufennummer (Level) <span class="text-red-500">*</span>
            </label>
            <input type="number" name="level" required min="1" max="99" value="<?= $editItem ? (int)$editItem['level'] : ((count($hierarchien) + 1)) ?>" class="light-input w-full rounded-xl px-4 py-2 text-sm font-medium">
            <span class="text-[11px] text-slate-400 mt-0.5 block">Niedrigere Zahlen (z.B. 1) stehen ganz oben.</span>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
              Bezeichnung / Titel <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title" required value="<?= $editItem ? e($editItem['title']) : '' ?>" placeholder="z.B. Wehrleitung oder Fachwarte" class="light-input w-full rounded-xl px-4 py-2 text-sm font-medium">
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
              Beschreibung (optional)
            </label>
            <input type="text" name="description" value="<?= $editItem ? e($editItem['description']) : '' ?>" placeholder="z.B. Ortsbrandmeister & Stellvertretung" class="light-input w-full rounded-xl px-4 py-2 text-sm font-medium">
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
              Sortierung
            </label>
            <input type="number" name="sort_order" value="<?= $editItem ? (int)$editItem['sort_order'] : (count($hierarchien) + 1) ?>" class="light-input w-full rounded-xl px-4 py-2 text-sm font-medium">
          </div>

          <div class="pt-2 flex items-center gap-2">
            <button type="submit" class="flex-1 py-3 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm">
              <?= $editItem ? 'Aktualisieren' : 'Stufe anlegen' ?>
            </button>
            <?php if ($editItem): ?>
              <a href="/admin/hierarchien.php" class="px-4 py-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-bold uppercase text-xs transition">
                Abbrechen
              </a>
            <?php endif; ?>
          </div>
        </form>
      </div>
    </div>

    <!-- Tabelle der vorhandenen Hierarchiestufen -->
    <div class="lg:col-span-2">
      <div class="light-panel rounded-3xl overflow-hidden border border-slate-200 shadow-sm bg-white">
        <div class="p-5 border-b border-slate-100 flex items-center justify-between">
          <h3 class="text-sm font-bold text-navy uppercase tracking-wide">
            Definierte Hierarchiestufen (<?= count($hierarchien) ?>)
          </h3>
          <span class="text-[11px] text-slate-400">Steuert die Gruppierung auf der Kommando-Seite</span>
        </div>

        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs sm:text-sm">
            <thead class="bg-slate-50 text-slate-700 uppercase tracking-wider text-[11px] border-b border-slate-200">
              <tr>
                <th class="py-3 px-5">Stufe</th>
                <th class="py-3 px-5">Titel / Gruppenname</th>
                <th class="py-3 px-5">Beschreibung</th>
                <th class="py-3 px-5 text-center">Reihenfolge</th>
                <th class="py-3 px-5 text-right">Aktionen</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-slate-700">
              <?php foreach ($hierarchien as $h): ?>
                <tr class="hover:bg-slate-50/80 transition">
                  <td class="py-3.5 px-5 font-bold text-navy whitespace-nowrap">
                    <span class="px-2.5 py-1 rounded-md bg-sand/10 text-sand-dark border border-sand/20 font-mono font-bold">
                      Stufe <?= (int)$h['level'] ?>
                    </span>
                  </td>
                  <td class="py-3.5 px-5 font-bold text-navy">
                    <?= e($h['title']) ?>
                  </td>
                  <td class="py-3.5 px-5 text-slate-500 text-xs">
                    <?= e($h['description'] ?: '–') ?>
                  </td>
                  <td class="py-3.5 px-5 text-center whitespace-nowrap font-mono text-xs">
                    <?= (int)$h['sort_order'] ?>
                  </td>
                  <td class="py-3.5 px-5 text-right whitespace-nowrap">
                    <div class="inline-flex items-center gap-1.5">
                      <a href="/admin/hierarchien.php?edit=<?= $h['id'] ?>" class="px-2.5 py-1 rounded-lg bg-slate-100 hover:bg-navy hover:text-white text-navy font-bold text-xs uppercase transition border border-slate-200">
                        Bearbeiten
                      </a>
                      <a href="/admin/hierarchien.php?delete=<?= $h['id'] ?>&token=<?= $csrf ?>" onclick="return confirm('Stufe wirklich löschen?');" class="px-2 py-1 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white font-bold text-xs uppercase transition border border-red-200" title="Löschen">
                        ✕
                      </a>
                    </div>
                  </td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
