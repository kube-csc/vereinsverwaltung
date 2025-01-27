<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RaceType extends Model
{
    protected $fillable = [
        'regatta_id',
        'race_type_template_id',
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
        'meldeGebuehr'
    ];
}
