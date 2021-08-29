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
        $this->call(TeamSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(InstructionSeeder::class);
        $this->call(SportSectionSeeder::class);
        $this->call(EventGroupSeeder::class);
        $this->call(EventSeeder::class);
        $this->call(BoardSeeder::class);
        $this->call(BoardUserSeeder::class);
    }
}
