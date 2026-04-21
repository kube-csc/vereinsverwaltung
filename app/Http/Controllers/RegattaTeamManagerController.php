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

    /**
     * Bearbeitungsmaske für den teamlink eines Teams.
     */
    public function edit(Request $request, $id)
    {
        $regattaId = session()->get('regattaSelectId');
        $team = RegattaTeam::with(['teamWertungsGruppe.raceTypeTemplate', 'regatta'])->findOrFail($id);

        if ($request->has('assign_new_teamlink')) {
            $existingTeamlinks = RegattaTeam::where('teamlink', '>', 0)
                ->distinct()
                ->orderBy('teamlink')
                ->pluck('teamlink')
                ->toArray();

            $nextFreeTeamlink = 1;
            foreach ($existingTeamlinks as $link) {
                if ($link == $nextFreeTeamlink) {
                    $nextFreeTeamlink++;
                } elseif ($link > $nextFreeTeamlink) {
                    break;
                }
            }
            $team->teamlink = $nextFreeTeamlink;
            $team->save();

            return redirect()->route('regattaTeamManager.edit', $id)->with('status', 'Neuer Teamlink wurde vergeben.');
        }

        $q_team = $request->get('q_team');
        $q_plz = $request->get('q_plz');
        $q_bootstyp = $request->get('q_bootstyp');

        // Die Vorschläge werden berechnet.
        // Teams mit dem gleichen teamlink (falls > 0) werden separat gelistet.
        $teamlink = $team->teamlink;
        $linkedTeams = collect();

        if ($teamlink > 0) {
            $linkedTeams = RegattaTeam::query()
                ->where('teamlink', $teamlink)
                ->where('regatta_teams.id', '!=', $team->id)
                ->with(['regatta', 'teamWertungsGruppe.raceTypeTemplate'])
                ->get();
        }

        $suggestionsQuery = RegattaTeam::query()
            ->where('regatta_teams.id', '!=', $team->id)
            ->with(['regatta', 'teamWertungsGruppe.raceTypeTemplate']);

        // Teams ausschließen, die bereits denselben teamlink haben
        if ($teamlink > 0) {
            $suggestionsQuery->where('teamlink', '!=', $teamlink);
        }

        $templateId = optional($team->teamWertungsGruppe)->race_type_template_id;

        // Wenn Filter gesetzt sind, diese bevorzugen
        if ($q_team || $q_plz || $q_bootstyp) {
            if ($q_team) {
                $suggestionsQuery->where('teamname', 'like', '%' . $q_team . '%');
            }
            if ($q_plz) {
                $suggestionsQuery->where('plz', $q_plz);
            }
            if ($q_bootstyp) {
                $suggestionsQuery->whereHas('teamWertungsGruppe', function($q) use ($q_bootstyp) {
                    $q->where('race_type_template_id', $q_bootstyp);
                });
            }
        } else {
            // Standard-Autovorschlag (Ähnlichkeit)
            if ($templateId) {
                $suggestionsQuery->whereHas('teamWertungsGruppe', function($q) use ($templateId) {
                    $q->where('race_type_template_id', $templateId);
                })
                ->where(function($q) use ($team) {
                    if ($team->teamname) {
                        $q->where('teamname', 'like', '%' . $team->teamname . '%');
                    }
                    if ($team->plz) {
                        $q->orWhere('plz', $team->plz);
                    }
                    if ($team->email) {
                        $q->orWhere('email', $team->email);
                    }
                });
            } else {
                // Wenn kein TemplateId, dann keine automatischen Vorschläge ohne Filter
                $suggestionsQuery->whereRaw('1=0');
            }
        }

        $suggestions = $suggestionsQuery->get();

        // Sortierung: Gleicher Bootstyp zuerst, dann nach Namensähnlichkeit (simuliert durch einfache Sortierung)
        $suggestions = $suggestions->sortBy([
            function ($a, $b) use ($templateId) {
                $aIsSame = optional($a->teamWertungsGruppe)->race_type_template_id == $templateId;
                $bIsSame = optional($b->teamWertungsGruppe)->race_type_template_id == $templateId;
                if ($aIsSame && !$bIsSame) return -1;
                if (!$aIsSame && $bIsSame) return 1;
                return 0;
            },
            ['teamname', 'asc'],
        ]);

        // JETZT extrahieren wir die PLZ und Bootstypen NUR aus den tatsächlich gefundenen Vorschlägen.
        $plz_options = $suggestions->pluck('plz')->filter()->unique()->values()->sort();

        $bootstypen_ids = $suggestions->map(function($s) {
            return optional($s->teamWertungsGruppe)->race_type_template_id;
        })->filter()->unique();

        $bootstypen = RaceTypeTemplate::whereIn('id', $bootstypen_ids)
            ->orderBy('typ')
            ->get();

        // Nächste freie Teamlink-ID ermitteln (Kleinstmögliche freie ID suchen, um Lücken zu füllen)
        $existingTeamlinks = RegattaTeam::where('teamlink', '>', 0)
            ->distinct()
            ->orderBy('teamlink')
            ->pluck('teamlink')
            ->toArray();

        $nextFreeTeamlink = 1;
        foreach ($existingTeamlinks as $link) {
            if ($link == $nextFreeTeamlink) {
                $nextFreeTeamlink++;
            } elseif ($link > $nextFreeTeamlink) {
                break;
            }
        }

        return view('regattaManagement.regattaTeamManager.edit', [
            'team' => $team,
            'suggestions' => $suggestions,
            'linkedTeams' => $linkedTeams,
            'regattaId' => $regattaId,
            'bootstypen' => $bootstypen,
            'plz_options' => $plz_options,
            'q_team' => $q_team,
            'q_plz' => $q_plz,
            'q_bootstyp' => $q_bootstyp,
            'nextFreeTeamlink' => $nextFreeTeamlink,
        ]);
    }

    /**
     * Update des teamlink.
     */
    public function update(Request $request, $id)
    {
        $team = RegattaTeam::findOrFail($id);
        $teamlink = $request->get('teamlink');

        $team->teamlink = $teamlink;
        $team->save();

        return redirect()->route('regattaTeamManager.index')->with('success', 'Teamlink wurde aktualisiert.');
    }

    /**
     * Synchronisiert den Teamlink zwischen zwei Teams.
     */
    public function sync(Request $request, $id)
    {
        $team = RegattaTeam::findOrFail($id);
        $otherTeam = RegattaTeam::findOrFail($request->get('other_id'));
        $direction = $request->get('direction');

        if ($direction === 'take') {
            // Aktuelles Team übernimmt den Teamlink vom Vorschlag
            $team->teamlink = $otherTeam->teamlink;
            $team->save();
            return redirect()->route('regattaTeamManager.edit', $team->id)->with('success', 'Teamlink vom Vorschlag übernommen.');
        } elseif ($direction === 'merge_new') {
            // Beide Teams haben teamlink=0 -> neue ID finden und beiden zuweisen
            $existingTeamlinks = RegattaTeam::where('teamlink', '>', 0)
                ->distinct()
                ->orderBy('teamlink')
                ->pluck('teamlink')
                ->toArray();

            $nextFree = 1;
            foreach ($existingTeamlinks as $link) {
                if ($link == $nextFree) {
                    $nextFree++;
                } elseif ($link > $nextFree) {
                    break;
                }
            }

            $team->teamlink = $nextFree;
            $team->save();

            $otherTeam->teamlink = $nextFree;
            $otherTeam->save();

            return redirect()->route('regattaTeamManager.edit', $team->id)->with('success', "Beide Teams wurden mit dem neuen Teamlink #{$nextFree} verknüpft.");
        } else {
            // Vorschlag übernimmt den Teamlink vom aktuellen Team
            $otherTeam->teamlink = $team->teamlink;
            $otherTeam->save();
            return redirect()->route('regattaTeamManager.edit', $otherTeam->id)->with('success', 'Vorschlag wurde dem Teamlink zugeordnet und wird nun bearbeitet.');
        }
    }
}

