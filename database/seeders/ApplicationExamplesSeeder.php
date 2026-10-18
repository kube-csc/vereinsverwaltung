<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class ApplicationExamplesSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            UebungsleiterApplicationTypeSeeder::class,
            SportgeraeteApplicationTypeSeeder::class,
            JugendprojektApplicationTypeSeeder::class,
            IntegrationApplicationTypeSeeder::class,
            QuartiersprojektApplicationTypeSeeder::class,
            WohnwagenstellplatzApplicationTypeSeeder::class,
            HallennutzungApplicationTypeSeeder::class,
            LiegeplatzApplicationTypeSeeder::class,
            VereinsbeitrittApplicationTypeSeeder::class,
            ArbeitsdienstbefreiungApplicationTypeSeeder::class,
            BootsregistrierungApplicationTypeSeeder::class,
            VeranstaltungsanmeldungApplicationTypeSeeder::class,
            ZuschussantragApplicationTypeSeeder::class,
            MaterialanforderungApplicationTypeSeeder::class,
            ReservierungApplicationTypeSeeder::class,
        ]);
    }
}
