<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;

class SportSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('sport_sections')->delete();

       DB::table('sport_sections')
        ->insert(
          [
            array('ID' => '1','abteilung' => 'Verein','abteilungTeamBezeichnung' => 'Vorstand','event_id' => '1','status' => '1','sportSection_id' => NULL,'bild' => 'header-1.jpg','domain' => '','bearbeiter_id' => '1','user_id' => '1','farbe' => '','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '2','abteilung' => 'Abteilung 1','abteilungTeamBezeichnung' => 'Trainer','event_id' => '2','status' => '2','sportSection_id' => NULL,'bild' => '','domain' => '','bearbeiter_id' => '1','user_id' => '1','farbe' => '','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '3','abteilung' => 'Abteilung 2','abteilungTeamBezeichnung' => 'Trainer','event_id' => '3','status' => '2','sportSection_id' => NULL,'bild' => '','domain' => '','bearbeiter_id' => '1','user_id' => '1','farbe' => '','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '4','abteilung' => 'Mannschaft 1','abteilungTeamBezeichnung' => 'Trainer','event_id' => '4','status' => '2','sportSection_id' => '3','bild' => '','domain' => '','bearbeiter_id' => '1','user_id' => '1','farbe' => '','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '5','abteilung' => 'Mannschaft 2','abteilungTeamBezeichnung' => 'Trainer','event_id' => '5','status' => '2','sportSection_id' => '3','bild' => '','domain' => '','bearbeiter_id' => '1','user_id' => '1','farbe' => '','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
          ]
        );
     }
}
