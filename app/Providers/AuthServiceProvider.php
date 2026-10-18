<?php

namespace App\Providers;

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

        Gate::define('antraege.verwalten', fn (User $user) => $user->hasRole(\App\Models\Role::ADMINISTRATOR));
        Gate::define('antraege.rechte_verwalten', fn (User $user) => $user->hasRole(\App\Models\Role::ADMINISTRATOR));
        Gate::define('antraege.foerdermittel_verwalten', fn (User $user) => $user->hasPermission('antraege.foerdermittel_verwalten'));
        Gate::define('antraege.pruefen', fn (User $user) => $user->hasPermission('antraege.pruefen'));
        Gate::define('antraege.entscheiden', fn (User $user) => $user->hasPermission('antraege.entscheiden'));
        Gate::define('antraege.antraege_loeschen', fn (User $user) => $user->hasPermission('antraege.antraege_loeschen'));
        Gate::define('antraege.pruefbereich', fn (User $user) => $user->can('antraege.pruefen')
            || $user->can('antraege.antraege_loeschen'));
        Gate::define('antraege.stellen', fn (User $user) => $user->hasPermission('antraege.stellen'));
        // TODO: Keep this legacy dashboard entry until all existing admin users are migrated to the new roles.
        Gate::define('antraege.dashboard', fn (User $user) => $user->admin >= 4
            || $user->can('antraege.foerdermittel_verwalten')
            || $user->can('antraege.pruefen')
            || $user->can('antraege.stellen'));
        Gate::define('antraege.benutzer', fn (User $user) => $user->admin >= 4
            || $user->hasRole(\App\Models\Role::ADMINISTRATOR)
            || $user->can('antraege.foerdermittel_verwalten'));
    }
}
