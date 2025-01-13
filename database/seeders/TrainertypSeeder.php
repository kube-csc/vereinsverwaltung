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
        DB::table('trainertyps')->delete();

        DB::table('trainertyps')
            ->insert(
                [
                  array('id' => '1','trainerfunktion' => 'kein Trainer','status' => '0'),
                  array('id' => '2','trainerfunktion' => 'Test-Trainer','status' => '1'),
                  array('id' => '3','trainerfunktion' => 'Drachenboot','status' => '1'),
                  array('id' => '4','trainerfunktion' => 'SUP','status' => '1'),
                  array('id' => '5','trainerfunktion' => 'Ferienspass','status' => '1')
                ]);
    }
}
