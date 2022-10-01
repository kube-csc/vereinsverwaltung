<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ClubSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        \DB::table('clubs')->delete();

        \DB::table('clubs')->insert(array (
            array('id' => '1','clubname' => 'Verband 1','clubemail' => NULL,'clubtelefon' => NULL,'clubhomepage' => NULL,'clublogofile' => NULL,'visible' => '1','freigeber_id' => NULL,'bearbeiter_id' => '1','user_id' => '1','letzteFreigabe' => NULL,'deleted_at' => NULL,'created_at' => '2022-09-28 00:40:07','updated_at' => '2022-09-28 00:52:31'),
            array('id' => '2','clubname' => 'Verband 2','clubemail' => NULL,'clubtelefon' => NULL,'clubhomepage' => NULL,'clublogofile' => NULL,'visible' => '1','freigeber_id' => NULL,'bearbeiter_id' => '1','user_id' => '1','letzteFreigabe' => NULL,'deleted_at' => NULL,'created_at' => '2022-09-29 23:32:01','updated_at' => '2022-09-30 00:34:06'),
            array('id' => '3','clubname' => 'Verband 3','clubemail' => NULL,'clubtelefon' => NULL,'clubhomepage' => NULL,'clublogofile' => NULL,'visible' => '1','freigeber_id' => NULL,'bearbeiter_id' => '1','user_id' => '1','letzteFreigabe' => NULL,'deleted_at' => NULL,'created_at' => '2022-09-30 00:34:03','updated_at' => '2022-09-30 00:34:03')
        ));
    }
}
