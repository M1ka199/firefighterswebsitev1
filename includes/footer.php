<?php
declare(strict_types=1);
?>
  </main>

  <!-- Global Footer (Dunkelblaues Fundament mit klaren Kontrasten) -->
  <footer class="bg-[#001e47] text-slate-200 mt-20 relative border-t-4 border-sand">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
      <div class="grid grid-cols-1 md:grid-cols-3 gap-12 lg:gap-16">
        
        <!-- Spalte 1: Adresse -->
        <div class="space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-white/10 border border-sand/40 flex items-center justify-center p-1.5 shadow">
              <svg class="w-full h-full text-sand-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/>
              </svg>
            </div>
            <h4 class="text-white font-bold text-base uppercase tracking-wider">Feuerwehrhaus</h4>
          </div>
          
          <address class="not-italic text-sm text-slate-300 leading-relaxed pl-1">
            <strong class="text-white block font-semibold">Freiwillige Feuerwehr Wulften am Harz</strong>
            Steinstraße 1<br>
            37199 Wulften am Harz<br>
            Landkreis Göttingen, Niedersachsen
          </address>

          <div class="pt-2 text-xs text-slate-400">
            <span>E-Mail: </span>
            <a href="mailto:<?= e(getSetting('contact_email', 'info@feuerwehr-wulften.de')) ?>" class="text-sand-light hover:underline font-semibold">
              <?= e(getSetting('contact_email', 'info@feuerwehr-wulften.de')) ?>
            </a>
          </div>
        </div>

        <!-- Spalte 2: Notfallnummern -->
        <div class="space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-red-600/30 border border-red-500/50 flex items-center justify-center p-1.5 shadow">
              <svg class="w-full h-full text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/>
              </svg>
            </div>
            <h4 class="text-white font-bold text-base uppercase tracking-wider">Notfallnummern</h4>
          </div>

          <ul class="space-y-2.5 text-sm">
            <li class="flex items-center justify-between border-b border-white/10 pb-2">
              <span class="text-slate-300">Feuerwehr & Rettungsdienst:</span>
              <a href="tel:112" class="font-extrabold text-red-400 hover:underline text-lg flex items-center gap-1.5">
                <span class="w-2 h-2 rounded-full bg-red-500 animate-ping"></span>
                112
              </a>
            </li>
            <li class="flex items-center justify-between border-b border-white/10 pb-2">
              <span class="text-slate-300">Polizei:</span>
              <a href="tel:110" class="font-bold text-white hover:underline text-base">110</a>
            </li>
            <li class="flex items-center justify-between pt-1">
              <span class="text-slate-300">Ärztl. Bereitschaftsdienst:</span>
              <a href="tel:116117" class="font-bold text-sand-light hover:underline text-base">116 117</a>
            </li>
          </ul>
        </div>

        <!-- Spalte 3: Schnellzugriff -->
        <div class="space-y-4">
          <div class="flex items-center gap-3">
            <div class="w-8 h-8 rounded-lg bg-white/10 border border-sand/40 flex items-center justify-center p-1.5 shadow">
              <svg class="w-full h-full text-sand-light" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <h4 class="text-white font-bold text-base uppercase tracking-wider">Schnellzugriff</h4>
          </div>

          <ul class="space-y-2 text-sm">
            <li>
              <a href="/einsaetze.php" class="hover:text-sand-light transition inline-flex items-center gap-2">
                <span class="text-sand-light text-xs">&rsaquo;</span> Einsatzverlauf
              </a>
            </li>
            <li>
              <a href="/kommando.php" class="hover:text-sand-light transition inline-flex items-center gap-2">
                <span class="text-sand-light text-xs">&rsaquo;</span> Ortskommando
              </a>
            </li>
            <li>
              <a href="/schnupperdienst.php" class="text-sand-light font-bold hover:text-white transition inline-flex items-center gap-2">
                <span>🔥</span> Schnupperdienst
              </a>
            </li>
            <li>
              <a href="/kontakt.php" class="hover:text-sand-light transition inline-flex items-center gap-2">
                <span class="text-sand-light text-xs">&rsaquo;</span> Kontakt
              </a>
            </li>
          </ul>
        </div>

      </div>

      <!-- Bottom Bar -->
      <div class="mt-14 pt-8 border-t border-white/10 flex flex-col sm:flex-row items-center justify-between text-xs text-slate-400 gap-4">
        <div>
          &copy; 2026 Freiwillige Feuerwehr Wulften am Harz. Alle Rechte vorbehalten.
        </div>
        <div class="flex items-center space-x-6">
          <a href="/impressum.php" class="hover:text-slate-200 transition">Impressum</a>
          <a href="/datenschutz.php" class="hover:text-slate-200 transition">Datenschutz</a>
          <button type="button" onclick="if(window.openCookieSettingsModal){window.openCookieSettingsModal();}" class="hover:text-slate-200 transition">
            Cookies
          </button>
          <a href="/admin/login.php" class="text-sand-light hover:text-white font-semibold transition flex items-center gap-1.5 py-1 px-3 rounded-lg border border-sand/40 bg-white/5">
            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
              <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
            </svg>
            <span>Interner Bereich</span>
          </a>
        </div>
      </div>
    </div>
  </footer>

  <!-- Zentrale Steuerung Mitte unten auf dem Smartphone (Mobile Floating Dock) -->
  <?php require_once __DIR__ . '/mobile_dock.php'; ?>

  <!-- DSGVO Cookie Consent Banner & Modal -->
  <?php require_once __DIR__ . '/cookie_banner.php'; ?>

  <!-- Scripts -->
  <script src="/assets/js/main.js"></script>
  <?php if (isset($extraScripts) && is_array($extraScripts)): ?>
    <?php foreach ($extraScripts as $script): ?>
      <script src="<?= $script ?>"></script>
    <?php endforeach; ?>
  <?php endif; ?>
</body>
</html>
