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
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];

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
