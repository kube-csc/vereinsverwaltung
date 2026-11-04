<?php

namespace App\Http\Livewire;

use App\Models\Application;
use App\Models\ApplicationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Validation\Rule;
use Livewire\Component;

class MeineAntraege extends Component
{
    public array $questionResponses = [];

    public array $payoutAccounts = [];

    public function mount(): void
    {
        Gate::authorize('antraege.stellen');
    }

    /** Ergänzt oder ändert die Bankverbindung eines bewilligten Antrags; bereits exportierte Zahlungen werden erneut offen. */
    public function updatePayoutAccount(int $applicationId): void
    {
        Gate::authorize('antraege.stellen');
        $key = 'payoutAccounts.' . $applicationId;
        $this->validate([
            $key => ['required', 'integer', Rule::exists('bank_accounts', 'id')
                ->where(fn ($query) => $query->where('user_id', auth()->id())->whereNull('deleted_at'))],
        ], [], [$key => 'Bankkonto']);

        DB::transaction(function () use ($applicationId) {
            $application = auth()->user()->applications()
                ->where('status', Application::STATUS_APPROVED)
                ->lockForUpdate()
                ->findOrFail($applicationId);
            $account = auth()->user()->bankAccounts()->findOrFail((int) $this->payoutAccounts[$applicationId]);
            $hadAccount = filled($application->bank_account_snapshot['iban'] ?? null);
            $reopened = $application->payout_exported_at !== null;

            $application->update([
                'bank_account_id' => $account->id,
                'bank_account_snapshot' => [
                    'kontoinhaber' => $account->kontoinhaber,
                    'bank_name' => $account->bank_name,
                    'iban' => $account->iban,
                    'bic' => $account->bic,
                ],
                'payout_exported_at' => null,
                'payout_exported_by' => null,
            ]);
            $application->events()->create([
                'actor_id' => auth()->id(),
                'actor_name' => auth()->user()->name,
                'event_type' => Application::EVENT_BANK_CHANGED,
                'message' => ($hadAccount ? 'Bankverbindung geändert' : 'Bankverbindung ergänzt')
                    . ($reopened ? '; die Überweisung war bereits exportiert und wird erneut erzeugt. Doppelzahlung prüfen.' : '.'),
            ]);
        });

        unset($this->payoutAccounts[$applicationId]);
        session()->flash('message', 'Bankverbindung gespeichert. Die Überweisung wird mit der nächsten Überweisungsdatei berücksichtigt.');
    }

    public function respondToQuestion(int $applicationId): void
    {
        Gate::authorize('antraege.stellen');
        $responseKey = 'questionResponses.' . $applicationId;
        $this->questionResponses[$applicationId] = trim($this->questionResponses[$applicationId] ?? '');
        $this->validate([
            $responseKey => ['required', 'string', 'min:3', 'max:5000'],
        ], [], [$responseKey => 'Antwort']);

        DB::transaction(function () use ($applicationId) {
            $application = auth()->user()->applications()
                ->lockForUpdate()
                ->findOrFail($applicationId);
            abort_unless($application->status === Application::STATUS_QUESTION, 409);

            $response = $this->questionResponses[$applicationId];
            $application->events()->create([
                'actor_id' => auth()->id(),
                'actor_name' => auth()->user()->name,
                'event_type' => Application::EVENT_ANSWER,
                'message' => $response,
            ]);
            $application->update(['status' => Application::STATUS_SUBMITTED]);
        });

        unset($this->questionResponses[$applicationId]);
        session()->flash('message', 'Ihre Antwort wurde übermittelt. Der Antrag wird erneut geprüft.');
    }

    public function render()
    {
        $user = auth()->user();

        return view('livewire.meine-antraege', [
            'applicationTypes' => ApplicationType::with(['fundingPots.program'])
                ->where('aktiv', true)
                ->where(fn ($query) => $query->whereNull('frist')->orWhereDate('frist', '>=', today()))
                ->orderBy('name')
                ->get()
                ->filter(fn (ApplicationType $type) => ! $type->requires_funding_pot
                    || $type->fundingPots->contains(fn ($pot) => $pot->istOffen())),
            'applications' => $user->applications()
                ->with('applicationType', 'club', 'fundingPot.program', 'decider', 'events.actor')
                ->latest()
                ->get(),
            'accounts' => $user->bankAccounts()->orderBy('kontoinhaber')->get(),
            'draftStatus' => Application::STATUS_DRAFT,
            'submittedStatus' => Application::STATUS_SUBMITTED,
        ]);
    }
}
