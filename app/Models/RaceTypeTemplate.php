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
        'max_wertungsteilnehmer',
        'weiblichmin',
        'weiblichmax',
        'manmin',
        'manmax',
        'training',
        'max_trainingstermine',
        'training_preis',
        'bahnen',
        'zusatzmanschaft',
        'beschreibung',
        'distanz',
        'meldeGebuehr',
        'autor_id',
        'bearbeiter_id'
    ];
}
