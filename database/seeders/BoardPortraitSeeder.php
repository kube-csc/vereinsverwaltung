<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoardPortraitSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('board_portraits')->delete();

        DB::table('board_portraits')
            ->insert(
                [
                    array(
                        'id' => '1',
                        'postenPortraet_id' => '1',
                        'postenPortraet' => 'posten-1.jpg',
                        'datumvon' => '2005-05-21',
                        'datumbis' => '2020-05-22',
                        'bearbeiter_id' => 1,
                        'user_id' => 1,
                        'visible' => '1',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                    ),
                    array(
                        'id' => '2',
                        'postenPortraet_id' => '2',
                        'postenPortraet' => 'posten-1.jpg',
                        'datumvon' => '2005-05-21',
                        'datumbis' => '2020-05-22',
                        'bearbeiter_id' => 1,
                        'user_id' => 1,
                        'visible' => '1',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                    ),
                    array(
                        'id' => '3',
                        'postenPortraet_id' => '3',
                        'postenPortraet' => 'posten-3.jpg',
                        'datumvon' => '2005-05-21',
                        'datumbis' => '2020-05-22',
                        'bearbeiter_id' => 1,
                        'user_id' => 1,
                        'visible' => '1',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                    ),
                ]);
    }
}
