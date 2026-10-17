<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Support\Facades\Gate;
use Illuminate\Database\Eloquent\Relations\HasMany;

//Ergänzt
use Illuminate\Database\Eloquent\SoftDeletes;

class User extends Authenticatable
{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use HasTeams;
    use Notifiable;
    use TwoFactorAuthenticatable;

//Ergänzt
    use SoftDeletes;


    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'vorname', 'nachname', 'geschlecht', 'admin', 'sportSections_id',
                'vereinseintritt', 'vereinsaustritt',
            ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'vereinseintritt' => 'date:Y-m-d',
        'vereinsaustritt' => 'date:Y-m-d',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class)->withTimestamps();
    }

    public function hasRole(string ...$roles): bool
    {
        return $this->roles->pluck('name')->intersect($roles)->isNotEmpty();
    }

    public function assignRole(string $role): void
    {
        $this->roles()->syncWithoutDetaching([Role::where('name', $role)->firstOrFail()->id]);
        $this->unsetRelation('roles');
    }

    public function removeRole(string $role): void
    {
        $this->roles()->detach(Role::where('name', $role)->firstOrFail()->id);
        $this->unsetRelation('roles');
    }

    /**
     * Rollen, die dieser Benutzer anderen Konten zuweisen darf:
     * Administratoren alle, Gremiumsmitglieder nur „Antragsteller“.
     */
    public function assignableRoles(): array
    {
        if (Gate::forUser($this)->allows('antraege.verwalten')) {
            return [Role::ADMINISTRATOR, Role::GREMIUM, Role::ANTRAGSTELLER];
        }

        return $this->hasRole(Role::GREMIUM) ? [Role::ANTRAGSTELLER] : [];
    }

    public function bankAccounts(): HasMany
    {
        return $this->hasMany(BankAccount::class);
    }

    public function trainerZuordnungen(): HasMany
    {
        return $this->hasMany(Trainertable::class, 'user_id');
    }

    /**
     * Aktive Trainerfunktionen (Soft-Deleted Zuordnungen werden ausgefiltert).
     */
    public function trainertyps(): BelongsToMany
    {
        return $this->belongsToMany(Trainertyp::class, 'trainertables', 'user_id', 'trainertyp_id')
            ->withPivot(['id', 'sportSection_id', 'organiser_id', 'status', 'sichtbar', 'autor_id', 'bearbeiter_id', 'deleted_at', 'created_at', 'updated_at'])
            ->wherePivotNull('deleted_at');
    }
}
