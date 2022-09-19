<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RaceSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('races')->delete();

        DB::table('races')
         ->insert(
            [
              array('id' => '1','event_id' => '7','tabele_id' => '1','tabelerennen_id' => '0','gruppe_id' => '1','rennDatum' => '2050-06-11','rennUhrzeit' => '09:00:00','verspaetungUhrzeit' => '09:00:00','visible' => '1','rennBezeichnung' => '1. Vorlauf','beschreibung' => NULL,'ergebnisBeschreibung' => NULL,'bahnen' => NULL,'nummer' => '1','level' => '1','status' => '0','bild' => NULL,'pixx' => NULL,'pixy' => NULL,'programmDatei' => 'programm-1.pdf','fileProgrammDatei' => 'programm-1.pdf','ergebnisDatei' => 'ergebnis-1.pdf','fileErgebnisDatei' => 'ergebnis-1.pdf','mix' => '0','autor_id' => '1','bearbeiter_id' => '1','veroeffentlichungUhrzeit' => '19:14:00','created_at' => '2022-04-29 20:31:26','updated_at' => '2022-04-29 20:42:46'),
              array('id' => '2','event_id' => '7','tabele_id' => '1','tabelerennen_id' => '0','gruppe_id' => '1','rennDatum' => '2050-06-11','rennUhrzeit' => '09:05:00','verspaetungUhrzeit' => '09:05:00','visible' => '1','rennBezeichnung' => '2. Vorlauf','beschreibung' => NULL,'ergebnisBeschreibung' => NULL,'bahnen' => NULL,'nummer' => '2','level' => '2','status' => '0','bild' => NULL,'pixx' => NULL,'pixy' => NULL,'programmDatei' => 'programm-2.pdf','fileProgrammDatei' => 'programm-2.pdf','ergebnisDatei' => 'ergebnis-2.pdf','fileErgebnisDatei' => 'ergebnis-2.pdf','mix' => '0','autor_id' => '1','bearbeiter_id' => '1','veroeffentlichungUhrzeit' => '19:14:00','created_at' => '2022-04-29 20:31:52','updated_at' => '2022-04-29 20:43:14'),
              array('id' => '3','event_id' => '7','tabele_id' => '2','tabelerennen_id' => '0','gruppe_id' => '1','rennDatum' => '2050-06-11','rennUhrzeit' => '09:10:00','verspaetungUhrzeit' => '09:10:00','visible' => '1','rennBezeichnung' => '1. Zwischenlauf','beschreibung' => NULL,'ergebnisBeschreibung' => NULL,'bahnen' => NULL,'nummer' => '3','level' => '0','status' => '3','bild' => NULL,'pixx' => NULL,'pixy' => NULL,'programmDatei' => 'programm-3.pdf','fileProgrammDatei' => 'programm-3.pdf','ergebnisDatei' => NULL,'fileErgebnisDatei' => NULL,'mix' => '0','autor_id' => '1','bearbeiter_id' => '1','veroeffentlichungUhrzeit' => '19:14:00','created_at' => '2022-04-29 20:32:17','updated_at' => '2022-04-29 20:38:26'),
              array('id' => '4','event_id' => '7','tabele_id' => '3','tabelerennen_id' => '0','gruppe_id' => '1','rennDatum' => '2050-06-11','rennUhrzeit' => '09:15:00','verspaetungUhrzeit' => '09:15:00','visible' => '1','rennBezeichnung' => 'A-Finale Endlauf','beschreibung' => NULL,'ergebnisBeschreibung' => NULL,'bahnen' => NULL,'nummer' => '4','level' => '0','status' => '4','bild' => NULL,'pixx' => NULL,'pixy' => NULL,'programmDatei' => NULL,'fileProgrammDatei' => NULL,'ergebnisDatei' => NULL,'fileErgebnisDatei' => NULL,'mix' => '0','autor_id' => '1','bearbeiter_id' => '1','veroeffentlichungUhrzeit' => '19:14:00','created_at' => '2022-04-29 20:32:31','updated_at' => '2022-04-29 20:32:31')
           ]);
    }
}
