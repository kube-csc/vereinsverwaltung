<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('player_data')->delete();

        DB::table('player_data')->insert([
           array('id' => '1','player_id' => '1','playervisible' => '1','visibleLandingpage' => '0','visibleEventpage' => '1','visiblePlayerpage' => '1','breite' => '560','hoehe' => '315','deleted_at' => NULL,'created_at' => '2025-02-14 13:06:42','updated_at' => '2025-02-14 13:06:42'),
           array('id' => '2','player_id' => '2','playervisible' => '1','visibleLandingpage' => '0','visibleEventpage' => '1','visiblePlayerpage' => '0','breite' => '0','hoehe' => '0','deleted_at' => NULL,'created_at' => '2025-02-14 13:06:42','updated_at' => '2025-02-14 13:06:42'),
           array('id' => '3','player_id' => '1','playervisible' => '1','visibleLandingpage' => '1','visibleEventpage' => '0','visiblePlayerpage' => '0','breite' => '200','hoehe' => '112','deleted_at' => NULL,'created_at' => '2025-02-14 13:06:42','updated_at' => '2025-02-14 13:06:42')
        ]);
    }
}
