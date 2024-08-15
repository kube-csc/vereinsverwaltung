<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Race extends Model
{
    protected $guarded = [];

    public function raceTabele()
    {
        return $this->belongsTo(Tabele::class, 'tabele_id');
    }
}
