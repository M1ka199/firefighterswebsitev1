<?php
declare(strict_types=1);

$adminTitle = 'Fahrzeug bearbeiten';
$activeNav = 'fahrzeuge';
require_once __DIR__ . '/includes/admin_header.php';

$db = Database::getConnection();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$fahrzeug = null;

if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM fahrzeuge WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $fahrzeug = $stmt->fetch();
}

$isEdit = !empty($fahrzeug);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
        die('Ungültiger CSRF-Token');
    }

    $name              = trim($_POST['name'] ?? '');
    $bezeichnung       = trim($_POST['bezeichnung'] ?? '');
    $tacticalRole      = trim($_POST['tactical_role'] ?? '');
    $callsign          = trim($_POST['callsign'] ?? '');
    $responsiblePerson = trim($_POST['responsible_person'] ?? '');
    $description       = trim($_POST['description'] ?? '');
    $technicalData     = trim($_POST['technical_data'] ?? '');
    $sortOrder         = (int)($_POST['sort_order'] ?? 0);
    $isActive          = isset($_POST['is_active']) ? 1 : 0;

    $photoUrl = $isEdit ? $fahrzeug['photo_url'] : null;

    // 1. Priorisiere zugeschnittenes Bild aus dem interaktiven Cropper
    if (!empty($_POST['cropped_image']) && str_starts_with($_POST['cropped_image'], 'data:image/')) {
        $parts = explode(',', $_POST['cropped_image'], 2);
        if (count($parts) === 2) {
            $decoded = base64_decode($parts[1]);
            if ($decoded !== false) {
                $fileName = 'fahrzeug_' . time() . '_' . bin2hex(random_bytes(3)) . '.jpg';
                $targetDir = __DIR__ . '/../uploads/fahrzeuge/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                if (file_put_contents($targetDir . $fileName, $decoded)) {
                    $photoUrl = '/uploads/fahrzeuge/' . $fileName;
                }
            }
        }
    } 
    // 2. Regulärer Fallback-Upload falls kein Crop aktiv
    elseif (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/webp' => '.webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['photo']['tmp_name']);

        if (array_key_exists($mime, $allowed)) {
            $ext = $allowed[$mime];
            $fileName = 'fahrzeug_' . time() . '_' . bin2hex(random_bytes(3)) . $ext;
            $targetDir = __DIR__ . '/../uploads/fahrzeuge/';
            if (!is_dir($targetDir)) {
                mkdir($targetDir, 0755, true);
            }

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetDir . $fileName)) {
                $photoUrl = '/uploads/fahrzeuge/' . $fileName;
            }
        }
    }

    if ($isEdit) {
        $stmtUp = $db->prepare('
            UPDATE fahrzeuge 
            SET name = ?, bezeichnung = ?, tactical_role = ?, callsign = ?, responsible_person = ?,
                description = ?, technical_data = ?, photo_url = ?, sort_order = ?, is_active = ?
            WHERE id = ?
        ');
        $stmtUp->execute([
            $name, $bezeichnung, $tacticalRole, $callsign, $responsiblePerson,
            $description, $technicalData, $photoUrl, $sortOrder, $isActive, $id
        ]);
        setFlash('success', 'Fahrzeug erfolgreich aktualisiert.');
    } else {
        $stmtIns = $db->prepare('
            INSERT INTO fahrzeuge 
            (name, bezeichnung, tactical_role, callsign, responsible_person, description, technical_data, photo_url, sort_order, is_active)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmtIns->execute([
            $name, $bezeichnung, $tacticalRole, $callsign, $responsiblePerson,
            $description, $technicalData, $photoUrl, $sortOrder, $isActive
        ]);
        setFlash('success', 'Neues Fahrzeug erfolgreich angelegt.');
    }

    header('Location: /admin/fahrzeuge.php');
    exit;
}

$csrf = Auth::csrfToken();
?>

<div class="max-w-4xl mx-auto space-y-8">
  <div>
    <a href="/admin/fahrzeuge.php" class="text-xs font-bold text-navy hover:text-sand uppercase inline-flex items-center gap-1 mb-2">
      <span>&larr;</span> Zurück zur Fahrzeugübersicht
    </a>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight">
      <?= $isEdit ? 'Fahrzeug bearbeiten' : 'Neues Fahrzeug anlegen' ?>
    </h1>
  </div>

  <div class="light-panel rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm bg-white">
    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

      <!-- Basisdaten -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Kurzbezeichnung / Rufkürzel <span class="text-red-500">*</span>
          </label>
          <input type="text" name="name" required value="<?= $isEdit ? e($fahrzeug['name']) : '' ?>" placeholder="z.B. LF 10 oder MTW" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Volle Bezeichnung <span class="text-red-500">*</span>
          </label>
          <input type="text" name="bezeichnung" required value="<?= $isEdit ? e($fahrzeug['bezeichnung']) : '' ?>" placeholder="z.B. Löschgruppenfahrzeug LF 10" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>
      </div>

      <!-- Funkrufname & Ansprechpartner -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Funkrufname (OPTA)
          </label>
          <input type="text" name="callsign" value="<?= $isEdit ? e($fahrzeug['callsign']) : '' ?>" placeholder="Florian Göttingen 14-45-1" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Zuständige Person / Gerätewart
          </label>
          <input type="text" name="responsible_person" value="<?= $isEdit ? e($fahrzeug['responsible_person']) : '' ?>" placeholder="z.B. Gerätewart: Tobias Bornemann" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>
      </div>

      <!-- Technische Daten -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
          Technische Daten & Ausstattung (pro Zeile eine Information)
        </label>
        <textarea name="technical_data" rows="6" placeholder="Fahrgestell: MAN / Aufbau: Schlingmann&#10;Besatzung: 1/8 (Gruppe)&#10;Löschwasser: 1.200 Liter&#10;Pumpe: FPN 10-2000&#10;Besonderheiten: 4 Atemschutzgeräte, Schnellangriff" class="light-input w-full rounded-xl p-4 text-xs font-mono resize-y leading-relaxed bg-slate-50"><?= $isEdit ? e($fahrzeug['technical_data']) : '' ?></textarea>
        <span class="text-[11px] text-slate-400 mt-1 block">Wird auf der Über-uns-Seite als übersichtliche Ausstattungsliste in den großen 2er-Kacheln gerendert.</span>
      </div>

      <!-- Foto Upload mit integriertem Live-Cropper -->
      <div class="image-crop-wrapper space-y-3 bg-slate-50/70 p-5 rounded-2xl border border-slate-200">
        <input type="hidden" name="cropped_image" value="">

        <div class="flex items-center justify-between">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
            Fahrzeugfoto (Großes Querformat, z.B. 16:10 / 16:9)
          </label>
          <span class="text-[10px] font-bold text-sand-dark uppercase bg-sand/10 px-2 py-0.5 rounded">
            Mit Live-Zuschnitt
          </span>
        </div>

        <!-- Aktuelle / Neue Bildvorschau -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-3 rounded-xl bg-white border border-slate-200 shadow-2xs">
          <div class="w-36 h-24 rounded-lg overflow-hidden bg-slate-100 border border-slate-300 flex-shrink-0 relative">
            <img src="<?= ($isEdit && !empty($fahrzeug['photo_url'])) ? e($fahrzeug['photo_url']) : '' ?>" 
                 alt="Fahrzeugfoto Vorschau" 
                 class="crop-form-preview w-full h-full object-cover <?= ($isEdit && !empty($fahrzeug['photo_url'])) ? '' : 'hidden' ?>">
            <div class="no-photo-placeholder w-full h-full flex items-center justify-center text-[11px] text-slate-400 <?= ($isEdit && !empty($fahrzeug['photo_url'])) ? 'hidden' : '' ?>">
              Kein Bild
            </div>
          </div>

          <div class="flex-1 space-y-1">
            <span class="text-xs font-bold text-navy block">Bildausschnitt & Vorschau</span>
            <p class="text-[11px] text-slate-500 leading-tight">
              Wähle ein neues Bild aus – der interaktive Zuschnitt öffnet sich automatisch mit Echtzeit-Vorschau.
            </p>
            <div class="crop-success-badge hidden pt-1">
              <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200">
                ✓ Bildausschnitt festgelegt (wird beim Speichern übernommen)
              </span>
            </div>
          </div>

          <?php if ($isEdit && !empty($fahrzeug['photo_url'])): ?>
            <button type="button" class="btn-adjust-crop px-3 py-2 rounded-xl bg-slate-100 hover:bg-navy hover:text-white text-navy text-xs font-bold transition self-stretch sm:self-auto flex items-center justify-center gap-1.5 border border-slate-200">
              <span>✂️</span> Ausschnitt anpassen
            </button>
          <?php endif; ?>
        </div>

        <input type="file" 
               name="photo" 
               accept="image/jpeg,image/png,image/webp" 
               data-cropper="true" 
               data-aspect-ratio="1.6" 
               class="light-input w-full rounded-xl px-4 py-2 text-xs">
      </div>

      <!-- Sortierung & Status -->
      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
        <div class="flex items-center gap-3">
          <input type="checkbox" id="is_active" name="is_active" value="1" <?= (!$isEdit || $fahrzeug['is_active'] == 1) ? 'checked' : '' ?> class="w-4 h-4 rounded border-slate-300 text-sand focus:ring-sand">
          <label for="is_active" class="text-xs font-bold text-navy uppercase tracking-wider">
            Auf der Website öffentlich anzeigen (Aktiv)
          </label>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Sortierreihenfolge
          </label>
          <input type="number" name="sort_order" value="<?= $isEdit ? (int)$fahrzeug['sort_order'] : 0 ?>" class="light-input w-28 rounded-xl px-3 py-1.5 text-xs font-medium">
        </div>
      </div>

      <!-- Buttons -->
      <div class="pt-4 border-t border-slate-100 flex items-center gap-4">
        <button type="submit" class="px-8 py-3.5 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm">
          <?= $isEdit ? 'Änderungen speichern' : 'Fahrzeug anlegen' ?>
        </button>
        <a href="/admin/fahrzeuge.php" class="px-6 py-3.5 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold uppercase tracking-wider text-xs transition">
          Abbrechen
        </a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
