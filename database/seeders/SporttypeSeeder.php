<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class SporttypeSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('sporttypes')->delete();

        \DB::table('sporttypes')->insert(array (
            array('id' => '1','sportart' => 'Sportart 1','sportartbeschreibung' => NULL,'sportarthomepage' => NULL,'sportartlogofile' => NULL,'visible' => '1','freigeber_id' => NULL,'bearbeiter_id' => '1','user_id' => '1','letzteFreigabe' => NULL,'deleted_at' => NULL,'created_at' => '2022-10-04 23:03:18','updated_at' => '2022-10-07 01:06:39'),
            array('id' => '2','sportart' => 'Sportart 2','sportartbeschreibung' => NULL,'sportarthomepage' => NULL,'sportartlogofile' => NULL,'visible' => '1','freigeber_id' => NULL,'bearbeiter_id' => '1','user_id' => '1','letzteFreigabe' => NULL,'deleted_at' => NULL,'created_at' => '2022-10-04 23:03:36','updated_at' => '2022-10-07 01:06:54'),
            array('id' => '3','sportart' => 'Sportart 3','sportartbeschreibung' => NULL,'sportarthomepage' => NULL,'sportartlogofile' => NULL,'visible' => '1','freigeber_id' => NULL,'bearbeiter_id' => '1','user_id' => '1','letzteFreigabe' => NULL,'deleted_at' => NULL,'created_at' => '2022-10-07 01:07:46','updated_at' => '2022-10-07 01:07:46')
      ));
    }
}
