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
                       'user_id' => '1',
                       'vorstandsbild' => 'team-1.jpg',
                       'vorstandsemail' => 'vorstand1@verein.de',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                   ),

                    array(
                        'id' => '2',
                        'board_id' => '1',
                        'position' => '2',
                        'nummer' => '2',
                        'user_id' => '2',
                        'vorstandsbild' => 'team-2.jpg',
                        'vorstandsemail' => 'vorstand2@verein.de',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                    ),

                    array(
                        'id' => '3',
                        'board_id' => '2',
                        'position' => '1',
                        'nummer' => '1',
                        'user_id' => '3',
                        'vorstandsbild' => 'team-2.jpg',
                        'vorstandsemail' => 'trainer@verein.de',
                        'created_at' => '2021-01-01 12:00:00',
                        'updated_at' => '2021-01-01 12:00:00'
                    ),
                ]);
    }
}
