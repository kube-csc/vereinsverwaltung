<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RafflePlanItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'raffle_plan_id',
        'race_number',
        'gruppe_id',
        'time',
        'lane',
        'team_id',
        'is_final',
        'final_type',
        'placeholder_name',
        'heat_index',
        'level',
        'pause_minutes',
        'conflicts',
        'org_intervals'
    ];

    protected $casts = [
        'is_final' => 'boolean',
        'org_intervals' => 'array'
    ];

    public function plan()
    {
        return $this->belongsTo(RafflePlan::class, 'raffle_plan_id');
    }

    public function team()
    {
        return $this->belongsTo(RegattaTeam::class, 'team_id');
    }

    public function gruppe()
    {
        return $this->belongsTo(Tabele::class, 'gruppe_id');
    }
}
