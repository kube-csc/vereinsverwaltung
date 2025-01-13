<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseParticipantBookedSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('course_participant_bookeds')->delete();

        DB::table('course_participant_bookeds')
            ->insert(
                [
                        array('id' => '1','trainer_id' => '1','mitglied_id' => NULL,'participant_id' => NULL,'kurs_id' => '1','deleted_at' => NULL,'created_at' => '2024-03-02 03:05:05','updated_at' => '2024-03-02 03:05:05'),
                        array('id' => '2','trainer_id' => '1','mitglied_id' => NULL,'participant_id' => NULL,'kurs_id' => '1','deleted_at' => NULL,'created_at' => '2024-03-02 03:06:00','updated_at' => '2024-03-02 03:06:00')
                ]);
    }
}
