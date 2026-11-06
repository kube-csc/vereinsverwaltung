<h1>Internetauftritt für Vereine</h1>
<p>Version: V00.11.0</p>
<p>
Ausgelegt z.B. für einen Verein mit verschiedenen Abteilungen / Sportarten
</p>
Beispiel für einen Kanuverein mit Abteilungen / Sportarten:
    <ul>
      <li>Jugend</li>
      <li>Wandersport</li>
      <li>Rennsport</li>
      <li>Drachenboot mit drei Mannschaften</li>
      <li>SUP</li>
    </ul>

<a href="https://www.kel-datteln.de">Beispiel eines Frontend</a>

<h2>Installierte Programme / Templets</h2>
<ul>
  <li>Laravel 12 mit Jetstream 5, Livewire 3 und TailwindCSS</li>
  <li><a href="https://jetstream.laravel.com/introduction.html">Jetstream-Dokumentation</a></li>
  <li><a href="https://livewire.laravel.com/docs">Livewire-Dokumentation</a></li>
  <li><a href="https://boxicons.com/">boxicons</a>(Forntend)</li>
  <li><a href="https://tailwindcss.com/">Tailwindcss</a>(Backend)</li>
  <li><a href="https://bootstrapmade.com/squadfree-free-bootstrap-template-creative/">BootstrapMade.com </a></li>
  <li>.htaccess für ionos.de Server</li>
  <li><a href="https://botman.io">Botman</a></li>
  <li><a href="https://www.tiny.cloud/">TinyMCE</a>(Backend)</li>
</ul>

<h2>Benötigte Lizenzen</h2>
Folgende Lizenz wird für die Nutzung des Frontends benötigt:
<a href="https://bootstrapmade.com/squadfree-free-bootstrap-template-creative/">Squadfree von bootstrapmade</a>
<a href="https://www.tiny.cloud/">TinyMCE Editor </a>


<h2>Frontend</h2>
<ul>
    <li>Header ist abhängig von den Abteilungen / Sportarten *</li>
    <li>Leanding Page
         <ul>
          <li>Ausgabe der Vereinsbeschreibung</li>
          <li>Präsentation der Abteilung * mit deren Mannschaften *</li>
          <li>Präsentation der kommenden Events</li>
          <li>Präsentation der vergangenen Events</li>
          <li>Präsentation des Teams</li>
          <li>Kontakt des Vereins inc. Map **</li>
        </ul>
    </li>
    <li>Präsentation der Abteilungen / Sportarten</li>
    <li>Präsentation der Mannschaften</li>
    <li>Präsentation des Events
        <ul>
            <li>Beschreibung der Events</li>
            <li>Social Medien einbinden</li>  
            <li>Bildergallery</li>
            <li>Dokumente zum Herunterladen
              <ul>
                  <li>Ausschreibung</li>
                  <li>Programm</li>
                  <li>Ergebnisse</li>
                  <li>Flyer / Plakat</li>
              </ul>
            </li>
        </ul>
    </li>
    <li>Informationsseiten
    <ul>
        <li>Anfahrt **</li>
        <li>Selbst angelegte Informationsseiten</li>
        <li><strong>Optionaler Header (Hero)</strong>: wenn ein <code>headerBild</code> gesetzt ist, wird im Frontend automatisch ein Hero-Bereich mit Hintergrundbild angezeigt (Layout <code>layouts.headFrontend</code>). Optional sind <code>headerTitel</code> und <code>headerSlogen</code> möglich.</li>
        <li>Abteilungen *
          <ul>
            <li>Sportarten *</li>
          </ul> 
        </li>
    </ul>
  </li>
    <li>Footer
    <ul>
        <li>Dokumente aus dem Dokumentenmanagement werden angezeigt</li>
        <li>Impresssum</li>
        <li>Datenschutzerklärung</li>
    </ul>
  </li>
    <li>401 Errorseite oder Weiterleitung 301 zu dem im Backlink verwalteten Backend</li>
</ul>

* Begriff wird in der .env eintragen  
** Anfahrt kann in der .env aktiviert bzw. deaktiviert werden 

<h2>Backend</h2>
<h3>Vereinsverwaltung</h3>
<strong>Demodaten</strong>
<p>
  Email: info@info.de<br>
  Password: password
