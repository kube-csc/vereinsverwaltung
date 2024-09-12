<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RaceTypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('race_types')->delete();

        $data = [
            array('id' => '1','regatta_id' => '7','race_type_template_id' => '1','typ' => 'Klasse 1','altervon' => '0','alterbis' => '0','min' => '0','max' => '0','weiblichmin' => '0','weiblichmax' => '0','manmin' => '0','manmax' => '0','training' => '1','bahnen' => '0','zusatzmanschaft' => '0','beschreibung' => NULL,'distanz' => '250 m','meldeGebuehr' => NULL,'autor_id' => '1','bearbeiter_id' => '1','created_at' => '2024-08-18 13:06:42','updated_at' => '2024-08-18 13:06:42')
       ];

        DB::table('race_types')->insert($data);
    }
}
