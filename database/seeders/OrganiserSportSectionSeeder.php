<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class OrganiserSportSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('organiser_sport_section')->delete();

        DB::table('organiser_sport_section')->insert(
            [
                array('id' => '1','organiser_id' => '1','sport_section_id' => '4','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                array('id' => '2','organiser_id' => '2','sport_section_id' => '2','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                array('id' => '3','organiser_id' => '2','sport_section_id' => '4','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42')
            ]);
    }
}
