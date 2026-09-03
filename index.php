<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Helpers.php';

$db = Database::getConnection();
$seo = getPageSeo('startseite');

// 1. Hero Slide laden
$stmtHero = $db->query('SELECT * FROM hero_slides WHERE is_active = 1 ORDER BY sort_order ASC, id ASC LIMIT 1');
$hero = $stmtHero->fetch() ?: [
    'title' => 'Gemeinschaft. Einsatz. Ehrensache.',
    'subtitle' => 'Seit über 100 Jahren rund um die Uhr im Einsatz für Wulften am Harz.',
    'bg_image_url' => '/uploads/hero/hero-firefighters.jpg',
    'button_text' => 'Über uns',
    'button_link' => '/ueber-uns.php'
];

// 2. Letzte Einsätze laden
$stmtEinsaetze = $db->query('SELECT * FROM einsaetze WHERE is_published = 1 ORDER BY date DESC, time DESC LIMIT 3');
$letzteEinsaetze = $stmtEinsaetze->fetchAll();

// 3. Ansprechpartner
$stmtKontakte = $db->query('SELECT * FROM mitglieder WHERE show_on_homepage = 1 ORDER BY hierarchy_level ASC, sort_order ASC LIMIT 4');
$ansprechpartner = $stmtKontakte->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<!-- 1. Hero-Bereich (CMS-gesteuert) -->
<section class="relative min-h-[75vh] sm:min-h-[82vh] flex items-center justify-center overflow-hidden bg-[#001738]">
  <!-- Hintergrundbild mit feinem Kontrast-Overlay -->
  <div class="absolute inset-0 z-0">
    <img src="<?= e($hero['bg_image_url']) ?>" alt="FF Wulften am Harz" class="w-full h-full object-cover object-center scale-105 transition-transform duration-[12000ms] hover:scale-100">
    <div class="absolute inset-0 bg-gradient-to-r from-[#001738]/90 via-[#002b66]/75 to-[#001738]/85"></div>
    <div class="absolute inset-0 bg-gradient-to-t from-black/50 via-transparent to-transparent"></div>
  </div>

  <!-- Hero Content -->
  <div class="relative z-10 max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-20 text-center">
    <div class="inline-flex items-center gap-2 px-4 py-1.5 rounded-full bg-white/10 backdrop-blur-md border border-white/20 text-sand-light text-xs sm:text-sm font-bold tracking-widest uppercase mb-6 shadow-md">
      <span class="w-2 h-2 rounded-full bg-sand-light animate-pulse"></span>
      24/7 Einsatzbereit • Wulften am Harz
    </div>

    <h1 class="text-4xl sm:text-6xl lg:text-7xl font-extrabold uppercase tracking-tight text-white mb-6 leading-tight drop-shadow-md">
      <?= e($hero['title']) ?>
    </h1>

    <p class="max-w-2xl mx-auto text-base sm:text-xl text-slate-100 font-light mb-10 leading-relaxed drop-shadow">
      <?= e($hero['subtitle']) ?>
    </p>

    <div class="flex flex-wrap items-center justify-center gap-4">
      <a href="<?= e($hero['button_link']) ?>" class="px-8 py-3.5 rounded-xl bg-sand hover:bg-sand-light text-white font-extrabold uppercase tracking-wider text-xs sm:text-sm transition-all duration-300 shadow-md transform hover:-translate-y-0.5">
        <?= e($hero['button_text']) ?>
      </a>
      <a href="/schnupperdienst.php" class="px-8 py-3.5 rounded-xl bg-white/15 hover:bg-white/25 text-white backdrop-blur-md border border-white/30 font-extrabold uppercase tracking-wider text-xs sm:text-sm transition-all duration-300 shadow-md transform hover:-translate-y-0.5">
        🔥 Schnupperdienst
      </a>
    </div>
  </div>
</section>


