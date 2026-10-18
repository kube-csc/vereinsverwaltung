<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Role extends Model
{
    public const ADMINISTRATOR = 'administrator';
    public const FOERDERTOPFVERWALTER = 'foerdertopfverwalter';
    public const GREMIUM = 'gremium';
    public const ANTRAGSTELLER = 'antragsteller';

    protected $fillable = ['name', 'label'];

    public function users(): BelongsToMany
    {
        return $this->belongsToMany(User::class)->withTimestamps();
    }

    public function permissions(): BelongsToMany
    {
        return $this->belongsToMany(Permission::class)->withTimestamps();
    }
}
