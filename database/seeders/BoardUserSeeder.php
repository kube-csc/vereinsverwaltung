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
        DB::table('board_user')->delete();

        DB::table('board_user')
            ->insert(
                [
                    array(
                       'id' => '1',
                       'board_id' => '1',
                       'position' => '1',
                       'nummer' => '1',
                       'user_id' => '1'
                   ),

                    array(
                        'id' => '2',
                        'board_id' => '1',
                        'position' => '1',
                        'nummer' => '2',
                        'user_id' => '2'
                    ),
                ]);
    }
}