<!-- 2. Sektion: Letzte Einsätze (Helles Kachel-Design) -->
<section class="py-16 sm:py-20 relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <!-- Section Header -->
    <div class="flex flex-col md:flex-row md:items-end justify-between mb-12 border-b border-slate-200 pb-6">
      <div>
        <div class="text-xs font-bold text-sand uppercase tracking-widest mb-1.5 flex items-center gap-2">
          <span class="w-2 h-2 rounded-full bg-alarm"></span>
          Einsatzgeschehen
        </div>
        <h2 class="text-3xl sm:text-4xl font-extrabold text-navy uppercase tracking-tight">
          Letzte Einsätze
        </h2>
      </div>
      <a href="/einsaetze.php" class="mt-4 md:mt-0 inline-flex items-center gap-2 text-sand-dark hover:text-navy font-bold text-xs uppercase tracking-wider group transition">
        <span>Alle Einsätze im Archiv anzeigen</span>
        <span class="transform group-hover:translate-x-1 transition">&rarr;</span>
      </a>
    </div>

    <!-- Einsatz Kacheln -->
    <div class="tile-grid">
      <?php if (!empty($letzteEinsaetze)): ?>
        <?php foreach ($letzteEinsaetze as $einsatz): ?>
          <article class="light-tile rounded-2xl overflow-hidden flex flex-col justify-between group">
            
            <!-- Bild mit Badges -->
            <div class="relative h-48 w-full overflow-hidden bg-slate-100">
              <?php if (!empty($einsatz['image_url'])): ?>
                <img src="<?= e($einsatz['image_url']) ?>" alt="<?= e($einsatz['title']) ?>" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
              <?php else: ?>
                <div class="w-full h-full flex items-center justify-center bg-slate-100 text-slate-400">
                  <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 18.657A8 8 0 016.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0120 13a7.975 7.975 0 01-2.343 5.657z"/></svg>
                </div>
              <?php endif; ?>

              <!-- Kategorie Badge -->
              <div class="absolute top-3.5 left-3.5">
                <?= getCategoryBadge($einsatz['category']) ?>
              </div>

              <!-- Datum -->
              <div class="absolute top-3.5 right-3.5 text-xs font-semibold text-slate-800 bg-white/95 px-2.5 py-1 rounded-md shadow-sm border border-slate-200">
                <?= formatDateGerman($einsatz['date']) ?>
              </div>
            </div>

            <!-- Inhalt -->
            <div class="p-6 flex-1 flex flex-col justify-between">
              <div>
                <!-- Ort -->
                <div class="flex items-center gap-1.5 text-xs font-semibold text-sand mb-2 uppercase tracking-wide">
                  <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                  <span class="truncate"><?= e($einsatz['location']) ?></span>
                </div>

                <!-- Stichwort & Titel -->
                <h3 class="text-lg font-bold text-navy mb-2.5 group-hover:text-sand-dark transition line-clamp-2 leading-snug">
                  <?= e($einsatz['title']) ?>
                </h3>

                <!-- Kurzbeschreibung -->
                <p class="text-sm text-slate-600 line-clamp-3 leading-relaxed mb-4 font-light">
                  <?= e($einsatz['description']) ?>
                </p>
              </div>

              <!-- Kachel Footer -->
              <div class="pt-4 border-t border-slate-100 flex items-center justify-between text-xs">
                <span class="text-slate-500 truncate max-w-[65%] font-medium">
                  <?= !empty($einsatz['vehicles']) ? e($einsatz['vehicles']) : 'FF Wulften' ?>
                </span>
                <a href="/einsatz-detail.php?id=<?= $einsatz['id'] ?>" class="text-navy font-bold hover:text-sand inline-flex items-center gap-1 group-hover:translate-x-1 transition flex-shrink-0">
                  Details <span>&rarr;</span>
                </a>
              </div>
            </div>

          </article>
        <?php endforeach; ?>
      <?php else: ?>
        <div class="col-span-full text-center py-12 light-tile rounded-2xl">
          <p class="text-slate-500">Aktuell sind keine Einsätze erfasst.</p>
        </div>
      <?php endif; ?>
    </div>

  </div>
</section>


