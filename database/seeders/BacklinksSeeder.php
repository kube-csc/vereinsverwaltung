<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class BacklinksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('backlinks')->delete();

        DB::table('backlinks')
            ->insert([
                array('id' => '1','ip' => '127.0.0.1','backlink' => 'www.alteseite.de','neueUrl' => 'https://www.beispiel.de','nichtgefundenDatum' => '2022-01-07 17:35:50','nichtgefundenAnzahl' => '1','weiterleitAnzahl' => '0','created_at' => '2022-01-07 17:30:05','updated_at' => '2022-01-07 17:30:05')
        ]);
    }
}
