<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FundingPotBinding extends Model
{
    public const RESERVIERT = 'reserviert';
    public const BEWILLIGT = 'bewilligt';

    protected $fillable = ['funding_pot_id', 'art', 'betrag', 'application_id', 'referenz'];

    protected $casts = ['betrag' => 'decimal:2'];

    public function pot(): BelongsTo
    {
        return $this->belongsTo(FundingPot::class, 'funding_pot_id');
    }
}
