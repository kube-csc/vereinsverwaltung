<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RafflePlan extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_id',
        'version_name',
        'start_time',
        'interval',
        'min_pause',
        'final_pause',
        'final_start_time',
        'award_ceremony_time',
        'min_award_pause',
        'heat_count',
        'params',
        'is_active',
        'is_draft',
        'user_id'
    ];

    protected $casts = [
        'params' => 'array',
        'is_active' => 'boolean',
        'is_draft' => 'boolean'
    ];

    public function event()
    {
        return $this->belongsTo(Event::class);
    }

    public function items()
    {
        return $this->hasMany(RafflePlanItem::class);
    }
}
