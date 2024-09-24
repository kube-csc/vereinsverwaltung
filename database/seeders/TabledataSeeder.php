<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TabledataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('tabledatas')->delete();

        DB::table('tabledatas')
            ->insert([
            array('id' => '1', 'regatta_id' => '8', 'tabele_id' => '5', 'mannschaft_id' => '1', 'zeit' => '00:00:00', 'hundert' => '0', 'punkte' => '4', 'rennanzahl' => '1', 'zeitpunktegleich' => '00:00:00', 'hundertpunktegleich' => '0','autor_id' => '1','bearbeiter_id' => '1','created_at' => '2024-08-18 13:06:42','updated_at' => '2024-08-18 13:06:42'),
            array('id' => '2', 'regatta_id' => '8', 'tabele_id' => '5', 'mannschaft_id' => '2', 'zeit' => '00:00:00', 'hundert' => '0', 'punkte' => '3', 'rennanzahl' => '1', 'zeitpunktegleich' => '00:00:00', 'hundertpunktegleich' => '0','autor_id' => '1','bearbeiter_id' => '1','created_at' => '2024-08-18 13:06:42','updated_at' => '2024-08-18 13:06:42')
            ]);
    }
}