</p>
<ul>
  <li>Eingabe und Bearbeitung der Vereinsbeschreibung</li>
  <li>Eingabe und Bearbeitung der Abteilungsbeschreibung</li>
  <li>Eingabe und Bearbeitung der Mannschaftsbeschreibung</li>
  <li><strong>Trainerverwaltung / Kursangebot</strong>
      <ul>
          <li><strong>Trainerfunktionen definieren</strong> (Menü: „Trainertypen verwalten“)
              <ul>
                  <li>Hier legst du fest, welche Rollen/Funktionen es gibt (z. B. „SUP Trainer“, „Ferienspass Kursleiter“, „Drachenboot Trainer“).</li>
                  <li>Eine Trainerfunktion kann ein- und ausgeschaltet werden (deaktivierte Funktionen werden im Normalfall nicht mehr zur Auswahl angeboten).</li>
                  <li><strong>Voreinstellungen</strong> pro Trainerfunktion:
                      <ul>
                          <li>Standardmäßig aktiv/inaktiv, wenn ein Trainer diese Funktion erhält</li>
                          <li>Standardmäßig sichtbar/unsichtbar (öffentlich)</li>
                          <li>Standard-Veranstaltung/Kursangebot, zu dem die Funktion gehört</li>
                          <li>Standard-Abteilung/Mannschaft innerhalb der Veranstaltung</li>
                          <li><em>Im Kursangebot verwendbar</em>: Steuert, ob diese Funktion im Kurs-/Training-Bereich angeboten werden soll</li>
                      </ul>
                  </li>
              </ul>
          </li>
          <li><strong>Trainer einem Benutzer zuordnen</strong> (Menü: „Trainer“)
              <ul>
                  <li>Wähle einen Benutzer aus und ordne ihm eine oder mehrere Trainerfunktionen zu.</li>
                  <li>Beim Zuweisen werden die Voreinstellungen der Trainerfunktion automatisch übernommen (z. B. Standard-Veranstaltung und Standard-Abteilung).</li>
                  <li>Zuordnungen können deaktiviert und später reaktiviert werden.</li>
                  <li>Die Sichtbarkeit kann pro Zuordnung umgeschaltet werden.</li>
              </ul>
          </li>
          <li><strong>Übersicht</strong> (Menü: „Trainertypen – Übersicht“): Gruppierte Anzeige aller Zuordnungen nach Trainerfunktion</li>
      </ul>
  </li>
  <li>Eingabe und Bearbeitung von Trainingszeiten von Abteilung / Mannschaften</li>
  <li>Eingabe und Bearbeitung von Events
      <ul>
        <li>Eventbeschreibung</li>
        <li>Bilder-Gallery</li>
        <li>Dokumente zum Herunterladen</li>
          <ul>
              <li>Ausschreibungsunterlagen</li>
              <li>Programmunterlagen</li>
              <li>Ergebnislisten</li>
              <li>Flyer / Plakat</li>
          </ul>
        <li>Regatta anlegen</li>  
       <li>Mitglieder können Bilder zu Veranstaltungen hochladen, wenn sie ein Passwort haben</li>
      </ul>
  </li>
  <li>Eingabe und Bearbeitung von Event Gruppen</li>
  <li>Dokumentenmanagement</li>
  <li>Anfahrt</li>
  <li>Informationsseiten
    <ul>
        <li>Beiträge</li>
        <li>Übernachtungskosten</li>
        <li>Impresssum</li>
        <li>Datenschutzerklärung</li>
        <li><strong>Optionales Headerbild</strong> (inkl. Header-Titel/Slogen) kann pro Informationsseite im Edit-Formular gepflegt werden.</li>
    </ul>
  </li>
<li>Backlinksverwaltung Umleitung von Links die nicht mehr Existieren</li>
</ul>

<h3>Fördermittel- und Antragsverwaltung</h3>
<p>
Der Bereich „Anträge" (unter <code>/antraege/...</code>) bietet eine vollständige Fördermittel- und Antragsverwaltung. Er ist als eigener Funktionsbereich in die bestehende Anwendung integriert und nutzt dieselbe Datenbank.
</p>

<strong>Demodaten</strong>
<p>
  Die Standard-Demodaten (Email: info@info.de, Password: password) werden beim <code>php artisan migrate --seed</code> oder <code>php artisan db:seed</code> angelegt. Der Benutzer erhält automatisch die Administrator-Rolle.<br>
  Zusätzlich können Beispielantragsarten und Fördertöpfe mit <code>php artisan db:seed --class=ApplicationExamplesSeeder</code> befüllt werden.
</p>

