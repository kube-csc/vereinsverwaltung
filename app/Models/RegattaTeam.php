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

    public function raffleOrganizations()
    {
        return $this->belongsToMany(RaffleOrganization::class, 'raffle_organization_teams', 'team_id', 'organization_id')->withTimestamps();
    }

    public function teamWertungsGruppe()
    {
        return $this->belongsTo(RaceType::class, 'gruppe_id');
    }

    public function regatta()
    {
        return $this->belongsTo(Event::class, 'regatta_id');
    }

}
