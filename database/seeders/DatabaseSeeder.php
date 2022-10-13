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
       $this->call(BoardPortraitSeeder::class);
       $this->call(DocumentSeeder::class);
       $this->call(ReportSeeder::class);
       $this->call(BacklinksSeeder::class);
       $this->call(TabeleSeeder::class);
       $this->call(RaceSeeder::class);
       $this->call(RegattaInformationSeeder::class);
       $this->call(SporttypeSeeder::class);
       $this->call(ClubSeeder::class);
    }
}
