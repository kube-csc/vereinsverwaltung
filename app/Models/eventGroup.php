<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class eventGroup extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $guarded = [];

    public function events(): HasMany
    {
        return $this->hasMany(Event::class, 'eventGroup_id');
    }

}
