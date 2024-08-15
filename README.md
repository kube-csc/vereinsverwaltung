<h1>Internetauftritt von Vereine</h1>
<p>Version: V00.05.xx</p>
<p>
Ausgelegt z.B. für einen Verein mit verschiedenen Abteilungen / Sportarten
</p>
Beispiel ein Kanu Verein mit Abteilungen / Sportarten:
    <ul>
      <li>Jugend</li>
      <li>Wandersport</li>
      <li>Rennsport</li>
      <li>Drachenboot mit drei Mannschaften</li>
      <li>SUP</li>
    </ul>

<a href="https://neu.kel-datteln.de">Beispiel eines Frontend</a>

<h2>Installierte Programme / Templets</h2>
<ul>
  <li>Installation Laravel 9.* mit jetstream 2.* , livewire 2.* teams  und tailwindcss</li>
  <li><a href="https://jetstream.laravel.com/2.x/introduction.html">jetstream 2.x Anleitung</a></li>
  <li><a href="https://jetstream.laravel.com/2.x/stacks/livewire.html">livewire</a></li>
  <li><a href="https://boxicons.com/">boxicons</a>(Forntend)</li>
  <li><a href="https://tailwindcss.com/">Tailwindcss</a>(Backend)</li>
  <li><a href="https://bootstrapmade.com/squadfree-free-bootstrap-template-creative/">BootstrapMade.com </a></li>
  <li>.htaccess für ionos.de (1und1.de) Server</li>
  <li><a href="https://botman.io">Botman</a></li>
</ul>

<h2>Benötigte Lizenzen</h2>
Es wird eine Lizenz für
<a href="https://bootstrapmade.com/squadfree-free-bootstrap-template-creative/">Squadfree von bootstrapmade</a>
benötigt.

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
    </ul>
  </li>
<li>Backlinksverwaltung Umleitung von Links die nicht mehr Existieren</li>
</ul>
<h3>Regattaverwaltung</h3>
<p>
In der .env kann mit der Variabel APP_REGATTA die Regattaverwaltug ein und ausgeschaltet werden.
</p>
<ul>
    <li>Regatta auswählen</li>
    <li>Regatta Informationen können angelegt und bearbeitet werden</li>
    <li>Rennen anlegen und bearbeiten</li>
    <li>Programm und Ergebnislisten für jedes Rennen oder Gruppenweise hochladen</li>
    <li>Tabellen anlegen und bearbeiten</li>
    <li>Ergebnislisten für jede Tabelle hochladen</li>
    <li>Startzeitverschiebungen werden berechnet</li>
    <li>Rennergebnisse und Tabellen können mit einer Veröffendlichungszeiten versehen werden</li>
</ul>

<h2>Installation</h2>
<ul>
   <li>git clone https://github.com/kube-csc/vereinsverwaltung.git</li>
   <li>.env Datei ausfüllen (Es werden auch Informationen über den Verein abgefragt.)</li>
   <li>cd vereinsverwaltung</li>
   <li>curl -sS https://getcomposer.org/installer</li>
   <li>php composer.phar</li>
   <li>php composer.phar install</li>
   <li>anlegen der Unterordner unter "public/storage/"
      <ul>
        <li>boardPortrait</li>
        <li>botman</li>
        <li>dokumente</li>
        <li>eventDokumente</li>
        <li>eventImage</li>
        <li>header</li>
        <li>raceDokumente</li>
        <li>tabeleDokumente</li>
      </ul>
    </li>
   <li>in Ordner "/recources/views/textimport ist folgendes zu Bearbeiten:
   <ul>
     <li>anfahrt.blade.php anlegen und mit der Vorlage von anfahrt_example.blade.php ausfüllen</li>
     <li>recht.blade.php anlegen und mit der Vorlage von recht_example.blade.php ausfüllen</li>
     <li>cssColor.blade.php anlegen und mit der Vorlage von cssColor_example.blade.php ausfüllen</li>
     <li>map.blade.php anlegen und mit der Vorlage von map_example.blade.php ausfüllen</li>
     <li>footer.blade.php anlegen und mit der Vorlage von footer_example.blade.php ausfüllen</li>
   </ul>
   <li>in Ordner "public sind die folgenden Dateien anzulegen:
   <ul>
     <li>apple-touch-icon.png</li>
     <li>favicon.ico</li>
   </ul>
   <li>php artisan migrate --seed<br>(Datenbank wird mit Demodaten geladen)</li>
</ul>

<h2>Update</h2>
<ul>
   <li>git pull origin main</li>
   <li>php artisan migrate:fresh --seed (solange noch keine einige Daten in die Datenbank eingegeben worden sind / Alle Daten werden gelöscht)</li>
   <li>php artisan migrate (Wenn schon eigene Daten in der Datenbank vorhanden sind)</li>
