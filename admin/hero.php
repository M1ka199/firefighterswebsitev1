<?php
declare(strict_types=1);

$adminTitle = 'Hero-Slider Verwaltung';
$activeNav = 'hero';
require_once __DIR__ . '/includes/admin_header.php';
Auth::requireAdmin();

$db = Database::getConnection();

// Slide laden (ID 1)
$stmt = $db->query('SELECT * FROM hero_slides ORDER BY sort_order ASC LIMIT 1');
$slide = $stmt->fetch();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
        die('Ungültiger CSRF-Token');
    }

    $title      = trim($_POST['title'] ?? '');
    $subtitle   = trim($_POST['subtitle'] ?? '');
    $buttonText = trim($_POST['button_text'] ?? 'Über uns');
    $buttonLink = trim($_POST['button_link'] ?? '/ueber-uns.php');
    $bgImage    = $slide['bg_image_url'] ?? '/uploads/hero/hero-firefighters.jpg';

    // Bild Upload
    if (isset($_FILES['bg_image']) && $_FILES['bg_image']['error'] === UPLOAD_ERR_OK) {
        $allowed = ['image/jpeg' => '.jpg', 'image/png' => '.png', 'image/webp' => '.webp'];
        $finfo = new finfo(FILEINFO_MIME_TYPE);
        $mime = $finfo->file($_FILES['bg_image']['tmp_name']);

        if (array_key_exists($mime, $allowed)) {
            $ext = $allowed[$mime];
            $fileName = 'hero_' . time() . $ext;
            $targetDir = __DIR__ . '/../uploads/hero/';
            if (!is_dir($targetDir)) mkdir($targetDir, 0755, true);

            if (move_uploaded_file($_FILES['bg_image']['tmp_name'], $targetDir . $fileName)) {
                $bgImage = '/uploads/hero/' . $fileName;
            }
        }
    }

    if ($slide) {
        $stmtUp = $db->prepare('
            UPDATE hero_slides 
            SET title = ?, subtitle = ?, bg_image_url = ?, button_text = ?, button_link = ?
            WHERE id = ?
        ');
        $stmtUp->execute([$title, $subtitle, $bgImage, $buttonText, $buttonLink, $slide['id']]);
    } else {
        $stmtIns = $db->prepare('
            INSERT INTO hero_slides (title, subtitle, bg_image_url, button_text, button_link, is_active)
            VALUES (?, ?, ?, ?, ?, 1)
        ');
        $stmtIns->execute([$title, $subtitle, $bgImage, $buttonText, $buttonLink]);
    }

    setFlash('success', 'Startseiten-Hero erfolgreich aktualisiert.');
    header('Location: /admin/hero.php');
    exit;
}

$csrf = Auth::csrfToken();
?>

<div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
  
  <div class="light-panel rounded-3xl p-6 border border-slate-200 shadow-sm bg-white">
    <span class="text-xs font-bold text-sand uppercase tracking-widest block">CMS Modul</span>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-1">
      Startseiten Hero-Bereich
    </h1>
    <p class="text-slate-600 text-xs mt-1 font-light">
      Gestalten Sie den ersten visuellen Eindruck für die Besucher unserer Website.
    </p>
  </div>

  <div class="light-panel rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm bg-white">
    <form action="/admin/hero.php" method="POST" enctype="multipart/form-data" class="space-y-6">
      <input type="hidden" name="csrf_token" value="<?= $csrf ?>">

      <!-- Live Vorschau -->
      <div class="p-6 rounded-2xl bg-slate-50 border border-slate-200 mb-6">
        <span class="text-xs font-bold uppercase tracking-wider text-sand block mb-3">Aktuelles Hintergrundbild</span>
        <div class="relative h-48 rounded-xl overflow-hidden bg-slate-100 border border-slate-300">
          <img src="<?= e($slide['bg_image_url'] ?? '/uploads/hero/hero-firefighters.jpg') ?>" alt="Hero Hintergrund" class="w-full h-full object-cover">
          <div class="absolute inset-0 bg-black/40 flex items-center justify-center p-4 text-center">
            <div>
              <h3 class="text-xl font-extrabold text-white uppercase"><?= e($slide['title'] ?? 'Gemeinschaft. Einsatz. Ehrensache.') ?></h3>
              <p class="text-xs text-slate-200 mt-1"><?= e($slide['subtitle'] ?? '') ?></p>
            </div>
          </div>
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
          Haupt-Überschrift (Groß) <span class="text-red-500">*</span>
        </label>
        <input type="text" name="title" required value="<?= e($slide['title'] ?? '') ?>" placeholder="z.B. Gemeinschaft. Einsatz. Ehrensache." class="light-input w-full rounded-xl px-4 py-3 text-base font-bold">
      </div>

      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
          Untertitel / Einleitung <span class="text-red-500">*</span>
        </label>
        <textarea name="subtitle" rows="3" required class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium resize-y"><?= e($slide['subtitle'] ?? '') ?></textarea>
      </div>

      <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Button-Beschriftung
          </label>
          <input type="text" name="button_text" value="<?= e($slide['button_text'] ?? 'Über uns') ?>" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>

        <div>
          <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
            Button-Verlinkung (URL)
          </label>
          <input type="text" name="button_link" value="<?= e($slide['button_link'] ?? '/ueber-uns.php') ?>" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
        </div>
      </div>

      <div>
        <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
          Neues Hintergrundbild hochladen (JPEG, PNG oder WebP)
        </label>
        <input type="file" name="bg_image" accept="image/jpeg,image/png,image/webp" class="light-input w-full rounded-xl px-4 py-2 text-xs">
      </div>

      <div class="pt-4 border-t border-slate-100">
        <button type="submit" class="px-8 py-3.5 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm">
          Hero-Einstellungen speichern
        </button>
      </div>

    </form>
  </div>

</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
