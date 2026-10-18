<?php

namespace App\Providers;

use App\Models\Role;
use App\Models\Team;
use App\Models\User;
use App\Policies\TeamPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Team::class => TeamPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // TODO: Übergangsregel `admin >= 4` entfernen, sobald das neue Rollensystem vollständig eingeführt ist.
        Gate::define('antraege.verwalten', fn (User $user) => $user->admin >= 4 || $user->hasRole(Role::ADMINISTRATOR));
        Gate::define('antraege.foerdermittel_verwalten', fn (User $user) => $user->admin >= 4 || $user->hasRole(Role::ADMINISTRATOR, Role::GREMIUM));
        Gate::define('antraege.entscheiden', fn (User $user) => $user->hasRole(Role::GREMIUM));
        Gate::define('antraege.stellen', fn (User $user) => $user->hasRole(Role::ANTRAGSTELLER));
        Gate::define('antraege.dashboard', fn (User $user) => $user->can('antraege.foerdermittel_verwalten') || $user->can('antraege.stellen'));
        Gate::define('antraege.benutzer', fn (User $user) => $user->assignableRoles() !== []);
    }
}
