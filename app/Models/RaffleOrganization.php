<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RaffleOrganization extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'rubrik_team_id', 'criteria', 'event_id'];

    protected $casts = [
        'criteria' => 'array',
    ];

    public function event()
    {
        return $this->belongsTo(Event::class, 'event_id');
    }

    public function rubrikTeam()
    {
        return $this->belongsTo(RegattaTeam::class, 'rubrik_team_id');
    }

    public function teams()
    {
        return $this->belongsToMany(RegattaTeam::class, 'raffle_organization_teams', 'organization_id', 'team_id')->withTimestamps();
    }
}
