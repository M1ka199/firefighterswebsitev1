<?php
declare(strict_types=1);

$currentMobPage = basename($_SERVER['PHP_SELF'], '.php');
$instagramUrlMob = getSetting('social_instagram', 'https://instagram.com/feuerwehr_wulften');

// Zuordnung von Seitennamen und Icons zur aktiven Seite
$pageNamesMob = [
    'index' => 'Startseite',
    'ueber-uns' => 'Über Uns',
    'einsaetze' => 'Einsätze',
    'einsatz-detail' => 'Einsatzbericht',
    'kommando' => 'Kommando',
    'termine' => 'Termine',
    'kontakt' => 'Kontakt',
    'schnupperdienst' => 'Schnuppern',
    'impressum' => 'Impressum',
    'datenschutz' => 'Datenschutz',
];

$pageIconsMob = [
    'index' => '🏠',
    'ueber-uns' => '🚒',
    'einsaetze' => '🚨',
    'einsatz-detail' => '🚨',
    'kommando' => '👥',
    'termine' => '📅',
    'kontakt' => '✉️',
    'schnupperdienst' => '🔥',
    'impressum' => '⚖️',
    'datenschutz' => '🔒',
];

$activePageTitleMob = $pageNamesMob[$currentMobPage] ?? 'Startseite';
$activePageIconMob  = $pageIconsMob[$currentMobPage] ?? '🚒';
?>

<!-- =========================================================================
     BÜRGERMENÜ UNTEN (MOBILE FLOATING BAR & SLIDE-UP BOTTOM SHEET)
     ========================================================================= -->

<!-- 1. Feste Leiste unten in der Mitte des Handy-Bildschirms mit aktiver Seite -->
<aside aria-label="Bürgermenü Schnellzugriff" class="lg:hidden fixed bottom-3 left-1/2 -translate-x-1/2 z-40 w-auto max-w-[96vw] pointer-events-auto select-none">
  <div class="bg-[#001738]/95 backdrop-blur-xl text-white rounded-full p-1.5 border border-white/20 shadow-2xl flex items-center gap-1.5 sm:gap-2">
    
    <!-- Aktive Seite Anzeige (Live-Badge) -->
    <div class="flex items-center gap-2 pl-3 pr-2.5 py-1 text-slate-200 border-r border-white/15">
      <span class="text-sm leading-none flex-shrink-0"><?= $activePageIconMob ?></span>
      <div class="flex flex-col text-left">
        <span class="text-[8px] font-bold text-sand uppercase tracking-wider leading-none">Aktuell:</span>
        <span class="text-[11px] font-extrabold text-white uppercase tracking-wider leading-tight max-w-[95px] truncate">
          <?= e($activePageTitleMob) ?>
        </span>
      </div>
      <span class="w-1.5 h-1.5 rounded-full bg-emerald-400 flex-shrink-0" title="Aktive Seite"></span>
    </div>

    <!-- Bürgermenü Trigger-Button -->
    <button id="buergermenu-open-btn" type="button" aria-expanded="false" aria-controls="buergermenu-sheet" class="flex items-center gap-1.5 px-3.5 py-2 rounded-full bg-white/10 hover:bg-white/20 active:scale-95 text-white font-extrabold text-xs uppercase tracking-wider transition border border-white/15">
      <span>Menü</span>
      <svg class="w-3.5 h-3.5 text-sand transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 15l7-7 7 7"/></svg>
    </button>

  </div>
</aside>


<!-- 2. Halbtransparenter Backdrop -->
<div id="buergermenu-backdrop" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden opacity-0 transition-opacity duration-300 lg:hidden"></div>


