<?php

namespace App\Http\Livewire;

use App\Models\Application;
use App\Models\ApplicationType;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;

class MeineAntraege extends Component
{
    public function mount(): void
    {
        Gate::authorize('antraege.stellen');
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
                ->with('applicationType', 'club', 'fundingPot.program', 'decider')
                ->latest()
                ->get(),
            'draftStatus' => Application::STATUS_DRAFT,
            'submittedStatus' => Application::STATUS_SUBMITTED,
        ]);
    }
}
