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
                        'position' => '10',
                        'nummer' => '1',
                        'boardUser_id' => '1',
                        'postenemail' => 'vorstand1@verein.de',
                        'bearbeiter_id' => 1,
                        'user_id' => 1,
                        'visible' => '1',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                    ),
                    array(
                        'id' => '2',
                        'board_id' => '1',
                        'position' => '20',
                        'nummer' => '2',
                        'boardUser_id' => '2',
                        'postenemail' => 'vorstand2@verein.de',
                        'bearbeiter_id' => 1,
                        'user_id' => 1,
                        'visible' => '1',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                    ),
                    array(
                        'id' => '3',
                        'board_id' => '2',
                        'position' => '10',
                        'nummer' => '1',
                        'boardUser_id' => '3',
                        'postenemail' => 'trainer@verein.de',
                        'bearbeiter_id' => 1,
                        'user_id' => 1,
                        'visible' => '1',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                    ),
                    array(
                        'id' => '4',
                        'board_id' => '3',
                        'position' => '20',
                        'nummer' => '1',
                        'boardUser_id' => '2',
                        'bearbeiter_id' => 1,
                        'user_id' => 1,
                        'visible' => '1',
                        'postenemail' => '',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                    ),
                ]);
    }
}
