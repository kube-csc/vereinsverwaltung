<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TabeleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tabeles')->delete();

        DB::table('tabeles')
          ->insert([
            array('id' => '1', 'event_id' => '7', 'tabelleDatumVon' => '2050-06-11', 'ueberschrift' => 'Vorläufe', 'beschreibung' => '', 'gruppe_id' => '1', 'tabelleLevelVon' => '1', 'tabelleLevelBis' => '2', 'wertungsart' => '1', 'created_at' => '2006-07-30', 'status' => '0', 'tabelleVisible' => '1', 'system_id' => '0', 'finale' => '0', 'finaleAnzeigen' => '2022-08-23 19:14:00', 'getrenntewertung' => '0', 'punktegleich' => '0', 'punktegleichlauf' => '0', 'tabelleDatei' => 'tabele-1.pdf', 'filetabelleDatei' => 'tabele-1.pdf', 'bearbeiter_id' => '1', 'autor_id' => '1', 'updated_at' => '2022-08-21 17:59:00'),
            array('id' => '2', 'event_id' => '7', 'tabelleDatumVon' => '2050-06-11', 'ueberschrift' => 'Zwischenlauf', 'beschreibung' => '', 'gruppe_id' => '1', 'tabelleLevelVon' => '3', 'tabelleLevelBis' => '3', 'wertungsart' => '1', 'created_at' => '2006-07-30', 'status' => '0', 'tabelleVisible' => '1', 'system_id' => '0', 'finale' => '0', 'finaleAnzeigen' => '2022-08-23 19:14:00', 'getrenntewertung' => '0', 'punktegleich' => '0', 'punktegleichlauf' => '0', 'tabelleDatei' => 'tabele-2.pdf', 'filetabelleDatei' => 'tabele-2.pdf', 'bearbeiter_id' => '1', 'autor_id' => '1', 'updated_at' => '2022-08-21 17:59:00'),
            array('id' => '3', 'event_id' => '7', 'tabelleDatumVon' => '2050-06-11', 'ueberschrift' => 'A-Finale Endlauf', 'beschreibung' => '', 'gruppe_id' => '1', 'tabelleLevelVon' => '4', 'tabelleLevelBis' => '4', 'wertungsart' => '1', 'created_at' => '2006-07-30', 'status' => '0', 'tabelleVisible' => '1', 'system_id' => '0', 'finale' => '1', 'finaleAnzeigen' => '2022-08-23 19:14:00', 'getrenntewertung' => '0', 'punktegleich' => '0', 'punktegleichlauf' => '0', 'tabelleDatei' => 'tabele-3.pdf', 'filetabelleDatei' => 'tabele-3.pdf', 'bearbeiter_id' => '1', 'autor_id' => '1', 'updated_at' => '2022-08-21 17:59:00'),
          ]);
    }
}
