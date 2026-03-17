<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Organiser extends Model
{
    use SoftDeletes;

    protected $table = 'organisers';

    protected $fillable = [
        'veranstaltung',
        'veranstaltungDomain',
        'veranstaltungHeader',
        'sportartUeberschrift',
        'materialUeberschrift',
        'trainerUeberschrift',
        'kurseUeberschrift',
        'bearbeiter_id',
        'autor_id',
    ];

    protected $casts = [
        'deleted_at' => 'datetime',
    ];
}

