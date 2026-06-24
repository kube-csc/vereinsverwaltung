<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaceType extends Model
{
    use HasFactory;

    protected $fillable = [
        'regatta_id',
        'race_type_template_id',
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

    public function raceTypeTemplate()
    {
        return $this->belongsTo(RaceTypeTemplate::class, 'race_type_template_id');
    }
}