<!-- 3. Bürgermenü Bottom-Sheet (klappt geschmeidig von unten aus) -->
<nav id="buergermenu-sheet" aria-label="Bürger-Hauptmenü" class="fixed inset-x-0 bottom-0 z-50 transform translate-y-full transition-transform duration-300 ease-out bg-white rounded-t-[32px] border-t border-slate-200 shadow-2xl p-6 sm:p-8 max-h-[85vh] overflow-y-auto lg:hidden flex flex-col justify-between">
  
  <div>
    <!-- Griff zum Zuziehen -->
    <div class="w-12 h-1.5 bg-slate-300 rounded-full mx-auto mb-4 cursor-pointer" id="buergermenu-handle"></div>

    <!-- Header im Menü -->
    <div class="flex items-center justify-between border-b border-slate-100 pb-4 mb-5">
      <div class="flex items-center gap-3">
        <img src="/assets/img/logo.png" alt="FF Wulften Logo" class="h-10 w-auto object-contain mix-blend-multiply">
        <div>
          <h3 class="text-base font-extrabold uppercase text-navy tracking-tight leading-tight">
            Bürgermenü
          </h3>
          <span class="text-[11px] text-slate-500 font-light block">
            Freiwillige Feuerwehr Wulften am Harz
          </span>
        </div>
      </div>

      <button id="buergermenu-close-btn" type="button" aria-label="Menü schließen" class="w-9 h-9 rounded-full bg-slate-100 hover:bg-slate-200 text-slate-500 hover:text-navy flex items-center justify-center font-bold text-sm transition">
        ✕
      </button>
    </div>

    <!-- Navigation Kacheln & Links -->
    <div class="grid grid-cols-2 gap-2.5 text-xs font-bold uppercase tracking-wider mb-6">
      
      <!-- Startseite -->
      <a href="/index.php" class="p-3 rounded-2xl border transition flex items-center justify-between <?= ($currentMobPage === 'index') ? 'bg-navy text-white border-sand ring-2 ring-sand/50 shadow' : 'bg-slate-50 border-slate-200 text-navy hover:bg-slate-100' ?>">
        <div class="flex items-center gap-2">
          <span class="text-base">🏠</span>
          <span>Startseite</span>
        </div>
        <?php if ($currentMobPage === 'index'): ?>
          <span class="w-2 h-2 rounded-full bg-emerald-400" title="Aktive Seite"></span>
        <?php endif; ?>
      </a>

      <!-- Über Uns -->
      <a href="/ueber-uns.php" class="p-3 rounded-2xl border transition flex items-center justify-between <?= ($currentMobPage === 'ueber-uns') ? 'bg-navy text-white border-sand ring-2 ring-sand/50 shadow' : 'bg-slate-50 border-slate-200 text-navy hover:bg-slate-100' ?>">
        <div class="flex items-center gap-2">
          <span class="text-base">🚒</span>
          <span>Über Uns</span>
        </div>
        <?php if ($currentMobPage === 'ueber-uns'): ?>
          <span class="w-2 h-2 rounded-full bg-emerald-400" title="Aktive Seite"></span>
        <?php endif; ?>
      </a>

      <!-- Einsätze -->
      <a href="/einsaetze.php" class="p-3 rounded-2xl border transition flex items-center justify-between <?= ($currentMobPage === 'einsaetze' || $currentMobPage === 'einsatz-detail') ? 'bg-navy text-white border-sand ring-2 ring-sand/50 shadow' : 'bg-slate-50 border-slate-200 text-navy hover:bg-slate-100' ?>">
        <div class="flex items-center gap-2">
          <span class="text-base">🚨</span>
          <span>Einsätze</span>
        </div>
        <?php if ($currentMobPage === 'einsaetze' || $currentMobPage === 'einsatz-detail'): ?>
          <span class="w-2 h-2 rounded-full bg-emerald-400" title="Aktive Seite"></span>
        <?php endif; ?>
      </a>

      <!-- Kommando -->
      <a href="/kommando.php" class="p-3 rounded-2xl border transition flex items-center justify-between <?= ($currentMobPage === 'kommando') ? 'bg-navy text-white border-sand ring-2 ring-sand/50 shadow' : 'bg-slate-50 border-slate-200 text-navy hover:bg-slate-100' ?>">
        <div class="flex items-center gap-2">
          <span class="text-base">👥</span>
          <span>Kommando</span>
        </div>
        <?php if ($currentMobPage === 'kommando'): ?>
          <span class="w-2 h-2 rounded-full bg-emerald-400" title="Aktive Seite"></span>
        <?php endif; ?>
      </a>

      <!-- Termine -->
      <a href="/termine.php" class="p-3 rounded-2xl border transition flex items-center justify-between <?= ($currentMobPage === 'termine') ? 'bg-navy text-white border-sand ring-2 ring-sand/50 shadow' : 'bg-slate-50 border-slate-200 text-navy hover:bg-slate-100' ?>">
        <div class="flex items-center gap-2">
          <span class="text-base">📅</span>
          <span>Termine</span>
        </div>
        <?php if ($currentMobPage === 'termine'): ?>
          <span class="w-2 h-2 rounded-full bg-emerald-400" title="Aktive Seite"></span>
        <?php endif; ?>
      </a>

      <!-- Schnupperdienst -->
      <a href="/schnupperdienst.php" class="p-3 rounded-2xl border transition flex items-center justify-between <?= ($currentMobPage === 'schnupperdienst') ? 'bg-sand text-white border-white ring-2 ring-sand shadow' : 'bg-amber-50 border-amber-200 text-amber-900 hover:bg-amber-100' ?>">
        <div class="flex items-center gap-2">
          <span class="text-base">🔥</span>
          <span>Schnuppern</span>
        </div>
        <?php if ($currentMobPage === 'schnupperdienst'): ?>
          <span class="w-2 h-2 rounded-full bg-white" title="Aktive Seite"></span>
        <?php endif; ?>
      </a>

      <!-- Kontakt -->
      <a href="/kontakt.php" class="p-3 rounded-2xl border transition flex items-center justify-between <?= ($currentMobPage === 'kontakt') ? 'bg-navy text-white border-sand ring-2 ring-sand/50 shadow' : 'bg-slate-50 border-slate-200 text-navy hover:bg-slate-100' ?>">
        <div class="flex items-center gap-2">
          <span class="text-base">✉️</span>
          <span>Kontakt</span>
        </div>
        <?php if ($currentMobPage === 'kontakt'): ?>
          <span class="w-2 h-2 rounded-full bg-emerald-400" title="Aktive Seite"></span>
        <?php endif; ?>
      </a>

      <!-- Instagram -->
      <a href="<?= e($instagramUrlMob) ?>" target="_blank" rel="noopener noreferrer" class="p-3 rounded-2xl bg-slate-50 border border-slate-200 text-navy hover:bg-pink-50 hover:text-pink-700 transition flex items-center gap-2">
        <span class="text-base">📸</span>
        <span>Instagram</span>
      </a>

    </div>

    <!-- Zusätzliche Links & Rechtliches -->
    <div class="pt-4 border-t border-slate-100 flex flex-wrap items-center justify-between text-xs text-slate-500 mb-5">
      <div class="flex items-center gap-4">
        <a href="/impressum.php" class="hover:text-navy underline <?= ($currentMobPage === 'impressum') ? 'font-bold text-navy' : '' ?>">Impressum</a>
        <a href="/datenschutz.php" class="hover:text-navy underline <?= ($currentMobPage === 'datenschutz') ? 'font-bold text-navy' : '' ?>">Datenschutz</a>
        <button type="button" onclick="if(window.openCookieSettingsModal){window.openCookieSettingsModal();}" class="hover:text-navy underline">
          Cookies
        </button>
      </div>
      <a href="/admin/login.php" class="font-bold text-sand hover:text-sand-dark">
        Interner Bereich &rarr;
      </a>
    </div>
  </div>

  <!-- Roter Notruf-Balken am Ende des Sheets -->
  <div class="pt-2">
    <a href="tel:112" class="w-full py-3.5 px-4 rounded-2xl bg-gradient-to-r from-red-600 via-alarm to-red-600 text-white font-extrabold uppercase tracking-wider text-xs flex items-center justify-center gap-2 shadow-md active:scale-95 transition">
      <span class="w-2.5 h-2.5 rounded-full bg-white animate-ping"></span>
      <span>Im Notfall immer Notruf 112 anrufen</span>
    </a>
  </div>

