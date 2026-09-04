<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/Database.php';
require_once __DIR__ . '/../src/Auth.php';
require_once __DIR__ . '/../src/Helpers.php';

Auth::requireLogin();

$db = Database::getConnection();
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$mitglied = null;

if ($id > 0) {
    $stmt = $db->prepare('SELECT * FROM mitglieder WHERE id = ? LIMIT 1');
    $stmt->execute([$id]);
    $mitglied = $stmt->fetch();
}

$isEdit = !empty($mitglied);

$stmtHierarchien = $db->query('SELECT * FROM kommando_hierarchien ORDER BY sort_order ASC, level ASC');
$hierarchien = $stmtHierarchien->fetchAll();
if (empty($hierarchien)) {
    $hierarchien = [
        ['level' => 1, 'title' => 'Ortsbrandmeister (Wehrleitung)'],
        ['level' => 2, 'title' => 'Stellvertretende Wehrleitung'],
        ['level' => 3, 'title' => 'Gruppenführer & Fachwarte'],
        ['level' => 4, 'title' => 'Erweitertes Kommando & Gerätewarte']
    ];
}

$selectedLevel = $isEdit ? (int)$mitglied['hierarchy_level'] : 3;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
        die('Ungültiger CSRF-Token');
    }

    $name           = trim($_POST['name'] ?? '');
    $rank           = trim($_POST['rank'] ?? '');
    $roleTitle      = trim($_POST['role_title'] ?? '');
    $hierarchyLevel = (int)($_POST['hierarchy_level'] ?? 3);
    $email          = trim($_POST['email'] ?? '');
    $phone          = trim($_POST['phone'] ?? '');
    $sortOrder      = (int)($_POST['sort_order'] ?? 0);
    $showOnHomepage = isset($_POST['show_on_homepage']) ? 1 : 0;

    $photoUrl = $isEdit ? $mitglied['photo_url'] : null;

    // 1. Priorisiere zugeschnittenes Bild aus dem interaktiven Cropper
    if (!empty($_POST['cropped_image']) && str_starts_with($_POST['cropped_image'], 'data:image/')) {
        $parts = explode(',', $_POST['cropped_image'], 2);
        if (count($parts) === 2) {
            $decoded = base64_decode($parts[1]);
            if ($decoded !== false) {
                $fileName = 'mitglied_' . time() . '_' . bin2hex(random_bytes(3)) . '.jpg';
                $targetDir = __DIR__ . '/../uploads/mitglieder/';
                if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);
                if (file_put_contents($targetDir . $fileName, $decoded)) {
                    $photoUrl = '/uploads/mitglieder/' . $fileName;
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
            $fileName = 'mitglied_' . time() . '_' . bin2hex(random_bytes(3)) . $ext;
            $targetDir = __DIR__ . '/../uploads/mitglieder/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

            if (move_uploaded_file($_FILES['photo']['tmp_name'], $targetDir . $fileName)) {
                $photoUrl = '/uploads/mitglieder/' . $fileName;
            }
        }
    }

    if ($isEdit) {
        $stmtUp = $db->prepare('
            UPDATE mitglieder 
            SET name = ?, rank = ?, role_title = ?, hierarchy_level = ?, photo_url = ?,
                email = ?, phone = ?, show_on_homepage = ?, sort_order = ?
            WHERE id = ?
        ');
        $stmtUp->execute([
            $name, $rank, $roleTitle, $hierarchyLevel, $photoUrl,
            $email, $phone, $showOnHomepage, $sortOrder, $id
        ]);
        setFlash('success', 'Kommandomitglied erfolgreich aktualisiert.');
    } else {
        $stmtIns = $db->prepare('
            INSERT INTO mitglieder 
            (name, rank, role_title, hierarchy_level, photo_url, email, phone, show_on_homepage, sort_order)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
        ');
        $stmtIns->execute([
            $name, $rank, $roleTitle, $hierarchyLevel, $photoUrl,
            $email, $phone, $showOnHomepage, $sortOrder
        ]);
        setFlash('success', 'Neues Mitglied erfolgreich angelegt.');
    }

    header('Location: /admin/mitglieder.php');
    exit;
}

$adminTitle = $isEdit ? 'Mitglied bearbeiten' : 'Neues Mitglied anlegen';
$activeNav = 'mitglieder';
require_once __DIR__ . '/includes/admin_header.php';

$csrf = Auth::csrfToken();
?>

<div class="max-w-3xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
  <div>
    <a href="/admin/mitglieder.php" class="text-xs font-bold text-navy hover:text-sand uppercase inline-flex items-center gap-1 mb-2">
      <span>&larr;</span> Zurück zur Mitgliederliste
    </a>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight">
      <?= $isEdit ? 'Mitglied bearbeiten' : 'Neues Mitglied anlegen' ?>
    </h1>
  </div>

  <div class="light-panel rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm bg-white">
    <form action="" method="POST" enctype="multipart/form-data" class="space-y-6">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Vollständiger Name <span class="text-red-500">*</span>
          </label>
          <input type="text" name="name" required value="<?= $isEdit ? e($mitglied['name']) : '' ?>" placeholder="z.B. Michael Müller" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Dienstgrad <span class="text-red-500">*</span>
          </label>
          <input type="text" name="rank" required value="<?= $isEdit ? e($mitglied['rank']) : '' ?>" placeholder="z.B. Erster Hauptbrandmeister" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Funktion / Position <span class="text-red-500">*</span>
          </label>
          <input type="text" name="role_title" required value="<?= $isEdit ? e($mitglied['role_title']) : '' ?>" placeholder="z.B. Ortsbrandmeister" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>

        <div>
          <div class="flex items-center justify-between mb-2">
            <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
              Hierarchiestufe <span class="text-red-500">*</span>
            </label>
            <a href="/admin/hierarchien.php" target="_blank" class="text-[11px] text-sand hover:underline font-bold">
              ⚙️ Stufen anpassen
            </a>
          </div>
          <select name="hierarchy_level" required class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
            <?php foreach ($hierarchien as $h): ?>
              <option value="<?= (int)$h['level'] ?>" <?= ($selectedLevel === (int)$h['level']) ? 'selected' : '' ?>>
                Stufe <?= (int)$h['level'] ?>: <?= e($h['title']) ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            E-Mail-Adresse (öffentlich)
          </label>
          <input type="email" name="email" value="<?= $isEdit ? e($mitglied['email']) : '' ?>" placeholder="name@feuerwehr-wulften.de" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
            Telefonnummer (optional)
          </label>
          <input type="text" name="phone" value="<?= $isEdit ? e($mitglied['phone']) : '' ?>" placeholder="+49 5556 112" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>
      </div>

      <!-- Foto Upload mit integriertem Live-Cropper -->
      <div class="image-crop-wrapper space-y-3 bg-slate-50/70 p-5 rounded-2xl border border-slate-200">
        <input type="hidden" name="cropped_image" value="">

        <div class="flex items-center justify-between">
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700">
            Porträtfoto (Hochformat 3:4 / Porträt)
          </label>
          <span class="text-[10px] font-bold text-sand-dark uppercase bg-sand/10 px-2 py-0.5 rounded">
            Mit Live-Zuschnitt
          </span>
        </div>

        <!-- Aktuelle / Neue Bildvorschau -->
        <div class="flex flex-col sm:flex-row items-start sm:items-center gap-4 p-3 rounded-xl bg-white border border-slate-200 shadow-2xs">
          <div class="w-20 h-24 rounded-xl overflow-hidden bg-slate-100 border border-slate-300 flex-shrink-0 relative">
            <img src="<?= ($isEdit && !empty($mitglied['photo_url'])) ? e($mitglied['photo_url']) : '' ?>" 
                 alt="Porträtfoto Vorschau" 
                 class="crop-form-preview w-full h-full object-cover <?= ($isEdit && !empty($mitglied['photo_url'])) ? '' : 'hidden' ?>">
            <div class="no-photo-placeholder w-full h-full flex items-center justify-center text-[10px] text-slate-400 <?= ($isEdit && !empty($mitglied['photo_url'])) ? 'hidden' : '' ?>">
              Kein Foto
            </div>
          </div>

          <div class="flex-1 space-y-1">
            <span class="text-xs font-bold text-navy block">Porträt-Ausschnitt & Vorschau</span>
            <p class="text-[11px] text-slate-500 leading-tight">
              Wähle ein neues Foto aus – der interaktive Zuschnitt öffnet sich mit Echtzeit-Live-Vorschau.
            </p>
            <div class="crop-success-badge hidden pt-1">
              <span class="inline-flex items-center gap-1 text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2.5 py-0.5 rounded-md border border-emerald-200">
                ✓ Porträtausschnitt festgelegt (wird beim Speichern übernommen)
              </span>
            </div>
          </div>

          <?php if ($isEdit && !empty($mitglied['photo_url'])): ?>
            <button type="button" class="btn-adjust-crop px-3 py-2 rounded-xl bg-slate-100 hover:bg-navy hover:text-white text-navy text-xs font-bold transition self-stretch sm:self-auto flex items-center justify-center gap-1.5 border border-slate-200">
              <span>✂️</span> Ausschnitt anpassen
            </button>
          <?php endif; ?>
        </div>

        <input type="file" 
               name="photo" 
               accept="image/jpeg,image/png,image/webp" 
               data-cropper="true" 
               data-aspect-ratio="0.75" 
               class="light-input w-full rounded-xl px-4 py-2 text-xs">
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-2">
        <div class="flex items-center gap-3">
          <input type="checkbox" id="show_on_homepage" name="show_on_homepage" value="1" <?= ($isEdit && $mitglied['show_on_homepage'] == 1) ? 'checked' : '' ?> class="rounded border-slate-300 text-sand focus:ring-sand">
          <label for="show_on_homepage" class="text-xs font-bold text-navy uppercase tracking-wider">
            Auf Startseite bei "Ansprechpartner" anzeigen
          </label>
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Sortierreihenfolge
          </label>
          <input type="number" name="sort_order" value="<?= $isEdit ? (int)$mitglied['sort_order'] : 0 ?>" class="light-input w-28 rounded-xl px-3 py-1.5 text-xs font-medium">
        </div>
      </div>

      <div class="pt-4 border-t border-slate-100 flex items-center gap-4">
        <button type="submit" class="px-8 py-3.5 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm">
          <?= $isEdit ? 'Änderungen speichern' : 'Mitglied anlegen' ?>
        </button>
        <a href="/admin/mitglieder.php" class="px-6 py-3.5 rounded-xl bg-white border border-slate-300 text-slate-700 hover:bg-slate-50 font-bold uppercase tracking-wider text-xs transition">
          Abbrechen
        </a>
      </div>

    </form>
  </div>
</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
