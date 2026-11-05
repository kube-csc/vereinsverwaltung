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
    public const STATUS_QUESTION = 'rueckfrage';
    public const STATUS_APPROVED = 'bewilligt';
    public const STATUS_REJECTED = 'abgelehnt';

    public const EVENT_SUBMITTED = 'eingereicht';
    public const EVENT_REVIEW_STARTED = 'pruefung_gestartet';
    public const EVENT_QUESTION = 'rueckfrage';
    public const EVENT_ANSWER = 'antwort';
    public const EVENT_APPROVED = 'bewilligt';
    public const EVENT_REJECTED = 'abgelehnt';
    public const EVENT_ARCHIVED = 'archiviert';
    public const EVENT_SETTLED = 'endabrechnung';
    public const EVENT_SETTLEMENT_REVOKED = 'endabrechnung_zurueckgenommen';

    public const EVENT_PAYOUT_EXPORTED = 'ueberweisung_exportiert';
    public const EVENT_BANK_CHANGED = 'bankdaten_geaendert';

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
        'approved_amount',
        'final_amount',
        'payout_exported_at',
        'payout_exported_by',
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
        'payout_exported_at' => 'datetime',
        'approved_amount' => 'decimal:2',
        'final_amount' => 'decimal:2',
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

    public function events(): HasMany
    {
        return $this->hasMany(ApplicationEvent::class)->orderBy('created_at')->orderBy('id');
    }

    /** Bankdaten für die Auszahlung: fixierter Stand oder, falls keiner vorhanden ist, das einzige Konto des Antragstellers. */
    public function payoutAccountData(): ?array
    {
        if (filled($this->bank_account_snapshot['iban'] ?? null)) {
            return $this->bank_account_snapshot;
        }

        $accounts = $this->user?->bankAccounts()->limit(2)->get();
        if ($accounts === null || $accounts->count() !== 1) {
            return null;
        }

        return [
            'kontoinhaber' => $accounts[0]->kontoinhaber,
            'bank_name' => $accounts[0]->bank_name,
            'iban' => $accounts[0]->iban,
            'bic' => $accounts[0]->bic,
        ];
    }

    public function isDraft(): bool
    {
        return $this->status === self::STATUS_DRAFT;
    }

    /**
     * Gibt die eingetragenen Antragsdaten als formatierte Zeilen zurück.
     * Jedes Feld wird als „Label: Wert" ausgegeben.
     * Wenn form_snapshot die Labels enthält, werden diese verwendet, sonst die field keys.
     */
    public function formattedAnswers(): array
    {
        $answers = $this->answers ?? [];
        if (empty($answers)) {
            return [];
        }

        $snapshot = $this->form_snapshot ?? [];
        $labels = [];
        foreach ($snapshot as $field) {
            if (isset($field['key']) && isset($field['label'])) {
                $labels[$field['key']] = $field['label'];
            }
        }

        $formatted = [];
        foreach ($answers as $key => $value) {
            if ($value === null || $value === '') {
                continue;
            }

            $label = $labels[$key] ?? $key;

            // Checkbox-Werte lesbar machen
            if (is_bool($value)) {
                $formatted[] = $label . ': ' . ($value ? 'Ja' : 'Nein');
            } elseif (is_array($value)) {
                $formatted[] = $label . ': ' . implode(', ', array_filter($value, fn ($v) => $v !== '' && $v !== null));
            } else {
                $formatted[] = $label . ': ' . $value;
            }
        }

        return $formatted;
    }

    /**
     * Gibt die Antragsdaten als einzelner Textblock mit Zeilenumbrüchen zurück (für Platzhalter).
     */
    public function formattedAnswersText(): string
    {
        $lines = $this->formattedAnswers();
        if (empty($lines)) {
            return '';
        }

        return implode("\n", $lines);
    }
}
