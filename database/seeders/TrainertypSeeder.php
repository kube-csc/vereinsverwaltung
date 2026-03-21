<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrainertypSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('trainertypen')->insert([
            // Hinweis: default_* Felder werden in der App genutzt, um neue Trainertable-Zuordnungen vorzubelegen.
            array('id' => '1','trainerfunktion' => 'kein Trainer','status' => '0','default_sichtbar' => '0','organiser_id' => '0','deleted_at' => NULL,'created_at' => '2021-08-11 19:47:25','updated_at' => '2021-08-11 19:47:25'),
            array('id' => '2','trainerfunktion' => 'Test-Trainer','status' => '1','default_sichtbar' => '1','organiser_id' => '0','deleted_at' => NULL,'created_at' => '2021-08-11 19:47:25','updated_at' => '2021-08-11 19:47:25'),
            array('id' => '3','trainerfunktion' => 'Drachenboot','status' => '1','default_sichtbar' => '1','organiser_id' => '0','deleted_at' => NULL,'created_at' => '2021-08-11 19:47:25','updated_at' => '2021-08-11 19:47:25'),
            array('id' => '4','trainerfunktion' => 'SUP','status' => '1','default_sichtbar' => '1','organiser_id' => '1','deleted_at' => NULL,'created_at' => '2021-08-11 19:47:25','updated_at' => '2021-08-11 19:47:25'),
            array('id' => '5','trainerfunktion' => 'Ferienspass','status' => '1','default_sichtbar' => '1','organiser_id' => '2','deleted_at' => NULL,'created_at' => '2021-08-11 19:47:25','updated_at' => '2021-08-11 19:47:25'),
        ]);
    }
}
