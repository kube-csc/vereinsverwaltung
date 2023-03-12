<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class timetabelHelperListSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('timetabel_helper_lists')->delete();

        \DB::table('timetabel_helper_lists')->insert(
            array(
                array('id' => '1','event_id' => '7','operational_location_id' => '3','datum' => '2050-06-11','startZeit' => '10:00:00','endZeit' => '18:00:00','laenge' => '02:00:00','anzahlHelfer' => '3','deleted_at' => NULL,'created_at' => '2025-02-22 20:05:39','updated_at' => '2025-02-22 20:47:14'),
                array('id' => '2','event_id' => '7','operational_location_id' => '4','datum' => '2050-06-11','startZeit' => '10:00:00','endZeit' => '18:00:00','laenge' => '02:00:00','anzahlHelfer' => '3','deleted_at' => NULL,'created_at' => '2025-02-22 23:35:46','updated_at' => '2025-02-22 23:35:46'),
                array('id' => '3','event_id' => '7','operational_location_id' => '5','datum' => '2050-06-11','startZeit' => '10:00:00','endZeit' => '18:00:00','laenge' => '02:00:00','anzahlHelfer' => '2','deleted_at' => NULL,'created_at' => '2025-02-22 23:45:40','updated_at' => '2025-02-22 23:45:40')
                )
        );
    }
}