</ul>

<h2>Zugehörige Projekte</h2>
<h3>Präsentation der Regatta</h3>  
<p>
    Für die live Präsentation der Regatta kann folgende Software verwendet werden.<br>
    Die Version V00.13.XX <a href="https://github.com/kube-csc/regattaView.git" target="_blank">https://github.com/kube-csc/regattaView.git</a> ist kompatibel mit der Version V00.03.XX <a href="https://github.com/kube-csc/vereinsverwaltung.git" target="_blank">https://github.com/kube-csc/vereinsverwaltung.git</a>.
</p>
<h3>Helferlisten</h3>
<p>
Helferliste ist eine APP, die eine Liste von Personen verwaltet, die bereit sind, bei einem Event, einer Veranstaltung oder einem Projekt zu helfen. 
Die Liste enthält die Namen und E-Mail-Adressen der Helfer. Die Helferliste wird von der Person oder Organisation erstellt, die für die Organisation der Veranstaltung verantwortlich ist. Die Liste kann verwendet werden, um 
die Helfer zu koordinieren und sicherzustellen, dass alle Aufgaben abgedeckt sind.<br>
Die Version V00.01.XX <a href="https://github.com/kube-csc/helferplanung.git" target="_blank">https://github.com/kube-csc/helferplanung.git</a> ist kompatibel mit der Version V00.04.XX <a href="https://github.com/kube-csc/helferplanung.git" target="_blank">https://github.com/kube-csc/helferplanung.git</a>.
</p>
<br>
<hr>
<br>
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400" alt="Laravel"></a></p>

<p align="center">
<a href="https://travis-ci.org/laravel/framework"><img src="https://travis-ci.org/laravel/framework.svg" alt="Build Status"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/dt/laravel/framework" alt="Total Downloads"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/v/laravel/framework" alt="Latest Stable Version"></a>
<a href="https://packagist.org/packages/laravel/framework"><img src="https://img.shields.io/packagist/l/laravel/framework" alt="License"></a>
</p>

## About Laravel

Laravel is a web application framework with expressive, elegant syntax. We believe development must be an enjoyable and creative experience to be truly fulfilling. Laravel takes the pain out of development by easing common tasks used in many web projects, such as:

- [Simple, fast routing engine](https://laravel.com/docs/routing).
- [Powerful dependency injection container](https://laravel.com/docs/container).
- Multiple back-ends for [session](https://laravel.com/docs/session) and [cache](https://laravel.com/docs/cache) storage.
- Expressive, intuitive [database ORM](https://laravel.com/docs/eloquent).
- Database agnostic [schema migrations](https://laravel.com/docs/migrations).
- [Robust background job processing](https://laravel.com/docs/queues).
- [Real-time event broadcasting](https://laravel.com/docs/broadcasting).

Laravel is accessible, powerful, and provides tools required for large, robust applications.

## Learning Laravel

Laravel has the most extensive and thorough [documentation](https://laravel.com/docs) and video tutorial library of all modern web application frameworks, making it a breeze to get started with the framework.

If you don't feel like reading, [Laracasts](https://laracasts.com) can help. Laracasts contains over 1500 video tutorials on a range of topics including Laravel, modern PHP, unit testing, and JavaScript. Boost your skills by digging into our comprehensive video library.

## Laravel Sponsors

We would like to extend our thanks to the following sponsors for funding Laravel development. If you are interested in becoming a sponsor, please visit the Laravel [Patreon page](https://patreon.com/taylorotwell).

### Premium Partners

- **[Vehikl](https://vehikl.com/)**
- **[Tighten Co.](https://tighten.co)**
- **[Kirschbaum Development Group](https://kirschbaumdevelopment.com)**
- **[64 Robots](https://64robots.com)**
- **[Cubet Techno Labs](https://cubettech.com)**
- **[Cyber-Duck](https://cyber-duck.co.uk)**
- **[Many](https://www.many.co.uk)**
- **[Webdock, Fast VPS Hosting](https://www.webdock.io/en)**
- **[DevSquad](https://devsquad.com)**
- **[Curotec](https://www.curotec.com/)**
- **[OP.GG](https://op.gg)**

## Contributing

Thank you for considering contributing to the Laravel framework! The contribution guide can be found in the [Laravel documentation](https://laravel.com/docs/contributions).

## Code of Conduct

In order to ensure that the Laravel community is welcoming to all, please review and abide by the [Code of Conduct](https://laravel.com/docs/contributions#code-of-conduct).

## Security Vulnerabilities

If you discover a security vulnerability within Laravel, please send an e-mail to Taylor Otwell via [taylor@laravel.com](mailto:taylor@laravel.com). All security vulnerabilities will be promptly addressed.

## License

The Laravel framework is open-sourced software licensed under the [MIT license](https://opensource.org/licenses/MIT).
