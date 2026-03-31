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
        $templateId = $request->get('template_id');

        // Basis: alle Teams der aktuellen Regatta
        $regattaTeamsQuery = RegattaTeam::query()
            ->where('regatta_id', $regattaId)
            ->with(['teamWertungsGruppe']);

        if ($query !== '' || $templateId) {
            // Wenn Filter aktiv sind, laden wir zuerst die teamlink IDs, die den Kriterien entsprechen
            $filteredQuery = RegattaTeam::query()->where('regatta_id', $regattaId);

            if ($query !== '') {
                $filteredQuery->where('teamname', 'like', '%' . $query . '%');
            }

            if ($templateId) {
                $raceTypeIds = RaceType::where('race_type_template_id', $templateId)->pluck('id');
                $filteredQuery->whereIn('gruppe_id', $raceTypeIds);
            }

            $matchingTeamlinks = $filteredQuery->pluck('teamlink')->unique()->filter();

            // Nun laden wir ALLE Teams dieser Mannschaften (teamlink)
            $regattaTeamsQuery->whereIn('teamlink', $matchingTeamlinks);
        }

        $regattaTeams = $regattaTeamsQuery
            ->orderBy('teamlink')
            ->orderBy('teamname')
            ->orderBy('datum')
            ->get();

        // Gruppenbildung: teamlink => Teams
        $groups = $regattaTeams->groupBy('teamlink');

        // Alle verfügbaren Bootsklassen-Templates für das Dropdown laden (nur die der Regatta zugeordnet sind)
        $templates = RaceTypeTemplate::whereIn('id', function($query) use ($regattaId) {
            $query->select('race_type_template_id')
                ->from('race_types')
                ->where('regatta_id', $regattaId);
        })->orderBy('typ')->get();

        return view('regattaManagement.regattaTeamManager.index', [
            'regattaId' => $regattaId,
            'groups' => $groups,
            'query' => $query,
            'templateId' => $templateId,
            'templates' => $templates,
        ]);
    }
}

