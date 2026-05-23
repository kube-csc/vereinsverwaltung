<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RaffleOrganization;
use App\Models\RegattaTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;

class RegattaRaffleOrganizationController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
    }

    /**
     * Führt die automatische Erstzuordnung für die aktuelle Regatta durch.
     */
    public function autoAssign()
    {
        $regattaId = Session::get('regattaSelectId');
        if (!$regattaId) return back()->with('error', 'Keine Regatta ausgewählt.');

        $teams = RegattaTeam::where('regatta_id', $regattaId)->get();

        // Aktuelle Blöcke berechnen (Logik aus der View übernommen)
        $orgBlocks = [];
        $processedTeamIds = [];

        foreach($teams as $team) {
            if (in_array($team->id, $processedTeamIds)) continue;

            $keys = array_filter([$team->verein, $team->plz, $team->ort]);
            $foundBlock = false;
            foreach($orgBlocks as &$block) {
                foreach($keys as $k) {
                    if(in_array($k, $block['criteria'])) {
                        $block['teams'][$team->id] = $team;
                        $block['criteria'] = array_unique(array_merge($block['criteria'], $keys));
                        $foundBlock = true;
                        $processedTeamIds[] = $team->id;
                        break 2;
                    }
                }
            }
            if(!$foundBlock) {
                $orgBlocks[] = [
                    'criteria' => $keys,
                    'teams' => [$team->id => $team]
                ];
                $processedTeamIds[] = $team->id;
            }
        }

        // Konsolidierung
        $changed = true;
        while($changed) {
            $changed = false;
            for($i=0; $i < count($orgBlocks); $i++) {
                for($j=$i+1; $j < count($orgBlocks); $j++) {
                    $intersect = array_intersect($orgBlocks[$i]['criteria'], $orgBlocks[$j]['criteria']);
                    if(!empty($intersect)) {
                        $orgBlocks[$i]['criteria'] = array_unique(array_merge($orgBlocks[$i]['criteria'], $orgBlocks[$j]['criteria']));
                        foreach($orgBlocks[$j]['teams'] as $id => $t) {
                            $orgBlocks[$i]['teams'][$id] = $t;
                        }
                        array_splice($orgBlocks, $j, 1);
                        $changed = true;
                        break 2;
                    }
                }
            }
        }

        // Speichern in der Datenbank
        foreach($orgBlocks as $block) {
            // Nur Organisationen mit mehr als einem Team speichern
            if (count($block['teams']) <= 1) {
                continue;
            }

            // Finde das erste Team als Rubrik-Team
            $rubrikTeamId = null;
            $rubrikName = "Organisation";
            if (!empty($block['teams'])) {
                $rubrikTeamId = key($block['teams']);
                $rubrikName = $block['teams'][$rubrikTeamId]->teamname ?? "Organisation";
            }

            $org = RaffleOrganization::create([
                'name' => $rubrikName,
                'rubrik_team_id' => $rubrikTeamId,
                'criteria' => array_values($block['criteria']),
                'event_id' => $regattaId
            ]);

            $org->teams()->sync(array_keys($block['teams']));
        }

        return back()->with('success', 'Automatische Zuordnung abgeschlossen.');
    }

    /**
     * Erstellt eine neue Organisation aus einem Team und setzt dieses als Rubrik.
     */
    public function createFromTeam(Request $request)
    {
        $regattaId = Session::get('regattaSelectId');
        $request->validate([
            'team_id' => 'required|exists:regatta_teams,id'
        ]);

        $team = RegattaTeam::findOrFail($request->team_id);

        $org = RaffleOrganization::create([
            'name' => $team->teamname ?? "Organisation",
            'rubrik_team_id' => $team->id,
            'event_id' => $regattaId
        ]);

        $org->teams()->attach($team->id);

        return back()->with('success', 'Organisation aus Team erstellt.');
    }

    /**
     * Erstellt eine neue Organisation manuell.
     */
    public function store(Request $request)
    {
        $regattaId = Session::get('regattaSelectId');

        $request->validate([
            'name' => 'required|string|max:255',
        ]);

        RaffleOrganization::create([
            'name' => $request->name,
            'event_id' => $regattaId
        ]);

        return back()->with('success', 'Organisation erstellt.');
    }

    /**
     * Aktualisiert eine Organisation.
     */
    public function update(Request $request, $id)
    {
        $org = RaffleOrganization::findOrFail($id);
        $org->update($request->only('name'));
        return back()->with('success', 'Organisation aktualisiert.');
    }

    /**
     * Löscht eine Organisation und hebt die Team-Zuweisungen auf.
     */
    public function destroy($id)
    {
        $org = RaffleOrganization::findOrFail($id);
        $org->delete(); // Cascade delete handled by DB or relationship if defined, but mapping table has cascade on org delete
        return back()->with('success', 'Organisation gelöscht.');
    }

    /**
     * Setzt das Rubrik-Team für eine Organisation.
     */
    public function setRubrik(Request $request, $id)
    {
        $request->validate([
            'rubrik_team_id' => 'required|exists:regatta_teams,id'
        ]);

        $org = RaffleOrganization::findOrFail($id);
        $org->update(['rubrik_team_id' => $request->rubrik_team_id]);

        return back()->with('success', 'Rubrik-Team aktualisiert.');
    }

    /**
     * Weist ein Team einer Organisation zu.
     */
    public function assignTeam(Request $request)
    {
        $request->validate([
            'team_id' => 'required|exists:regatta_teams,id',
            'organization_id' => 'nullable'
        ]);

        // Entferne Team aus allen Organisationen für dieses Event (da 1-to-many logisch beibehalten wird)
        $regattaId = Session::get('regattaSelectId');
        $orgIds = RaffleOrganization::where('event_id', $regattaId)->pluck('id');

        DB::table('raffle_organization_teams')
            ->whereIn('organization_id', $orgIds)
            ->where('team_id', $request->team_id)
            ->delete();

        if ($request->organization_id) {
            $org = RaffleOrganization::findOrFail($request->organization_id);
            $org->teams()->attach($request->team_id);
        }

        return back()->with('success', 'Team-Zuweisung aktualisiert.');
    }

    /**
     * Löscht alle Organisationen für die aktuelle Regatta und setzt die Team-Zuweisungen zurück.
     */
    public function reset()
    {
        $regattaId = Session::get('regattaSelectId');
        if (!$regattaId) return back()->with('error', 'Keine Regatta ausgewählt.');

        // Alle Organisationen dieses Events löschen
        RaffleOrganization::where('event_id', $regattaId)->delete();
        // Die Teams-Zuweisungen werden durch cascade delete in raffle_organization_teams entfernt.

        return back()->with('success', 'Organisationen und Teams wurden zurückgesetzt.');
    }
}