<!-- 3. Sektion: Ansprechpartner (Helles Design) -->
<section class="py-16 sm:py-20 bg-slate-100/70 border-y border-slate-200 relative">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    
    <div class="text-center max-w-2xl mx-auto mb-14">
      <span class="text-xs font-bold text-sand uppercase tracking-widest">Führung & Vertrauen</span>
      <h2 class="text-3xl sm:text-4xl font-extrabold text-navy uppercase tracking-tight mt-1 mb-2">
        Ihre Ansprechpartner
      </h2>
      <p class="text-slate-600 text-sm sm:text-base font-light">
        Verantwortlich für Ausbildung, Einsätze und die Gemeinschaft in Wulften am Harz.
      </p>
    </div>

    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
      <?php foreach ($ansprechpartner as $kontakt): ?>
        <div class="light-tile rounded-2xl p-5 text-center flex flex-col items-center group">
          <!-- Foto / Portrait (anteilig deutlich größer im Hochformat) -->
          <div class="w-full aspect-[4/5] rounded-2xl overflow-hidden mb-4 border border-slate-200 shadow-sm bg-slate-100">
            <?php if (!empty($kontakt['photo_url'])): ?>
              <img src="<?= e($kontakt['photo_url']) ?>" alt="<?= e($kontakt['name']) ?>" class="w-full h-full object-cover object-top group-hover:scale-105 transition-transform duration-500">
            <?php else: ?>
              <div class="w-full h-full flex flex-col items-center justify-center bg-slate-100 text-sand p-4">
                <svg class="w-12 h-12 opacity-60 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                <span class="text-[11px] text-slate-400">Kein Foto</span>
              </div>
            <?php endif; ?>
          </div>

          <!-- Name -->
          <h3 class="text-base sm:text-lg font-bold text-navy uppercase group-hover:text-sand-dark transition">
            <?= e($kontakt['name']) ?>
          </h3>

          <!-- Dienstgrad direkt unter dem Namen -->
          <span class="text-xs text-slate-500 font-semibold uppercase tracking-wider mt-0.5">
            <?= e($kontakt['rank']) ?>
          </span>

          <!-- Funktion mit Abstand darunter -->
          <div class="mt-2.5">
            <span class="inline-block text-[11px] font-bold text-sand-dark uppercase tracking-wider bg-sand/10 border border-sand/30 px-3 py-1 rounded-full shadow-xs">
              <?= e($kontakt['role_title']) ?>
            </span>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <div class="text-center mt-10">
      <a href="/kommando.php" class="inline-flex items-center gap-2 text-xs font-bold uppercase tracking-wider text-navy hover:text-sand transition">
        <span>Das gesamte Ortskommando kennenlernen</span>
        <span>&rarr;</span>
      </a>
    </div>

  </div>
</section>


<!-- 4. Call-to-Action: LUST AUF EINEN SCHNUPPERDIENST (Helle Kontrast-Sektion) -->
<section class="py-16 sm:py-20 relative overflow-hidden">
  <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
    <div class="relative bg-gradient-to-r from-amber-50/90 via-white to-amber-50/70 rounded-3xl p-8 sm:p-14 border border-amber-200/80 shadow-md">
      
      <div class="relative z-10 max-w-3xl">
        <div class="inline-flex items-center gap-2 px-3.5 py-1 rounded-full bg-red-100 text-red-700 text-xs font-bold uppercase tracking-wider mb-4 border border-red-200">
          <span>🔥</span> Werde Lebensretter vor Ort
        </div>

        <h2 class="text-3xl sm:text-4xl lg:text-5xl font-extrabold text-navy uppercase tracking-tight mb-4 leading-tight">
          Lust auf einen <span class="text-sand">Schnupperdienst?</span>
        </h2>

        <p class="text-slate-700 text-base sm:text-lg mb-8 leading-relaxed font-light">
          Egal ob Handwerker, Student, Angestellter oder Quereinsteiger: Bei der Freiwilligen Feuerwehr Wulften am Harz zählt Teamgeist, Verlässlichkeit und gegenseitige Unterstützung. Schau ganz unverbindlich bei einem unserer Ausbildungsdienste vorbei!
        </p>

        <div class="flex flex-wrap items-center gap-4">
          <a href="/schnupperdienst.php" class="px-8 py-3.5 rounded-xl bg-sand hover:bg-sand-light text-white font-extrabold uppercase tracking-wider text-xs sm:text-sm transition-all duration-300 shadow-sm transform hover:-translate-y-0.5">
            Jetzt unverbindlich anmelden
          </a>
          <a href="/termine.php" class="px-6 py-3.5 rounded-xl bg-white border border-slate-300 text-slate-800 hover:text-navy hover:border-sand font-bold uppercase tracking-wider text-xs sm:text-sm transition">
            Dienstplan ansehen
          </a>
        </div>
      </div>

    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
