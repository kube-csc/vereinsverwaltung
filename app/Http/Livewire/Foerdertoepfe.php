<?php

namespace App\Http\Livewire;

use App\Models\FundingPot;
use App\Models\FundingProgram;
use App\Models\ApplicationType;
use App\Models\BankAccount;
use DomainException;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Foerdertoepfe extends Component
{
    public ?int $programId = null;
    public string $programName = '';
    public string $programBeschreibung = '';
    public bool $programAktiv = true;

    public ?int $potId = null;
    public ?int $potProgramId = null;
    public string $potName = '';
    public string $potBeschreibung = '';
    public string $potJahr = '';
    public string $potLaufzeit = FundingPot::BEFRISTET;
    public string $potBeginn = '';
    public string $potEnde = '';
    public string $potBudget = '';
    public string $potMaxApplicationsPerUser = '';
    public string $potMaxApplicationsPerClub = '';
    public array $potApplicationTypeIds = [];
    public string $potBankAccountId = '';

    public ?int $accountId = null;
    public string $accountBezeichnung = '';
    public string $accountKontoinhaber = '';
    public string $accountBankName = '';
    public string $accountIban = '';
    public string $accountBic = '';

    public function mount(): void
    {
        $this->authorizeVerwalten();
    }

    private function authorizeVerwalten(): void
    {
        abort_unless(Gate::allows('antraege.foerdermittel_verwalten'), 403);
    }

    /** Akzeptiert „1.500,50“ und „1500.50“. */
    private function normalizeBetrag(string $value): string
    {
        $value = trim($value);

        return str_contains($value, ',')
            ? str_replace(',', '.', str_replace('.', '', $value))
            : $value;
    }

    public function saveProgram(): void
    {
        $this->authorizeVerwalten();

        $data = $this->validate([
            'programName' => ['required', 'string', 'max:255'],
            'programBeschreibung' => ['nullable', 'string', 'max:2000'],
            'programAktiv' => ['boolean'],
        ], [], ['programName' => 'Name']);

        $attributes = [
            'name' => $data['programName'],
            'beschreibung' => $data['programBeschreibung'] ?: null,
            'aktiv' => $data['programAktiv'],
        ];

        $this->programId
            ? FundingProgram::findOrFail($this->programId)->update($attributes)
            : FundingProgram::create($attributes);

        $this->cancelProgram();
        session()->flash('message', 'Förderprogramm gespeichert.');
    }

    public function editProgram(int $id): void
    {
        $this->authorizeVerwalten();
        $program = FundingProgram::findOrFail($id);

        $this->resetValidation();
        $this->programId = $program->id;
        $this->programName = $program->name;
        $this->programBeschreibung = (string) $program->beschreibung;
        $this->programAktiv = $program->aktiv;
    }

    public function cancelProgram(): void
    {
        $this->reset(['programId', 'programName', 'programBeschreibung']);
        $this->programAktiv = true;
        $this->resetValidation();
    }

    public function deleteProgram(int $id): void
    {
        $this->authorizeVerwalten();
        $program = FundingProgram::findOrFail($id);

        if ($program->pots()->exists()) {
            session()->flash('error', 'Das Programm enthält Fördertöpfe und kann nicht gelöscht werden. Deaktivieren Sie es stattdessen.');
            return;
        }

        $program->delete();
        $this->cancelProgram();
    }

    public function savePot(): void
    {
        $this->authorizeVerwalten();
        $this->potBudget = $this->normalizeBetrag($this->potBudget);

        $data = $this->validate([
            'potProgramId' => ['required', 'integer', 'exists:funding_programs,id'],
            'potName' => ['required', 'string', 'max:255'],
            'potBeschreibung' => ['nullable', 'string', 'max:2000'],
            'potJahr' => ['nullable', 'integer', 'between:2000,2100'],
            'potLaufzeit' => ['required', 'in:' . FundingPot::BEFRISTET . ',' . FundingPot::FORTLAUFEND],
            'potBeginn' => ['nullable', 'date', 'required_if:potLaufzeit,' . FundingPot::BEFRISTET],
            'potEnde' => ['nullable', 'date', 'required_if:potLaufzeit,' . FundingPot::BEFRISTET, 'after_or_equal:potBeginn'],
            'potBudget' => ['required', 'numeric', 'min:0', 'max:9999999999'],
            'potMaxApplicationsPerUser' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'potMaxApplicationsPerClub' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'potApplicationTypeIds' => ['array'],
            'potApplicationTypeIds.*' => ['integer', 'distinct', 'exists:application_types,id'],
            'potBankAccountId' => ['nullable', 'integer', Rule::exists('bank_accounts', 'id')
                ->where(fn ($query) => $query->whereNull('user_id')->whereNull('deleted_at'))],
        ], [], [
            'potBankAccountId' => 'Auftraggeberkonto',
            'potProgramId' => 'Förderprogramm', 'potName' => 'Name', 'potJahr' => 'Jahr',
            'potBeginn' => 'Beginn', 'potEnde' => 'Frist', 'potBudget' => 'Budget',
        ]);

        $fortlaufend = $data['potLaufzeit'] === FundingPot::FORTLAUFEND;
        $requiresClub = ApplicationType::whereIn('id', array_map('intval', $data['potApplicationTypeIds']))
            ->where('requires_club', false)
            ->exists();

        if (filled($data['potMaxApplicationsPerClub'])
            && (empty($data['potApplicationTypeIds']) || $requiresClub)) {
            $this->addError('potMaxApplicationsPerClub', 'Eine Höchstzahl je Verein ist nur möglich, wenn alle zugeordneten Antragsarten eine Vereinszuordnung verlangen.');
            return;
        }

        $attributes = [
            'funding_program_id' => $data['potProgramId'],
            'name' => $data['potName'],
            'beschreibung' => $data['potBeschreibung'] ?: null,
            'jahr' => $data['potJahr'] ?: null,
            'laufzeit' => $data['potLaufzeit'],
            'beginn' => $fortlaufend ? null : $data['potBeginn'],
            'ende' => $fortlaufend ? null : $data['potEnde'],
            'budget' => $data['potBudget'],
            'max_applications_per_user' => filled($data['potMaxApplicationsPerUser'])
                ? (int) $data['potMaxApplicationsPerUser']
                : null,
            'max_applications_per_club' => $data['potMaxApplicationsPerClub'] ?: null,
            'bank_account_id' => filled($data['potBankAccountId']) ? (int) $data['potBankAccountId'] : null,
        ];

        if ($this->potId) {
            $pot = FundingPot::findOrFail($this->potId);
            $gebunden = $pot->budgetInCent() - (int) round($pot->verfuegbar() * 100);
            if ((int) round($data['potBudget'] * 100) < $gebunden) {
                $this->addError('potBudget', 'Das Budget darf nicht unter dem bereits gebundenen Betrag liegen.');
                return;
            }
            $pot->update($attributes);
        } else {
            $pot = FundingPot::create($attributes);
        }

        $pot->applicationTypes()->sync(array_map('intval', $data['potApplicationTypeIds']));
        $this->cancelPot();
        session()->flash('message', 'Fördertopf und Zuordnung zu Antragsarten gespeichert.');
    }

    public function editPot(int $id): void
    {
        $this->authorizeVerwalten();
        $pot = FundingPot::findOrFail($id);

        $this->resetValidation();
        $this->potId = $pot->id;
        $this->potProgramId = $pot->funding_program_id;
        $this->potName = $pot->name;
        $this->potBeschreibung = (string) $pot->beschreibung;
        $this->potJahr = (string) $pot->jahr;
        $this->potLaufzeit = $pot->laufzeit;
        $this->potBeginn = $pot->beginn?->format('Y-m-d') ?? '';
        $this->potEnde = $pot->ende?->format('Y-m-d') ?? '';
        $this->potBudget = (string) $pot->budget;
        $this->potMaxApplicationsPerUser = (string) $pot->max_applications_per_user;
        $this->potMaxApplicationsPerClub = (string) $pot->max_applications_per_club;
        $this->potApplicationTypeIds = $pot->applicationTypes()->pluck('application_types.id')->map(fn ($id) => (string) $id)->all();
        $this->potBankAccountId = (string) $pot->bank_account_id;
    }

    public function newPot(int $programId): void
    {
        $this->cancelPot();
        $this->potProgramId = $programId;
    }

    public function cancelPot(): void
    {
        $this->reset([
            'potId', 'potProgramId', 'potName', 'potBeschreibung', 'potJahr',
            'potBeginn', 'potEnde', 'potBudget', 'potMaxApplicationsPerUser',
            'potMaxApplicationsPerClub', 'potApplicationTypeIds', 'potBankAccountId',
        ]);
        $this->potLaufzeit = FundingPot::BEFRISTET;
        $this->resetValidation();
    }

    private function payerAccounts()
    {
        return BankAccount::operatorAccounts();
    }

    /** Ordnet einem Topf (auch nach der Endabrechnung) ein Auftraggeberkonto zu, solange nichts exportiert wurde. */
    public function assignPotAccount(int $potId, $accountId): void
    {
        $this->authorizeVerwalten();

        $pot = FundingPot::findOrFail($potId);
        $accountId = filled($accountId) ? (int) $accountId : null;

        if ($accountId !== null) {
            $this->payerAccounts()->findOrFail($accountId);
        }

        if ($pot->applications()->whereNotNull('payout_exported_at')->exists()) {
            $this->addError('potAccount' . $potId, 'Es wurden bereits Überweisungen exportiert; das Konto kann nicht mehr geändert werden.');

            return;
        }

        $pot->update(['bank_account_id' => $accountId]);
        $this->resetErrorBag('potAccount' . $potId);
    }

    public function saveAccount(): void
    {
        $this->authorizeVerwalten();

        $data = $this->validate([
            'accountBezeichnung' => ['required', 'string', 'max:100'],
            'accountKontoinhaber' => ['required', 'string', 'max:255'],
            'accountBankName' => ['nullable', 'string', 'max:255'],
            'accountIban' => ['required', 'string', 'max:42', function ($attribute, $value, $fail) {
                if (! BankAccount::isValidIban($value)) {
                    $fail('Die IBAN ist ungültig.');
                }
            }],
            'accountBic' => ['nullable', 'string', 'regex:/^[A-Za-z]{6}[A-Za-z0-9]{2}([A-Za-z0-9]{3})?$/'],
        ], [
            'accountBic.regex' => 'Die BIC ist ungültig.',
        ], [
            'accountBezeichnung' => 'Bezeichnung', 'accountKontoinhaber' => 'Kontoinhaber',
            'accountIban' => 'IBAN', 'accountBic' => 'BIC',
        ]);

        $attributes = [
            'bezeichnung' => $data['accountBezeichnung'],
            'kontoinhaber' => $data['accountKontoinhaber'],
            'bank_name' => $data['accountBankName'] ?: null,
            'iban' => strtoupper(preg_replace('/\s+/', '', $data['accountIban'])),
            'bic' => $data['accountBic'] ? strtoupper($data['accountBic']) : null,
        ];

        $this->accountId
            ? $this->payerAccounts()->findOrFail($this->accountId)->update($attributes)
            : BankAccount::create($attributes);

        $this->cancelAccount();
        session()->flash('message', 'Auftraggeberkonto gespeichert.');
    }

    public function editAccount(int $id): void
    {
        $this->authorizeVerwalten();
        $account = $this->payerAccounts()->findOrFail($id);

        $this->resetValidation();
        $this->accountId = $account->id;
        $this->accountBezeichnung = (string) $account->bezeichnung;
        $this->accountKontoinhaber = $account->kontoinhaber;
        $this->accountBankName = (string) $account->bank_name;
        $this->accountIban = $account->formattedIban();
        $this->accountBic = (string) $account->bic;
    }

    public function cancelAccount(): void
    {
        $this->reset(['accountId', 'accountBezeichnung', 'accountKontoinhaber', 'accountBankName', 'accountIban', 'accountBic']);
        $this->resetValidation();
    }

    public function deleteAccount(int $id): void
    {
        $this->authorizeVerwalten();
        $account = $this->payerAccounts()->findOrFail($id);

        if (FundingPot::where('bank_account_id', $account->id)->exists()) {
            session()->flash('error', 'Das Konto ist einem Fördertopf zugeordnet und kann nicht entfernt werden.');
            return;
        }

        $account->delete();
        $this->cancelAccount();
    }

    public function toggleGeschlossen(int $id): void
    {
        $this->authorizeVerwalten();
        $pot = FundingPot::findOrFail($id);
        $pot->update(['geschlossen' => ! $pot->geschlossen]);
    }

    public ?int $settlementPotId = null;

    public function prepareSettlement(int $id): void
    {
        $this->authorizeVerwalten();
        $pot = FundingPot::findOrFail($id);

        if ($pot->isSettled()) {
            session()->flash('error', 'Der Fördertopf wurde bereits abgerechnet.');
            return;
        }
        if (! $pot->geschlossen) {
            session()->flash('error', 'Die Endabrechnung ist erst möglich, wenn der Fördertopf geschlossen wurde.');
            return;
        }
        if ($pot->applicationsInProgressCount() > 0) {
            session()->flash('error', 'Es sind noch Anträge in Bearbeitung. Die Endabrechnung ist erst nach Abschluss aller Prüfungen möglich.');
            return;
        }

        $this->settlementPotId = $pot->id;
    }

    public function cancelSettlement(): void
    {
        $this->settlementPotId = null;
    }

    public function settlePot(int $id): void
    {
        $this->authorizeVerwalten();

        try {
            $pot = FundingPot::findOrFail($id);
            $pot->settle(auth()->user());
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
            return;
        }

        app(\App\Services\ApplicationNotifier::class)->settled($pot->fresh());

        $this->settlementPotId = null;
        session()->flash('message', 'Endabrechnung durchgeführt. Die endgültigen Förderbeträge sind gespeichert.');
    }

    public function downloadPayoutFile(int $id)
    {
        $this->authorizeVerwalten();
        $pot = FundingPot::findOrFail($id);

        try {
            $xml = $pot->exportPayouts(auth()->user());
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
            return null;
        }

        $name = 'ueberweisung-fordertopf-' . $pot->id . '-' . now()->format('Ymd-His') . '.xml';

        return response()->streamDownload(fn () => print($xml), $name, ['Content-Type' => 'application/xml']);
    }

    public function revokeSettlement(int $id): void
    {
        abort_unless(Gate::allows('antraege.verwalten'), 403);

        try {
            FundingPot::findOrFail($id)->revokeSettlement(auth()->user());
        } catch (DomainException $exception) {
            session()->flash('error', $exception->getMessage());
            return;
        }

        session()->flash('message', 'Endabrechnung zurückgenommen. Antragsteller sehen wieder nur den beantragten Betrag.');
    }

    public function deletePot(int $id): void
    {
        $this->authorizeVerwalten();
        $pot = FundingPot::findOrFail($id);

        if ($pot->bindings()->exists() || $pot->applications()->exists() || $pot->applicationTypes()->exists()) {
            session()->flash('error', 'Der Fördertopf ist bereits mit Anträgen, Antragsarten oder gebundenen Beträgen verknüpft und kann nicht gelöscht werden. Schließen Sie ihn stattdessen.');
            return;
        }

        $pot->delete();
        $this->cancelPot();
    }

    public function render()
    {
        return view('livewire.foerdertoepfe', [
            'programs' => FundingProgram::with([
                'pots.applicationTypes' => fn ($query) => $query->where('application_types.aktiv', true),
            ])->orderBy('name')->get(),
            'applicationTypes' => ApplicationType::where('aktiv', true)->orderBy('name')->get(),
            'payerAccounts' => $this->payerAccounts()->orderBy('bezeichnung')->get(),
            'settlementPreview' => $this->settlementPotId
                ? FundingPot::find($this->settlementPotId)?->settlementPreview()
                : null,
        ]);
    }
}
