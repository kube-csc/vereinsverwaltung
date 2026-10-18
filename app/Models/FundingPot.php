<?php

namespace App\Models;

use DomainException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class FundingPot extends Model
{
    public const BEFRISTET = 'befristet';
    public const FORTLAUFEND = 'fortlaufend';

    protected $fillable = [
        'funding_program_id', 'name', 'beschreibung', 'jahr', 'laufzeit',
        'beginn', 'ende', 'budget', 'geschlossen', 'max_applications_per_user',
        'max_applications_per_club',
    ];

    protected $attributes = [
        'max_applications_per_user' => 1,
    ];

    protected $casts = [
        'beginn' => 'date',
        'ende' => 'date',
        'budget' => 'decimal:2',
        'geschlossen' => 'boolean',
        'max_applications_per_user' => 'integer',
        'max_applications_per_club' => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(FundingProgram::class, 'funding_program_id');
    }

    public function bindings(): HasMany
    {
        return $this->hasMany(FundingPotBinding::class);
    }

    public function applicationTypes(): BelongsToMany
    {
        return $this->belongsToMany(ApplicationType::class, 'application_type_funding_pot')->withTimestamps();
    }

    public function applications(): HasMany
    {
        return $this->hasMany(Application::class);
    }

    private function summeInCent(?string $art = null): int
    {
        $query = $this->bindings();
        if ($art !== null) {
            $query->where('art', $art);
        }

        return (int) round((float) $query->sum('betrag') * 100);
    }

    public function budgetInCent(): int
    {
        return (int) round((float) $this->budget * 100);
    }

    public function reserviert(): float
    {
        return $this->summeInCent(FundingPotBinding::RESERVIERT) / 100;
    }

    public function bewilligt(): float
    {
        return $this->summeInCent(FundingPotBinding::BEWILLIGT) / 100;
    }

    public function verfuegbar(): float
    {
        return ($this->budgetInCent() - $this->summeInCent()) / 100;
    }

    public function istInLaufzeit(?Carbon $now = null): bool
    {
        if ($this->laufzeit === self::FORTLAUFEND) {
            return true;
        }

        $heute = ($now ?? now())->startOfDay();

        return ($this->beginn === null || $this->beginn->lte($heute))
            && ($this->ende === null || $this->ende->gte($heute));
    }

    public function istErschoepft(): bool
    {
        return $this->budgetInCent() - $this->summeInCent() <= 0;
    }

    /** Anträge sind nur möglich, wenn der Topf offen, in der Laufzeit und nicht erschöpft ist. */
    public function istOffen(?Carbon $now = null): bool
    {
        return ! $this->geschlossen && $this->istInLaufzeit($now) && ! $this->istErschoepft();
    }

    public function statusLabel(): string
    {
        return match (true) {
            $this->geschlossen => 'Geschlossen',
            $this->istErschoepft() => 'Erschöpft',
            ! $this->istInLaufzeit() => 'Außerhalb der Laufzeit',
            default => 'Offen',
        };
    }

    /**
     * Bindet einen Betrag (reserviert/bewilligt) am Budget. Die Prüfung läuft in einer
     * Transaktion mit Zeilensperre, damit parallele Anträge das Budget nicht überziehen.
     */
    public function bind(string $art, float $betrag, ?int $applicationId = null, ?string $referenz = null): FundingPotBinding
    {
        if (! in_array($art, [FundingPotBinding::RESERVIERT, FundingPotBinding::BEWILLIGT], true)) {
            throw new DomainException('Unbekannte Bindungsart.');
        }
        $cent = (int) round($betrag * 100);
        if ($cent <= 0) {
            throw new DomainException('Der Betrag muss größer als 0 sein.');
        }

        return DB::transaction(function () use ($art, $cent, $applicationId, $referenz) {
            /** @var self $pot */
            $pot = static::whereKey($this->getKey())->lockForUpdate()->firstOrFail();

            if (! $pot->istOffen()) {
                throw new DomainException('Der Fördertopf ist nicht offen.');
            }
            if ($cent > $pot->budgetInCent() - $pot->summeInCent()) {
                throw new DomainException('Das verfügbare Budget reicht nicht aus.');
            }

            return $pot->bindings()->create([
                'art' => $art,
                'betrag' => $cent / 100,
                'application_id' => $applicationId,
                'referenz' => $referenz,
            ]);
        });
    }
}
