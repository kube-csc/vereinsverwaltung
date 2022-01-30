<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class ReportSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('reports')->delete();

        DB::table('reports')
            ->insert(
                [
                    array(
                        'id' => '1',
                        'event_id' => '6',
                        'position' => '10',
                        'titel' => 'Bild 1 Eventsersie 1',
                        'kommentar' => NULL,
                        'bild' => 'portfolio-1.jpg',
                        'image' => '',
                        'pixx' => '4032',
                        'pixy' => '2268',
                        'filename' => 'portfolio-1.jpg',
                        'player' => NULL,
                        'typ' => '1',
                        'visible' => '1',
                        'startseite' => '1',
                        'webseite' => '1',
                        'bearbeiter_id' => '1',
                        'user_id' => '1',
                        'deleted_at' => NULL,
                        'created_at' => '2021-12-05 16:12:31',
                        'updated_at' => '2021-12-05 16:12:31'),
                    array(
                        'id' => '2',
                        'event_id' => '6',
                        'position' => '20',
                        'titel' => 'Bild 2 Eventsersie 1',
                        'kommentar' => NULL,
                        'bild' => 'portfolio-2.jpg',
                        'image' => '',
                        'pixx' => '4032',
                        'pixy' => '2268',
                        'filename' => 'portfolio-2.jpg',
                        'player' => NULL,
                        'typ' => '1',
                        'visible' => '1',
                        'startseite' => '0',
                        'webseite' => '1',
                        'bearbeiter_id' => '1',
                        'user_id' => '1',
                        'deleted_at' => NULL,
                        'created_at' => '2021-12-05 16:12:31',
                        'updated_at' => '2021-12-05 16:12:31'),
                    array(
                        'id' => '3',
                        'event_id' => '6',
                        'position' => '30',
                        'titel' => 'Bild 3 Eventsersie 1',
                        'kommentar' => NULL,
                        'bild' => 'portfolio-3.jpg',
                        'image' => '',
                        'pixx' => '4032',
                        'pixy' => '2268',
                        'filename' => 'portfolio-3.jpg',
                        'player' => NULL,
                        'typ' => '1',
                        'visible' => '1',
                        'startseite' => '0',
                        'webseite' => '1',
                        'bearbeiter_id' => '1',
                        'user_id' => '1',
                        'deleted_at' => NULL,
                        'created_at' => '2021-12-05 16:12:31',
                        'updated_at' => '2021-12-05 16:12:31'),
                    array(
                        'id' => '4',
                        'event_id' => '6',
                        'position' => '40',
                        'titel' => 'Bild 4 Eventsersie 1',
                        'kommentar' => NULL,
                        'bild' => 'portfolio-4.jpg',
                        'image' => '',
                        'pixx' => '4032',
                        'pixy' => '2268',
                        'filename' => 'portfolio-4.jpg',
                        'player' => NULL,
                        'typ' => '1',
                        'visible' => '1',
                        'startseite' => '0',
                        'webseite' => '0',
                        'bearbeiter_id' => '1',
                        'user_id' => '1',
                        'deleted_at' => NULL,
                        'created_at' => '2021-12-05 16:12:31',
                        'updated_at' => '2021-12-05 16:12:31')
                ]);
    }
}
