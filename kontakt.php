<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Helpers.php';

$seo = getPageSeo('kontakt');
$extraScripts = ['/assets/js/forms.js'];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/banner.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

  <!-- Wichtiger Notfall-Hinweis (Hell, auffällig, unverwechselbar) -->
  <div class="mb-10 p-5 rounded-2xl bg-red-50 border-2 border-red-500 text-slate-800 shadow-sm flex items-center gap-4">
    <div class="w-12 h-12 rounded-xl bg-red-600 flex items-center justify-center text-white flex-shrink-0 font-extrabold text-2xl animate-pulse">
      !
    </div>
    <div>
      <h3 class="text-base font-extrabold uppercase tracking-wide text-red-800">
        Wichtiger Hinweis für akute Notfälle:
      </h3>
      <p class="text-xs sm:text-sm text-slate-700 mt-0.5 font-light">
        Dieses Formular wird <strong class="text-red-700 font-bold">nicht</strong> rund um die Uhr überwacht. Bei Bränden, Unfällen oder akuter Gefahr wählen Sie bitte unverzüglich den <strong class="text-red-700 underline text-base font-extrabold">Notruf 112</strong>!
      </p>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-10">
    
    <!-- Linke Spalte: Kontaktdaten & Anfahrt -->
    <div class="space-y-6">
      
      <div class="light-panel rounded-3xl p-8 border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-sand uppercase tracking-widest block mb-1">Standort</span>
        <h3 class="text-2xl font-bold uppercase text-navy mb-6">Feuerwehrhaus</h3>

        <div class="space-y-4 text-sm text-slate-700 font-light">
          <div class="flex items-start gap-3">
            <svg class="w-5 h-5 text-sand flex-shrink-0 mt-0.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
            <div>
              <strong class="text-navy block font-semibold">Freiwillige Feuerwehr Wulften</strong>
              Steinstraße 1<br>
              37199 Wulften am Harz
            </div>
          </div>

          <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-sand flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
            <div>
              <span class="text-xs text-slate-500 block">E-Mail für allgemeine Anfragen:</span>
              <a href="mailto:<?= e(getSetting('contact_email', 'info@feuerwehr-wulften.de')) ?>" class="text-sand font-bold hover:underline">
                <?= e(getSetting('contact_email', 'info@feuerwehr-wulften.de')) ?>
              </a>
            </div>
          </div>

          <div class="flex items-center gap-3">
            <svg class="w-5 h-5 text-sand flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
            <div>
              <span class="text-xs text-slate-500 block">Diensttelefon (während der Dienste):</span>
              <span class="text-navy font-semibold">+49 5556 112</span>
            </div>
          </div>
        </div>

        <div class="mt-8 pt-6 border-t border-slate-100">
          <a href="/schnupperdienst.php" class="block w-full text-center py-3 rounded-xl bg-amber-50 text-amber-900 border border-amber-300 hover:bg-amber-100 font-bold uppercase tracking-wider text-xs transition">
            🔥 Lust auf Feuerwehr? Zum Schnupperdienst
          </a>
        </div>
      </div>

    </div>

    <!-- Rechte Spalte: Kontaktformular -->
    <div class="lg:col-span-2">
      <div class="light-panel rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm">
        <span class="text-xs font-bold text-sand uppercase tracking-widest block mb-1">Nachricht senden</span>
        <h3 class="text-2xl sm:text-3xl font-extrabold uppercase text-navy mb-6">
          Schreiben Sie uns
        </h3>

        <!-- Form Feedback Container -->
        <div class="form-feedback hidden"></div>

        <form action="/api/submit_form.php" method="POST" class="ajax-form space-y-6">
          <input type="hidden" name="type" value="kontakt">

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Ihr vollständiger Name <span class="text-red-500">*</span>
              </label>
              <input type="text" id="name" name="name" required placeholder="Max Mustermann" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
            </div>

            <div>
              <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Ihre E-Mail-Adresse <span class="text-red-500">*</span>
              </label>
              <input type="email" id="email" name="email" required placeholder="max@beispiel.de" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Telefonnummer (optional)
              </label>
              <input type="tel" id="phone" name="phone" placeholder="0170 1234567" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
            </div>

            <div>
              <label for="abteilung" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Abteilung / Zuständigkeit
              </label>
              <select id="abteilung" name="abteilung" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
                <option value="Allgemein">Allgemeine Anfrage</option>
                <option value="Einsatzabteilung">Einsatzabteilung</option>
                <option value="Jugendfeuerwehr">Jugendfeuerwehr</option>
                <option value="Kinderfeuerwehr">Kinderfeuerwehr (Löschbande)</option>
                <option value="Ortskommando">Ortskommando / Wehrleitung</option>
              </select>
            </div>
          </div>

          <div>
            <label for="topic" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
              Betreff / Thema
            </label>
            <input type="text" id="topic" name="topic" placeholder="z.B. Schnupperdienst, Mitgliedschaft, Brandschutzerziehung" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
          </div>

          <div>
            <label for="message" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
              Ihre Nachricht <span class="text-red-500">*</span>
            </label>
            <textarea id="message" name="message" rows="5" required placeholder="Wie können wir Ihnen weiterhelfen?" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium resize-y"></textarea>
          </div>

          <div class="flex items-start gap-3 text-xs text-slate-600">
            <input type="checkbox" id="privacy" required class="mt-1 rounded border-slate-300 text-sand focus:ring-sand">
            <label for="privacy">
              Ich stimme zu, dass meine Angaben zur Bearbeitung der Anfrage erhoben werden. Weitere Details finden Sie in unserer <a href="/datenschutz.php" class="text-sand font-bold underline" target="_blank">Datenschutzerklärung</a>.
            </label>
          </div>

          <div>
            <button type="submit" class="w-full sm:w-auto px-8 py-3.5 rounded-xl bg-navy hover:bg-navy-dark text-white font-extrabold uppercase tracking-wider text-xs transition shadow-sm">
              Nachricht absenden
            </button>
          </div>
        </form>

      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
