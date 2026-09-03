<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Helpers.php';

$db = Database::getConnection();
$seo = getPageSeo('ueber-uns');

$stmtF = $db->query('SELECT * FROM fahrzeuge WHERE is_active = 1 ORDER BY sort_order ASC, name ASC');
$fahrzeuge = $stmtF->fetchAll();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/banner.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14 space-y-20">

  <!-- Über die Wehr: Einleitung mit Bild -->
  <section class="grid grid-cols-1 lg:grid-cols-2 gap-12 items-center">
    <div>
      <span class="text-xs font-bold text-sand uppercase tracking-widest">Unsere Geschichte & Auftrag</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-navy uppercase tracking-tight mt-1 mb-6 leading-tight">
        Tradition trifft auf modernste Rettungstechnik
      </h2>
      <div class="space-y-4 text-slate-700 text-base sm:text-lg leading-relaxed font-light">
        <p>
          Die <strong class="text-navy font-semibold">Freiwillige Feuerwehr Wulften am Harz</strong> ist ein unverzichtbarer Pfeiler der Gefahrenabwehr und des Zusammenlebens in unserer Gemeinde im Landkreis Göttingen.
        </p>
        <p>
          Rund um die Uhr, an 365 Tagen im Jahr, stehen ehrenamtliche Bürgerinnen und Bürger bereit, um bei Bränden, schweren Verkehrsunfällen auf der B243 und den umliegenden Straßen sowie bei Unwettern professionelle Hilfe zu leisten.
        </p>
        <p>
          Neben dem aktiven Einsatzdienst legen wir großen Wert auf fundierte Nachwuchsarbeit in der Jugend- und Kinderfeuerwehr sowie auf die Pflege der Dorfgemeinschaft.
        </p>
      </div>

      <div class="mt-8 flex flex-wrap gap-4">
        <a href="/schnupperdienst.php" class="px-6 py-3.5 rounded-xl bg-sand hover:bg-sand-light text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm">
          Teil unseres Teams werden
        </a>
        <a href="/kommando.php" class="px-6 py-3.5 rounded-xl bg-white border border-slate-300 text-navy hover:bg-slate-50 font-bold uppercase tracking-wider text-xs transition">
          Zum Ortskommando
        </a>
      </div>
    </div>

    <!-- Foto Kachel -->
    <div class="light-panel rounded-3xl p-3 border border-slate-200 shadow-md">
      <div class="rounded-2xl overflow-hidden relative">
        <img src="/assets/img/hero-firefighters.jpg" alt="FF Wulften Mannschaft" class="w-full h-full object-cover">
        <div class="absolute bottom-4 left-4 right-4 bg-white/90 backdrop-blur-md p-4 rounded-xl border border-slate-200 shadow-sm">
          <span class="text-xs font-bold text-sand uppercase tracking-widest block mb-0.5">Einsatzabteilung Wulften</span>
          <p class="text-navy font-bold text-base uppercase">Gemeinsam stark für unsere Bürger</p>
        </div>
      </div>
    </div>
  </section>


  <!-- Abteilungen (Kachel-Grid) -->
  <section>
    <div class="text-center max-w-2xl mx-auto mb-12">
      <span class="text-xs font-bold text-sand uppercase tracking-widest">Gemeinschaft in allen Altersklassen</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-navy uppercase tracking-tight mt-1">
        Unsere Abteilungen
      </h2>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
      
      <!-- Aktive Einsatzabteilung -->
      <div class="light-tile rounded-2xl p-8 flex flex-col justify-between group">
        <div>
          <div class="w-12 h-12 rounded-xl bg-navy/10 border border-navy/20 flex items-center justify-center text-navy mb-6">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
          </div>
          <h3 class="text-xl font-bold text-navy uppercase mb-3 group-hover:text-sand-dark transition">
            Einsatzabteilung
          </h3>
          <p class="text-sm text-slate-600 leading-relaxed font-light mb-6">
            Männer und Frauen ab 16 Jahren, die regelmäßig geschult werden, Spezialausbildungen (Atemschutz, Funk, Maschinist) absolvieren und bei Alarmierungen ausrücken.
          </p>
        </div>
        <div class="pt-4 border-t border-slate-100 text-xs text-sand font-bold uppercase tracking-wider">
          Dienst: Alle 14 Tage freitags um 19:00 Uhr
        </div>
      </div>

      <!-- Jugendfeuerwehr -->
      <div class="light-tile rounded-2xl p-8 flex flex-col justify-between group">
        <div>
          <div class="w-12 h-12 rounded-xl bg-sand/10 border border-sand/30 flex items-center justify-center text-sand-dark mb-6">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
          </div>
          <h3 class="text-xl font-bold text-navy uppercase mb-3 group-hover:text-sand-dark transition">
            Jugendfeuerwehr
          </h3>
          <p class="text-sm text-slate-600 leading-relaxed font-light mb-6">
            Für Jugendliche zwischen 10 und 16 Jahren. Feuerwehrtechnik spielerisch erlernen, Zeltlager, Wettbewerbe und echte Team-Erlebnisse.
          </p>
        </div>
        <div class="pt-4 border-t border-slate-100 text-xs text-sand font-bold uppercase tracking-wider">
          Dienst: Wöchentlich dienstags um 17:30 Uhr
        </div>
      </div>

      <!-- Kinderfeuerwehr -->
      <div class="light-tile rounded-2xl p-8 flex flex-col justify-between group">
        <div>
          <div class="w-12 h-12 rounded-xl bg-amber-50 border border-amber-300 flex items-center justify-center text-amber-600 mb-6">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14.828 14.828a4 4 0 01-5.656 0M9 10h.01M15 10h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
          </div>
          <h3 class="text-xl font-bold text-navy uppercase mb-3 group-hover:text-sand-dark transition">
            Kinderfeuerwehr
          </h3>
          <p class="text-sm text-slate-600 leading-relaxed font-light mb-6">
            Für Mädchen und Jungen von 6 bis 10 Jahren: Spielerische Brandschutzerziehung, Basteln, gemeinsame Ausflüge und erstes Kennenlernen der Feuerwehr im Team der "Löschbande".
          </p>
        </div>
        <div class="pt-4 border-t border-slate-100 text-xs text-sand font-bold uppercase tracking-wider">
          Dienst: Jeden 2. Samstag im Monat
        </div>
      </div>

    </div>
  </section>


  <!-- Fuhrpark & Technik -->
  <section class="light-panel rounded-3xl p-8 sm:p-12 border border-slate-200 shadow-sm">
    <div class="text-center max-w-2xl mx-auto mb-12">
      <span class="text-xs font-bold text-sand uppercase tracking-widest">Technik im Dienst der Sicherheit</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-navy uppercase tracking-tight mt-1">
        Fahrzeuge & Ausrüstung
      </h2>
      <div class="w-12 h-1 bg-sand mx-auto mt-3 rounded-full"></div>
    </div>

    <?php if (empty($fahrzeuge)): ?>
      <p class="text-center text-slate-500 py-8">Derzeit sind keine Fahrzeuge im System hinterlegt.</p>
    <?php else: ?>
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-5">
        <?php foreach ($fahrzeuge as $f): ?>
          <div class="light-tile rounded-2xl p-4 sm:p-5 flex flex-col justify-between group transition-all duration-300">
            <div>
              <!-- Foto (kompakt, ohne Overlays) -->
              <div class="w-full h-36 sm:h-40 rounded-xl bg-slate-100 overflow-hidden mb-3.5 border border-slate-200 shadow-sm relative">
                <?php if (!empty($f['photo_url'])): ?>
                  <img src="<?= e($f['photo_url']) ?>" alt="<?= e($f['name']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                <?php else: ?>
                  <div class="w-full h-full flex items-center justify-center text-slate-400 text-xs">Kein Foto hinterlegt</div>
                <?php endif; ?>
              </div>

              <div>
                <h4 class="text-base font-bold text-navy uppercase mb-0.5">
                  <?= e($f['name']) ?>
                </h4>
                
                <?php if (!empty($f['callsign'])): ?>
                  <span class="block text-xs font-mono font-bold text-sand-dark tracking-wide mb-2">
                    <?= e($f['callsign']) ?>
                  </span>
                <?php endif; ?>

                <p class="text-xs text-slate-600 leading-relaxed font-light mb-3">
                  <?= nl2br(e($f['description'])) ?>
                </p>

                <?php if (!empty($f['technical_data'])): ?>
                  <div class="mb-3 bg-slate-50 border border-slate-200 rounded-lg p-2.5 text-[11px] text-slate-700 space-y-0.5">
                    <span class="block text-[9px] font-bold text-sand uppercase tracking-widest mb-1">Details:</span>
                    <?php 
                      $lines = array_filter(array_map('trim', explode("\n", $f['technical_data'])));
                      foreach ($lines as $line): 
                    ?>
                      <div class="flex items-start gap-1 leading-tight">
                        <span class="text-sand font-bold">•</span>
                        <span><?= e($line) ?></span>
                      </div>
                    <?php endforeach; ?>
                  </div>
                <?php endif; ?>
              </div>
            </div>

            <!-- Footer der Kachel mit Zuständiger Person -->
            <?php if (!empty($f['responsible_person'])): ?>
              <div class="pt-2.5 border-t border-slate-100 flex items-center gap-1 text-slate-700 font-semibold bg-amber-50/70 border border-amber-200/80 px-2.5 py-1 rounded-lg text-[11px]">
                <span class="text-sand">👤</span>
                <span class="truncate"><?= e($f['responsible_person']) ?></span>
              </div>
            <?php endif; ?>
          </div>
        <?php endforeach; ?>
      </div>
    <?php endif; ?>
  </section>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
