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
                        'bild' => 'eventBildDemo.jpg',
                        'pixx' => '4032',
                        'pixy' => '2268',
                        'filename' => 'IMG_20210711_192819_1 1.jpg',
                        'player' => NULL,
                        'typ' => '1',
                        'visible' => '1',
                        'startseite' => '0',
                        'bearbeiter_id' => '1',
                        'user_id' => '1',
                        'deleted_at' => NULL,
                        'created_at' => '2021-12-05 16:12:31',
                        'updated_at' => '2021-12-05 16:12:31')
                ]);
    }
}
