<?php

namespace App\Http\Livewire;

use App\Models\Application;
use App\Models\ApplicationType;
use App\Models\FundingPot;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class Auswertungen extends Component
{
    public string $pot = '';
    public string $type = '';
    public string $status = '';
    public string $from = '';
    public string $to = '';

    private const STATUS_LABELS = [
        Application::STATUS_SUBMITTED => 'Eingereicht',
        Application::STATUS_IN_REVIEW => 'In Prüfung',
        Application::STATUS_QUESTION => 'Rückfrage',
        Application::STATUS_APPROVED => 'Bewilligt',
        Application::STATUS_REJECTED => 'Abgelehnt',
    ];

    public function mount(): void
    {
        $this->authorizeAccess();
    }

    public function resetFilters(): void
    {
        $this->reset(['pot', 'type', 'status', 'from', 'to']);
    }

    private function authorizeAccess(): void
    {
        abort_unless(Gate::allows('antraege.auswertung'), 403);
    }

    private function query(): Builder
    {
        $this->validate([
            'from' => ['nullable', 'date'],
            'to' => ['nullable', 'date'],
        ]);

        return Application::query()
            ->where('status', '!=', Application::STATUS_DRAFT)
            ->when($this->pot !== '', fn (Builder $q) => $q->where('funding_pot_id', (int) $this->pot))
            ->when($this->type !== '', fn (Builder $q) => $q->where('application_type_id', (int) $this->type))
            ->when($this->status !== '' && isset(self::STATUS_LABELS[$this->status]), fn (Builder $q) => $q->where('status', $this->status))
            ->when($this->from !== '', fn (Builder $q) => $q->whereDate('submitted_at', '>=', $this->from))
            ->when($this->to !== '', fn (Builder $q) => $q->whereDate('submitted_at', '<=', $this->to));
    }

    public function exportCsv()
    {
        $this->authorizeAccess();
        $applications = $this->query()
            ->with(['applicationType', 'fundingPot', 'user', 'club'])
            ->orderBy('id')
            ->get();

        $cell = function ($value): string {
            $value = (string) $value;
            // Schutz vor Formel-Injektion in Tabellenkalkulationen.
            return preg_match('/^[=+\-@\t\r]/', $value) ? "'" . $value : $value;
        };

        return response()->streamDownload(function () use ($applications, $cell) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF");
            fputcsv($out, ['Antrag', 'Antragsart', 'Fördertopf', 'Verein', 'Antragsteller', 'Status', 'Eingereicht', 'Bewilligt (EUR)', 'Endgültig (EUR)'], ';');
            foreach ($applications as $application) {
                $user = $application->user;
                fputcsv($out, [
                    $application->id,
                    $cell($application->applicationType?->name),
                    $cell($application->fundingPot?->name),
                    $cell($application->club?->clubname),
                    $cell($user ? (trim(($user->vorname ?? '') . ' ' . ($user->nachname ?? '')) ?: $user->name) : ''),
                    self::STATUS_LABELS[$application->status] ?? $application->status,
                    $application->submitted_at?->format('d.m.Y'),
                    $application->approved_amount !== null ? number_format((float) $application->approved_amount, 2, ',', '') : '',
                    $application->final_amount !== null ? number_format((float) $application->final_amount, 2, ',', '') : '',
                ], ';');
            }
            fclose($out);
        }, 'auswertung-antraege-' . now()->format('Ymd') . '.csv', ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    public function render()
    {
        $this->authorizeAccess();
        $applications = $this->query()->with(['fundingPot', 'applicationType'])->get();

        $byStatus = [];
        foreach (self::STATUS_LABELS as $key => $label) {
            $byStatus[$key] = ['label' => $label, 'count' => $applications->where('status', $key)->count()];
        }

        $approved = $applications->where('status', Application::STATUS_APPROVED);
        $perPot = $applications->groupBy('funding_pot_id')->map(function ($items) {
            $approvedItems = $items->where('status', Application::STATUS_APPROVED);
            return [
                'name' => $items->first()->fundingPot?->name ?? 'Ohne Fördertopf',
                'budget' => $items->first()->fundingPot?->budget,
                'count' => $items->count(),
                'approved_count' => $approvedItems->count(),
                'approved_sum' => (float) $approvedItems->sum('approved_amount'),
                'final_sum' => (float) $approvedItems->sum('final_amount'),
            ];
        })->sortBy('name')->values();

        $perType = $applications->groupBy('application_type_id')->map(fn ($items) => [
            'name' => $items->first()->applicationType?->name ?? '-',
            'count' => $items->count(),
        ])->sortBy('name')->values();

        return view('livewire.auswertungen', [
            'total' => $applications->count(),
            'byStatus' => $byStatus,
            'approvedSum' => (float) $approved->sum('approved_amount'),
            'finalSum' => (float) $approved->sum('final_amount'),
            'perPot' => $perPot,
            'perType' => $perType,
            'pots' => FundingPot::orderBy('name')->get(['id', 'name']),
            'types' => ApplicationType::orderBy('name')->get(['id', 'name']),
            'statusLabels' => self::STATUS_LABELS,
        ]);
    }
}
