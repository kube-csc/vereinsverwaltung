<?php

namespace App\Models;

use DomainException;
use App\Services\SepaCreditTransferXml;
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
        'beginn', 'ende', 'budget', 'bank_account_id', 'geschlossen', 'max_applications_per_user',
        'max_applications_per_club', 'settled_at', 'settled_by', 'settlement_factor', 'settlement_total',
    ];

    protected $attributes = [
        'max_applications_per_user' => null,
        'max_applications_per_club' => null,
    ];

    protected $casts = [
        'beginn' => 'date',
        'ende' => 'date',
        'budget' => 'decimal:2',
        'geschlossen' => 'boolean',
        'settled_at' => 'datetime',
        'settlement_factor' => 'decimal:6',
        'settlement_total' => 'decimal:2',
        'max_applications_per_user' => 'integer',
        'max_applications_per_club' => 'integer',
    ];

    public function program(): BelongsTo
    {
        return $this->belongsTo(FundingProgram::class, 'funding_program_id');
    }

    public function bankAccount(): BelongsTo
    {
        return $this->belongsTo(BankAccount::class);
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
        $query = $this->bindings()->whereIn('art', [
            FundingPotBinding::RESERVIERT,
            FundingPotBinding::BEWILLIGT,
        ]);
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

    public function gebunden(): float
    {
        return $this->summeInCent() / 100;
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
        return ! $this->geschlossen && ! $this->isSettled() && $this->istInLaufzeit($now) && ! $this->istErschoepft();
    }

    public function statusLabel(): string
    {
        return match (true) {
            $this->isSettled() => 'Abgerechnet',
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

            if ($art === FundingPotBinding::RESERVIERT && ! $pot->istOffen()) {
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

    public function reserveRequestedAmount(Application $application): FundingPotBinding
    {
        return DB::transaction(function () use ($application) {
            /** @var self $pot */
            $pot = static::whereKey($this->getKey())->lockForUpdate()->firstOrFail();

            $existing = $pot->bindings()
                ->where('application_id', $application->id)
                ->where('art', FundingPotBinding::RESERVIERT)
                ->lockForUpdate()
                ->first();

            if ($existing) {
                return $existing;
            }

            $requestedAmount = $application->answers['beantragter_zuschuss'] ?? null;
            if (! is_numeric($requestedAmount) || (float) $requestedAmount <= 0) {
                throw new DomainException('Für die Prüfung muss ein positiver beantragter Zuschuss angegeben sein.');
            }
            $pot->assertNotSettled();

            $cent = (int) round((float) $requestedAmount * 100);

            return $pot->bindings()->create([
                'art' => FundingPotBinding::RESERVIERT,
                'betrag' => $cent / 100,
                'application_id' => $application->id,
                'referenz' => 'Beantragter Zuschuss',
            ]);
        });
    }

    public function releaseApplicationReservation(int $applicationId): void
    {
        DB::transaction(function () use ($applicationId) {
            /** @var self $pot */
            $pot = static::whereKey($this->getKey())->lockForUpdate()->firstOrFail();
            $reservation = $pot->bindings()
                ->where('application_id', $applicationId)
                ->where('art', FundingPotBinding::RESERVIERT)
                ->lockForUpdate()
                ->first();

            $reservation?->update(['art' => FundingPotBinding::FREIGEGEBEN]);
        });
    }

    public function approveApplicationReservation(
        int $applicationId,
        float $approvedAmount,
        ?string $reference = null
    ): FundingPotBinding {
        return DB::transaction(function () use ($applicationId, $approvedAmount, $reference) {
            /** @var self $pot */
            $pot = static::whereKey($this->getKey())->lockForUpdate()->firstOrFail();
            $reservation = $pot->bindings()
                ->where('application_id', $applicationId)
                ->where('art', FundingPotBinding::RESERVIERT)
                ->lockForUpdate()
                ->first();

            $pot->assertNotSettled();

            $approvedCent = (int) round($approvedAmount * 100);
            if ($approvedCent <= 0) {
                throw new DomainException('Der Betrag muss größer als 0 sein.');
            }

            // Bewilligungen dürfen das Budget übersteigen; die Kürzung erfolgt in der Endabrechnung.
            if (! $reservation) {
                return $pot->bindings()->create([
                    'art' => FundingPotBinding::BEWILLIGT,
                    'betrag' => $approvedCent / 100,
                    'application_id' => $applicationId,
                    'referenz' => $reference,
                ]);
            }

            $reservation->update([
                'art' => FundingPotBinding::BEWILLIGT,
                'betrag' => $approvedCent / 100,
                'referenz' => $reference,
            ]);

            return $reservation;
        });
    }

    public function isSettled(): bool
    {
        return $this->settled_at !== null;
    }

    public function assertNotSettled(): void
    {
        if ($this->isSettled()) {
            throw new DomainException('Der Fördertopf wurde bereits abgerechnet.');
        }
    }

    public function endeNochNichtErreicht(?Carbon $now = null): bool
    {
        return $this->ende !== null && $this->ende->gte(($now ?? now())->startOfDay());
    }

    public function applicationsInProgressCount(): int
    {
        return $this->applications()->whereIn('status', [
            Application::STATUS_SUBMITTED,
            Application::STATUS_IN_REVIEW,
            Application::STATUS_QUESTION,
        ])->count();
    }

    /**
     * Berechnet die endgültigen Beträge aller bewilligten Anträge. Reicht das Budget nicht,
     * werden die Beträge anteilig gekürzt; Restcent gehen deterministisch an die größten Nachkommaanteile.
     *
     * @return array{rows: array<int, array{application: Application, approved_cent: int, final_cent: int}>, total_cent: int, budget_cent: int, factor: float}
     */
    public function settlementPreview(): array
    {
        $applications = $this->applications()
            ->with('applicationType', 'user')
            ->where('status', Application::STATUS_APPROVED)
            ->where('approved_amount', '>', 0)
            ->orderBy('id')
            ->get();

        $budget = $this->budgetInCent();
        $approved = $applications->mapWithKeys(
            fn (Application $application) => [$application->id => (int) round((float) $application->approved_amount * 100)]
        );
        $total = (int) $approved->sum();

        $final = $approved->all();
        if ($total > $budget) {
            $remainders = [];
            $assigned = 0;
            foreach ($approved as $id => $cent) {
                $final[$id] = intdiv($cent * $budget, $total);
                $remainders[$id] = ($cent * $budget) % $total;
                $assigned += $final[$id];
            }
            uksort($remainders, fn ($a, $b) => [$remainders[$b], $a] <=> [$remainders[$a], $b]);
            foreach (array_slice(array_keys($remainders), 0, $budget - $assigned) as $id) {
                $final[$id]++;
            }
        }

        return [
            'rows' => $applications->map(fn (Application $application) => [
                'application' => $application,
                'approved_cent' => $approved[$application->id],
                'final_cent' => $final[$application->id],
            ])->all(),
            'total_cent' => $total,
            'budget_cent' => $budget,
            'factor' => $total > $budget && $total > 0 ? $budget / $total : 1.0,
        ];
    }

    /** Führt die Endabrechnung durch und speichert die endgültigen Beträge in den Anträgen. */
    public function settle(User $user): void
    {
        DB::transaction(function () use ($user) {
            /** @var self $pot */
            $pot = static::whereKey($this->getKey())->lockForUpdate()->firstOrFail();
            $pot->assertNotSettled();

            if (! $pot->geschlossen) {
                throw new DomainException('Die Endabrechnung ist erst möglich, wenn der Fördertopf geschlossen wurde.');
            }

            if ($pot->applicationsInProgressCount() > 0) {
                throw new DomainException('Es sind noch Anträge in Bearbeitung. Die Endabrechnung ist erst nach Abschluss aller Prüfungen möglich.');
            }

            $preview = $pot->settlementPreview();
            if ($preview['rows'] === []) {
                throw new DomainException('Der Fördertopf enthält keine bewilligten Anträge.');
            }

            $suffix = sprintf(
                ' (Anteil %s %%, Topfvolumen %s €).',
                number_format($preview['factor'] * 100, 2, ',', '.'),
                number_format($preview['budget_cent'] / 100, 2, ',', '.')
            );

            foreach ($preview['rows'] as $row) {
                /** @var Application $application */
                $application = $row['application'];
                $finalAmount = $row['final_cent'] / 100;

                Application::whereKey($application->id)->update(['final_amount' => $finalAmount]);
                $pot->bindings()
                    ->where('application_id', $application->id)
                    ->where('art', FundingPotBinding::BEWILLIGT)
                    ->update(['betrag' => $finalAmount]);
                $application->events()->create([
                    'actor_id' => $user->id,
                    'actor_name' => $user->name,
                    'event_type' => Application::EVENT_SETTLED,
                    'message' => 'Endabrechnung: bewilligt '
                        . number_format($row['approved_cent'] / 100, 2, ',', '.') . ' €, endgültig '
                        . number_format($finalAmount, 2, ',', '.') . ' €' . $suffix,
                ]);
            }

            $pot->update([
                'settled_at' => now(),
                'settled_by' => $user->id,
                'settlement_factor' => $preview['factor'],
                'settlement_total' => $preview['total_cent'] / 100,
            ]);
        });
    }

    /** Auftraggeberkonto des Topfs; gibt es genau ein Vereinskonto, wird dieses automatisch genutzt. */
    public function debtorAccount(): ?BankAccount
    {
        if ($this->bankAccount) {
            return $this->bankAccount;
        }

        $accounts = BankAccount::operatorAccounts()->limit(2)->get();

        return $accounts->count() === 1 ? $accounts->first() : null;
    }

    /** Abgerechnete, noch nicht exportierte Auszahlungen, getrennt nach vorhandener und fehlender Bankverbindung. */
    public function openPayouts(): array
    {
        $applications = $this->applications()
            ->where('status', Application::STATUS_APPROVED)
            ->where('final_amount', '>', 0)
            ->whereNull('payout_exported_at')
            ->orderBy('id')
            ->get();

        [$ready, $missing] = $applications->partition(
            fn (Application $application) => $application->payoutAccountData() !== null
        );

        return ['ready' => $ready->values(), 'missing' => $missing->values()];
    }

    /** Erzeugt die SEPA-Datei für alle offenen Auszahlungen und markiert diese als exportiert. */
    public function exportPayouts(User $user, ?Carbon $executionDate = null): string
    {
        return DB::transaction(function () use ($user, $executionDate) {
            /** @var self $pot */
            $pot = static::whereKey($this->getKey())->lockForUpdate()->firstOrFail();

            if (! $pot->isSettled()) {
                throw new DomainException('Überweisungen sind erst nach der Endabrechnung möglich.');
            }

            $ready = $pot->openPayouts()['ready'];
            if ($ready->isEmpty()) {
                throw new DomainException('Es gibt keine offenen Auszahlungen mit Bankverbindung.');
            }

            $payments = $ready->map(function (Application $application) use ($pot) {
                $account = $application->payoutAccountData();

                return [
                    'end_to_end' => 'ANTRAG' . $application->id,
                    'name' => (string) ($account['kontoinhaber'] ?? ''),
                    'iban' => (string) $account['iban'],
                    'bic' => $account['bic'] ?? null,
                    'amount_cent' => (int) round((float) $application->final_amount * 100),
                    'purpose' => 'Foerderung ' . $pot->name . ' Antrag ' . $application->id,
                ];
            })->all();

            $debtor = $pot->debtorAccount();
            if (! $debtor) {
                throw new DomainException('Es ist kein Auftraggeberkonto zugeordnet. Bitte dem Fördertopf ein Auftraggeberkonto zuweisen.');
            }

            $xml = (new SepaCreditTransferXml())->build(
                (string) $debtor->kontoinhaber,
                (string) $debtor->iban,
                $debtor->bic,
                $payments,
                $executionDate ?? now()->addDay(),
                'FT' . $pot->id . '-' . now()->format('YmdHis')
            );

            foreach ($ready as $application) {
                $account = $application->payoutAccountData();
                $application->update([
                    'bank_account_snapshot' => $account,
                    'payout_exported_at' => now(),
                    'payout_exported_by' => $user->id,
                ]);
                $application->events()->create([
                    'actor_id' => $user->id,
                    'actor_name' => $user->name,
                    'event_type' => Application::EVENT_PAYOUT_EXPORTED,
                    'message' => 'Überweisung in Überweisungsdatei aufgenommen.',
                ]);
            }

            return $xml;
        });
    }

    /** Nimmt die Endabrechnung zurück: endgültige Beträge entfallen, Bindungen zeigen wieder den bewilligten Betrag. */
    public function revokeSettlement(User $user): void
    {
        DB::transaction(function () use ($user) {
            /** @var self $pot */
            $pot = static::whereKey($this->getKey())->lockForUpdate()->firstOrFail();

            if (! $pot->isSettled()) {
                throw new DomainException('Der Fördertopf wurde noch nicht abgerechnet.');
            }

            $applications = $pot->applications()->whereNotNull('final_amount')->get();
            if ($applications->contains(fn (Application $application) => $application->payout_exported_at !== null)) {
                throw new DomainException('Für diesen Fördertopf wurden bereits Überweisungen exportiert; die Endabrechnung kann nicht mehr zurückgenommen werden.');
            }
            foreach ($applications as $application) {
                $pot->bindings()
                    ->where('application_id', $application->id)
                    ->where('art', FundingPotBinding::BEWILLIGT)
                    ->update(['betrag' => $application->approved_amount]);
                Application::whereKey($application->id)->update(['final_amount' => null]);
                $application->events()->create([
                    'actor_id' => $user->id,
                    'actor_name' => $user->name,
                    'event_type' => Application::EVENT_SETTLEMENT_REVOKED,
                    'message' => 'Endabrechnung zurückgenommen; der endgültige Betrag ist für Antragsteller nicht mehr sichtbar.',
                ]);
            }

            $pot->update([
                'settled_at' => null,
                'settled_by' => null,
                'settlement_factor' => null,
                'settlement_total' => null,
            ]);
        });
    }
}
