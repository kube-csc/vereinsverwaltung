<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RaceType;
use App\Models\RegattaTeam;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\File;

class RegattaRaffleController extends Controller
{
    /**
     * Zeigt die Spezifikation für die Rennplan-Erstellung (Logik-Anleitung) an.
     */
    public function __construct()
    {
        $this->middleware(['auth:sanctum', 'verified']);
    }

    public function index()
    {
        $regattaId = Session::get('regattaSelectId');

        if (!$regattaId) {
            return redirect()->route('event.indexRegatta')->with('error', 'Bitte wählen Sie zuerst eine Regatta aus.');
        }

        // Teams für die aktuelle Regatta laden
        $teams = RegattaTeam::where('regatta_id', $regattaId)
            ->with(['teamWertungsGruppe'])
            ->get();

        $raceTypes = RaceType::where('regatta_id', $regattaId)->get();

        return view('regattaManagement.regattaRaffle.index', [
            'regattaId' => $regattaId,
            'teams' => $teams,
            'raceTypes' => $raceTypes,
            'previewData' => $this->hydratePreview(Session::get('rafflePreview')),
            'teamOpponents' => Session::get('raffleOpponents'),
            'maxHeatEndTime' => Session::get('raffleMaxHeatEndTime')
        ]);
    }

    private function hydratePreview($preview)
    {
        if (!$preview) return null;

        $gruppeNames = Session::get('raffleGruppeNames', []);
        $teamNames = Session::get('raffleTeamNames', []);

        foreach ($preview as &$row) {
            $row['gruppe_name'] = $gruppeNames[$row['gruppe_id']] ?? 'Unbekannt';
            if ($row['is_final']) {
                $row['team_name'] = $row['placeholder_name'] ?? 'Platzhalter';
            } else {
                $row['team_name'] = $teamNames[$row['team_id']] ?? 'Unbekannt';
            }
        }

        return $preview;
    }

