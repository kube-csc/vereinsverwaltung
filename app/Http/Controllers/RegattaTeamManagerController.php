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

        // Basis: alle Teams der aktuellen Regatta
        $regattaTeamsQuery = RegattaTeam::query()
            ->where('regatta_id', $regattaId)
            ->with(['teamWertungsGruppe.raceTypeTemplate', 'regatta']);

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

        // Alle verfügbaren Bootsklassen für das Dropdown laden (direkt aus race_types der Regatta)
        $raceTypes = RaceType::where('regatta_id', $regattaId)
            ->orderBy('typ')
            ->get();

        return view('regattaManagement.regattaTeamManager.index', [
            'regattaId' => $regattaId,
            'regattaTeams' => $regattaTeams,
            'query' => $query,
            'raceTypeId' => $raceTypeId,
            'raceTypes' => $raceTypes,
        ]);
    }
}

