<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Tabele extends Model
{
    use HasFactory;

    use SoftDeletes;

    protected $guarded = [];

    public function getTeamWertungsGruppe()
    {
        return $this->belongsTo(RaceType::class, 'gruppe_id');
    }
}
