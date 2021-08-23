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
            array('ID' => '1','abteilung' => 'Verein','event_id' => '1','status' => '1','sportSections_id' => NULL,'bild' => '','domain' => '','user_id' => '1','farbe' => '','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '2','abteilung' => 'Abteilung 1','event_id' => '2','status' => '2','sportSections_id' => NULL,'bild' => '','domain' => '','user_id' => '1','farbe' => '','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '3','abteilung' => 'Abteilung 2','event_id' => '3','status' => '2','sportSections_id' => NULL,'bild' => '','domain' => '','user_id' => '1','farbe' => '','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '4','abteilung' => 'Mannschaft 1','event_id' => '4','status' => '2','sportSections_id' => '3','bild' => '','domain' => '','user_id' => '1','farbe' => '','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '5','abteilung' => 'Mannschaft 2','event_id' => '5','status' => '2','sportSections_id' => '3','bild' => '','domain' => '','user_id' => '1','farbe' => '','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '6','abteilung' => 'Eventserie 1','event_id' => '6','status' => '2','sportSections_id' => NULL,'bild' => '','domain' => '','user_id' => '1','farbe' => '','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
            array('ID' => '7','abteilung' => 'Eventserie 2','event_id' => '7','status' => '2','sportSections_id' => NULL,'bild' => '','domain' => '','user_id' => '1','farbe' => '','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),

          ]
        );
     }
}
