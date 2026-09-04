<?php
declare(strict_types=1);

require_once __DIR__ . '/../../src/Database.php';
require_once __DIR__ . '/../../src/Auth.php';
require_once __DIR__ . '/../../src/Helpers.php';

Auth::requireAdmin();

$db = Database::getConnection();

$selectedSlug = $_GET['page'] ?? 'startseite';

// Speichern
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!Auth::validateCsrf($_POST['csrf_token'] ?? '')) {
        die('Ungültiger CSRF-Token');
    }

    $slug        = trim($_POST['slug'] ?? 'startseite');
    $pageTitle   = trim($_POST['page_title'] ?? '');
    $metaDesc    = trim($_POST['meta_description'] ?? '');
    $keywords    = trim($_POST['keywords'] ?? '');
    $bannerTitle = trim($_POST['banner_title'] ?? '');
    $bannerIntro = trim($_POST['banner_intro'] ?? '');

    $stmtUp = $db->prepare('
        UPDATE seiten_seo 
        SET page_title = ?, meta_description = ?, keywords = ?, banner_title = ?, banner_intro = ?, updated_at = CURRENT_TIMESTAMP
        WHERE slug = ?
    ');
    $stmtUp->execute([$pageTitle, $metaDesc, $keywords, $bannerTitle, $bannerIntro, $slug]);

    setFlash('success', "Seiteninhalte und SEO für '{$slug}' erfolgreich gespeichert.");
    header("Location: /admin/seiten.php?page={$slug}");
    exit;
}

$adminTitle = 'Seiten & SEO Verwaltung';
$activeNav = 'seiten';
require_once __DIR__ . '/includes/admin_header.php';

// Alle Seiten abfragen
$allPages = $db->query('SELECT slug, page_title, banner_title FROM seiten_seo ORDER BY id ASC')->fetchAll();

// Aktuelle Seite laden
$stmtCur = $db->prepare('SELECT * FROM seiten_seo WHERE slug = ? LIMIT 1');
$stmtCur->execute([$selectedSlug]);
$curPage = $stmtCur->fetch();

if (!$curPage && !empty($allPages)) {
    $curPage = $allPages[0];
    $selectedSlug = $curPage['slug'];
}

$csrf = Auth::csrfToken();
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-8">
  
  <div class="light-panel rounded-3xl p-6 border border-slate-200 shadow-sm bg-white">
    <span class="text-xs font-bold text-sand uppercase tracking-widest block">CMS Modul</span>
    <h1 class="text-2xl sm:text-3xl font-extrabold text-navy uppercase tracking-tight mt-1">
      Seiten-, Banner- & SEO-Verwaltung
    </h1>
    <p class="text-slate-600 text-xs mt-1 font-light">
      Passen Sie Titel, Meta-Beschreibungen, Suchbegriffe und die Unterseiten-Bannertexte an.
    </p>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-12 gap-8 items-start">
    
    <!-- Linke Spalte: Seiten-Auswahl -->
    <div class="lg:col-span-4">
      <div class="light-panel rounded-3xl p-5 border border-slate-200 shadow-sm bg-white space-y-2">
        <span class="text-xs font-bold uppercase tracking-wider text-sand block px-3 py-1">Unterseite wählen</span>
        <?php foreach ($allPages as $p): ?>
          <a href="/admin/seiten.php?page=<?= $p['slug'] ?>" class="block px-4 py-3 rounded-xl transition text-xs font-bold uppercase tracking-wide <?= ($p['slug'] === $selectedSlug) ? 'bg-navy text-white shadow-sm' : 'bg-slate-50 border border-slate-200 text-slate-700 hover:bg-slate-100' ?>">
            <?= e($p['slug']) ?>
            <span class="block text-[10px] font-normal opacity-80 mt-0.5"><?= e($p['banner_title'] ?? $p['page_title']) ?></span>
          </a>
        <?php endforeach; ?>
      </div>
    </div>

    <!-- Rechte Spalte: Editor-Formular -->
    <div class="lg:col-span-8">
      <div class="light-panel rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm bg-white">
        <h3 class="text-lg font-bold text-navy uppercase mb-6 flex items-center gap-2">
          <span class="w-2.5 h-2.5 bg-sand rounded-sm"></span>
          Inhalte für "<?= e($selectedSlug) ?>" bearbeiten
        </h3>

        <form action="/admin/seiten.php" method="POST" class="space-y-6">
          <input type="hidden" name="csrf_token" value="<?= $csrf ?>">
          <input type="hidden" name="slug" value="<?= e($selectedSlug) ?>">

          <!-- Banner-Texte (Globaler Unterseiten-Banner) -->
          <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
            <span class="text-xs font-bold text-navy uppercase tracking-wider block">1. Oberer Seiten-Banner</span>
            
            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Banner-Titel (Große Überschrift)
              </label>
              <input type="text" name="banner_title" value="<?= e($curPage['banner_title'] ?? '') ?>" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Banner-Einleitungstext (Subheadline)
              </label>
              <textarea name="banner_intro" rows="2" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium resize-y"><?= e($curPage['banner_intro'] ?? '') ?></textarea>
            </div>
          </div>

          <!-- SEO & Browser Meta -->
          <div class="p-5 rounded-2xl bg-slate-50 border border-slate-200 space-y-4">
            <span class="text-xs font-bold text-navy uppercase tracking-wider block">2. SEO & Metadaten</span>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Browser-Seitentitel (&lt;title&gt;) <span class="text-red-500">*</span>
              </label>
              <input type="text" name="page_title" required value="<?= e($curPage['page_title'] ?? '') ?>" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Meta-Description (Suchmaschinen-Vorschau) <span class="text-red-500">*</span>
              </label>
              <textarea name="meta_description" rows="3" required class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium resize-y"><?= e($curPage['meta_description'] ?? '') ?></textarea>
            </div>

            <div>
              <label class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-1">
                Meta-Keywords (kommagetrennt)
              </label>
              <input type="text" name="keywords" value="<?= e($curPage['keywords'] ?? '') ?>" class="light-input w-full rounded-xl px-4 py-2.5 text-sm font-medium">
            </div>
          </div>

          <div>
            <button type="submit" class="px-8 py-3.5 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm">
              Änderungen speichern
            </button>
          </div>

        </form>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/admin_footer.php'; ?>
