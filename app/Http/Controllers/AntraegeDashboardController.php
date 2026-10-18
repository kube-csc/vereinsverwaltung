<?php

namespace App\Http\Controllers;

use App\Models\FundingPot;
use App\Models\FundingProgram;
use App\Models\User;

class AntraegeDashboardController extends Controller
{
    public function __invoke()
    {
        $stats = [];
        if (auth()->user()->can('antraege.verwalten')) {
            $pots = FundingPot::all();
            $offene = $pots->filter(fn (FundingPot $pot) => $pot->istOffen());
            $verfuegbar = $offene->sum(fn (FundingPot $pot) => $pot->verfuegbar());

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
                    'label' => 'Konten ohne Rolle',
                    'value' => User::doesntHave('roles')->count(),
                    'hint' => 'noch ohne Berechtigung',
                    'border' => 'border-gray-400',
                ],
            ];
        }

        return view('antraege.dashboard', compact('stats'));
    }
}
