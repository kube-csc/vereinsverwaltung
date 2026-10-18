<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FundingProgram extends Model
{
    protected $fillable = ['name', 'beschreibung', 'aktiv'];

    protected $casts = ['aktiv' => 'boolean'];

    public function pots(): HasMany
    {
        return $this->hasMany(FundingPot::class);
    }
}
