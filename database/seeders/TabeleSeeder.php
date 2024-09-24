<?php

namespace Database\Seeders;

use Carbon\Carbon;
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
        $currentDate1 = Carbon::now()->subDay(4)->toDateString();
        $currentDate2 = Carbon::now()->subDay(3)->toDateString();
        $currentDate3 = Carbon::now()->subDay(2)->toDateString();
        $currentDate = Carbon::now()->toDateString();

        DB::table('tabeles')->delete();

        DB::table('tabeles')
          ->insert([
            array('id' => '1', 'event_id' => '7', 'tabelleDatumVon' => $currentDate3, 'ueberschrift' => 'Vorläufe', 'beschreibung' => '', 'gruppe_id' => Null, 'tabelleLevelVon' => '1', 'tabelleLevelBis' => '2', 'wertungsart' => '1', 'maxrennen' => '0','created_at' => '2006-07-30', 'status' => '0', 'tabelleVisible' => '1', 'system_id' => NULL, 'finale' => '0', 'finaleAnzeigen' => '19:14:00', 'punktegleich' => '0', 'punktegleichlauf' => '0', 'tabelleDatei' => 'tabele-1.pdf', 'filetabelleDatei' => 'tabele-1.pdf', 'bearbeiter_id' => '1', 'autor_id' => '1', 'updated_at' => '2022-08-21 17:29:00'),
            array('id' => '2', 'event_id' => '7', 'tabelleDatumVon' => $currentDate3, 'ueberschrift' => 'Zwischenlauf', 'beschreibung' => '', 'gruppe_id' => Null, 'tabelleLevelVon' => '3', 'tabelleLevelBis' => '3', 'wertungsart' => '1', 'maxrennen' => '0', 'created_at' => '2006-07-30', 'status' => '0', 'tabelleVisible' => '1', 'system_id' => NULL, 'finale' => '0', 'finaleAnzeigen' => '19:14:00', 'punktegleich' => '0', 'punktegleichlauf' => '0', 'tabelleDatei' => 'tabele-2.pdf', 'filetabelleDatei' => 'tabele-2.pdf', 'bearbeiter_id' => '1', 'autor_id' => '1', 'updated_at' => '2022-08-21 17:39:00'),
            array('id' => '3', 'event_id' => '7', 'tabelleDatumVon' => $currentDate3, 'ueberschrift' => 'B-Finale Endlauf', 'beschreibung' => '', 'gruppe_id' => Null, 'tabelleLevelVon' => '4', 'tabelleLevelBis' => '4', 'wertungsart' => '1', 'maxrennen' => '0', 'created_at' => '2006-07-30', 'status' => '0', 'tabelleVisible' => '1', 'system_id' => NULL, 'finale' => '1', 'finaleAnzeigen' => '19:14:00', 'punktegleich' => '0', 'punktegleichlauf' => '0', 'tabelleDatei' => 'tabele-3.pdf', 'filetabelleDatei' => 'tabele-3.pdf', 'bearbeiter_id' => '1', 'autor_id' => '1', 'updated_at' => '2022-08-21 17:49:00'),
            array('id' => '4', 'event_id' => '7', 'tabelleDatumVon' => $currentDate3, 'ueberschrift' => 'A-Finale Endlauf', 'beschreibung' => '', 'gruppe_id' => Null, 'tabelleLevelVon' => '4', 'tabelleLevelBis' => '4', 'wertungsart' => '1', 'maxrennen' => '0', 'created_at' => '2006-07-30', 'status' => '0', 'tabelleVisible' => '1', 'system_id' => NULL, 'finale' => '1', 'finaleAnzeigen' => '19:14:00', 'punktegleich' => '0', 'punktegleichlauf' => '0', 'tabelleDatei' => 'tabele-3.pdf', 'filetabelleDatei' => 'tabele-3.pdf', 'bearbeiter_id' => '1', 'autor_id' => '1', 'updated_at' => '2022-08-21 17:59:00'),

            array('id' => '5', 'event_id' => '8', 'tabelleDatumVon' => $currentDate, 'ueberschrift' => 'Vorläufe', 'beschreibung' => '', 'gruppe_id' => '1', 'tabelleLevelVon' => '1', 'tabelleLevelBis' => '2', 'wertungsart' => '1', 'maxrennen' => '2', 'created_at' => '2006-07-30', 'status' => '0', 'tabelleVisible' => '1', 'system_id' => '1', 'finale' => '0', 'finaleAnzeigen' => '19:14:00', 'punktegleich' => '0', 'punktegleichlauf' => '0', 'tabelleDatei' => '', 'filetabelleDatei' => '', 'bearbeiter_id' => '1', 'autor_id' => '1', 'updated_at' => '2022-08-21 17:29:00'),
            array('id' => '6', 'event_id' => '8', 'tabelleDatumVon' => $currentDate, 'ueberschrift' => 'Vorläufe Mix', 'beschreibung' => '', 'gruppe_id' => '1', 'tabelleLevelVon' => '1', 'tabelleLevelBis' => '2', 'wertungsart' => '1', 'maxrennen' => '2', 'created_at' => '2006-07-30', 'status' => '0', 'tabelleVisible' => '1', 'system_id' => '1', 'finale' => '0', 'finaleAnzeigen' => '19:14:00', 'punktegleich' => '0', 'punktegleichlauf' => '0', 'tabelleDatei' => '', 'filetabelleDatei' => '', 'bearbeiter_id' => '1', 'autor_id' => '1', 'updated_at' => '2022-08-21 17:29:00'),
          ]);
    }
}
