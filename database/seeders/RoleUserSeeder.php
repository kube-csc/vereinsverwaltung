<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Database\Seeder;

class AdministratorSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::find(1);

        if (! $user) {
            $this->command?->warn('Benutzer mit ID 1 nicht gefunden.');
            return;
        }

        Role::firstOrCreate(
            ['name' => Role::ADMINISTRATOR],
            ['label' => 'Administrator']
        );

        $user->assignRole(Role::ADMINISTRATOR);
        $this->command?->info('Benutzer 1 hat jetzt die Rolle Administrator.');
    }
}
