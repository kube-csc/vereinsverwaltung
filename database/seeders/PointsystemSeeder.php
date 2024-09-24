<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PointsystemSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('pointsystems')->delete();

        $data = array(
          array('id' => '1','system_id' => '1','platz' => '1','punkte' => '4','autor_id' => '1','bearbeiter_id' => '1','created_at' => '2024-08-18 13:06:42','updated_at' => '2024-08-18 13:06:42'),
          array('id' => '2','system_id' => '1','platz' => '2','punkte' => '3','autor_id' => '1','bearbeiter_id' => '1','created_at' => '2024-08-18 13:06:42','updated_at' => '2024-08-18 13:06:42'),
          array('id' => '3','system_id' => '1','platz' => '3','punkte' => '2','autor_id' => '1','bearbeiter_id' => '1','created_at' => '2024-08-18 13:06:42','updated_at' => '2024-08-18 13:06:42'),
          array('id' => '4','system_id' => '1','platz' => '4','punkte' => '1','autor_id' => '1','bearbeiter_id' => '1','created_at' => '2024-08-18 13:06:42','updated_at' => '2024-08-18 13:06:42'),
        );

        DB::table('pointsystems')->insert($data);
    }
}

