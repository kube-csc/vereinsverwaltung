<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
      DB::table('users')->delete();

      DB::table('users')
       ->insert(
         [
          array('id' => '1',
                'nachname' => 'Nachname 1',
                'vorname' => 'Vorname 1',
                'geschlecht' => 'm',
                'name' => 'Admin',
                'email' => 'info@info.de',
                'email_verified_at' => NULL,
                'password' => '$2y$10$KjSWWuLzgumtluwPbs1/S.jht7Hm79U11B9b3tVVhTx2o0N7gGwt2', //password
                'two_factor_secret' => NULL,
                'two_factor_recovery_codes' => NULL,
                'remember_token' => NULL,
                'current_team_id' => '1',
                'profile_photo_path' => NULL,
                'created_at' => '2021-04-12 19:13:55',
                'updated_at' => '2021-04-12 19:13:55'),

          array('id' => '2',
                'nachname' => 'Nachname 2',
                'vorname' => 'Vorname 2',
                'geschlecht' => 'w',
                'name' => 'Admin',
                'email' => 'info2@info.de',
                'email_verified_at' => NULL,
                'password' => '$2y$10$KjSWWuLzgumtluwPbs1/S.jht7Hm79U11B9b3tVVhTx2o0N7gGwt2', //password
                'two_factor_secret' => NULL,
                'two_factor_recovery_codes' => NULL,
                'remember_token' => NULL,
                'current_team_id' => '1',
                'profile_photo_path' => NULL,
                'created_at' => '2021-04-12 19:13:55',
                'updated_at' => '2021-04-12 19:13:55'),
        ]);
    }
}
