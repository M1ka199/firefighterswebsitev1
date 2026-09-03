<?php
declare(strict_types=1);
?>

<!-- ==========================================
     DSGVO COOKIE CONSENT BANNER & MODAL
     ========================================== -->

<!-- 1. Floating Bottom Banner (wird angezeigt, wenn noch keine Auswahl getroffen wurde) -->
<div id="ffw-cookie-banner" class="fixed bottom-4 left-4 right-4 sm:left-6 sm:right-6 md:max-w-xl md:left-auto z-50 transform translate-y-full opacity-0 transition-all duration-500 pointer-events-none">
  <div class="light-panel rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-2xl bg-white/95 backdrop-blur-md">
    <div class="flex items-start gap-4 mb-4">
      <div class="w-12 h-12 rounded-2xl bg-sand/15 text-sand-dark flex items-center justify-center text-2xl flex-shrink-0 shadow-xs">
        🍪
      </div>
      <div>
        <h3 class="text-base sm:text-lg font-bold uppercase tracking-tight text-navy">
          Privatsphäre & Cookies
        </h3>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light mt-1">
          Wir nutzen Cookies und ähnliche Technologien, um die Website betriebssicher zu halten und Ihnen die bestmögliche Nutzung unseres Informationsangebots zu ermöglichen.
        </p>
      </div>
    </div>

    <!-- Buttons -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-2.5 pt-2">
      <button id="btn-cookie-accept-all" class="flex-1 py-3 px-4 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm text-center">
        Alle akzeptieren
      </button>
      <button id="btn-cookie-essential-only" class="flex-1 py-3 px-4 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-800 font-bold uppercase tracking-wider text-xs transition border border-slate-300 text-center">
        Nur essenzielle
      </button>
      <button id="btn-cookie-open-settings" class="py-3 px-4 rounded-xl bg-transparent hover:bg-slate-100 text-slate-600 font-semibold text-xs transition text-center underline sm:no-underline">
        Einstellungen
      </button>
    </div>

    <div class="mt-3 pt-3 border-t border-slate-100 flex items-center justify-between text-[11px] text-slate-400">
      <span>Freiwillige Feuerwehr Wulften am Harz</span>
      <div class="space-x-3">
        <a href="/datenschutz.php" class="hover:text-navy underline">Datenschutz</a>
        <a href="/impressum.php" class="hover:text-navy underline">Impressum</a>
      </div>
    </div>
  </div>
</div>


<!-- 2. Detailliertes Einstellungs-Modal -->
<div id="ffw-cookie-modal" class="fixed inset-0 z-50 bg-black/60 backdrop-blur-xs hidden items-center justify-center p-4 sm:p-6 transition-opacity">
  <div class="light-panel rounded-3xl max-w-xl w-full p-6 sm:p-8 border border-slate-200 shadow-2xl bg-white max-h-[90vh] overflow-y-auto space-y-6">
    
    <div class="flex items-center justify-between border-b border-slate-100 pb-4">
      <div class="flex items-center gap-3">
        <span class="text-2xl">⚙️</span>
        <div>
          <h3 class="text-lg font-bold uppercase text-navy">Cookie-Einstellungen</h3>
          <p class="text-xs text-slate-500 font-light">Legen Sie fest, welche Dienste geladen werden dürfen.</p>
        </div>
      </div>
      <button id="btn-cookie-modal-close" class="p-2 rounded-xl text-slate-400 hover:text-navy hover:bg-slate-100 transition" aria-label="Schließen">
        ✕
      </button>
    </div>

    <!-- Kategorien -->
    <div class="space-y-4 text-xs sm:text-sm">
      
      <!-- 1. Essenzielle Cookies (immer aktiv) -->
      <div class="p-4 rounded-2xl bg-slate-50 border border-slate-200">
        <div class="flex items-center justify-between mb-2">
          <div class="flex items-center gap-2">
            <span class="font-bold text-navy uppercase text-xs">1. Notwendig & Essenziell</span>
            <span class="px-2 py-0.5 rounded-full text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">Immer aktiv</span>
          </div>
          <input type="checkbox" checked disabled class="rounded text-sand focus:ring-sand cursor-not-allowed">
        </div>
        <p class="text-slate-600 text-xs font-light leading-relaxed">
          Diese Cookies und Speicherfunktionen sind für den sicheren Betrieb der Website, CSRF-Schutz in Formularen sowie das Speichern Ihrer Einwilligungen zwingend erforderlich.
        </p>
      </div>

      <!-- 2. Externe Medien & Soziale Netzwerke -->
      <div class="p-4 rounded-2xl bg-white border border-slate-200">
        <div class="flex items-center justify-between mb-2">
          <span class="font-bold text-navy uppercase text-xs">2. Externe Medien (Instagram, Karten)</span>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" id="cookie-toggle-external" class="sr-only peer">
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-navy"></div>
          </label>
        </div>
        <p class="text-slate-600 text-xs font-light leading-relaxed">
          Ermöglicht das direkte Einbetten externer Inhalte wie Lagekarten oder Beiträgen aus sozialen Medien (z.B. Instagram).
        </p>
      </div>

      <!-- 3. Statistische Erfassung & Optimierung -->
      <div class="p-4 rounded-2xl bg-white border border-slate-200">
        <div class="flex items-center justify-between mb-2">
          <span class="font-bold text-navy uppercase text-xs">3. Statistik & Nutzungsanalyse</span>
          <label class="relative inline-flex items-center cursor-pointer">
            <input type="checkbox" id="cookie-toggle-stats" class="sr-only peer">
            <div class="w-11 h-6 bg-slate-200 peer-focus:outline-none rounded-full peer peer-checked:after:translate-x-full peer-checked:after:border-white after:content-[''] after:absolute after:top-[2px] after:left-[2px] after:bg-white after:border-slate-300 after:border after:rounded-full after:h-5 after:w-5 after:transition-all peer-checked:bg-navy"></div>
          </label>
        </div>
        <p class="text-slate-600 text-xs font-light leading-relaxed">
          Erlaubt uns die anonymisierte Auswertung von Besucherströmen, um Bürgerinformationen wie Einsatzberichte und Termine noch gezielter bereitzustellen.
        </p>
      </div>

    </div>

    <!-- Modal Footer Actions -->
    <div class="flex flex-col sm:flex-row items-stretch sm:items-center justify-end gap-3 pt-4 border-t border-slate-100">
      <button id="btn-cookie-save-settings" class="py-3 px-5 rounded-xl bg-slate-100 hover:bg-slate-200 text-navy font-bold uppercase tracking-wider text-xs transition border border-slate-300">
        Auswahl speichern
      </button>
      <button id="btn-cookie-modal-accept-all" class="py-3 px-6 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm">
        Alle akzeptieren
      </button>
    </div>

  </div>
