<?php

namespace App\Http\Controllers;

use App\Models\Application;
use App\Models\ApplicationType;
use App\Models\FundingPot;
use App\Models\FundingProgram;
use Illuminate\Support\Facades\Gate;

class AntraegeDashboardController extends Controller
{
    public function __invoke()
    {
        $user = auth()->user();
        $hasManagementAccess = $user->can('antraege.verwaltung') || $user->admin >= 4;

        if (! $hasManagementAccess) {
            return $this->applicantDashboard();
        }

        $stats = [];
        // TODO: Remove the legacy admin-level fallback after existing administrators are migrated to roles.
        if ($user->can('antraege.verwalten') || $user->admin >= 4) {
            $pots = FundingPot::all();
            $offene = $pots->filter(fn (FundingPot $pot) => $pot->istOffen());
            $verfuegbar = $offene->sum(fn (FundingPot $pot) => $pot->verfuegbar());
            $bewilligtImJahr = Application::where('status', Application::STATUS_APPROVED)
                ->whereYear('decided_at', now()->year)
                ->sum('approved_amount');

            $stats = [
                [
                    'label' => 'Aktive Förderprogramme',
                    'value' => FundingProgram::where('aktiv', true)->count(),
                    'border' => 'border-blue-500',
                ],
                [
                    'label' => 'Offene Fördertöpfe',
                    'value' => $offene->count(),
                    'hint' => 'von ' . $pots->count() . ' insgesamt',
                    'border' => 'border-green-500',
                ],
                [
                    'label' => 'Verfügbares Budget',
                    'value' => number_format($verfuegbar, 0, ',', '.') . ' €',
                    'hint' => 'in offenen Fördertöpfen',
                    'border' => 'border-amber-500',
                ],
                [
                    'label' => 'Bewilligte Fördergelder',
                    'value' => number_format($bewilligtImJahr, 2, ',', '.') . ' €',
                    'hint' => 'im Jahr ' . now()->year . ' bewilligt',
                    'border' => 'border-green-500',
                ],
            ];
        }

        return view('antraege.dashboard', [
            'stats' => $stats,
            'canApply' => $user->can('antraege.stellen'),
        ]);
    }

    public function applicant()
    {
        Gate::authorize('antraege.stellen');

        return $this->applicantDashboard();
    }

    private function applicantDashboard()
    {
        $user = auth()->user();
        $availableTypes = ApplicationType::with(['fundingPots.program'])
            ->where('aktiv', true)
            ->where(fn ($query) => $query->whereNull('frist')->orWhereDate('frist', '>=', today()))
            ->orderBy('name')
            ->get()
            ->filter(fn (ApplicationType $type) => ! $type->requires_funding_pot
                || $type->fundingPots->contains(fn (FundingPot $pot) => $pot->istOffen()))
            ->values();

        $applications = $user->applications();
        $statusCounts = [
            Application::STATUS_DRAFT => (clone $applications)->where('status', Application::STATUS_DRAFT)->count(),
            Application::STATUS_SUBMITTED => (clone $applications)->where('status', Application::STATUS_SUBMITTED)->count(),
            Application::STATUS_IN_REVIEW => (clone $applications)->where('status', Application::STATUS_IN_REVIEW)->count(),
            Application::STATUS_QUESTION => (clone $applications)->where('status', Application::STATUS_QUESTION)->count(),
            Application::STATUS_APPROVED => (clone $applications)->where('status', Application::STATUS_APPROVED)->count(),
            Application::STATUS_REJECTED => (clone $applications)->where('status', Application::STATUS_REJECTED)->count(),
        ];

        return view('antraege.antragsteller-dashboard', [
            'availableTypesWithPot' => $availableTypes->where('requires_funding_pot', true)->values(),
            'availableTypesWithoutPot' => $availableTypes->where('requires_funding_pot', false)->values(),
            'statusCounts' => $statusCounts,
            'approvedAmount' => (float) (clone $applications)
                ->where('status', Application::STATUS_APPROVED)
                ->sum('approved_amount'),
            'draftApplications' => (clone $applications)
                ->with('applicationType')
                ->where('status', Application::STATUS_DRAFT)
                ->latest('updated_at')
                ->limit(5)
                ->get(),
            'recentApplications' => (clone $applications)
                ->with('applicationType')
                ->whereNotNull('submitted_at')
                ->latest('updated_at')
                ->limit(5)
                ->get(),
            'upcomingDeadlines' => $availableTypes
                ->filter(fn (ApplicationType $type) => $type->frist
                    && $type->frist->between(today(), today()->addDays(30)))
                ->sortBy('frist')
                ->values(),
            'hasManagementAccess' => $user->can('antraege.verwaltung') || $user->admin >= 4,
        ]);
    }
}