    /**
     * Generiert einen Vorschlag für den Rennplan.
     */
    public function generatePreview(Request $request)
    {
        $regattaId = Session::get('regattaSelectId');
        if (!$regattaId) return back()->with('error', 'Keine Regatta ausgewählt.');

        $startTime = $request->input('start_time', '10:00');
        $interval = $request->input('interval', 10); // Minuten
        $wertungsart = $request->input('wertungsart', 1); // 1 = Punkte
        $heatsCount = $request->input('heats_count', 3);
        $minPause = $request->input('min_pause', 20);
        $pauseAfterHeats = $request->input('pause_after_heats', 30);
        $finalsStartTimeStr = $request->input('finals_start_time', '14:00');

        $finalsCount = $request->input('finals_count', 1);

        $teamsByGroup = RegattaTeam::where('regatta_id', $regattaId)
            ->where('status', 'Neuanmeldung')
            ->get()
            ->groupBy('gruppe_id');

        $preview = [];
        $startTimeObj = \Carbon\Carbon::createFromFormat('H:i', $startTime);

        // Globaler Zeit-Tracker für alle Rennen
        $currentGlobalTime = $startTimeObj->copy();

        $raceNumber = 1;
        $opponentHistory = []; // Trackt, gegen wen ein Team bereits gefahren ist: [team_id => [opponent_id => count]]
        $lastStartTimes = []; // Trackt die letzte Startzeit pro Team: [team_id => Carbon]

        // Wir berechnen die maximale Endzeit der Vorläufe, um danach die Pause einzuhalten
        $maxHeatEndTime = $startTimeObj->copy();

        // Vorläufe sammeln und planen
        $allHeats = [];
        $gruppeNames = []; // Cache für Gruppennamen
        $teamNames = [];   // Cache für Teamnamen

        foreach ($teamsByGroup as $gruppeId => $gruppeTeams) {
            $raceType = RaceType::find($gruppeId);
            $lanesCount = $raceType->bahnen ?? 4;
            $gruppeNames[$gruppeId] = $raceType->typ ?? 'Unbekannt';

            $teamList = $gruppeTeams->shuffle()->values();
            $totalTeams = $teamList->count();
            if ($totalTeams === 0) continue;

            foreach ($teamList as $t) {
                $teamNames[$t->id] = $t->teamname;
            }

            $neededHeatsPerRound = ceil($totalTeams / $lanesCount);

            for ($h = 1; $h <= $heatsCount; $h++) {
                $rotatedTeams = [];
                for ($i = 0; $i < $totalTeams; $i++) {
                    $rotatedTeams[] = $teamList->get(($i + ($h - 1)) % $totalTeams);
                }

                $heats = [];
                for ($i = 0; $i < $neededHeatsPerRound; $i++) {
                    $heats[$i] = [];
                }

                foreach ($rotatedTeams as $idx => $team) {
                    $heatIdx = $idx % $neededHeatsPerRound;
                    if (count($heats[$heatIdx]) < $lanesCount) {
                        $heats[$heatIdx][] = $team;
                    }
                }

                foreach ($heats as $heatIdx => $heatTeams) {
                    $allHeats[] = [
                        'gruppe_id' => $gruppeId,
                        'heat_index' => $h,
                        'teams' => $heatTeams,
                        'lanes_count' => $lanesCount,
                        'type' => 'heat'
                    ];
                }
            }
        }

        // Vorläufe chronologisch planen
        foreach ($allHeats as $heatData) {
            $currentRaceConflicts = [];
            foreach ($heatData['teams'] as $teamA) {
                $conflictsForA = 0;
                foreach ($heatData['teams'] as $teamB) {
                    if ($teamA->id !== $teamB->id) {
                        if (isset($opponentHistory[$teamA->id][$teamB->id])) {
                            $conflictsForA += $opponentHistory[$teamA->id][$teamB->id];
                        }
                    }
                }
                $currentRaceConflicts[$teamA->id] = $conflictsForA;
            }

            foreach ($heatData['teams'] as $teamA) {
                foreach ($heatData['teams'] as $teamB) {
                    if ($teamA->id !== $teamB->id) {
                        $opponentHistory[$teamA->id][$teamB->id] = ($opponentHistory[$teamA->id][$teamB->id] ?? 0) + 1;
                    }
                }
            }

            $raceTime = $currentGlobalTime->copy();
            if ($raceTime->gt($maxHeatEndTime)) {
                $maxHeatEndTime = $raceTime->copy();
            }

            foreach ($heatData['teams'] as $index => $team) {
                $pause = '-';
                if (isset($lastStartTimes[$team->id])) {
                    $diff = $lastStartTimes[$team->id]->diffInMinutes($raceTime);
                    $pause = $diff . ' Min';
                }
                $lastStartTimes[$team->id] = $raceTime;

                $preview[] = [
                    'time' => $raceTime->format('H:i'),
                    'pause' => $pause,
                    'conflicts' => $currentRaceConflicts[$team->id] ?? 0,
                    'race_number' => $raceNumber,
                    'lane' => $index + 1,
                    'team_id' => $team->id,
                    'gruppe_id' => $heatData['gruppe_id'],
                    'heat_index' => $heatData['heat_index'],
                    'is_final' => false
                ];
            }
            $raceNumber++;
            $currentGlobalTime->addMinutes($interval);
        }

        // Finale generieren
        $finalsStartTime = \Carbon\Carbon::createFromFormat('H:i', $finalsStartTimeStr);
        // Sicherstellen, dass Finals nach Vorläufen + Pause starten
        $earliestFinalsStart = $maxHeatEndTime->copy()->addMinutes($pauseAfterHeats);
        if ($finalsStartTime->lt($earliestFinalsStart)) {
            $finalsStartTime = $earliestFinalsStart;
        }

        $currentGlobalTime = $finalsStartTime->copy();

        foreach ($teamsByGroup as $gruppeId => $gruppeTeams) {
            $raceType = RaceType::find($gruppeId);
            $lanesCount = $raceType->bahnen ?? 4;
            $gruppeName = $gruppeNames[$gruppeId] ?? 'Unbekannt';
            $totalTeamsInGroup = $gruppeTeams->count();

            // Berechne, wie viele Finals für diese Gruppe maximal sinnvoll sind
            // (Alle Teams sollen maximal ein Finale fahren)
            $maxSaneFinals = ceil($totalTeamsInGroup / $lanesCount);
            $actualFinalsCount = min($finalsCount, $maxSaneFinals);

            for ($f = $actualFinalsCount - 1; $f >= 0; $f--) {
                $finalLetter = chr(65 + $f); // A, B, C...
                $finalTypeName = $finalLetter . '-Finale';

                $startPlatz = ($f * $lanesCount) + 1;
                $endPlatz = ($f + 1) * $lanesCount;

                for ($l = 1; $l <= $lanesCount; $l++) {
                    $platzImRanking = $startPlatz + $l - 1;
                    $preview[] = [
                        'time' => $currentGlobalTime->format('H:i'),
                        'pause' => '-',
                        'conflicts' => 0,
                        'race_number' => $raceNumber,
                        'lane' => $l,
                        'team_id' => null, // Platzhalter
                        'placeholder_name' => "Platz $platzImRanking der Tabelle $gruppeName",
                        'gruppe_id' => $gruppeId,
                        'is_final' => true,
                        'final_type' => $finalTypeName
                    ];
                }
                $raceNumber++;
                $currentGlobalTime->addMinutes($interval);
            }
        }

        $preview = collect($preview)->sortBy([
            ['time', 'asc'],
            ['race_number', 'asc'],
            ['lane', 'asc']
        ])->values()->all();

        // Gegner-Zusammenfassung erstellen
        $teamOpponents = [];
        foreach ($opponentHistory as $teamId => $opponents) {
            $opponentsList = [];
            foreach ($opponents as $oppId => $count) {
                if (isset($teamNames[$oppId])) {
                    $opponentsList[] = $teamNames[$oppId] . ($count > 1 ? " ({$count}x)" : "");
                }
            }

            $teamName = $teamNames[$teamId] ?? "Unbekannt ($teamId)";
            $gruppeId = RegattaTeam::where('id', $teamId)->value('gruppe_id');
            $gruppeName = $gruppeNames[$gruppeId] ?? 'Unbekannt';

            $teamOpponents[$gruppeName][$teamName] = $opponentsList;
        }

        // Sortiere nach Gruppennamen und dann nach Teamnamen
        ksort($teamOpponents);
        foreach ($teamOpponents as $gruppeName => &$teams) {
            ksort($teams);
        }

        Session::put('rafflePreview', $preview);
        Session::put('raffleOpponents', $teamOpponents);
        Session::put('raffleGruppeNames', $gruppeNames);
        Session::put('raffleTeamNames', $teamNames);
        Session::put('raffleMaxHeatEndTime', $maxHeatEndTime->format('H:i'));
        Session::put('raffleParams', $request->only(['start_time', 'interval', 'wertungsart', 'heats_count', 'min_pause', 'pause_after_heats', 'finals_start_time', 'finals_count']));

        return redirect()->route('regattaRaffle.index')->with('success', 'Vorschlag generiert.');
    }

