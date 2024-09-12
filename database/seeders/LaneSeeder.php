<?php

namespace Database\Seeders;

use Carbon\Carbon;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class LaneSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('lanes')->delete();

        $lanes = array(
            array('ID' => '1','regatta_id' => '7','rennen_id' => '1','tabele_id' => '1','mannschaft_id' => '1','zeit' => '00:00:00','hundert' => '0','punkte' => '0','platz' => '0','rennenvor_id' => '0','tabelevor_id' => '0','platzvor' => '0','bahn' => '0','autor_id' => '1','bearbeiter_id' => '1','created_at' => '2024-07-28 00:00:00','updated_at' => '2024-07-28 12:00:00'),
            array('ID' => '2','regatta_id' => '7','rennen_id' => '1','tabele_id' => '1','mannschaft_id' => '2','zeit' => '00:00:00','hundert' => '0','punkte' => '0','platz' => '0','rennenvor_id' => '0','tabelevor_id' => '0','platzvor' => '0','bahn' => '0','autor_id' => '1','bearbeiter_id' => '1','created_at' => '2024-07-28 00:00:00','updated_at' => '2024-07-28 12:00:00'),
        );

        DB::table('lanes')->insert($lanes);
    }
}
