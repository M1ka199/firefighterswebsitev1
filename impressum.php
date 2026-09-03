<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Helpers.php';

$seo = getPageSeo('impressum');

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/banner.php';
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

  <!-- Intro-Box mit Wappen/Logo & Notfall-Warnung -->
  <div class="light-panel rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm bg-white mb-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div class="flex items-center gap-5">
        <img src="/assets/img/logo.png" alt="Wappen Feuerwehr Wulften" class="h-16 sm:h-20 w-auto object-contain flex-shrink-0">
        <div>
          <span class="text-xs font-bold text-sand uppercase tracking-widest block">Rechtliche Hinweise & Transparenz</span>
          <h1 class="text-2xl sm:text-4xl font-extrabold text-navy uppercase tracking-tight mt-1">
            Impressum
          </h1>
          <p class="text-slate-600 text-xs sm:text-sm mt-1 font-light">
            Angaben gemäß § 5 DDG (Digitale-Dienste-Gesetz) und § 18 MStV (Medienstaatsvertrag)
          </p>
        </div>
      </div>

      <div class="bg-red-50 border border-red-200 rounded-2xl p-4 text-xs text-slate-700 max-w-sm">
        <strong class="text-red-700 font-bold block uppercase tracking-wider mb-1 flex items-center gap-1.5">
          <span>⚠️</span> Wichtiger Hinweis
        </strong>
        Diese Kontaktdaten dienen ausschließlich redaktionellen und administrativen Zwecken. 
        Im Notfall wählen Sie bitte jederzeit direkt den <strong class="text-red-700 font-bold">Notruf 112</strong>!
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Linke Spalte: Träger, Führung & Kontakt (1 Spalte) -->
    <div class="space-y-6">
      
      <!-- 1. Diensteanbieter & Träger -->
      <div class="light-panel rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm bg-white space-y-4">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-navy/10 text-navy flex items-center justify-center font-bold text-base">
            🏛️
          </div>
          <h2 class="text-base font-bold uppercase text-navy">Träger der Feuerwehr</h2>
        </div>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          <p class="font-bold text-navy text-sm sm:text-base mb-1">Freiwillige Feuerwehr Wulften am Harz</p>
          <p class="mb-3">
            Die Freiwillige Feuerwehr Wulften am Harz ist eine öffentliche Einrichtung der Gemeinde Wulften am Harz (Samtgemeinde Hattorf am Harz).
          </p>
          <p>
            <strong class="text-navy font-semibold block">Samtgemeinde Hattorf am Harz</strong>
            Der Samtgemeindebürgermeister<br>
            Otto-Escher-Straße 12<br>
            37197 Hattorf am Harz<br>
            Deutschland
          </p>
        </div>
      </div>

      <!-- 2. Vertretung / Führung der Ortsfeuerwehr -->
      <div class="light-panel rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm bg-white space-y-4">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-sand/15 text-sand-dark flex items-center justify-center font-bold text-base">
            👨‍🚒
          </div>
          <h2 class="text-base font-bold uppercase text-navy">Ortsfeuerwehr-Führung</h2>
        </div>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light space-y-2">
          <p>
            Die Ortsfeuerwehr Wulften am Harz wird im Dienstbetrieb vertreten durch die Ortswehrführung:
          </p>
          <div class="bg-slate-50 p-3 rounded-xl border border-slate-200 space-y-1">
            <p><strong class="text-navy font-semibold">Ortsbrandmeister:</strong> Michael Müller</p>
            <p><strong class="text-navy font-semibold">Stellv. Ortsbrandmeisterin:</strong> Sarah Lindemann</p>
          </div>
        </div>
      </div>

      <!-- 3. Standort & Kontakt -->
      <div class="light-panel rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm bg-white space-y-4">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-emerald-50 text-emerald-700 flex items-center justify-center font-bold text-base">
            📍
          </div>
          <h2 class="text-base font-bold uppercase text-navy">Standort & Kontakt</h2>
        </div>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light space-y-2.5">
          <p>
            <strong class="text-navy font-semibold block">Feuerwehrhaus Wulften am Harz</strong>
            Steinstraße 1<br>
            37199 Wulften am Harz<br>
            Landkreis Göttingen, Niedersachsen
          </p>
          <div class="pt-2 border-t border-slate-100 space-y-1">
            <p>
              <strong class="text-navy font-semibold">Diensttelefon:</strong> +49 5556 112 <span class="text-[11px] text-slate-400">(während der Dienste)</span>
            </p>
            <p>
              <strong class="text-navy font-semibold">E-Mail:</strong> <a href="mailto:info@feuerwehr-wulften.de" class="text-sand font-bold hover:underline">info@feuerwehr-wulften.de</a>
            </p>
            <p>
              <strong class="text-navy font-semibold">Internet:</strong> <a href="https://feuerwehr-wulften.de" class="text-sand font-bold hover:underline">www.feuerwehr-wulften.de</a>
            </p>
          </div>
        </div>
      </div>

      <!-- 4. Aufsichtsbehörde -->
      <div class="light-panel rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm bg-white space-y-3">
        <h2 class="text-xs font-bold uppercase tracking-wider text-slate-500">Zuständige Aufsichtsbehörde</h2>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          Samtgemeinde Hattorf am Harz – Ordnungs- und Brandschutzamt<br>
          Otto-Escher-Straße 12, 37197 Hattorf am Harz<br>
          Kreisbrandmeister Landkreis Göttingen
        </p>
      </div>

    </div>

    <!-- Rechte Spalte: Rechtliche Abschnitte, Redaktion & Haftung (2 Spalten) -->
    <div class="lg:col-span-2 space-y-6">
      
      <!-- Redaktionelle Verantwortung -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-3">
        <h2 class="text-lg sm:text-xl font-bold uppercase text-navy">
          Verantwortlich für den Inhalt nach § 18 Abs. 2 MStV
        </h2>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          Verantwortlich für redaktionelle, journalistische Inhalte sowie Berichte über Einsätze, Übungsdienste, Veranstaltungen und die Arbeit der Kinder- und Jugendfeuerwehr:
        </p>
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-700">
          <p class="font-bold text-navy">Michael Müller (Ortsbrandmeister)</p>
          <p>Freiwillige Feuerwehr Wulften am Harz</p>
          <p>Steinstraße 1, 37199 Wulften am Harz</p>
          <p class="mt-1 text-slate-500">E-Mail: redaktion@feuerwehr-wulften.de</p>
        </div>
      </div>

      <!-- Grundsätze der Einsatzberichterstattung -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-4">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-alarm/10 text-alarm flex items-center justify-center font-bold text-base">
            🚨
          </div>
          <h2 class="text-lg sm:text-xl font-bold uppercase text-navy">
            Grundsätze der Einsatzberichterstattung
          </h2>
        </div>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light space-y-3">
          <p>
            Die Freiwillige Feuerwehr Wulften berichtet auf dieser Website und in den sozialen Medien im Rahmen ihres satzungsgemäßen und gesetzlichen Informationsauftrags sachlich, neutral und wahrheitsgetreu über Einsätze und Tätigkeiten.
          </p>
          <ul class="list-disc pl-5 space-y-1.5 text-slate-700">
            <li><strong>Wahrung der Persönlichkeitsrechte:</strong> Es werden grundsätzlich keine Namen von Unfallbeteiligten, Geschädigten oder Patienten genannt.</li>
            <li><strong>Anonymisierung von Fotos:</strong> Einsatzfotos werden so ausgewählt oder bearbeitet, dass Kennzeichen von Privatfahrzeugen, Hausnummern und Gesichter nicht erkennbar sind, sofern keine ausdrückliche Einwilligung vorliegt.</li>
            <li><strong>Keine Vorverurteilung:</strong> Aussagen zur Brand- oder Unfallursache sowie zur Schadenshöhe erfolgen nicht durch die Feuerwehr, sondern obliegen ausschließlich den zuständigen Ermittlungsbehörden (Polizei).</li>
            <li><strong>Einsatzgeschehen geht vor:</strong> Fotos und Berichte entstehen niemals zum Nachteil von Rettungsmaßnahmen und erst nach Freigabe durch die Einsatzleitung.</li>
          </ul>
        </div>
      </div>

      <!-- Haftung für eigene Inhalte -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-3">
        <h2 class="text-lg sm:text-xl font-bold uppercase text-navy">
          Haftung für Inhalte
        </h2>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          Als Diensteanbieter sind wir gemäß § 7 Abs. 1 DDG für eigene Inhalte auf diesen Seiten nach den allgemeinen Gesetzen verantwortlich. Nach §§ 8 bis 10 DDG sind wir als Diensteanbieter jedoch nicht verpflichtet, übermittelte oder gespeicherte fremde Informationen zu überwachen oder nach Umständen zu forschen, die auf eine rechtswidrige Tätigkeit hinweisen.
        </p>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          Verpflichtungen zur Entfernung oder Sperrung der Nutzung von Informationen nach den allgemeinen Gesetzen bleiben hiervon unberührt. Eine diesbezügliche Haftung ist jedoch erst ab dem Zeitpunkt der Kenntnis einer konkreten Rechtsverletzung möglich. Bei Bekanntwerden von entsprechenden Rechtsverletzungen werden wir diese Inhalte umgehend entfernen.
        </p>
      </div>

      <!-- Haftung für Links -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-3">
        <h2 class="text-lg sm:text-xl font-bold uppercase text-navy">
          Haftung für externe Links
        </h2>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          Unser Angebot enthält Links zu externen Websites Dritter (wie z.B. Instagram, Kreisfeuerwehrverband, Samtgemeinde), auf deren Inhalte wir keinen Einfluss haben. Deshalb können wir für diese fremden Inhalte auch keine Gewähr übernehmen. Für die Inhalte der verlinkten Seiten ist stets der jeweilige Anbieter oder Betreiber der Seiten verantwortlich.
        </p>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          Die verlinkten Seiten wurden zum Zeitpunkt der Verlinkung auf mögliche Rechtsverstöße überprüft. Rechtswidrige Inhalte waren zum Zeitpunkt der Verlinkung nicht erkennbar. Eine permanente inhaltliche Kontrolle der verlinkten Seiten ist jedoch ohne konkrete Anhaltspunkte einer Rechtsverletzung nicht zumutbar. Bei Bekanntwerden von Rechtsverletzungen werden wir derartige Links unverzüglich entfernen.
        </p>
      </div>

      <!-- Urheberrecht & Bildnachweise -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-3">
        <h2 class="text-lg sm:text-xl font-bold uppercase text-navy">
          Urheberrecht & Bildrechte
        </h2>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          Die durch die Seitenbetreiber erstellten Inhalte und Werke auf diesen Seiten unterliegen dem deutschen Urheberrecht. Die Vervielfältigung, Bearbeitung, Verbreitung und jede Art der Verwertung außerhalb der Grenzen des Urheberrechtes bedürfen der schriftlichen Zustimmung der Freiwilligen Feuerwehr Wulften am Harz bzw. des jeweiligen Autors oder Erstellers.
        </p>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          Downloads und Kopien dieser Seite sind nur für den privaten, nicht kommerziellen Gebrauch gestattet. Soweit die Inhalte auf dieser Seite nicht vom Betreiber erstellt wurden, werden die Urheberrechte Dritter beachtet. Insbesondere werden Inhalte Dritter als solche gekennzeichnet.
        </p>
        <div class="pt-2 text-xs text-slate-500">
          <strong>Bildnachweise:</strong> Bildmaterial stammt aus dem Medienarchiv der Freiwilligen Feuerwehr Wulften am Harz (Fachgruppe Öffentlichkeitsarbeit) sowie von Kameradinnen und Kameraden der Wehr.
        </div>
      </div>

      <!-- Streitbeilegung -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-3">
        <h2 class="text-lg sm:text-xl font-bold uppercase text-navy">
          Verbraucherstreitbeilegung / Universalschlichtungsstelle
        </h2>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          Die Europäische Kommission stellt eine Plattform zur Online-Streitbeilegung (OS) bereit: 
          <a href="https://ec.europa.eu/consumers/odr" target="_blank" rel="noopener noreferrer" class="text-sand font-bold underline">https://ec.europa.eu/consumers/odr</a>.<br>
          Wir sind nicht bereit oder verpflichtet, an Streitbeilegungsverfahren vor einer Verbraucherschlichtungsstelle teilzunehmen.
        </p>
      </div>

    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
