<?php

namespace App\Http\Livewire;

use App\Models\Application;
use App\Models\FundingPotBinding;
use DomainException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class AntraegePruefung extends Component
{
    use WithPagination;

    public bool $showArchived = false;
    public array $decisionReasons = [];
    public array $reviewNotes = [];
    public array $deletionReasons = [];

    public function mount(): void
    {
        Gate::authorize('antraege.pruefbereich');
    }

    public function updatedShowArchived(): void
    {
        $this->resetPage();
    }

    public function markForReview(int $applicationId): void
    {
        Gate::authorize('antraege.pruefen');
        $noteKey = 'reviewNotes.' . $applicationId;
        $this->validate([
            $noteKey => ['nullable', 'string', 'max:5000'],
        ], [], [$noteKey => 'Prüfnotiz']);
        $application = $this->findApplication($applicationId);
        abort_unless(! $application->trashed() && $application->status === Application::STATUS_SUBMITTED, 409);

        $note = trim($this->reviewNotes[$applicationId] ?? '');
        $application->update([
            'status' => Application::STATUS_IN_REVIEW,
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'review_note' => $note !== '' ? $note : null,
        ]);
    }

    public function decide(int $applicationId, string $decision): void
    {
        Gate::authorize('antraege.entscheiden');
        abort_unless(in_array($decision, [Application::STATUS_APPROVED, Application::STATUS_REJECTED], true), 422);

        $reasonKey = 'decisionReasons.' . $applicationId;
        $this->validate([
            $reasonKey => ['required', 'string', 'min:5', 'max:5000'],
        ], [], [$reasonKey => 'Begründung']);

        try {
            DB::transaction(function () use ($applicationId, $decision, $reasonKey) {
                $application = $this->findApplication($applicationId, lock: true);
                abort_unless(
                    ! $application->trashed()
                        && in_array($application->status, [
                            Application::STATUS_SUBMITTED,
                            Application::STATUS_IN_REVIEW,
                        ], true),
                    409
                );

                if ($decision === Application::STATUS_APPROVED && $application->funding_pot_id !== null) {
                    $amount = $application->answers['beantragter_zuschuss'] ?? null;
                    if (! is_numeric($amount) || (float) $amount <= 0) {
                        $this->addError($reasonKey, 'Der Antrag enthält keinen gültigen beantragten Zuschuss und kann nicht bewilligt werden.');
                        return;
                    }

                    $application->fundingPot->bind(
                        FundingPotBinding::BEWILLIGT,
                        (float) $amount,
                        $application->id,
                        'Bewilligung durch ' . auth()->user()->name
                    );
                }

                $application->update([
                    'status' => $decision,
                    'reviewed_by' => $application->reviewed_by ?? auth()->id(),
                    'reviewed_at' => $application->reviewed_at ?? now(),
                    'decided_by' => auth()->id(),
                    'decided_at' => now(),
                    'decision_reason' => $this->decisionReasons[$applicationId],
                ]);
            });
        } catch (DomainException $exception) {
            $this->addError($reasonKey, $exception->getMessage());
        }
    }

    public function archive(int $applicationId): void
    {
        Gate::authorize('antraege.antraege_loeschen');
        $reasonKey = 'deletionReasons.' . $applicationId;
        $this->validate([
            $reasonKey => ['required', 'string', 'min:5', 'max:5000'],
        ], [], [$reasonKey => 'Archivierungsgrund']);

        $application = $this->findApplication($applicationId);
        abort_unless(
            ! $application->trashed()
                && $application->funding_pot_id !== null
                && $application->submitted_at !== null
                && $application->status !== Application::STATUS_APPROVED,
            409
        );

        DB::transaction(function () use ($application) {
            $application->update([
                'deleted_by' => auth()->id(),
                'deletion_reason' => $this->deletionReasons[$application->id],
            ]);
            $application->delete();
        });
    }

    private function findApplication(int $id, bool $lock = false): Application
    {
        $query = Application::query()
            ->with(['user', 'applicationType', 'fundingPot.program', 'documents', 'reviewer', 'decider', 'deleter']);

        if ($this->showArchived) {
            $query->withTrashed();
        }

        if (! auth()->user()->can('antraege.pruefen')) {
            $query->whereNotNull('funding_pot_id');
        }

        if ($lock) {
            $query->lockForUpdate();
        }

        return $query->findOrFail($id);
    }

    public function render()
    {
        Gate::authorize('antraege.pruefbereich');
        $canReview = auth()->user()->can('antraege.pruefen');
        $query = Application::query()
            ->with(['user', 'applicationType', 'fundingPot.program', 'documents', 'reviewer', 'decider', 'deleter'])
            ->whereNotNull('submitted_at');

        if (! $canReview) {
            $query->whereNotNull('funding_pot_id');
        }

        if ($this->showArchived) {
            $query->onlyTrashed();
        } else {
            $query->whereIn('status', [
                Application::STATUS_SUBMITTED,
                Application::STATUS_IN_REVIEW,
                Application::STATUS_APPROVED,
                Application::STATUS_REJECTED,
            ]);
        }

        return view('livewire.antraege-pruefung', [
            'applications' => $query->latest('submitted_at')->paginate(20),
            'canReview' => $canReview,
            'canDecide' => auth()->user()->can('antraege.entscheiden'),
            'canArchive' => auth()->user()->can('antraege.antraege_loeschen'),
        ]);
    }
}
