<?php

namespace App\Http\Controllers;

use App\Models\RaceType;
use App\Models\RaceTypeTemplate;
use App\Models\RegattaTeam;
use Illuminate\Http\Request;

class RegattaTeamManagerController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Übersicht zur Verwaltung von Mannschaften (Gruppierung über teamlink) inkl. Vorschlagsliste.
     */
    public function index(Request $request)
    {
        $regattaId = session()->get('regattaSelectId');

        if (!$regattaId) {
            return redirect('/Regattamenu')->with('success', 'Bitte zuerst eine Regatta auswählen.');
        }

        $query = trim((string)$request->get('q', ''));
        $raceTypeId = $request->get('race_type_id');
        $teamlinkFilter = $request->get('teamlink_filter');

        // Basis: alle Teams der aktuellen Regatta
        $regattaTeamsQuery = RegattaTeam::query()
            ->with(['teamWertungsGruppe.raceTypeTemplate', 'regatta']);

        if ($teamlinkFilter === 'all_db_0') {
            // Regatta-übergreifend: nur Teams mit teamlink 0 oder NULL
            $regattaTeamsQuery->where(function($q) {
                $q->where('teamlink', 0)->orWhereNull('teamlink');
            });
        } elseif ($teamlinkFilter === 'once') {
            // Nur Teams, deren teamlink genau einmal vorkommt (ID > 0)
            $regattaTeamsQuery->where('regatta_id', $regattaId)
                ->where('teamlink', '>', 0)
                ->whereIn('teamlink', function($q) {
                    $q->select('teamlink')
                        ->from('regatta_teams')
                        ->where('teamlink', '>', 0)
                        ->groupBy('teamlink')
                        ->havingRaw('COUNT(*) = 1');
                });
        } else {
            // Standard: nur Teams der aktuellen Regatta
            $regattaTeamsQuery->where('regatta_id', $regattaId);

            if ($teamlinkFilter === '0') {
                $regattaTeamsQuery->where(function($q) {
                    $q->where('teamlink', 0)->orWhereNull('teamlink');
                });
            }
        }

        if ($query !== '') {
            $regattaTeamsQuery->where('teamname', 'like', '%' . $query . '%');
        }

        if ($raceTypeId) {
            $regattaTeamsQuery->where('gruppe_id', $raceTypeId);
        }

        $regattaTeams = $regattaTeamsQuery
            ->orderBy('teamlink')
            ->orderBy('teamname')
            ->orderBy('datum')
            ->get();

        // NEU: Für jedes Team mit teamlink > 0 andere Regatten laden, in denen sie gestartet sind
        foreach ($regattaTeams as $team) {
            $team->andereRegatten = collect();

            if ($team->teamlink > 0) {
                // Finde andere Teams mit gleichem teamlink, aber anderer Regatta
                $team->andereRegatten = RegattaTeam::query()
                    ->where('teamlink', $team->teamlink)
                    ->where('regatta_id', '!=', $regattaId)
                    ->with(['regatta', 'teamWertungsGruppe.raceTypeTemplate'])
                    ->get()
                    ->sortBy(function($item) {
                        return optional($item->regatta)->datumvon;
                    });
            }
        }

        // Alle verfügbaren Bootsklassen für das Dropdown laden (direkt aus race_types der Regatta)
        $raceTypes = RaceType::where('regatta_id', $regattaId)
            ->orderBy('typ')
            ->get();

        return view('regattaManagement.regattaTeamManager.index', [
            'regattaId' => $regattaId,
            'regattaTeams' => $regattaTeams,
            'query' => $query,
            'raceTypeId' => $raceTypeId,
            'teamlinkFilter' => $teamlinkFilter,
            'raceTypes' => $raceTypes,
        ]);
    }
}

