<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class CoursedateUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('coursedate_user')->delete();

        DB::table('coursedate_user')->insert(
          [
            array('id' => '1','coursedate_id' => '1','user_id' => '1','created_at' => NULL,'updated_at' => NULL),
            array('id' => '2','coursedate_id' => '2','user_id' => '1','created_at' => NULL,'updated_at' => NULL),
            array('id' => '3','coursedate_id' => '3','user_id' => '2','created_at' => NULL,'updated_at' => NULL),
            array('id' => '4','coursedate_id' => '4','user_id' => '2','created_at' => NULL,'updated_at' => NULL)
          ]);
    }
}
