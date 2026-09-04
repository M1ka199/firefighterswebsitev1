<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Helpers.php';

Auth::requireLogin();

$db = Database::getConnection();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$einsatz = null;

if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM einsaetze WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $einsatz = $stmt->fetch();
}

$isEdit = !empty($einsatz);

// Formularverarbeitung POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
        die('Ungültiger CSRF-Token');
    }

    $title       = trim($_POST['title'] ?? '');
    $keyword     = trim($_POST['keyword'] ?? '');
    $category    = in_array($_POST['category'], ['brand', 'th', 'sonstige', 'bma']) ? $_POST['category'] : 'th';
    $date        = $_POST['date'] ?? date('Y-m-d');
    $time        = $_POST['time'] ?? date('H:i');
    $location    = trim($_POST['location'] ?? '');
    $vehicles    = trim($_POST['vehicles'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $isPublished = isset($_POST['is_published']) ? 1 : 0;
    $year        = (int)date('Y', strtotime($date));

    $imageUrl = $isEdit ? $einsatz['image_url'] : null;

    // 1. Priorisiere zugeschnittenes Bild aus dem interaktiven Cropper
    if (!empty($_POST['cropped_image']) && str_starts_with($_POST['cropped_image'], 'data:image/')) {
        $parts = explode(',', $_POST['cropped_image'], 2);
        if (count($parts) === 2) {
            $decoded = base64_decode($parts[1]);
            if ($decoded !== false) {
                $fileName = 'einsatz_' . $year . '_' . time() . '_' . bin2hex(random_bytes(3)) . '.jpg';
                $targetDir = __DIR__ . '/../uploads/einsaetze/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                if (file_put_contents($targetDir . $fileName, $decoded)) {
                    $imageUrl = '/uploads/einsaetze/' . $fileName;
                }
            }
        }
    }
    // 2. Regulärer Fallback-Upload falls kein Crop aktiv
    elseif (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/webp' => '.webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['image']['tmp_name']);

        if (array_key_exists($mime, $allowed)) {
            $ext = $allowed[$mime];
            $fileName = 'einsatz_' . $year . '_' . time() . '_' . bin2hex(random_bytes(3)) . $ext;
            $targetDir = __DIR__ . '/../uploads/einsaetze/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
            
            if (move_uploaded_file($_FILES['image']['tmp_name'], $targetDir . $fileName)) {
                $imageUrl = '/uploads/einsaetze/' . $fileName;
            }
        }
    }

    if ($isEdit) {
        $stmtUp = $db->prepare('
            UPDATE einsaetze 
            SET year = ?, title = ?, keyword = ?, category = ?, date = ?, time = ?, 
                location = ?, vehicles = ?, description = ?, image_url = ?, is_published = ?
            WHERE id = ?
        ');
        $stmtUp->execute([
            $year, $title, $keyword, $category, $date, $time,
            $location, $vehicles, $description, $imageUrl, $isPublished, $id
        ]);
        setFlash('success', 'Einsatz erfolgreich aktualisiert.');
    } else {
        // Nächste Einsatznummer des Jahres ermitteln
        $stmtNr = $db->prepare('SELECT COALESCE(MAX(incident_number), 0) + 1 FROM einsaetze WHERE year = ?');
        $stmtNr->execute([$year]);
        $incNr = (int)$stmtNr->fetchColumn();

        $stmtIns = $db->prepare('
            INSERT INTO einsaetze 
            (year, incident_number, title, keyword, category, date, time, location, vehicles, description, image_url, is_published)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmtIns->execute([
            $year, $incNr, $title, $keyword, $category, $date, $time,
            $location, $vehicles, $description, $imageUrl, $isPublished
        ]);
        setFlash('success', 'Neuer Einsatz #' . $incNr . '/' . $year . ' erfolgreich angelegt.');
    }

    header('Location: /admin/einsaetze.php');
    exit;
}

$adminTitle = $isEdit ? 'Einsatz bearbeiten' : 'Neuen Einsatz erfassen';
$activeNav = 'einsaetze';
require_once __DIR__ . '/includes/admin_header.php';

$csrf = Auth::csrfToken();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
  
  <div class="flex items-center justify-between">
    <div>
      <a href="/admin/einsaetze.php" class="text-xs font-bold text-navy hover:text-sand uppercase inline-flex items-center gap-1 mb-2">
        <span>&larr;</span> Zurück zur Einsatzliste
      </a>
      <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight">
        <?= $isEdit ? 'Einsatz #' . $einsatz['incident_number'] . '/' . $einsatz['year'] . ' bearbeiten' : 'Neuen Einsatz erfassen' ?>
      </h1>
    </div>
  </div>

  <div class="light-panel rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm bg-white">
    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

      <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Einsatzart / Kategorie <span class="text-red-500">*</span>
          </label>
          <select name="category" required class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
            <option value="brand" <?= ($isEdit && $einsatz['category'] === 'brand') ? 'selected' : '' ?>>🔥 Brand</option>
            <option value="th" <?= (!$isEdit || $einsatz['category'] === 'th') ? 'selected' : '' ?>>🛠️ TH (Hilfeleistung)</option>
            <option value="bma" <?= ($isEdit && $einsatz['category'] === 'bma') ? 'selected' : '' ?>>🔔 BMA (Fehlalarm)</option>
            <option value="sonstige" <?= ($isEdit && $einsatz['category'] === 'sonstige') ? 'selected' : '' ?>>⚠️ Sonstige</option>
          </select>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Datum <span class="text-red-500">*</span>
          </label>
          <input type="date" name="date" required value="<?= $isEdit ? e($einsatz['date']) : date('Y-m-d') ?>" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Uhrzeit (Alarmierung) <span class="text-red-500">*</span>
          </label>
          <input type="time" name="time" required value="<?= $isEdit ? e(substr($einsatz['time'], 0, 5)) : date('H:i') ?>" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Alarmstichwort <span class="text-red-500">*</span>
          </label>
          <input type="text" name="keyword" required value="<?= $isEdit ? e($einsatz['keyword']) : '' ?>" placeholder="z.B. TH 2 - VU mit P-Klemmt oder B 2 - Zimmerbrand" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Einsatzort <span class="text-red-500">*</span>
          </label>
          <input type="text" name="location" required value="<?= $isEdit ? e($einsatz['location']) : '' ?>" placeholder="z.B. B243 Wulften Richtung Hattorf" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
          Einsatztitel (Überschrift) <span class="text-red-500">*</span>
        </label>
        <input type="text" name="title" required value="<?= $isEdit ? e($einsatz['title']) : '' ?>" placeholder="z.B. TH 2 – Verkehrsunfall mit zwei beteiligten Fahrzeugen" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
      </div>

      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
          Eingesetzte Fahrzeuge & Kräfte
        </label>
        <input type="text" name="vehicles" value="<?= $isEdit ? e($einsatz['vehicles']) : 'LF 10, MTW' ?>" placeholder="z.B. LF 10, MTW, Rettungsdienst, Polizei" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
      </div>

      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
          Ausführlicher Einsatzbericht <span class="text-red-500">*</span>
        </label>
        <textarea name="description" rows="6" required placeholder="Lage beim Eintreffen, durchgeführte Maßnahmen, Übergabe der Einsatzstelle..." class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium resize-y"><?= $isEdit ? e($einsatz['description']) : '' ?></textarea>
      </div>

      <!-- Bild Upload mit interaktivem Live-Cropper -->
      <div class="image-crop-wrapper space-y-3 bg-slate-50/70 p-5 rounded-2xl border border-slate-200">
        <input type="hidden" name="cropped_image" value="">

        <div class="flex items-center justify-between">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
            Einsatzfoto (Querformat 16:9 empfohlen)
          </label>
          <span class="text-[10px] font-bold text-sand-dark uppercase bg-sand/10 px-2 py-0.5 rounded">
            Mit Live-Zuschnitt
          </span>
        </div>

        <!-- Aktuelle / Neue Bildvorschau -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-3 rounded-xl bg-white border border-slate-200 shadow-2xs">
          <div class="w-36 h-24 rounded-lg overflow-hidden bg-slate-100 border border-slate-300 flex-shrink-0 relative">
            <img src="<?= ($isEdit && !empty($einsatz['image_url'])) ? e($einsatz['image_url']) : '' ?>" 
                 alt="Einsatzfoto Vorschau" 
                 class="crop-form-preview w-full h-full object-cover <?= ($isEdit && !empty($einsatz['image_url'])) ? '' : 'hidden' ?>">
            <div class="no-photo-placeholder w-full h-full flex items-center justify-center text-[11px] text-slate-400 <?= ($isEdit && !empty($einsatz['image_url'])) ? 'hidden' : '' ?>">
              Kein Bild
            </div>
          </div>

          <div class="flex-1 space-y-1">
            <span class="text-xs font-bold text-navy block">Einsatzfoto & Vorschau</span>
            <p class="text-[11px] text-slate-500 leading-tight">
              Wähle ein Einsatzfoto aus – der interaktive Zuschnitt öffnet sich mit Echtzeit-Live-Vorschau.
            </p>
            <div class="crop-success-badge hidden pt-1">
              <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200">
                ✓ Einsatzfoto festgelegt (wird beim Speichern übernommen)
              </span>
            </div>
          </div>

          <?php if ($isEdit && !empty($einsatz['image_url'])): ?>
            <button type="button" class="btn-adjust-crop px-3 py-2 rounded-xl bg-slate-100 hover:bg-navy hover:text-white text-navy text-xs font-bold transition self-stretch sm:self-auto flex items-center justify-center gap-1.5 border border-slate-200">
              <span>✂️</span> Ausschnitt anpassen
            </button>
          <?php endif; ?>
        </div>

        <input type="file" 
               name="image" 
               accept="image/jpeg,image/png,image/webp" 
               data-cropper="true" 
               data-aspect-ratio="1.77778" 
               class="light-input w-full rounded-xl px-4 py-2 text-xs">
        <p class="text-[11px] text-slate-500">Hinweis: Kennzeichen von Privatfahrzeugen und Gesichter von Betroffenen müssen unkenntlich gemacht sein.</p>
      </div>

      <div class="flex items-center gap-3 pt-2">
        <input type="checkbox" id="is_published" name="is_published" value="1" <?= (!$isEdit || $einsatz['is_published'] == 1) ? 'checked' : '' ?> class="rounded border-slate-300 text-sand focus:ring-sand">
        <label for="is_published" class="text-xs font-bold text-navy uppercase tracking-wider">
          Einsatzbericht sofort öffentlich auf der Website anzeigen
        </label>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center gap-4">
        <button type="submit" class="px-8 py-3.5 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm">
          <?= $isEdit ? 'Änderungen speichern' : 'Einsatz veröffentlichen' ?>
        </button>
        <a href="/admin/einsaetze.php" class="px-6 py-3.5 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold uppercase tracking-wider text-xs transition">
          Abbrechen
        </a>
      </div>

    </form>
  </div>

</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
