<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SportEquipmentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('sport_equipment')->delete();

        DB::table('sport_equipment')
            ->insert(
                [
                   array('id' => '1','sportgeraet' => 'Sportgerät 1','sportSection_id' => '4','bild' => 'sportgeraet-1.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'PE Standard','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                   array('id' => '2','sportgeraet' => 'Sportgerät 2','sportSection_id' => '4','bild' => 'sportgeraet-2.jpg','pixx' => '0','pixy' => '0','anschafdatum' => '2023-01-01','verschrottdatum' => Null,'mitgliedprivat_id' => '0','sportleranzahl' => '1','laenge' => '0.00','breite' => '0.00','hoehe' => '0.00','gewicht' => '0.00','tragkraft' => '0.00','typ' => 'PE Standard','privat' => '','bearbeiter_id' => '1','autor_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                ]);
     }
}
