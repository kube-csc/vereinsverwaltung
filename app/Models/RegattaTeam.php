<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RegattaTeam extends Model
{
    protected $fillable = [
        'regatta_id',
        'mannschaft_id',
        'platz',
        'punkte',
        'zeit',
        'rennen_id',
        'tabele_id',
        'rennenvor_id',
        'tabelevor_id',
        'platzvor',
        'bahn',
        'autor_id',
        'bearbeiter_id'
    ];

    public function teamWertungsGruppe()
    {
        return $this->belongsTo(RaceType::class, 'gruppe_id');
    }

}
