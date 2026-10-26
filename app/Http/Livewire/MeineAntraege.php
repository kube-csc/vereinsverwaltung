<?php

namespace App\Http\Livewire;

use App\Models\Application;
use App\Models\ApplicationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class MeineAntraege extends Component
{
    public array $questionResponses = [];

    public function mount(): void
    {
        Gate::authorize('antraege.stellen');
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
            'draftStatus' => Application::STATUS_DRAFT,
            'submittedStatus' => Application::STATUS_SUBMITTED,
        ]);
    }
}