<ul>
  <li><strong>Neues Rechtesystem:</strong> Rollenbasierte Berechtigungen (Administrator, Fördertopfverwalter, Gremium, Antragsteller). Berechtigungen werden serverseitig geprüft. Bestehende Benutzer mit <code>admin = 4</code> erhalten automatisch die Administrator-Rolle.</li>
  <li><strong>Benutzer- und Einladungsverwaltung:</strong> Unter <code>/antraege/benutzer</code> können Benutzer eingeladen, Rollen zugewiesen und Berechtigungen konfiguriert werden.</li>
  <li><strong>Bankkontenverwaltung:</strong> Antragsteller können unter <code>/antraege/bankkonten</code> mehrere Bankkonten verwalten; IBAN/BIC werden verschlüsselt gespeichert.</li>
  <li><strong>Förderprogramme und Fördertöpfe:</strong> Unter <code>/antraege/foerderprogramme</code> können Förderprogramme, befristete oder fortlaufende Fördertöpfe mit Budgetverwaltung verwaltet werden. Endabrechnung mit anteiliger Berechnung bei überschrittenem Budget.</li>
  <li><strong>Antragsarten und Formularbaukasten:</strong> Unter <code>/antraege/antragsarten</code> können Antragsarten mit flexibel konfigurierbaren Formularen (Text, Zahl, Datum, E-Mail, Auswahlliste, Bestätigung, Dateianlage) erstellt werden. Prüfer-Felder ermöglichen strukturierte Prüfer-Antworten, die nur bei der Prüfung sichtbar sind.</li>
  <li><strong>Antragstellung:</strong> Antragsteller können Anträge online ausfüllen, als Entwurf speichern und einreichen. Der Statusverlauf (Entwurf → Eingereicht → In Prüfung → Rückfrage → Bewilligt/Abgelehnt) ist nachvollziehbar dokumentiert.</li>
  <li><strong>Antragsprüfung:</strong> Unter <code>/antraege/pruefung</code> können berechtigte Benutzer Anträge prüfen, Rückfragen stellen und Entscheidungen (Bewilligung/Ablehnung) mit Begründung treffen.</li>
  <li><strong>Bescheide und Benachrichtigungen:</strong> Vorlagenbasierte PDF-Bescheide mit E-Mail-Benachrichtigungen. Bescheide ohne Fördertopf enthalten keine Betragsangaben. Vorlagen können unter <code>/antraege/bescheidvorlagen</code> angepasst werden.</li>
  <li><strong>SEPA-Überweisungsdatei:</strong> Nach der Endabrechnung kann eine SEPA-Überweisungsdatei (pain.001.001.03) für die Auszahlung der Förderbeträge erzeugt werden.</li>
  <li><strong>Auswertungen:</strong> Unter <code>/antraege/auswertungen</code> stehen filterbare Übersichten mit CSV-Export zur Verfügung.</li>
  <li><strong>Sonstige Anträge:</strong> Neben Förderanträgen unterstützt die Anwendung auch Anträge ohne Fördertopf (z. B. Bootsregistrierung, Liegeplatz, Hallennutzung, Vereinsbeitritt).</li>
</ul>