</nav>


<!-- 4. JavaScript Logik für das Bürgermenü -->
<script>
(function() {
  const openBtn = document.getElementById('buergermenu-open-btn');
  const closeBtn = document.getElementById('buergermenu-close-btn');
  const handle = document.getElementById('buergermenu-handle');
  const backdrop = document.getElementById('buergermenu-backdrop');
  const sheet = document.getElementById('buergermenu-sheet');

  function openSheet() {
    if (!sheet || !backdrop) return;
    backdrop.classList.remove('hidden');
    requestAnimationFrame(() => {
      backdrop.classList.remove('opacity-0');
      backdrop.classList.add('opacity-100');
      sheet.classList.remove('translate-y-full');
      sheet.classList.add('translate-y-0');
      if (openBtn) openBtn.setAttribute('aria-expanded', 'true');
    });
    document.body.classList.add('overflow-hidden');
  }

  function closeSheet() {
    if (!sheet || !backdrop) return;
    backdrop.classList.remove('opacity-100');
    backdrop.classList.add('opacity-0');
    sheet.classList.remove('translate-y-0');
    sheet.classList.add('translate-y-full');
    if (openBtn) openBtn.setAttribute('aria-expanded', 'false');

    setTimeout(() => {
      backdrop.classList.add('hidden');
      document.body.classList.remove('overflow-hidden');
    }, 300);
  }

  if (openBtn) openBtn.addEventListener('click', openSheet);
  if (closeBtn) closeBtn.addEventListener('click', closeSheet);
  if (backdrop) backdrop.addEventListener('click', closeSheet);
  if (handle) handle.addEventListener('click', closeSheet);

  document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') closeSheet();
  });
})();
</script>
