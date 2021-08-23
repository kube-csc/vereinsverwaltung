<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class EventGroupSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        DB::table('event_groups')->delete();

        DB::table('event_groups')
            ->insert(
                [
                    array('id' => '1','termingruppe' => 'Eventserie 1','user_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42'),
                    array('id' => '2','termingruppe' => 'Eventserie 2','user_id' => '1','created_at' => '2021-03-28 13:06:42','updated_at' => '2021-03-28 13:06:42')
                ]
            );
    }
}
