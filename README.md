<h1>Internetauftritt von Vereine</h1>
<p>
 Ausgelegt für ein Kanuverein mit verschiedenen Abteilungen/Sportarten
</p>
<h2>Beispiel der Abteilungen/Sportarten:</h2> 
    <ul>
      <li>Jugend</li>
      <li>Wandersport</li>
      <li>Rennsport</li>
      <li>Drachenboot mit drei Mannschaften</li>
      <li>SUP</li>
    </ul>

<a href="https://test.kel-datteln.de">Beispiel eines Frontend</a>

<h2>Installierte Programme</h2>
<ul>
  <li>Insallation Laravel 8.* mit jetstream 2.* , livewire 2.* teams  und tailwindcss
  <a href="https://jetstream.laravel.com/2.x/introduction.html"></a>
  <a href="https://jetstream.laravel.com/2.x/stacks/livewire.html"></a></li>
  <li><a href="https://boxicons.com/">boxicons </a></li>
  <li><a href="https://tailwindcss.com/">Tailwindcss </a></li>
  <li>.htaccess für 1und1 Server</li>
  <li><a href="https://botman.io">Botman</a></li>
</ul>

<h2>Frontend</h2>
<ul>
<li>Header Abhänigig von den Abteilungen / Sportarten</li>
<li>Leanding Page
     <ul>
      <li>Ausgabe der Vereinsbeschreibung</li>
      <li>Presendation der Abteilung mit deren Mannschaften</li>
      <li>Presendation der kommenden Events</li>
      <li>Presendation der vergangenden Events</li>
      <li>Presendation des Teams</li>
      <li>Kontakt des Vereins inc. Map</li>
    </ul>
</li>
  <li>Detail Presendation der Abteilung / Sportart</li>
  <li>Detail Presendation der Mannschaften</li>
  <li>Detail Presendation des Event</li>
  <li>Informationen
    <ul>
        <li>Anfahrt</li>
        <li>Beiträge</li>
        <li>Übernachtungskosten</li>
    </ul>
  </li>
  <li>Footer
    <ul>
        <li>Dokumente aus dem Dokumentenmanagment werden angezeigt</li>
        <li>Impresssum</li>
        <li>Datenschutzerklärung</li>
    </ul>
  </li>
</ul>

<h2>Backend</h2>
<ul>
  <li>Eingabe und Bearbeitung der Vereinsbeschreibung</li>
  <li>Eingabe und Bearbeitung der Abteilungsbeschreibung</li>
  <li>Eingabe und Bearbeitung der Mannschaftsbeschreibung</li>
  <li>Eingabe und Bearbeitung von Events</li>
  <li>Eingabe und Bearbeitung von Event Gruppen</li>
  <li>Dokumentenmanagent</li>
  <li>Anfahrt</li>
  <li>Informationsseiten
    <ul>
        <li>Beiträge</li>
        <li>Übernachtungskosten</li>
        <li>Impresssum</li>
        <li>Datenschutzerklärung</li>
    </ul>
  </li>
</ul>

<h2>Installation</h2>
<ul>
   <li>git clone  https://github.com/kube-csc/vereinsverwaltung.git</li>
   <li>.env Datei Ausfüllen Es werden auch Informationen über den Verein abgefragt
     <ul>
      <li></li>
     </ul>
   </li>
   <li>composer.phar install</li>
   <li>php artisan storage:link<br>
      anlegen der Unterordner unter "public/storage"
      <ul>
         <li>dokumente</li>
         <li>eventImage</li>
         <li>header</li>
         <li>posten</li>  
     </ul>
    </li>
   <li>php artisan migrate:fresh --seed Datenbank mit Demodaten</li>
</ul>

<h2>Update</h2>
<ul>
   <li>git pull origin main</li>
   <li>php artisan migrate:fresh --seed (solange noch eine eigenden Daten in der Datenbank eingeben worden sind)</li>
   <li>php artisan migrate (Wenn noch keine eigenden Daten in der Datenbank vorhanden sind kann fresh --seed verwendet werden.)</li>
</ul>

<br>
<hr>
<br>
<p align="center"><a href="https://laravel.com" target="_blank"><img src="https://raw.githubusercontent.com/laravel/art/master/logo-lockup/5%20SVG/2%20CMYK/1%20Full%20Color/laravel-logolockup-cmyk-red.svg" width="400"> alt="Laravel"</a></p>

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
