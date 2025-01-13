<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CourseParticipantSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('course_participants')->delete();

        DB::table('course_participants')
            ->insert(
                [
                  array(
                      'id' => '1',
                      'nachname'     => 'Nachname Kursteilnehmer SUP',
                      'vorname'      => 'Vorname Kursteilnehmer',
                      'name'         => 'Nachname Kursteilnehmer SUP, Vorname Kursteilnehmer',
                      'organiser_id' => '1',
                      'password'     => '$2y$10$KjSWWuLzgumtluwPbs1/S.jht7Hm79U11B9b3tVVhTx2o0N7gGwt2', //password
                      'email'        => 'kurs1@test.de',
                      'telefon'      => '123456789',
                      'nachricht'    => '0',
                      'status'       => '1',
                      'created_at'   => '2020-08-16'
                  )
                ]);
    }
}