<h3>Regatta-Verwaltung</h3>
<p>
In der .env kann mit der Variabel APP_REGATTA die Möglichkeit einer Regatta-Verwaltung ein und ausgeschaltet werden.
</p>
<ul>
    <li>Regatta auswählen</li>
    <li>Einverständniserklärung zur Regatta</li>
    <li>Rennklassen können als Vorlage angelegt und bearbeitet werden</li>
    <li>Rennklassen können der Regatta zugeordnet werden</li>
    <li>Regatta Informationen können angelegt und bearbeitet werden</li>
     <li>FAQ für die Regattamanagement APP</li>
     <li>Mannschaften können angelegt und bearbeitet werden</li>
      <li><strong>Team-Import ohne Meldetool:</strong> Mannschaften können direkt per Texteingabe (Textarea) importiert werden – eine Zeile pro Teamname. Pflichtfelder werden automatisch mit definierten Platzhalter-Werten befüllt (Verein, Teamcaptain, Straße, Ort: „nicht angegeben"; PLZ: 99999; Telefon: 999999999; E-Mail: import@invalid.local). Wenn ein Team bereits bei einem früheren Event gemeldet war, werden die Vorwerte übernommen und der Teamlink automatisch synchronisiert (bestehender übernommen oder neue freie ID vergeben).</li>
      <li><strong>Automatisierte Rennplanung (Verlosung [Beta-Version]:</strong> Intelligente Generierung eines Rennplans basierend auf Fairness-Kriterien:
        <ul>
          <li>Gegner-Durchmischung: Maximale Variation der Paarungen über mehrere Vorläufe hinweg</li>
          <li>Intelligente Bahnverteilung (Seeding): Stärkste Finals-Teams in der Mitte (Center-Out), schwächere an Außenbahnen</li>
          <li>Zeitabstands-Optimierung: Einhaltung von Mindestpausen je Team und Organisations-Pause (Verein/Standort)</li>
          <li>Interaktive Anpassung: Manuelle Verschiebung von Rennen und Neuberechnung von Startzeiten möglich</li>
          <li>Final-Planung: Blockweise Planung mehrerer Finalrunden (A/B-Finals) mit automatischer Zeitplanung</li>
          <li>Pausen & Siegerehrung: Pausenblöcke zwischen Vorläufen/Finals, Siegerehrung am Abschluss</li>
          <li>Gegner-Übersicht & Zeitplan: Detaillierte Reportings und chronologische Gesamt-Zeitplan</li>
        </ul>
      </li>
     <li>Rennen anlegen und bearbeiten</li>
    <li>Rennen können Tabellen oder mehrere Tabellen zugeordnet werden</li>
    <li>Programm und Ergebnislisten für jedes Rennen oder Gruppenweise hochladen</li>
    <li>Mannschaften können den Rennen zugeteilt werden</li>
    <li>Tabellen anlegen und bearbeiten</li>
    <li>Platzierungen können eingegeben und dann mit einer kontrolle freigeben werden</li> 
    <li>Tabellen werden ausgewertet inc. für die Feinwertung gibt es eine Bucholz-Wertung</li>
    <li>Ergebnislisten können für jede Tabelle hochladen</li>
    <li>Platzierungen können eingegeben werden</li> 
    <li>Tabellen werden ausgewertet inc. für die Feinwertung gibt es eine Bucholz-Wertung</li>
    <li>Ergebnislisten können für jede Tabelle hochladen</li>
    <li>Startzeitverschiebungen werden berechnet</li>
    <li>Rennergebnisse und Tabellen können mit einer Veröffentlichungszeiten versehen werden</li>
</ul>

<h2>Installation</h2>
<ul>
   <li>git clone https://github.com/kube-csc/vereinsverwaltung.git</li>
   <li>cd vereinsverwaltung</li>
   <li>.env Datei ausfüllen (Es werden auch Informationen über den Verein abgefragt)</li>
   <li>php artisan key:generate</li>
   <li>curl -sS https://getcomposer.org/installer</li>
   <li>php composer.phar</li>
   <li>php composer.phar install</li>
   <li>die Unterordner unter "/storage/app/public/" sollten angelegt sein, wenn nicht von hand anlegen
      <ul>
        <li>boardPortrait</li>
        <li>botman</li>
        <li>dokumente</li>
        <li>eventDokumente</li>
        <li>eventImage</li>
        <li>header</li>
          <li>instructionHeader <small>(Headerbilder für Informationsseiten)</small></li>
        <li>raceDokumente</li>
        <li>tabeleDokumente</li>
        <li>sportgeraete</li>
      </ul>
    </li>
   <li>php artisan storage:link</li>
    <li><strong>Information:</strong> Headerbilder für Informationsseiten werden im Disk <code>public</code> gespeichert, standardmäßig unter <code>storage/app/public/instructionHeader</code> und sind danach über <code>/storage/instructionHeader/...</code> erreichbar.</li>
   <li>in Ordner "resources/views/textimport" ist folgendes zu Bearbeiten:
   <ul>
     <li>anfahrt.blade.php anlegen und mit der Vorlage von anfahrt_example.blade.php ausfüllen</li>
     <li>recht.blade.php anlegen und mit der Vorlage von recht_example.blade.php ausfüllen</li>
     <li>cssColor.blade.php anlegen und mit der Vorlage von cssColor_example.blade.php ausfüllen</li>
     <li>map.blade.php anlegen und mit der Vorlage von map_example.blade.php ausfüllen</li>
     <li>footer.blade.php anlegen und mit der Vorlage von footer_example.blade.php ausfüllen</li>
     <li>werbung_options.php anlegen und mit der Vorlage von werbung_options_example.blade.php ausfüllen</li>
     <li><strong>mailImpressum.blade.php</strong> anlegen und mit der Vorlage von <strong>mailImpressum_example.blade.php</strong> ausfüllen (Vereinsadresse, Vorstand, Registernummer, Steuernummer für Bescheid-Impressum in PDF und E-Mail)</li>
   </ul>
   <li>in Ordner "public sind die folgenden Dateien anzulegen:
   <ul>
     <li>apple-touch-icon.png</li>
     <li>favicon.ico</li>
   </ul>
   <li>php artisan migrate --seed<br>(Datenbank wird mit Standard-Demodaten geladen, Benutzer erhält Administrator-Rolle)</li>
   <li><strong>Optional:</strong> Beispielantragsarten und Fördertöpfe befüllen: <code>php artisan db:seed --class=ApplicationExamplesSeeder</code></li>
   <li><strong>Information:</strong> Bescheid-PDFs werden unter <code>storage/app/local/bescheide/</code> gespeichert (nicht öffentlich zugänglich). Der Ordner wird bei Bedarf automatisch angelegt.</li>
</ul>

<h2>Update</h2>
<ul>
   <li>git pull origin Antraege</li>
   <li>composer install --no-dev --optimize-autoloader</li>
   <li>php artisan optimize:clear</li>
   <li>php artisan migrate:fresh --seed (solange noch keine eigenen Daten in die Datenbank eingegeben worden sind / Alle Daten werden gelöscht)</li>
   <li>php artisan migrate (Wenn schon eigene Daten in der Datenbank vorhanden sind)</li>
   <li><strong>Manuelle Anpassung:</strong> Falls noch nicht vorhanden, <code>mailImpressum.blade.php</code> in <code>resources/views/textimport/</code> anlegen (siehe Installation).</li>
</ul>

<h2>Zugehörige Projekte</h2>
<h3>Präsentation der Regatta</h3>  
<p>
    Für die live Präsentation der Regatta kann folgende Software verwendet werden.<br>
    Die Version V00.14.XX <a href="https://github.com/kube-csc/regattaView.git" target="_blank">https://github.com/kube-csc/regattaView.git</a> 
    ist kompatibel mit der Version V00.10.XX <a href="https://github.com/kube-csc/vereinsverwaltung.git" target="_blank">https://github.com/kube-csc/vereinsverwaltung.git</a>.
</p>
<h3>Regatta Managment</h3>  
<p>
    Meldeportal für Teilnehmer für Regatten<br>
    Die Version V00.02.XX <a href="https://github.com/kube-csc/regattamanager.git" target="_blank">https://github.com/kube-csc/regattamanager.git</a> 
    ist kompatibel mit der Version V00.10.01 <a href="https://github.com/kube-csc/vereinsverwaltung.git" target="_blank">https://github.com/kube-csc/vereinsverwaltung.git</a>.
</p>
<h3>Helferlisten</h3>
<p>
Kurse ist eine APP für ein Kursbuchungssystem. Es hat folgende Funktionen:<br>
Kursübersicht: Anzeige der verfügbaren Kurse und Termine.<br>
Buchung: Möglichkeit, Teilnehmer für Kurse zu buchen und die Startzeit zu ändern.<br>
Teilnehmerverwaltung: Hinzufügen und Entfernen von Teilnehmern<br>
Die Version V00.01.XX <a href="https://github.com/kube-csc/helferplanung.git" target="_blank">https://github.com/kube-csc/helferplanung.git</a>
ist kompatibel mit der Version V00.04.XX <a href="https://github.com/kube-csc/helferplanung.git" target="_blank">https://github.com/kube-csc/vereinsverwaltung.git</a>.
</p>
<h3>Kurse</h3>
<p>
Die App bietet ein Kursbuchungssystem, das auch für Fahrten oder Trainings verwendet werden kann. Sie hat folgende Funktionen:<br>
Verwaltung: Sportgeräte, Räume usw. können verwaltet werden.<br>
Teilnehmer: Accounts anlegen und verwalten.<br>
Kursübersicht: Anzeige der verfügbaren Kurse und Termine sowie Informationen zu den Kursen, Trainings, Übungen oder Fahrten, die angeboten werden.<br>
Trainer bzw. Fahrtenleiter: Trainer bzw. Fahrtenleiter können Termine für die Kurse anlegen und bearbeiten.<br>
Sportgeräte und Räume: Zu den Kursen können Sportgeräte, Räume usw. zugeordnet werden.<br>
Buchung: Teilnehmer können Kurse buchen und ihre eigenen Buchungen bearbeiten.<br>
Teilnehmerverwaltung: Hinzufügen und Entfernen von Teilnehmern durch Trainer bzw. Fahrtenleiter.<br>
Fahrtenbuch: Teilnehmer können ihre Fahrten erfassen, Distanzen eintragen und eine Jahresstatistik einsehen.<br>
Die Version V00.02.01 <a href="https://github.com/kube-csc/kurse.git" target="_blank">https://github.com/kube-csc/kurse.git</a> ist kompatibel ab der Version V00.010.03
<a href="https://github.com/kube-csc/vereinsverwaltung.git" target="_blank">https://github.com/kube-csc/vereinsverwaltung.git</a>.
</p>
<hr>
