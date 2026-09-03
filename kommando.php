<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Helpers.php';

$db = Database::getConnection();
$seo = getPageSeo('kommando');

// Mitglieder geladen und nach Hierarchie und Sortierung geordnet
$stmt = $db->query('SELECT * FROM mitglieder ORDER BY hierarchy_level ASC, sort_order ASC, name ASC');
$alleMitglieder = $stmt->fetchAll();

// Gruppierung nach Hierarchiestufen
$gruppiert = [];
foreach ($alleMitglieder as $m) {
    $lvl = (int)$m['hierarchy_level'];
    $gruppiert[$lvl][] = $m;
}

// Hierarchietitel dynamisch aus der Datenbank laden (oder Standard-Fallback)
$hierarchieTitel = [];
try {
    $stmtH = $db->query('SELECT level, title FROM kommando_hierarchien ORDER BY sort_order ASC, level ASC');
    while ($rowH = $stmtH->fetch()) {
        $hierarchieTitel[(int)$rowH['level']] = $rowH['title'];
    }
} catch (Throwable $e) {}

if (empty($hierarchieTitel)) {
    $hierarchieTitel = [
        1 => 'Ortsbrandmeister (Wehrleitung)',
        2 => 'Stellvertretende Wehrleitung',
        3 => 'Gruppenführer & Fachwarte',
        4 => 'Erweitertes Ortskommando & Gerätewarte'
    ];
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/banner.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

  <div class="text-center max-w-3xl mx-auto mb-16">
    <span class="text-xs font-bold text-sand uppercase tracking-widest">Gemeinschaft & Führung</span>
    <h2 class="text-3xl sm:text-4xl font-extrabold text-navy uppercase tracking-tight mt-1 mb-3">
      Das Ortskommando Wulften am Harz
    </h2>
    <p class="text-slate-600 text-sm sm:text-base leading-relaxed font-light">
      Das Ortskommando unterstützt den Ortsbrandmeister bei der Führung der Wehr. Es setzt sich aus erfahrenen Feuerwehrfrauen und -männern zusammen, die Verantwortung für Ausbildung, Atemschutz, Jugendfeuerwehr und Gerätetechnik tragen.
    </p>
  </div>

  <?php if (empty($gruppiert)): ?>
    <div class="light-tile rounded-3xl p-12 text-center max-w-xl mx-auto">
      <p class="text-slate-500">Aktuell sind keine Kommandomitglieder im System eingetragen.</p>
    </div>
  <?php else: ?>
    
    <div class="space-y-16">
      <?php foreach ($gruppiert as $level => $mitglieder): ?>
        <section class="max-w-7xl mx-auto">
          <!-- Gruppenüberschrift (Zentriert) -->
          <div class="text-center max-w-xl mx-auto mb-8">
            <span class="text-[11px] font-bold text-sand uppercase tracking-widest block mb-0.5">
              Hierarchie Stufe <?= $level ?>
            </span>
            <h3 class="text-xl sm:text-2xl font-bold uppercase tracking-wider text-navy">
              <?= e($hierarchieTitel[$level] ?? 'Funktionsträger') ?>
            </h3>
            <div class="w-10 h-1 bg-sand mx-auto mt-2 rounded-full"></div>
          </div>

          <!-- Mitglieder Kacheln: Zentriert & Kompakter gestaltet -->
          <div class="flex flex-wrap justify-center gap-5 sm:gap-6">
            <?php foreach ($mitglieder as $mitglied): ?>
              <?php
                // Kompaktere Abstufung der Kartenbreite
                $cardWidthClass = match($level) {
                  1 => 'w-60 sm:w-68 max-w-[280px] shadow-md', // Stufe 1: Wehrleitung im Zentrum
                  2 => 'w-56 sm:w-64 max-w-[260px]',          // Stufe 2: Stellvertretung
                  default => 'w-52 sm:w-60 max-w-[245px]',    // Stufe 3 & 4: Kompakt & elegant
                };
              ?>
              <article class="light-tile rounded-2xl overflow-hidden flex flex-col justify-between items-center text-center group transition-all duration-300 <?= $cardWidthClass ?>">
                
                <!-- Foto-Bereich (Hochformat) -->
                <div class="relative w-full aspect-[4/5] bg-slate-100 overflow-hidden">
                  <?php if (!empty($mitglied['photo_url'])): ?>
                    <img src="<?= e($mitglied['photo_url']) ?>" alt="<?= e($mitglied['name']) ?>" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
                  <?php else: ?>
                    <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100 text-slate-400 p-4">
                      <svg class="w-16 h-16 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                      <span class="text-[11px] font-semibold text-slate-500">Kein Foto</span>
                    </div>
                  <?php endif; ?>
                </div>

                <!-- Text-Inhalt (Kompakt & Zentriert) -->
                <div class="p-4 sm:p-5 flex-1 flex flex-col justify-between items-center text-center w-full">
                  <div class="w-full">
                    <!-- Name -->
                    <h4 class="text-base font-bold text-navy uppercase group-hover:text-sand-dark transition">
                      <?= e($mitglied['name']) ?>
                    </h4>

                    <!-- Dienstgrad direkt unter dem Namen -->
                    <span class="block text-[11px] font-semibold text-slate-500 uppercase tracking-wider mt-0.5">
                      <?= e($mitglied['rank']) ?>
                    </span>

                    <!-- Funktion mit ein bisschen Abstand darunter -->
                    <div class="mt-2.5">
                      <span class="inline-block text-[10px] sm:text-[11px] font-bold text-sand-dark uppercase tracking-wider bg-sand/10 border border-sand/30 px-2.5 py-1 rounded-full shadow-xs">
                        <?= e($mitglied['role_title']) ?>
                      </span>
                    </div>
                  </div>

                </div>

              </article>
            <?php endforeach; ?>
          </div>
        </section>
      <?php endforeach; ?>
    </div>

  <?php endif; ?>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
