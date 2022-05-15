<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RegattaInformationSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('regatta_information')->delete();

        DB::table('regatta_information')
            ->insert(
                [
                   array('id' => '1','event_id' => '7','position' => '10','visible' => '1','informationTittel' => 'Regattainformation 1','informationBeschreibung' => 'Hier stehen die Regattainformationen.','startDatum' => '2030-06-11 12:00:00','startDatumVerschoben' => '2030-06-11 12:00:00','startDatumAktiv' => '1','endDatum' => '2030-06-11 13:00:00','endDatumVerschoben' => '2030-06-11 13:00:00','endDatumAktiv' => '1','freigeber_id' => NULL,'letzteFreigabe' => NULL,'bearbeiter_id' => '1','user_id' => '1','created_at' => '2022-05-12 23:16:01','updated_at' => '2022-05-12 23:16:01')
                ]);
    }
}
