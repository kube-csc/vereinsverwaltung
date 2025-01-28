<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceTypeTemplate extends Model
{
    protected $fillable = [
        'regatta_id',
        'typ',
        'altervon',
        'alterbis',
        'min',
        'max',
        'weiblichmin',
        'weiblichmax',
        'manmin',
        'manmax',
        'training',
        'bahnen',
        'zusatzmanschaft',
        'beschreibung',
        'distanz',
        'meldeGebuehr',
        'autor_id',
        'bearbeiter_id'
    ];
}
