<?php

namespace App\Http\Livewire;

use App\Models\FundingPot;
use App\Models\FundingProgram;
use App\Models\ApplicationType;
use Illuminate\Support\Facades\Gate;
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
    public string $potMaxApplicationsPerUser = '1';
    public string $potMaxApplicationsPerClub = '';
    public array $potApplicationTypeIds = [];

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
            'potMaxApplicationsPerUser' => ['required', 'integer', 'min:1', 'max:4294967295'],
            'potMaxApplicationsPerClub' => ['nullable', 'integer', 'min:1', 'max:4294967295'],
            'potApplicationTypeIds' => ['array'],
            'potApplicationTypeIds.*' => ['integer', 'distinct', 'exists:application_types,id'],
        ], [], [
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
            'max_applications_per_user' => $data['potMaxApplicationsPerUser'] ?: null,
            'max_applications_per_club' => $data['potMaxApplicationsPerClub'] ?: null,
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
            'potMaxApplicationsPerClub', 'potApplicationTypeIds',
        ]);
        $this->potMaxApplicationsPerUser = '1';
        $this->potLaufzeit = FundingPot::BEFRISTET;
        $this->resetValidation();
    }

    public function toggleGeschlossen(int $id): void
    {
        $this->authorizeVerwalten();
        $pot = FundingPot::findOrFail($id);
        $pot->update(['geschlossen' => ! $pot->geschlossen]);
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
            'programs' => FundingProgram::with('pots.applicationTypes')->orderBy('name')->get(),
            'applicationTypes' => ApplicationType::orderBy('name')->get(),
        ]);
    }
}
