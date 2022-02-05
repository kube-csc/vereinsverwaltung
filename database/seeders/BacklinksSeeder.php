<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BacklinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('backlinks')->delete();

        DB::table('backlinks')
          ->insert([
              array('id' => '1','ip' => '114.119.149.1','backlink' => 'www.beispiel.de/termin/fahrtenbeschreibung.php?termin3=695','neueUrl' => 'www.beispiel.de/Bericht/','visible' => '1','teilUrl' => '2','bearbeiter_id' => '20','user_id' => NULL,'nichtgefundenDatum' => '2022-01-30 14:06:33','nichtgefundenAnzahl' => '2','weiterleitDatum' => '2022-02-02 19:54:21','weiterleitAnzahl' => '9','deleted_at' => NULL,'created_at' => '2022-01-30 14:05:36','updated_at' => '2022-02-02 00:39:26'),
              array('id' => '2','ip' => '114.119.148.94','backlink' => 'www.beispiel.de/standart/datenschutz.php','neueUrl' => 'www.beispiel.de/Information/Datenschutzerklärung','visible' => '1','teilUrl' => '0','bearbeiter_id' => '20','user_id' => NULL,'nichtgefundenDatum' => '2022-01-30 14:23:18','nichtgefundenAnzahl' => '1','weiterleitDatum' => '2022-02-02 19:05:43','weiterleitAnzahl' => '3','deleted_at' => NULL,'created_at' => '2022-01-30 14:23:18','updated_at' => '2022-01-30 14:31:39'),
              array('id' => '3','ip' => '185.191.171.8','backlink' => 'www.beispiel.de/termin/ausgabe.php?EndDatum=31.12.1990&StartDatum=01.01.1990','neueUrl' => 'www.beispiel.de/Berichte','visible' => '1','teilUrl' => '1','bearbeiter_id' => '20','user_id' => NULL,'nichtgefundenDatum' => '2022-01-30 14:30:50','nichtgefundenAnzahl' => '1','weiterleitDatum' => NULL,'weiterleitAnzahl' => NULL,'deleted_at' => NULL,'created_at' => '2022-01-30 14:30:50','updated_at' => '2022-02-02 00:38:25'),
              array('id' => '4','ip' => '66.249.66.78','backlink' => 'www.beispiel.de/standart/vorstand','neueUrl' => 'www.beispiel.de/#team','visible' => '1','teilUrl' => '0','bearbeiter_id' => '20','user_id' => NULL,'nichtgefundenDatum' => '2022-01-30 20:18:00','nichtgefundenAnzahl' => '1','weiterleitDatum' => NULL,'weiterleitAnzahl' => NULL,'deleted_at' => NULL,'created_at' => '2022-01-30 20:18:00','updated_at' => '2022-02-02 00:40:11'),
              array('id' => '5','ip' => '136.243.83.49','backlink' => 'www.beispiel.de/termin/terminfilter.php','neueUrl' => 'www.beispiel.de/Termine','visible' => '1','teilUrl' => '0','bearbeiter_id' => '20','user_id' => NULL,'nichtgefundenDatum' => '2022-01-31 07:51:03','nichtgefundenAnzahl' => '2','weiterleitDatum' => NULL,'weiterleitAnzahl' => NULL,'deleted_at' => NULL,'created_at' => '2022-01-30 22:05:26','updated_at' => '2022-01-31 12:31:49'),
              array('id' => '6','ip' => '2a03:94e0:ffff:185:181:61:0:24','backlink' => 'www.beispiel.de/kel/drachenchronik.php','neueUrl' => 'www.beispiel.de/Sportgruppen/Drachenboot','visible' => '1','teilUrl' => '0','bearbeiter_id' => '20','user_id' => NULL,'nichtgefundenDatum' => '2022-01-31 09:20:43','nichtgefundenAnzahl' => '1','weiterleitDatum' => '2022-02-02 12:58:37','weiterleitAnzahl' => '1','deleted_at' => NULL,'created_at' => '2022-01-31 09:20:43','updated_at' => '2022-01-31 11:57:48'),
              array('id' => '7','ip' => '207.241.231.143','backlink' => 'www.beispiel.de/download/vereinssatzung.pdf','neueUrl' => 'www.beispiel.de/storage/dokumente/DemoDokument.pdf','visible' => '1','teilUrl' => '0','bearbeiter_id' => '20','user_id' => NULL,'nichtgefundenDatum' => '2022-01-31 10:51:58','nichtgefundenAnzahl' => '1','weiterleitDatum' => NULL,'weiterleitAnzahl' => NULL,'deleted_at' => NULL,'created_at' => '2022-01-31 10:51:58','updated_at' => '2022-01-31 11:52:23'),
              array('id' => '8','ip' => '2a01:4f8:c0c:c03f::1','backlink' => 'www.beispiel.de/standart/impressum.php','neueUrl' => 'www.beispiel.de/Impressum','visible' => '1','teilUrl' => '0','bearbeiter_id' => '20','user_id' => NULL,'nichtgefundenDatum' => '2022-01-31 16:55:50','nichtgefundenAnzahl' => '1','weiterleitDatum' => NULL,'weiterleitAnzahl' => NULL,'deleted_at' => NULL,'created_at' => '2022-01-31 16:55:50','updated_at' => '2022-01-31 17:01:20'),
              array('id' => '9','ip' => '46.4.83.150','backlink' => 'www.beispiel.de/kel/rennsport.html','neueUrl' => 'www.beispiel.de/Sportgruppen/Rennsport','visible' => '1','teilUrl' => '0','bearbeiter_id' => '20','user_id' => NULL,'nichtgefundenDatum' => '2022-02-02 14:03:37','nichtgefundenAnzahl' => '2','weiterleitDatum' => NULL,'weiterleitAnzahl' => NULL,'deleted_at' => NULL,'created_at' => '2022-02-02 01:24:18','updated_at' => '2022-02-02 16:22:20'),
              array('id' => '10','ip' => '46.161.11.2','backlink' => 'www.beispiel.de/standart/beitraege.php','neueUrl' => 'www.beispiel.de/Information/Beiträge','visible' => '1','teilUrl' => '0','bearbeiter_id' => '20','user_id' => NULL,'nichtgefundenDatum' => '2022-02-02 06:09:26','nichtgefundenAnzahl' => '1','weiterleitDatum' => NULL,'weiterleitAnzahl' => NULL,'deleted_at' => NULL,'created_at' => '2022-02-02 06:09:26','updated_at' => '2022-02-02 16:25:29')
          ]);
    }
}
