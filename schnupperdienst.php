<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Helpers.php';

$seo = getPageSeo('schnupperdienst');
$extraScripts = ['/assets/js/forms.js'];

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/banner.php';
?>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

  <!-- Intro & Vorteile Grid (Helle Kacheln) -->
  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 mb-16">
    <div class="light-tile rounded-2xl p-6 border-l-4 border-sand">
      <div class="text-2xl mb-2">🤝</div>
      <h3 class="text-lg font-bold text-navy uppercase mb-2">Echte Kameradschaft</h3>
      <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
        Bei uns zählt der Mensch. Ein verlässliches Team, auf das man sich in jeder Lebenslage voll verlassen kann.
      </p>
    </div>

    <div class="light-tile rounded-2xl p-6 border-l-4 border-red-600">
      <div class="text-2xl mb-2">🚒</div>
      <h3 class="text-lg font-bold text-navy uppercase mb-2">Faszinierende Technik</h3>
      <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
        Lerne den sicheren Umgang mit modernsten Rettungsgeräten, Löschtechnik, Atemschutz und Funkgeräten.
      </p>
    </div>

    <div class="light-tile rounded-2xl p-6 border-l-4 border-blue-600">
      <div class="text-2xl mb-2">🛡️</div>
      <h3 class="text-lg font-bold text-navy uppercase mb-2">Sinnstiftendes Ehrenamt</h3>
      <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
        Schütze deine Nachbarn und deine Heimat direkt vor Ort in Wulften am Harz vor Gefahren.
      </p>
    </div>
  </div>


  <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 items-start">
    
    <!-- Anmeldeformular -->
    <div class="lg:col-span-7">
      <div class="light-panel rounded-3xl p-8 sm:p-10 border border-slate-200 shadow-sm">
        <div class="inline-flex items-center gap-2 px-3 py-1 rounded-full bg-red-50 text-red-700 text-xs font-bold uppercase tracking-wider mb-3 border border-red-200">
          <span>🔥</span> Schnuppertag vereinbaren
        </div>
        <h2 class="text-2xl sm:text-3xl font-extrabold uppercase text-navy mb-3">
          Jetzt zum Schnupperdienst anmelden
        </h2>
        <p class="text-slate-600 text-sm mb-8 font-light leading-relaxed">
          Komm einfach zu einem unserer Ausbildungsdienste vorbei. Du schaust dir alles in Ruhe an, lernst die Kameradinnen und Kameraden kennen und kannst die Ausrüstung ausprobieren. Völlig unverbindlich!
        </p>

        <!-- Feedback Container -->
        <div class="form-feedback hidden"></div>

        <form action="/api/submit_form.php" method="POST" class="ajax-form space-y-6">
          <input type="hidden" name="type" value="schnupperdienst">

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label for="name" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Vor- und Nachname <span class="text-red-500">*</span>
              </label>
              <input type="text" id="name" name="name" required placeholder="Vorname Nachname" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
            </div>

            <div>
              <label for="email" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                E-Mail-Adresse <span class="text-red-500">*</span>
              </label>
              <input type="email" id="email" name="email" required placeholder="deine@mail.de" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label for="phone" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Telefonnummer / Mobil
              </label>
              <input type="tel" id="phone" name="phone" placeholder="0170 ..." class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
            </div>

            <div>
              <label for="age" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Dein Alter (Kinderfeuerwehr ab 6 J.)
              </label>
              <input type="number" id="age" name="age" min="6" max="99" placeholder="z.B. 8 oder 24" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
            <div>
              <label for="interest" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Ich interessiere mich für:
              </label>
              <select id="interest" name="interest" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
                <option value="Aktive Einsatzabteilung (ab 16 Jahre)">Aktive Einsatzabteilung (ab 16 J.)</option>
                <option value="Jugendfeuerwehr (10 - 16 Jahre)">Jugendfeuerwehr (10 - 16 J.)</option>
                <option value="Kinderfeuerwehr (6 - 10 Jahre)">Kinderfeuerwehr (6 - 10 J.)</option>
              </select>
            </div>

            <div>
              <label for="prior_exp" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
                Feuerwehr-Vorerfahrung?
              </label>
              <select id="prior_exp" name="prior_exp" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium">
                <option value="Keine Vorerfahrung (Quereinsteiger)">Keine Vorerfahrung (Quereinstieg)</option>
                <option value="Früher in Jugendfeuerwehr gewesen">Früher in Jugendfeuerwehr gewesen</option>
                <option value="Erfahrung aus anderer Wehr (Umzug)">Ausbildung vorhanden (z.B. Umzug)</option>
                <option value="Handwerkliche / Medizinische Kenntnisse">Handwerkliche/Medizinische Kenntnisse</option>
              </select>
            </div>
          </div>

          <div>
            <label for="message" class="block text-xs font-bold uppercase tracking-wider text-slate-700 mb-2">
              Möchtest du uns noch etwas mitteilen?
            </label>
            <textarea id="message" name="message" rows="4" placeholder="Hast du an bestimmten Tagen Zeit oder Fragen?" class="light-input w-full rounded-xl px-4 py-3 text-sm font-medium resize-y"></textarea>
          </div>

          <div class="flex items-start gap-3 text-xs text-slate-600">
            <input type="checkbox" id="privacy-schnupper" required class="mt-1 rounded border-slate-300 text-sand focus:ring-sand">
            <label for="privacy-schnupper">
              Ich bin einverstanden, dass mich die Leitung der Freiwilligen Feuerwehr Wulften bezüglich des Schnupperdienstes per Mail oder Telefon kontaktiert.
            </label>
          </div>

          <div>
            <button type="submit" class="w-full py-4 rounded-xl bg-sand hover:bg-sand-light text-white font-extrabold uppercase tracking-wider text-xs transition shadow-md">
              🔥 Schnupper-Anfrage absenden
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- Häufige Fragen (FAQ) -->
    <div class="lg:col-span-5 space-y-6">
      <div class="light-panel rounded-3xl p-8 border border-slate-200 shadow-sm">
        <h3 class="text-lg font-bold uppercase text-navy mb-6 flex items-center gap-2">
          <span class="w-2.5 h-2.5 bg-sand rounded-sm"></span>
          Häufig gestellte Fragen (FAQ)
        </h3>

        <div class="space-y-4 text-xs sm:text-sm">
          <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
            <h4 class="font-bold text-navy uppercase text-xs mb-1">Muss ich Vorerfahrung mitbringen?</h4>
            <p class="text-slate-600 font-light leading-relaxed">
              Nein! Die allermeisten Mitglieder starten als Quereinsteiger. Alles Wichtige erlernst du Schritt für Schritt in der Ausbildung.
            </p>
          </div>

          <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
            <h4 class="font-bold text-navy uppercase text-xs mb-1">Kostet die Ausrüstung Geld?</h4>
            <p class="text-slate-600 font-light leading-relaxed">
              Nein! Die persönliche Schutzausrüstung (Helm, Einsatzanzug, Stiefel) und Lehrgänge werden von der Samtgemeinde kostenlos gestellt.
            </p>
          </div>

          <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
            <h4 class="font-bold text-navy uppercase text-xs mb-1">Was passiert bei Einsätzen während der Arbeit?</h4>
            <p class="text-slate-600 font-light leading-relaxed">
              Feuerwehrangehörige werden für Einsätze gesetzlich freigestellt. Dem Arbeitgeber wird das Gehalt von der Kommune erstattet.
            </p>
          </div>

          <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
            <h4 class="font-bold text-navy uppercase text-xs mb-1">Wann finden die Dienste statt?</h4>
            <p class="text-slate-600 font-light leading-relaxed">
              Die aktive Einsatzabteilung übt alle 14 Tage freitags ab 19:00 Uhr am Feuerwehrhaus Wulften.
            </p>
          </div>
        </div>
      </div>
    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
