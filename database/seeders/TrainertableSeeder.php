<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TrainertableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('trainertables')->delete();

        DB::table('trainertables')
            ->insert(
                [
                  array('id' => '1','user_id' => '1','trainertyp_id' => '4','sportSection_id' => '4','organiser_id' => '1','status' => '1','sichtbar' => '1','autor_id' => '1','bearbeiter_id' => '1','deleted_at' => NULL,'created_at' => '2021-08-11 19:47:25','updated_at' => '2021-08-11 19:47:25'),
                  array('id' => '2','user_id' => '3','trainertyp_id' => '5','sportSection_id' => '2','organiser_id' => '2','status' => '1','sichtbar' => '1','autor_id' => '1','bearbeiter_id' => '1','deleted_at' => NULL,'created_at' => '2021-08-11 19:47:25','updated_at' => '2021-08-11 19:47:25'),
                  array('id' => '3','user_id' => '2','trainertyp_id' => '4','sportSection_id' => '4','organiser_id' => '1','status' => '1','sichtbar' => '0','autor_id' => '1','bearbeiter_id' => '1','deleted_at' => NULL,'created_at' => '2021-08-11 19:47:25','updated_at' => '2021-08-11 19:47:25'),
                  array('id' => '4','user_id' => '3','trainertyp_id' => '4','sportSection_id' => '4','organiser_id' => '1','status' => '1','sichtbar' => '1','autor_id' => '1','bearbeiter_id' => '1','deleted_at' => NULL,'created_at' => '2021-08-11 19:47:25','updated_at' => '2021-08-11 19:47:25')
                 ]);
    }
}
