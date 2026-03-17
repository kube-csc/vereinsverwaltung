<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Trainertable extends Model
{
    use SoftDeletes;

    protected $table = 'trainertables';

    protected $fillable = [
        'user_id',
        'trainertyp_id',
        'sportSection_id',
        'organiser_id',
        'status',
        'sichtbar',
        'autor_id',
        'bearbeiter_id',
    ];

    protected $casts = [
        'status' => 'boolean',
        'sichtbar' => 'boolean',
        'deleted_at' => 'datetime',
    ];

    public function scopeActive(Builder $query): Builder
    {
        return $query->where('status', 1)->whereNull('deleted_at');
    }

    public function scopePublicVisible(Builder $query): Builder
    {
        return $query->active()->where('sichtbar', 1);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function trainertyp()
    {
        return $this->belongsTo(Trainertyp::class, 'trainertyp_id');
    }

    public function organiser(): BelongsTo
    {
        return $this->belongsTo(Organiser::class, 'organiser_id');
    }

    public function sportSection(): BelongsTo
    {
        return $this->belongsTo(SportSection::class, 'sportSection_id');
    }
}

