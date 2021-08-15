<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        // \App\Models\User::factory(1)->create();
        $this->call(TeamSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(InstructionSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(SportSectionSeeder::class);
    }
}
