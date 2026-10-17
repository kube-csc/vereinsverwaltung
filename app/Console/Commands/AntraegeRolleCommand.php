<?php

namespace App\Console\Commands;

use App\Models\Role;
use App\Models\User;
use Illuminate\Console\Command;

class AntraegeRolleCommand extends Command
{
    protected $signature = 'antraege:rolle {email : E-Mail-Adresse des Benutzers} {rolle : administrator, gremium oder antragsteller} {--entziehen : Rolle entziehen statt zuweisen}';

    protected $description = 'Weist einem Benutzer eine Rolle der Antragsverwaltung zu oder entzieht sie';

    public function handle(): int
    {
        $user = User::where('email', $this->argument('email'))->first();
        if (! $user) {
            $this->error('Benutzer nicht gefunden.');

            return self::FAILURE;
        }

        $roleName = $this->argument('rolle');
        if (! Role::where('name', $roleName)->exists()) {
            $this->error('Unbekannte Rolle: ' . $roleName);

            return self::FAILURE;
        }

        if ($this->option('entziehen')) {
            $user->removeRole($roleName);
            $this->info("Rolle {$roleName} wurde {$user->email} entzogen.");
        } else {
            $user->assignRole($roleName);
            $this->info("Rolle {$roleName} wurde {$user->email} zugewiesen.");
        }

        return self::SUCCESS;
    }
}
