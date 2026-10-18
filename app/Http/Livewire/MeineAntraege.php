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
            'applicationTypes' => ApplicationType::where('aktiv', true)
                ->where(fn ($query) => $query->whereNull('frist')->orWhereDate('frist', '>=', today()))
                ->orderBy('name')
                ->get(),
            'applications' => $user->applications()
                ->with('applicationType', 'club')
                ->latest()
                ->get(),
            'draftStatus' => Application::STATUS_DRAFT,
            'submittedStatus' => Application::STATUS_SUBMITTED,
        ]);
    }
}