</div>


<!-- 3. Cookie Consent JavaScript Logik -->
<script>
(function() {
  const STORAGE_KEY = 'ffw_cookie_consent_v1';
  const banner = document.getElementById('ffw-cookie-banner');
  const modal = document.getElementById('ffw-cookie-modal');
  const toggleExternal = document.getElementById('cookie-toggle-external');
  const toggleStats = document.getElementById('cookie-toggle-stats');

  function getConsent() {
    try {
      const data = localStorage.getItem(STORAGE_KEY);
      return data ? JSON.parse(data) : null;
    } catch (e) {
      return null;
    }
  }

  function saveConsent(essential, external, stats) {
    const payload = {
      essential: true,
      external: Boolean(external),
      stats: Boolean(stats),
      timestamp: new Date().toISOString()
    };
    try {
      localStorage.setItem(STORAGE_KEY, JSON.stringify(payload));
    } catch (e) {}

    hideBanner();
    hideModal();

    // Event für andere Komponenten auslösen falls nötig
    window.dispatchEvent(new CustomEvent('ffw_consent_updated', { detail: payload }));
  }

  function showBanner() {
    if (!banner) return;
    banner.classList.remove('pointer-events-none', 'translate-y-full', 'opacity-0');
    banner.classList.add('translate-y-0', 'opacity-100');
  }

  function hideBanner() {
    if (!banner) return;
    banner.classList.remove('translate-y-0', 'opacity-100');
    banner.classList.add('translate-y-full', 'opacity-0', 'pointer-events-none');
  }

  window.openCookieSettingsModal = function() {
    const current = getConsent() || { external: false, stats: false };
    if (toggleExternal) toggleExternal.checked = Boolean(current.external);
    if (toggleStats) toggleStats.checked = Boolean(current.stats);

    if (modal) {
      modal.classList.remove('hidden');
      modal.classList.add('flex');
    }
  };

  function hideModal() {
    if (modal) {
      modal.classList.add('hidden');
      modal.classList.remove('flex');
    }
  }

  // Initial Check
  document.addEventListener('DOMContentLoaded', function() {
    const consent = getConsent();
    if (!consent) {
      // Kleine Verzögerung für geschmeidige Einblendung
      setTimeout(showBanner, 600);
    }

    // Event Listeners Banner
    const btnAll = document.getElementById('btn-cookie-accept-all');
    if (btnAll) btnAll.addEventListener('click', () => saveConsent(true, true, true));

    const btnEssential = document.getElementById('btn-cookie-essential-only');
    if (btnEssential) btnEssential.addEventListener('click', () => saveConsent(true, false, false));

    const btnOpenSettings = document.getElementById('btn-cookie-open-settings');
    if (btnOpenSettings) btnOpenSettings.addEventListener('click', () => {
      hideBanner();
      window.openCookieSettingsModal();
    });

    // Event Listeners Modal
    const btnCloseModal = document.getElementById('btn-cookie-modal-close');
    if (btnCloseModal) btnCloseModal.addEventListener('click', hideModal);

    const btnModalAll = document.getElementById('btn-cookie-modal-accept-all');
    if (btnModalAll) btnModalAll.addEventListener('click', () => saveConsent(true, true, true));

    const btnSaveCustom = document.getElementById('btn-cookie-save-settings');
    if (btnSaveCustom) btnSaveCustom.addEventListener('click', () => {
      saveConsent(true, toggleExternal?.checked, toggleStats?.checked);
    });
  });
})();
</script>
