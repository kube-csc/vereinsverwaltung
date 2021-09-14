<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BoardSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
       DB::table('boards')->delete();

       DB::table('boards')
        ->insert(
          [
              array(
                    'id' => '1',
                    'sportSection_id' => '1',
                    'position' => '10',
                    'user_id' => '1',
                    'postenmaenlich' => 'Vorsitzender',
                    'postenweiblich' => 'Vorsitzende',
                    'visible' => '1'
                   ),
              array(
                    'id' => '2',
                    'sportSection_id' => '5',
                    'position' => '10',
                    'user_id' => '2',
                    'postenmaenlich' => 'Trainer',
                    'postenweiblich' => 'Trainerin',
                    'visible' => '1'
              ),
          ]);

    }
}
