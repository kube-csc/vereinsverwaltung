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
                      'sportSection_id' => '7',
                      'position' => '10',
                      'user_id' => '1',
                      'postenmaenlich' => 'Vorsitzender',
                      'postenweiblich' => 'Vorsitzende',
                      'email' => 'vorstand@verein.de',
                      'visible' => '1'
                   ),

        ]);

    }
}
