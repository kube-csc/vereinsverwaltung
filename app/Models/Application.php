<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Application extends Model
{
    public const STATUS_DRAFT = 'entwurf';
    public const STATUS_SUBMITTED = 'eingereicht';

    protected $fillable = [
        'application_type_id',
        'funding_pot_id',
        'user_id',
        'club_id',
        'bank_account_id',
        'bank_account_snapshot',
        'form_snapshot',
        'answers',
        'status',
        'submitted_at',
    ];

    protected $casts = [
        'bank_account_snapshot' => 'encrypted:array',
        'form_snapshot' => 'array',
        'answers' => 'array',
        'submitted_at' => 'datetime',
    ];

    public function applicationType(): BelongsTo
    {
        return $this->belongsTo(ApplicationType::class);
    }

    public function fundingPot(): BelongsTo
    {
        return $this->belongsTo(FundingPot::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function club(): BelongsTo
    {
        return $this->belongsTo(Club::class)->withTrashed();
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
    }

    public function documents(): HasMany
    {
        return $this->hasMany(ApplicationDocument::class);
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }
}
