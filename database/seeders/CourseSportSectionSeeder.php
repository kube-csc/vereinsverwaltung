<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseSportSectionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('course_sport_section')->delete();

        $now = Carbon::now();

        DB::table('course_sport_section')->insert(
            [
                [
                    'id' => '1',
                    'course_id' => '1',
                    'sport_section_id' => '4',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'id' => '2',
                    'course_id' => '2',
                    'sport_section_id' => '4',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'id' => '3',
                    'course_id' => '3',
                    'sport_section_id' => '1',
                    'created_at' => $now,
                    'updated_at' => $now
                ],
                [
                    'id' => '4',
                    'course_id' => '4',
                    'sport_section_id' => '1',
                    'created_at' => $now,
                    'updated_at' => $now
                ]
            ]);
    }
}
