<?php
declare(strict_types=1);

require_once __DIR__ . '/src/Database.php';
require_once __DIR__ . '/src/Helpers.php';

$seo = getPageSeo('datenschutz');

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/banner.php';
?>

<div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 py-14">

  <!-- Intro-Box mit Status & Übersicht -->
  <div class="light-panel rounded-3xl p-6 sm:p-10 border border-slate-200 shadow-sm bg-white mb-10">
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
      <div>
        <span class="text-xs font-bold text-sand uppercase tracking-widest block">EU-DSGVO • Transparenz & Schutz</span>
        <h1 class="text-2xl sm:text-4xl font-extrabold text-navy uppercase tracking-tight mt-1">
          Datenschutzerklärung
        </h1>
        <p class="text-slate-600 text-xs sm:text-sm mt-1 font-light">
          Informationen über die Art, den Umfang und den Zweck der Verarbeitung personenbezogener Daten
        </p>
      </div>

      <div class="bg-slate-50 border border-slate-200 rounded-2xl p-4 text-xs text-slate-600 max-w-sm">
        <strong class="text-navy font-bold block uppercase tracking-wider mb-1">Stand der Datenschutzerklärung</strong>
        Gültig ab März 2026. Entspricht den Anforderungen der EU-Datenschutz-Grundverordnung (DSGVO) und des NDSG.
      </div>
    </div>
  </div>

  <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
    
    <!-- Linke Spalte: Schnelle Navigation & Verantwortlicher (1 Spalte) -->
    <div class="space-y-6">
      
      <!-- Verantwortliche Stelle -->
      <div class="light-panel rounded-3xl p-6 sm:p-7 border border-slate-200 shadow-sm bg-white space-y-4 sticky top-28">
        <div class="flex items-center gap-3">
          <div class="w-9 h-9 rounded-xl bg-navy/10 text-navy flex items-center justify-center font-bold text-base">
            🛡️
          </div>
          <h2 class="text-base font-bold uppercase text-navy">Verantwortliche Stelle</h2>
        </div>
        
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light space-y-2">
          <p class="font-bold text-navy text-sm sm:text-base">
            Freiwillige Feuerwehr Wulften am Harz
          </p>
          <p>
            Eine Einrichtung der Gemeinde Wulften am Harz / Samtgemeinde Hattorf am Harz
          </p>
          <p class="pt-1">
            Steinstraße 1<br>
            37199 Wulften am Harz<br>
            Deutschland
          </p>
          <div class="pt-3 border-t border-slate-100 space-y-1">
            <p>
              <strong class="text-navy font-semibold">E-Mail für Datenschutzfragen:</strong><br>
              <a href="mailto:datenschutz@feuerwehr-wulften.de" class="text-sand font-bold hover:underline">
                datenschutz@feuerwehr-wulften.de
              </a>
            </p>
            <p>
              <strong class="text-navy font-semibold">Wehrführung:</strong><br>
              Michael Müller (Ortsbrandmeister)
            </p>
          </div>
        </div>

        <!-- Schnell-Button für Cookie-Einstellungen -->
        <div class="pt-4 border-t border-slate-100">
          <button onclick="if(window.openCookieSettingsModal){window.openCookieSettingsModal();}" class="w-full py-2.5 px-4 rounded-xl bg-slate-100 hover:bg-navy hover:text-white text-navy font-bold text-xs uppercase tracking-wider transition border border-slate-200 flex items-center justify-center gap-2">
            <span>🍪</span> Cookie-Einstellungen anpassen
          </button>
        </div>

        <!-- Zuständige Aufsichtsbehörde -->
        <div class="pt-2 text-[11px] text-slate-500 leading-relaxed">
          <strong class="text-navy block font-semibold mb-1">Zuständige Aufsichtsbehörde:</strong>
          Die Landesbeauftragte für den Datenschutz Niedersachsen (LfD Niedersachsen)<br>
          Prinzenstraße 5, 30159 Hannover<br>
          <a href="https://lfd.niedersachsen.de" target="_blank" rel="noopener noreferrer" class="text-sand hover:underline">www.lfd.niedersachsen.de</a>
        </div>
      </div>

    </div>

    <!-- Rechte Spalte: Haupt-Inhalt DSGVO (2 Spalten) -->
    <div class="lg:col-span-2 space-y-6">
      
      <!-- 1. Datenschutz auf einen Blick -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-4">
        <h2 class="text-xl font-bold uppercase text-navy flex items-center gap-2.5">
          <span class="text-sand font-mono text-base">01.</span> Datenschutz auf einen Blick
        </h2>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light space-y-3">
          <p>
            Die Betreiber dieser Seiten nehmen den Schutz Ihrer persönlichen Daten sehr ernst. Wir behandeln Ihre personenbezogenen Daten vertraulich und entsprechend den gesetzlichen Datenschutzvorschriften (EU-DSGVO, Bundesdatenschutzgesetz BDSG, Niedersächsisches Datenschutzgesetz NDSG) sowie dieser Datenschutzerklärung.
          </p>
          <p>
            Wenn Sie diese Website benutzen, werden verschiedene personenbezogene Daten erhoben. Personenbezogene Daten sind Daten, mit denen Sie persönlich identifiziert werden können. Die vorliegende Datenschutzerklärung erläutert, welche Daten wir erheben und wofür wir sie nutzen. Sie erläutert auch, wie und zu welchem Zweck das geschieht.
          </p>
        </div>
      </div>

      <!-- 2. Rechtsgrundlagen der Verarbeitung -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-4">
        <h2 class="text-xl font-bold uppercase text-navy flex items-center gap-2.5">
          <span class="text-sand font-mono text-base">02.</span> Rechtsgrundlagen der Datenverarbeitung
        </h2>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light space-y-3">
          <p>Wir verarbeiten personenbezogene Daten stets auf Grundlage gültiger Rechtsvorschriften nach Art. 6 DSGVO:</p>
          <ul class="list-disc pl-5 space-y-1.5 text-slate-700">
            <li><strong>Einwilligung (Art. 6 Abs. 1 lit. a DSGVO):</strong> Wenn Sie uns für die Kontaktaufnahme oder für den Schnupperdienst Ihre Daten übermitteln.</li>
            <li><strong>Vertragserfüllung & vorvertragliche Maßnahmen (Art. 6 Abs. 1 lit. b DSGVO):</strong> Zur Bearbeitung von Beitritts- und Schnupperdienst-Anfragen für die Einsatzabteilung, Jugendfeuerwehr oder Kinderfeuerwehr.</li>
            <li><strong>Wahrnehmung von Aufgaben im öffentlichen Interesse (Art. 6 Abs. 1 lit. e DSGVO i.V.m. NBrandSchG):</strong> Gesetzlicher Auftrag zur Brandschutzerziehung, Warnung der Bevölkerung und sachlichen Information über Einsätze.</li>
            <li><strong>Berechtigte Interessen (Art. 6 Abs. 1 lit. f DSGVO):</strong> Zur technischen Gewährleistung, Abwehr von Angriffen und Stabilität des Webangebots.</li>
          </ul>
        </div>
      </div>

      <!-- 3. Server-Log-Dateien & Hosting -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-4">
        <h2 class="text-xl font-bold uppercase text-navy flex items-center gap-2.5">
          <span class="text-sand font-mono text-base">03.</span> Server-Log-Dateien & Hosting
        </h2>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light space-y-3">
          <p>
            Der Provider unseres Webservers erhebt und speichert automatisch Informationen in so genannten Server-Log-Dateien, die Ihr Browser automatisch an uns übermittelt. Dies sind:
          </p>
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-2 bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs text-slate-700">
            <div>• Browsertyp und Browserversion</div>
            <div>• Verwendetes Betriebssystem</div>
            <div>• Referrer URL (die zuvor besuchte Seite)</div>
            <div>• Hostname des zugreifenden Rechners</div>
            <div>• Uhrzeit der Serveranfrage</div>
            <div>• IP-Adresse (anonymisiert gekürzt)</div>
          </div>
          <p>
            Eine Zusammenführung dieser Daten mit anderen Datenquellen wird nicht vorgenommen. Die Erfassung dieser Daten erfolgt auf Grundlage von Art. 6 Abs. 1 lit. f DSGVO. Der Websitebetreiber hat ein berechtigtes Interesse an der technisch fehlerfreien Darstellung und der Optimierung seiner Website.
          </p>
        </div>
      </div>

      <!-- 4. SSL- bzw. TLS-Verschlüsselung -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-3">
        <h2 class="text-xl font-bold uppercase text-navy flex items-center gap-2.5">
          <span class="text-sand font-mono text-base">04.</span> SSL- bzw. TLS-Verschlüsselung
        </h2>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          Diese Seite nutzt aus Sicherheitsgründen und zum Schutz der Übertragung vertraulicher Inhalte, wie zum Beispiel Anfragen über das Kontaktformular oder Schnupperdienst-Bewerbungen, eine SSL- bzw. TLS-Verschlüsselung. Eine verschlüsselte Verbindung erkennen Sie daran, dass die Adresszeile des Browsers von „http://“ auf „https://“ wechselt und an dem Schloss-Symbol in Ihrer Browserzeile. Wenn die Verschlüsselung aktiviert ist, können die Daten, die Sie an uns übermitteln, nicht von Dritten mitgelesen werden.
        </p>
      </div>

      <!-- 5. Cookies & Cookie-Consent-Management -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-4">
        <div class="flex items-center gap-3">
          <div class="w-8 h-8 rounded-lg bg-sand/15 text-sand-dark flex items-center justify-center font-bold text-sm">
            🍪
          </div>
          <h2 class="text-xl font-bold uppercase text-navy">
            05. Cookies & Cookie-Einstellungen
          </h2>
        </div>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light space-y-3">
          <p>
            Unsere Website verwendet Cookies und moderne Speichertechnologien (wie LocalStorage). Cookies sind kleine Textdateien, die auf Ihrem Rechner abgelegt werden und die Ihr Browser speichert. Sie richten auf Ihrem Rechner keinen Schaden an und enthalten keine Viren.
          </p>
          <div class="space-y-2">
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
              <strong class="text-navy font-semibold block mb-1">Essenzielle Cookies & LocalStorage (technisch notwendig):</strong>
              Dienen der Bereitstellung grundlegender Funktionen wie der Speicherung Ihrer Cookie-Einwilligung (<code class="text-sand font-mono">ffw_cookie_consent</code>) sowie Sitzungs-Tokens zur Vermeidung von CSRF-Angriffen. Rechtsgrundlage: Art. 6 Abs. 1 lit. f DSGVO.
            </div>
            <div class="bg-slate-50 p-3.5 rounded-xl border border-slate-200">
              <strong class="text-navy font-semibold block mb-1">Externe Medien (optional):</strong>
              Falls eingebunden (z.B. externe interaktive Karten oder Social-Media-Widgets), werden diese erst geladen, wenn Sie Ihre ausdrückliche Zustimmung erteilt haben. Rechtsgrundlage: Art. 6 Abs. 1 lit. a DSGVO.
            </div>
          </div>
          <p>
            Sie können Ihre Cookie-Einstellungen jederzeit über den Button im Fußbereich der Website oder über den Button in der linken Spalte dieser Seite ändern oder widerrufen.
          </p>
        </div>
      </div>

      <!-- 6. Formulare: Kontakt & Schnupperdienst -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-4">
        <h2 class="text-xl font-bold uppercase text-navy flex items-center gap-2.5">
          <span class="text-sand font-mono text-base">06.</span> Kontaktformular & Schnupperdienst-Anmeldung
        </h2>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light space-y-3">
          <p>
            Wenn Sie uns per Kontaktformular oder über die Schnupperdienst-Anmeldung Anfragen zukommen lassen, werden Ihre Angaben aus dem Formular inklusive der von Ihnen dort angegebenen Kontaktdaten zwecks Bearbeitung der Anfrage und für den Fall von Anschlussfragen bei uns gespeichert.
          </p>
          <ul class="list-disc pl-5 space-y-1.5 text-slate-700">
            <li><strong>Erfasste Daten:</strong> Name, E-Mail-Adresse, Telefonnummer (optional), Alter (bei Schnupperdienst), gewünschte Abteilung (Einsatzabteilung, Jugendfeuerwehr, Kinderfeuerwehr) sowie Ihre persönliche Nachricht.</li>
            <li><strong>Zweck:</strong> Kontaktaufnahme zur Vorbereitung von Schnupperdiensten oder Beantwortung von Bürgeranfragen.</li>
            <li><strong>Speicherdauer:</strong> Die von Ihnen im Formular eingegebenen Daten verbleiben bei uns, bis Sie uns zur Löschung auffordern, Ihre Einwilligung zur Speicherung widerrufen oder der Zweck für die Datenspeicherung entfällt. Zwingende gesetzliche Bestimmungen – insbesondere Aufbewahrungsfristen – bleiben unberührt.</li>
            <li><strong>Keine Weitergabe:</strong> Diese Daten geben wir selbstverständlich nicht ohne Ihre ausdrückliche Einwilligung an Dritte weiter.</li>
          </ul>
        </div>
      </div>

      <!-- 7. Social Media (Instagram) -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-3">
        <h2 class="text-xl font-bold uppercase text-navy flex items-center gap-2.5">
          <span class="text-sand font-mono text-base">07.</span> Social-Media-Präsenz (Instagram)
        </h2>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          Wir unterhalten eine öffentlich zugängliche Online-Präsenz auf der Plattform Instagram (Meta Platforms Ireland Ltd.). Auf unserer Website ist lediglich ein herkömmlicher Hyperlink (Icon) zu unserem Profil hinterlegt. Beim reinen Besuch unserer Website werden keine Daten an Instagram oder Meta übertragen. Erst wenn Sie den Instagram-Link anklicken, verlassen Sie unser Angebot und werden auf die Server von Instagram weitergeleitet. Bitte beachten Sie dazu die Datenschutzrichtlinie von Instagram.
        </p>
      </div>

      <!-- 8. Rechte der betroffenen Personen -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-4">
        <h2 class="text-xl font-bold uppercase text-navy flex items-center gap-2.5">
          <span class="text-sand font-mono text-base">08.</span> Ihre Betroffenenrechte nach DSGVO
        </h2>
        <div class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light space-y-3">
          <p>Sie haben als betroffene Person jederzeit umfangreiche gesetzliche Rechte:</p>
          <div class="space-y-2 text-slate-700">
            <div>• <strong class="text-navy">Recht auf Auskunft (Art. 15 DSGVO):</strong> Sie haben das Recht, jederzeit unentgeltlich Auskunft über Herkunft, Empfänger und Zweck Ihrer gespeicherten personenbezogenen Daten zu erhalten.</div>
            <div>• <strong class="text-navy">Recht auf Berichtigung (Art. 16 DSGVO):</strong> Sie können unverzüglich die Berichtigung unrichtiger oder Vervollständigung Ihrer Daten verlangen.</div>
            <div>• <strong class="text-navy">Recht auf Löschung (Art. 17 DSGVO):</strong> Sie haben das Recht auf Löschung („Recht auf Vergessenwerden“), sofern keine gesetzlichen Aufbewahrungspflichten entgegenstehen.</div>
            <div>• <strong class="text-navy">Recht auf Einschränkung der Verarbeitung (Art. 18 DSGVO):</strong> Sie können unter bestimmten Voraussetzungen die Einschränkung der Verarbeitung Ihrer Daten fordern.</div>
            <div>• <strong class="text-navy">Recht auf Datenübertragbarkeit (Art. 20 DSGVO):</strong> Sie haben das Recht, Daten in einem strukturierten, gängigen und maschinenlesbaren Format aushändigen zu lassen.</div>
            <div>• <strong class="text-navy">Widerspruchsrecht (Art. 21 DSGVO):</strong> Wenn die Datenverarbeitung auf Grundlage von Art. 6 Abs. 1 lit. e oder f DSGVO erfolgt, haben Sie jederzeit das Recht auf Widerspruch.</div>
            <div>• <strong class="text-navy">Widerruf Ihrer Einwilligung:</strong> Bereits erteilte Einwilligungen können Sie jederzeit mit Wirkung für die Zukunft formlos per E-Mail an <a href="mailto:datenschutz@feuerwehr-wulften.de" class="text-sand font-bold underline">datenschutz@feuerwehr-wulften.de</a> widerrufen.</div>
          </div>
        </div>
      </div>

      <!-- 9. Beschwerderecht bei der Aufsichtsbehörde -->
      <div class="light-panel rounded-3xl p-6 sm:p-8 border border-slate-200 shadow-sm bg-white space-y-3">
        <h2 class="text-xl font-bold uppercase text-navy flex items-center gap-2.5">
          <span class="text-sand font-mono text-base">09.</span> Beschwerderecht bei der Aufsichtsbehörde
        </h2>
        <p class="text-xs sm:text-sm text-slate-600 leading-relaxed font-light">
          Im Falle von datenschutzrechtlichen Verstößen steht dem Betroffenen ein Beschwerderecht bei der zuständigen Aufsichtsbehörde zu. Zuständige Aufsichtsbehörde in Niedersachsen ist:
        </p>
        <div class="bg-slate-50 p-4 rounded-xl border border-slate-200 text-xs sm:text-sm text-slate-700">
          <strong class="text-navy font-bold">Die Landesbeauftragte für den Datenschutz Niedersachsen</strong><br>
          Prinzenstraße 5, 30159 Hannover<br>
          Telefon: +49 511 120-4500<br>
          E-Mail: <a href="mailto:poststelle@lfd.niedersachsen.de" class="text-sand font-semibold hover:underline">poststelle@lfd.niedersachsen.de</a><br>
          Website: <a href="https://lfd.niedersachsen.de" target="_blank" rel="noopener noreferrer" class="text-sand font-semibold hover:underline">https://lfd.niedersachsen.de</a>
        </div>
      </div>

    </div>

  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
