<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationType extends Model
{
    protected $fillable = ['name', 'slug', 'beschreibung', 'frist', 'requires_club', 'requires_funding_pot', 'aktiv'];

    protected $casts = [
        'frist' => 'date',
        'requires_club' => 'boolean',
        'requires_funding_pot' => 'boolean',
        'aktiv' => 'boolean',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(ApplicationField::class)->orderBy('sort_order')->orderBy('id');
    }

    public function fundingPots(): BelongsToMany
    {
        return $this->belongsToMany(FundingPot::class, 'application_type_funding_pot')->withTimestamps();
    }
}