    /**
     * Speichert den generierten Plan in der Datenbank.
     */
    public function store(Request $request)
    {
        $regattaId = Session::get('regattaSelectId');
        $preview = $this->hydratePreview(Session::get('rafflePreview'));
        $params = Session::get('raffleParams');

        if (!$regattaId || !$preview) {
            return back()->with('error', 'Kein Vorschlag zum Speichern vorhanden.');
        }

        \DB::transaction(function () use ($regattaId, $preview, $params) {
            $userId = auth()->id();

            // Gruppenweise Tabellen und Rennen erstellen
            $groupedByRace = collect($preview)->groupBy('race_number');

            // Wir erstellen pro Renngruppe (z.B. Mixed) und pro Vorlauf eine Tabelle, falls noch nicht da
            $tabeleIds = [];

            foreach ($groupedByRace as $raceNum => $laneData) {
                $firstLane = $laneData->first();
                $gruppeId = $firstLane['gruppe_id'];
                $heatIndex = $firstLane['heat_index'] ?? 1;
                $key = $gruppeId . '_' . $heatIndex;

                if (!isset($tabeleIds[$key])) {
                    $tabele = new \App\Models\Tabele();
                    $tabele->event_id = $regattaId;
                    $tabele->gruppe_id = $gruppeId;
                    $tabele->tabelleDatumVon = now();

                    if ($firstLane['is_final']) {
                        $tabele->ueberschrift = $firstLane['final_type'] . ' ' . $firstLane['gruppe_name'];
                        $tabele->finale = 1;
                    } else {
                        $tabele->ueberschrift = $heatIndex . '. Vorlauf ' . $firstLane['gruppe_name'];
                        $tabele->finale = 0;
                    }

                    $tabele->tabelleLevelVon = 0;
                    $tabele->tabelleLevelBis = 0;
                    $tabele->wertungsart = $params['wertungsart'] ?? 1;
                    $tabele->autor_id = $userId;
                    $tabele->bearbeiter_id = $userId;
                    $tabele->save();

                    $tabeleIds[$key] = $tabele->id;
                }

                $race = new \App\Models\Race();
                $race->event_id = $regattaId;
                $race->tabele_id = $tabeleIds[$key];
                $race->gruppe_id = $gruppeId;
                $race->rennDatum = now();
                $race->rennUhrzeit = $firstLane['time'];

                if ($firstLane['is_final']) {
                    $race->rennBezeichnung = $firstLane['final_type'];
                } else {
                    $race->rennBezeichnung = 'Lauf ' . $raceNum;
                }

                $race->bahnen = count($laneData);
                $race->autor_id = $userId;
                $race->bearbeiter_id = $userId;
                $race->save();

                foreach ($laneData as $lane) {
                    $laneModel = new \App\Models\Lane();
                    $laneModel->regatta_id = $regattaId;
                    $laneModel->rennen_id = $race->id;
                    $laneModel->tabele_id = $tabeleIds[$key];
                    $laneModel->mannschaft_id = $lane['team_id'] ?? null;
                    $laneModel->bahn = $lane['lane'];
                    $laneModel->zeit = '00:00:00';
                    $laneModel->hundert = 0;
                    $laneModel->punkte = 0;
                    $laneModel->platz = 0;
                    $laneModel->autor_id = $userId;
                    $laneModel->bearbeiter_id = $userId;
                    $laneModel->save();
                }
            }
        });

        Session::forget(['rafflePreview', 'raffleParams']);

        return redirect()->route('regattaRaffle.index')->with('success', 'Rennplan erfolgreich gespeichert.');
    }
}
