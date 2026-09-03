<?php
declare(strict_types=1);

$adminTitle = 'Mitglied bearbeiten';
$activeNav = 'mitglieder';
require_once __DIR__ . '/includes/admin_header.php';

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

    // Foto Upload
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] === UPLOAD_ERR_OK) {
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

      <!-- Foto Upload -->
      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
          Porträtfoto (Quadratisch empfohlen, JPEG/PNG/WebP)
        </label>
        <?php if ($isEdit && !empty($mitglied['photo_url'])): ?>
          <div class="mb-3 flex items-center gap-4 p-3 rounded-xl bg-slate-50 border border-slate-200">
            <img src="<?= e($mitglied['photo_url']) ?>" alt="Aktuelles Porträt" class="w-16 h-16 object-cover rounded-full border border-slate-300">
            <span class="text-xs text-slate-600">Aktuelles Foto vorhanden.</span>
          </div>
        <?php endif; ?>
        <input type="file" name="photo" accept="image/jpeg,image/png,image/webp" class="light-input w-full rounded-xl px-4 py-2 text-xs">
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
