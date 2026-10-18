<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ApplicationType extends Model
{
    protected $fillable = ['name', 'slug', 'beschreibung', 'frist', 'requires_club', 'aktiv'];

    protected $casts = [
        'frist' => 'date',
        'requires_club' => 'boolean',
        'aktiv' => 'boolean',
    ];

    public function fields(): HasMany
    {
        return $this->hasMany(ApplicationField::class)->orderBy('sort_order')->orderBy('id');
    }
}
