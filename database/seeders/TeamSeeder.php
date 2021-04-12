<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TeamSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('teams')->delete();

      DB::table('teams')
       ->insert(
         [
          array('id' => '1','user_id' => '1','name' => 'Admin\'s Team','personal_team' => '1','created_at' => '2021-04-12 19:43:55','updated_at' => '2021-04-12 19:43:55')
        ]);
    }
}
