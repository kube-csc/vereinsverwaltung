<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoardUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('board_users')->delete();

        DB::table('board_users')
            ->insert(
                [
                    array(
                        'id' => '1',
                        'board_id' => '1',
                        'position' => '1',
                        'nummer' => '1',
                        'boardUser_id' => '1',
                        'postenportraet' => 'posten-1.jpg',
                        'postenemail' => 'vorstand1@verein.de',
                        'bearbeiter_id' => 1,
                        'visible' => '1',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                   ),

                    array(
                        'id' => '2',
                        'board_id' => '1',
                        'position' => '2',
                        'nummer' => '2',
                        'boardUser_id' => '2',
                        'postenportraet' => 'posten-2.jpg',
                        'postenemail' => 'vorstand2@verein.de',
                        'bearbeiter_id' => 1,
                        'visible' => '1',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                    ),

                    array(
                        'id' => '3',
                        'board_id' => '2',
                        'position' => '1',
                        'nummer' => '1',
                        'boardUser_id' => '3',
                        'postenportraet' => 'posten-3.jpg',
                        'bearbeiter_id' => 1,
                        'visible' => '1',
                        'postenemail' => 'trainer@verein.de',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                    ),
                ]);
    }
}
