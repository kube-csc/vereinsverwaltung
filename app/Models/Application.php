<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Application extends Model
{
    use SoftDeletes;

    public const STATUS_DRAFT = 'entwurf';
    public const STATUS_SUBMITTED = 'eingereicht';
    public const STATUS_IN_REVIEW = 'in_pruefung';
    public const STATUS_APPROVED = 'bewilligt';
    public const STATUS_REJECTED = 'abgelehnt';

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
        'reviewed_by',
        'reviewed_at',
        'review_note',
        'decided_by',
        'decided_at',
        'decision_reason',
        'deleted_by',
        'deletion_reason',
    ];

    protected $casts = [
        'bank_account_snapshot' => 'encrypted:array',
        'form_snapshot' => 'array',
        'answers' => 'array',
        'submitted_at' => 'datetime',
        'reviewed_at' => 'datetime',
        'decided_at' => 'datetime',
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
        return $this->belongsTo(User::class)->withTrashed();
    }

    public function reviewer(): BelongsTo
    {
        return $this->belongsTo(User::class, 'reviewed_by');
    }

    public function decider(): BelongsTo
    {
        return $this->belongsTo(User::class, 'decided_by');
    }

    public function deleter(): BelongsTo
    {
        return $this->belongsTo(User::class, 'deleted_by')->withTrashed();
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
