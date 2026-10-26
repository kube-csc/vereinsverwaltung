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
    public array $decisionAmounts = [];
    public array $decisionAmountModes = [];
    public array $reviewNotes = [];
    public array $questions = [];
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
        $note = trim($this->reviewNotes[$applicationId] ?? '');
        DB::transaction(function () use ($applicationId, $note) {
            $application = $this->findApplication($applicationId, lock: true);
            abort_unless(! $application->trashed() && $application->status === Application::STATUS_SUBMITTED, 409);
            $application->update([
                'status' => Application::STATUS_IN_REVIEW,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
                'review_note' => $note !== '' ? $note : null,
            ]);
            $this->recordEvent($application, Application::EVENT_REVIEW_STARTED, $note ?: 'Die Prüfung wurde aufgenommen.');
        });
    }

    public function requestInformation(int $applicationId): void
    {
        Gate::authorize('antraege.pruefen');
        $questionKey = 'questions.' . $applicationId;
        $this->questions[$applicationId] = trim($this->questions[$applicationId] ?? '');
        $this->validate([
            $questionKey => ['required', 'string', 'min:3', 'max:5000'],
        ], [], [$questionKey => 'Rückfrage']);

        DB::transaction(function () use ($applicationId) {
            $application = $this->findApplication($applicationId, lock: true);
            abort_unless(
                ! $application->trashed()
                    && in_array($application->status, [
                        Application::STATUS_SUBMITTED,
                        Application::STATUS_IN_REVIEW,
                    ], true),
                409
            );

            $question = $this->questions[$applicationId];
            $application->update([
                'status' => Application::STATUS_QUESTION,
                'reviewed_by' => auth()->id(),
                'reviewed_at' => now(),
            ]);
            $this->recordEvent($application, Application::EVENT_QUESTION, $question);
        });

        unset($this->questions[$applicationId]);
    }

    public function decide(int $applicationId, string $decision): void
    {
        Gate::authorize('antraege.entscheiden');
        abort_unless(in_array($decision, [Application::STATUS_APPROVED, Application::STATUS_REJECTED], true), 422);

        $application = $this->findApplication($applicationId);
        abort_unless(
            ! $application->trashed()
                && in_array($application->status, [
                    Application::STATUS_SUBMITTED,
                    Application::STATUS_IN_REVIEW,
                ], true),
            409
        );

        $reasonKey = 'decisionReasons.' . $applicationId;
        $amountKey = 'decisionAmounts.' . $applicationId;
        $amountModeKey = 'decisionAmountModes.' . $applicationId;
        $requiresApprovedAmount = $decision === Application::STATUS_APPROVED
            && ($application->funding_pot_id !== null
                || array_key_exists('beantragter_zuschuss', $application->answers ?? []));
        $amountMode = $this->decisionAmountModes[$applicationId] ?? 'amount';
        $requestedAmount = $application->answers['beantragter_zuschuss'] ?? null;
        $canCalculatePercentage = is_numeric($requestedAmount) && (float) $requestedAmount > 0;
        $this->validate([
            $reasonKey => ['required', 'string', 'min:5', 'max:5000'],
            $amountKey => $requiresApprovedAmount
                ? ($amountMode === 'percent'
                    ? ['required', 'numeric', 'min:0.01', 'max:100']
                    : ['required', 'numeric', 'min:0.01', 'max:9999999999.99'])
                : ['nullable', 'numeric', 'min:0', 'max:9999999999.99'],
            $amountModeKey => ['nullable', 'in:amount,percent'],
        ], [], [
            $reasonKey => 'Begründung',
            $amountKey => $amountMode === 'percent' ? 'Bewilligungsanteil' : 'bewilligter Betrag',
            $amountModeKey => 'Eingabeart',
        ]);

        if ($requiresApprovedAmount && $amountMode === 'percent' && ! $canCalculatePercentage) {
            $this->addError($amountModeKey, 'Eine Prozentangabe ist nur mit einem positiven beantragten Zuschuss möglich.');
            return;
        }

        $approvedAmount = null;
        if ($decision === Application::STATUS_APPROVED && $requiresApprovedAmount) {
            $inputAmount = (float) $this->decisionAmounts[$applicationId];
            $approvedAmount = $amountMode === 'percent'
                ? round((float) $requestedAmount * $inputAmount / 100, 2)
                : $inputAmount;
            if ($approvedAmount < 0.01 || $approvedAmount > 9999999999.99) {
                $this->addError($amountKey, 'Der berechnete bewilligte Betrag muss zwischen 0,01 € und 9.999.999.999,99 € liegen.');
                return;
            }
        }

        try {
            DB::transaction(function () use ($applicationId, $decision, $reasonKey, $approvedAmount) {
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
                    $application->fundingPot->bind(
                        FundingPotBinding::BEWILLIGT,
                        $approvedAmount,
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
                    'approved_amount' => $approvedAmount,
                ]);
                $this->recordEvent(
                    $application,
                    $decision === Application::STATUS_APPROVED ? Application::EVENT_APPROVED : Application::EVENT_REJECTED,
                    $this->decisionReasons[$applicationId]
                );
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
            $this->recordEvent($application, Application::EVENT_ARCHIVED, $this->deletionReasons[$application->id]);
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

    private function recordEvent(Application $application, string $type, string $message): void
    {
        $application->events()->create([
            'actor_id' => auth()->id(),
            'actor_name' => auth()->user()->name,
            'event_type' => $type,
            'message' => $message,
        ]);
    }

    public function render()
    {
        Gate::authorize('antraege.pruefbereich');
        $canReview = auth()->user()->can('antraege.pruefen');
        $query = Application::query()
            ->with(['user', 'applicationType', 'fundingPot.program', 'documents', 'reviewer', 'decider', 'deleter', 'events.actor'])
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
                Application::STATUS_QUESTION,
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
