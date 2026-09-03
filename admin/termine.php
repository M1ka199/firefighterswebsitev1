<?php
declare(strict_types=1);

$adminTitle = 'Termin-Verwaltung';
$activeNav = 'termine';
require_once __DIR__ . '/includes/admin_header.php';

$db = Database::getConnection();

// Löschen
if (isset($_GET['delete'])) {
    if (Auth::validateCsrf($_GET['token'] ?? '')) {
        $stmtDel = $db->prepare('DELETE FROM termine WHERE id = ?');
        $stmtDel->execute([(int)$_GET['delete']]);
        setFlash('success', 'Termin erfolgreich gelöscht.');
        header('Location: /admin/termine.php');
        exit;
    }
}

// Neuen Termin hinzufügen
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
        die('Ungültiger CSRF-Token');
    }

    $title       = trim($_POST['title'] ?? '');
    $category    = trim($_POST['category'] ?? 'dienst');
    $startDate   = $_POST['start_date'] ?? date('Y-m-d');
    $startTime   = $_POST['start_time'] ?? '19:00';
    $endTime     = !empty($_POST['end_time']) ? $_POST['end_time'] : null;
    $location    = trim($_POST['location'] ?? 'Feuerwehrhaus Wulften');
    $description = trim($_POST['description'] ?? '');

    $startDatetime = $startDate . ' ' . $startTime . ':00';
    $endDatetime   = ($endTime) ? ($startDate . ' ' . $endTime . ':00') : null;

    $stmtIns = $db->prepare('
        INSERT INTO termine (title, category, start_datetime, end_datetime, location, description, is_public)
        VALUES (?, ?, ?, ?, ?, ?, 1)
    ');
    $stmtIns->execute([$title, $category, $startDatetime, $endDatetime, $location, $description]);
    setFlash('success', 'Neuer Termin erfolgreich eingetragen.');
    header('Location: /admin/termine.php');
    exit;
}

$stmt = $db->query('SELECT * FROM termine ORDER BY start_datetime DESC');
$termine = $stmt->fetchAll();
$csrf = Auth::csrfToken();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
  
  <div class="light-panel rounded-3xl p-6 border border-slate-200 shadow-sm bg-white">
    <span class="text-xs font-bold text-sand uppercase tracking-widest block">CMS Modul</span>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-1">
      Dienstplan- & Terminverwaltung
    </h1>
    <p class="text-slate-600 text-xs mt-1 font-light">
      Übungsdienste der Einsatzabteilung, Termine der Jugendfeuerwehr und öffentliche Veranstaltungen pflegen.
    </p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Linke Spalte: Neuer Termin Formular -->
    <div class="lg:col-span-5">
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white">
        <h3 class="text-lg font-bold text-navy uppercase mb-4 flex items-center gap-2">
          <span class="w-2.5 h-2.5 bg-sand rounded-sm"></span>
          Neuen Termin anlegen
        </h3>

        <form action="/admin/termine.php" method="POST" class="space-y-4">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
              Bezeichnung / Thema <span class="text-red-500">*</span>
            </label>
            <input type="text" name="title" required placeholder="z.B. Ausbildungsdienst: Brandbekämpfung" class="light-input w-full rounded-xl px-4 py-2.5 text-xs font-medium">
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Kategorie <span class="text-red-500">*</span>
              </label>
              <select name="category" class="light-input w-full rounded-xl px-3 py-2 text-xs font-medium">
                <option value="dienst">Einsatzabteilung</option>
                <option value="jugend">Jugendfeuerwehr</option>
                <option value="oeffentlich">Öffentlich / Feste</option>
                <option value="kommando">Kommando / Sitzung</option>
              </select>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Datum <span class="text-red-500">*</span>
              </label>
              <input type="date" name="start_date" required value="<?= date('Y-m-d') ?>" class="light-input w-full rounded-xl px-3 py-2 text-xs font-medium">
            </div>
          </div>

          <div class="grid grid-cols-2 gap-4">
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Uhrzeit Beginn <span class="text-red-500">*</span>
              </label>
              <input type="time" name="start_time" required value="19:00" class="light-input w-full rounded-xl px-3 py-2 text-xs font-medium">
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Uhrzeit Ende
              </label>
              <input type="time" name="end_time" value="21:30" class="light-input w-full rounded-xl px-3 py-2 text-xs font-medium">
            </div>
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
              Ort
            </label>
            <input type="text" name="location" value="Feuerwehrhaus Wulften" class="light-input w-full rounded-xl px-4 py-2 text-xs font-medium">
          </div>

          <div>
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
              Beschreibung / Inhalt
            </label>
            <textarea name="description" rows="3" placeholder="Details zu Lehrgang, Ausrüstung oder Ablauf..." class="light-input w-full rounded-xl px-4 py-2 text-xs font-medium resize-y"></textarea>
          </div>

          <div>
            <button type="submit" class="w-full py-3 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm">
              Termin speichern
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Rechte Spalte: Terminliste -->
    <div class="lg:col-span-7">
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white">
        <h3 class="text-lg font-bold text-navy uppercase mb-4">Eingetragene Termine</h3>
        
        <?php if (empty($termine)): ?>
          <p class="text-slate-400 text-xs py-4">Noch keine Termine vorhanden.</p>
        <?php else: ?>
          <div class="space-y-3">
            <?php foreach ($termine as $t): ?>
              <div class="p-4 rounded-xl bg-slate-50 border border-slate-200 flex items-center justify-between gap-4">
                <div>
                  <div class="flex items-center gap-2 mb-1">
                    <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-white border border-slate-300 text-navy uppercase"><?= e($t['category']) ?></span>
                    <span class="text-xs text-slate-600 font-semibold"><?= formatDateTimeGerman($t['start_datetime']) ?></span>
                  </div>
                  <h4 class="text-sm font-bold text-navy"><?= e($t['title']) ?></h4>
                  <span class="text-xs text-slate-500 block mt-0.5">Ort: <?= e($t['location']) ?></span>
                </div>
                <a href="/admin/termine.php?delete=<?= $t['id'] ?>&token=<?= $csrf ?>" onclick="return confirm('Termin löschen?');" class="px-2.5 py-1.5 rounded-lg bg-red-50 hover:bg-red-600 text-red-600 hover:text-white text-xs font-bold transition border border-red-200">
                  ✕
                </a>
              </div>
            <?php endforeach; ?>
          </div>
        <?php endif; ?>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
