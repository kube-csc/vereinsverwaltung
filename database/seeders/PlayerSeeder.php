<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PlayerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('players')->delete();

        DB::table('players')->insert([
            array('id' => '1','playername' => 'Youtube','playerlink' => '<iframe width="[breite]" height="[hoehe]" src="https://www.youtube.com/embed/iAx1E_ethKA?si=[URL]" title="YouTube video player" frameborder="0" allow="accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share" referrerpolicy="strict-origin-when-cross-origin" allowfullscreen></iframe>','autor_id' => '1','bearbeiter_id' => '1','deleted_at' => NULL,'created_at' => '2025-02-14 13:06:42','updated_at' => '2025-02-14 13:06:42'),
            array('id' => '2','playername' => 'facebook Blog','playerlink' => '<div class="fb-post" data-href="[URL]"></div>','autor_id' => '1','bearbeiter_id' => '1','deleted_at' => NULL,'created_at' => '2025-02-14 13:06:42','updated_at' => '2025-02-14 13:06:42'),
        ]);
    }
}
