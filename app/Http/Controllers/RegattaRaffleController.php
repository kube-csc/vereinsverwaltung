<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Event;
use App\Models\RaceType;
use App\Models\RegattaTeam;
use App\Models\Tabledata;
use App\Models\Tabele;
use App\Models\Race;
use App\Models\Lane;
use App\Models\RaffleOrganization;
use App\Models\RafflePlan;
use App\Models\RafflePlanItem;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
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
            ->get()
            ->sortBy(['verein', 'ort', 'plz']);

        $raceTypes = RaceType::where('regatta_id', $regattaId)->get();

        $organizations = RaffleOrganization::where('event_id', $regattaId)
            ->with(['teams' => function($q) {
                $q->with(['teamWertungsGruppe']);
            }])
            ->get();

        // Teams ohne Organisation für dieses Event
        $assignedTeamIds = DB::table('raffle_organization_teams')
            ->whereIn('organization_id', $organizations->pluck('id'))
            ->pluck('team_id');

        $unassignedTeams = RegattaTeam::where('regatta_id', $regattaId)
            ->whereNotIn('id', $assignedTeamIds)
            ->with(['teamWertungsGruppe'])
            ->get();

        // Maximale Anzahl an Finals berechnen (für die UI)
        $teamsByGroup = $teams->where('status', 'Neuanmeldung')->groupBy('gruppe_id');
        $maxFinalsTotal = 0;
        foreach ($teamsByGroup as $gruppeId => $gruppeTeams) {
            $raceType = $raceTypes->firstWhere('id', $gruppeId);
            $lanesCount = $raceType->bahnen ?? 4;
            $maxFinalsInGroup = ceil($gruppeTeams->count() / $lanesCount);
            if ($maxFinalsInGroup > $maxFinalsTotal) {
                $maxFinalsTotal = $maxFinalsInGroup;
            }
        }

        $plans = RafflePlan::where('event_id', $regattaId)->where('is_draft', false)->orderBy('created_at', 'desc')->get();
        $draft = RafflePlan::where('event_id', $regattaId)->where('is_draft', true)->first();

        $previewData = null;
        if ($draft) {
            $previewData = $this->hydratePreview($draft->items()->orderBy('race_number')->orderBy('lane')->get()->toArray(), $draft);
        }

        return view('regattaManagement.regattaRaffle.index', [
            'regattaId' => $regattaId,
            'teams' => $teams,
            'raceTypes' => $raceTypes,
            'organizations' => $organizations,
            'unassignedTeams' => $unassignedTeams,
            'maxFinalsTotal' => $maxFinalsTotal,
            'previewData' => $previewData,
            'teamOpponents' => $draft ? ($draft->params['teamOpponents'] ?? []) : [],
            'maxHeatEndTime' => $draft ? ($draft->params['maxHeatEndTime'] ?? null) : null,
            'finalTeamlinks' => $this->getFinalTeamlinks($regattaId),
            'plans' => $plans,
            'draft' => $draft
        ]);
    }

    private function hydratePreview($preview, $draft = null)
    {
        if (!$preview) return null;

        $regattaId = Session::get('regattaSelectId');
        $gruppeNames = [];
        $teamNames = [];

        if ($draft && isset($draft->params['gruppeNames'])) {
            $gruppeNames = $draft->params['gruppeNames'];
            $teamNames = $draft->params['teamNames'];
        } else {
            // Fallback falls kein Draft vorhanden oder Params fehlen
            $teams = RegattaTeam::where('regatta_id', $regattaId)->get();
            foreach ($teams as $t) {
                $teamNames[$t->id] = ['name' => $t->teamname, 'teamlink' => $t->teamlink];
            }
            $raceTypes = RaceType::where('regatta_id', $regattaId)->get();
            foreach ($raceTypes as $rt) {
                $gruppeNames[$rt->id] = $rt->typ;
            }
        }

        $finalTeamlinks = $this->getFinalTeamlinks($regattaId);

        foreach ($preview as &$row) {
            // Markiere Siegerehrung basierend auf race_number und gruppe_id
            if ((isset($row['race_number']) && $row['race_number'] == 0) && (isset($row['gruppe_id']) && $row['gruppe_id'] == 0)) {
                $row['is_award_ceremony'] = true;
                $row['gruppe_name'] = 'Siegerehrung';
                // Wichtig: Wir behalten race_number 0, aber wir müssen sicherstellen, dass die Zeit für die Sortierung genutzt werden kann
            } else {
                $row['is_award_ceremony'] = false;
                $row['gruppe_name'] = $gruppeNames[$row['gruppe_id']] ?? 'Unbekannt';
            }
            if (isset($row['is_final']) && $row['is_final']) {
                $row['team_name'] = $row['placeholder_name'] ?? 'Platzhalter';
                $row['has_pokal'] = false;
                $row['last_final_platz'] = null;
            } else {
                $teamInfo = $teamNames[$row['team_id'] ?? null] ?? null;
                $row['team_name'] = is_array($teamInfo) ? ($teamInfo['name'] ?? 'Unbekannt') : ($teamInfo ?? 'Unbekannt');
                $teamlink = is_array($teamInfo) ? ($teamInfo['teamlink'] ?? 0) : 0;
                $row['has_pokal'] = ($teamlink > 0 && isset($finalTeamlinks[$teamlink]));
                $row['last_final_platz'] = $row['has_pokal'] ? $finalTeamlinks[$teamlink] : null;
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
        $awardCeremonyTimeStr = $request->input('award_ceremony_time', '18:00');
        $minTimeBeforeCeremony = $request->input('min_time_before_ceremony', 30);
        $finalePublishTimeStr = $request->input('finale_publish_time'); // Manuelle Eingabe falls vorhanden

        $finalsCount = $request->input('finals_count', 1);

        $teamsByGroup = RegattaTeam::where('regatta_id', $regattaId)
            ->where('status', 'Neuanmeldung')
            ->get()
            ->groupBy('gruppe_id');

        $preview = [];
        $startTimeObj = \Carbon\Carbon::parse($startTime);

        // Globaler Zeit-Tracker für alle Rennen
        $currentGlobalTime = $startTimeObj->copy();

        $raceNumber = 1;
        $opponentHistory = []; // Trackt, gegen wen ein Team bereits gefahren ist: [team_id => [opponent_id => count]]
        $lastStartTimes = []; // Trackt die letzte Startzeit pro Team: [team_id => Carbon]

        // Zuweisungen für raffle_organization vorab laden
        $orgAssignments = DB::table('raffle_organization_teams')
            ->whereIn('organization_id', RaffleOrganization::where('event_id', $regattaId)->pluck('id'))
            ->pluck('organization_id', 'team_id'); // [team_id => organization_id]

        $lastOrgStartTime = []; // Trackt die letzte Startzeit pro organization_id
        $lastOrgTeamId = [];    // Trackt die letzte Team-ID pro organization_id

        // Wir berechnen die maximale Endzeit der Vorläufe pro Gruppe, um danach den Abstand zum ersten Finale berechnen zu können
        $maxHeatEndTimePerGroup = [];
        $maxHeatEndTime = $startTimeObj->copy();

        // Vorläufe sammeln (Rundenweise abwechselnd nach Gruppen)
        $allHeats = [];
        $gruppeNames = []; // Cache für Gruppennamen
        $teamNames = [];   // Cache für Teamnamen

        // Zuerst Metadaten sammeln
        $groupsMeta = [];
        foreach ($teamsByGroup as $gruppeId => $gruppeTeams) {
            $raceType = RaceType::find($gruppeId);
            $lanesCount = $raceType->bahnen ?? 4;
            $gruppeNames[$gruppeId] = $raceType->typ ?? 'Unbekannt';

            foreach ($gruppeTeams as $t) {
                $teamNames[$t->id] = [
                    'name' => $t->teamname,
                    'teamlink' => $t->teamlink
                ];
            }

            $totalTeams = $gruppeTeams->count();
            if ($totalTeams === 0) continue;

            $neededHeatsPerRound = ceil($totalTeams / $lanesCount);
            $groupsMeta[$gruppeId] = [
                'lanes_count' => $lanesCount,
                'heats_per_round' => $neededHeatsPerRound
            ];
        }

        // Rundenweise abwechselnd sammeln
        for ($h = 1; $h <= $heatsCount; $h++) {
            // Wir suchen das Maximum an Läufen in dieser Runde über alle Gruppen
            $maxHeatsInRound = 0;
            foreach ($groupsMeta as $meta) {
                $maxHeatsInRound = max($maxHeatsInRound, $meta['heats_per_round']);
            }

            for ($i = 0; $i < $maxHeatsInRound; $i++) {
                foreach ($groupsMeta as $gruppeId => $meta) {
                    // Nur hinzufügen, wenn diese Gruppe in dieser Runde noch einen Lauf braucht
                    if ($i < $meta['heats_per_round']) {
                        $allHeats[] = [
                            'gruppe_id' => $gruppeId,
                            'heat_index' => $h,
                            'lanes_count' => $meta['lanes_count'],
                            'type' => 'heat'
                        ];
                    }
                }
            }
        }

        // Teams pro Gruppe für die Verteilung tracken
        $teamsRemainingPerHeat = []; // [gruppe_id][heat_index] => [team_ids]

        foreach ($teamsByGroup as $gruppeId => $gruppeTeams) {
            for ($h = 1; $h <= $heatsCount; $h++) {
                $teamsRemainingPerHeat[$gruppeId][$h] = $gruppeTeams->pluck('id')->toArray();
            }
        }

        // Vorläufe chronologisch planen und Teams zuweisen
        foreach ($allHeats as &$heatData) {
            $gruppeId = $heatData['gruppe_id'];
            $hIndex = $heatData['heat_index'];
            $lanesCount = $heatData['lanes_count'];

            $availableTeamIds = $teamsRemainingPerHeat[$gruppeId][$hIndex];
            $selectedTeams = [];

            // Bestimme die Anzahl der Teams für diesen Heat
            // Wir müssen die verbleibenden Teams gleichmäßig auf die verbleibenden Heats dieser Runde verteilen
            $remainingHeatsInRound = 0;
            foreach($allHeats as $futureHeat) {
                if ($futureHeat['gruppe_id'] == $gruppeId && $futureHeat['heat_index'] == $hIndex && !isset($futureHeat['teams'])) {
                    $remainingHeatsInRound++;
                }
            }

            // Wenn dies der aktuelle Heat ist (den wir gerade bearbeiten), ist er Teil der Zählung
            $numTeamsToPick = ceil(count($availableTeamIds) / $remainingHeatsInRound);
            $numTeamsToPick = min($numTeamsToPick, $lanesCount);

            for ($p = 0; $p < $numTeamsToPick; $p++) {
                $bestTeamId = null;
                $minScore = PHP_INT_MAX;

                foreach ($availableTeamIds as $teamId) {
                    // Score berechnen:
                    // 1. Durchmischung (Vorrang): Wie oft schon gegen Teams im aktuellen Lauf gefahren?
                    // 2. Durchmischung: Wie oft insgesamt gegen Gegner gefahren? (Sollte eigentlich über alle Heats gleich sein, aber hier relevant)
                    // 3. Pause: Zeit seit letztem Start.

                    $conflictScore = 0;
                    foreach ($selectedTeams as $selTeam) {
                        $conflictScore += ($opponentHistory[$teamId][$selTeam->id] ?? 0) * 1000;
                    }

                    // Historische Konflikte (gegen Teams, die nicht im aktuellen Lauf sind)
                    $totalHistoryConflicts = 0;
                    if (isset($opponentHistory[$teamId])) {
                        foreach($opponentHistory[$teamId] as $oppId => $count) {
                            $totalHistoryConflicts += $count;
                        }
                    }

                    $pauseInMinutes = 9999;
                    if (isset($lastStartTimes[$teamId])) {
                        // WICHTIG: diffInMinutes() gibt standardmäßig den absoluten Wert zurück.
                        // Da wir hier rückwärts blicken ($currentGlobalTime ist jetzt, $lastStartTimes ist früher),
                        // sollte es positiv sein. Wir erzwingen aber keine Richtung, sondern verlassen uns
                        // auf die sequentielle Abarbeitung.
                        $pauseInMinutes = $currentGlobalTime->diffInMinutes($lastStartTimes[$teamId]);
                    }

                    // Organisations-Pause berücksichtigen (Pivot-Zuordnung)
                    $orgId = $orgAssignments[$teamId] ?? null;
                    $orgMalus = 0;

                    if ($orgId && isset($lastOrgStartTime[$orgId])) {
                        // Berechnung des Abstands zum letzten Start EINES ANDEREN Teams dieser Organisation
                        $diff = $currentGlobalTime->diffInMinutes($lastOrgStartTime[$orgId]);

                        // Malus berechnen, wenn es ein anderes Team dieser Organisation ist
                        // Wir prüfen hier wieder auf $lastOrgTeamId != $teamId, weil der User möchte,
                        // dass derselbe Teilnehmer (Team) nicht betrachtet wird.
                        // Aber die anderen, die in einer Auswertungsgruppe zusammengefasst sind.
                        if ($lastOrgTeamId[$orgId] != $teamId && $diff < $minPause) {
                            $orgMalus = 1000 * ($minPause - $diff);
                        }
                    }

                    $score = $conflictScore + ($totalHistoryConflicts * 10) - ($pauseInMinutes) + $orgMalus;

                    if ($score < $minScore) {
                        $minScore = $score;
                        $bestTeamId = $teamId;
                    }
                }

                if ($bestTeamId) {
                    $teamObj = $teamsByGroup[$gruppeId]->firstWhere('id', $bestTeamId);
                    $selectedTeams[] = $teamObj;
                    $availableTeamIds = array_diff($availableTeamIds, [$bestTeamId]);
                }
            }

            $heatData['teams'] = $selectedTeams;
            $teamsRemainingPerHeat[$gruppeId][$hIndex] = array_values($availableTeamIds);

            // Konflikte für die Anzeige berechnen
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

            // Historie aktualisieren
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

            // Tracke Endzeit für die jeweilige Gruppe
            $gId = $heatData['gruppe_id'];
            if (!isset($maxHeatEndTimePerGroup[$gId]) || $raceTime->gt($maxHeatEndTimePerGroup[$gId])) {
                $maxHeatEndTimePerGroup[$gId] = $raceTime->copy();
            }

            foreach ($heatData['teams'] as $index => $team) {
                $pause = '-';
                $pauseMinutes = null;
                if (isset($lastStartTimes[$team->id])) {
                    $diff = $lastStartTimes[$team->id]->diffInMinutes($raceTime);
                    $pause = $diff;
                    $pauseMinutes = (int)$diff;
                }
                $lastStartTimes[$team->id] = $raceTime;

                // Organisations-Zeit aktualisieren und Abstand tracken
                $orgPause = '-';
                $orgName = '-';
                $orgTeamName = '';
                $orgId = $orgAssignments[$team->id] ?? null;

                if ($orgId) {
                    if (isset($lastOrgStartTime[$orgId])) {
                        $orgDiff = $lastOrgStartTime[$orgId]->diffInMinutes($raceTime);
                        // Wir zeigen den Organisations-Abstand an, wenn es ein anderes Team dieser Organisation war
                        $orgPause = $orgDiff;
                        $org = RaffleOrganization::find($orgId);
                        $orgName = $org->name ?? 'Organisation';

                        if (isset($lastOrgTeamId[$orgId]) && $lastOrgTeamId[$orgId] != $team->id) {
                            $lastTeamInfo = $teamNames[$lastOrgTeamId[$orgId]] ?? null;
                            $orgTeamName = is_array($lastTeamInfo) ? ($lastTeamInfo['name'] ?? 'Unbekannt') : ($lastTeamInfo ?? 'Unbekannt');
                        } else {
                            $orgPause = '-'; // Nicht anzeigen wenn es das gleiche Team ist
                        }
                    }
                    $lastOrgStartTime[$orgId] = $raceTime;
                    $lastOrgTeamId[$orgId] = $team->id;
                }

                $preview[] = [
                    'time' => $raceTime->format('H:i'),
                    'pause' => $pause,
                    'pause_minutes' => $pauseMinutes,
                    'org_pause' => $orgPause,
                    'org_name' => $orgName ?: ($team->verein ?: 'Verein/Ort'),
                    'org_team_name' => $orgTeamName,
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
        $finalsStartTime = \Carbon\Carbon::parse($finalsStartTimeStr);
        // Sicherstellen, dass Finals nach Vorläufen + Pause starten
        $earliestFinalsStart = $maxHeatEndTime->copy()->addMinutes($pauseAfterHeats);
        if ($finalsStartTime->lt($earliestFinalsStart)) {
            $finalsStartTime = $earliestFinalsStart;
        }

        $currentGlobalTime = $finalsStartTime->copy();

        // Finale sammeln (gruppiert nach Typ: A, B, C...)
        $finalsByType = [];
        foreach ($teamsByGroup as $gruppeId => $gruppeTeams) {
            $raceType = RaceType::find($gruppeId);
            $lanesCount = $raceType->bahnen ?? 4;
            $totalTeamsInGroup = $gruppeTeams->count();

            $maxSaneFinals = ceil($totalTeamsInGroup / $lanesCount);
            $actualFinalsCount = min($finalsCount, $maxSaneFinals);

            for ($f = 0; $f < $actualFinalsCount; $f++) {
                $finalLetter = chr(65 + ($actualFinalsCount - 1 - $f));
                $finalTypeName = $finalLetter . '-Finale';

                if (!isset($finalsByType[$finalTypeName])) {
                    $finalsByType[$finalTypeName] = [];
                }

                $finalsByType[$finalTypeName][] = [
                    'gruppe_id' => $gruppeId,
                    'lanes_count' => $lanesCount,
                    'total_teams' => $totalTeamsInGroup,
                    'f_index' => $f, // Index von hinten (0=A, 1=B...)
                    'final_type' => $finalTypeName
                ];
            }
        }

        // Sortiere Finals-Typen alphabetisch (A-Finale kommt zum Schluss, also Z-A sortieren für C, B, A)
        krsort($finalsByType);

        foreach ($finalsByType as $typeName => $finals) {
            foreach ($finals as $fData) {
                $gruppeId = $fData['gruppe_id'];
                $lanesCount = $fData['lanes_count'];
                $totalTeamsInGroup = $fData['total_teams'];
                $f = $fData['f_index'];
                $gruppeName = $gruppeNames[$gruppeId] ?? 'Unbekannt';

                $startPlatz = ($f * $lanesCount) + 1;
                $laneAssignment = $this->calculateSeededLanes($lanesCount);

                // Berechne Abstand zum letzten Vorlauf dieser Gruppe
                $finalPause = '-';
                $finalPauseMinutes = null;
                if (isset($maxHeatEndTimePerGroup[$gruppeId])) {
                    $diff = $currentGlobalTime->diffInMinutes($maxHeatEndTimePerGroup[$gruppeId]);
                    $finalPause = $diff . ' (Abst.)';
                    $finalPauseMinutes = (int)$diff;
                }

                foreach ($laneAssignment as $lIdx => $laneNumber) {
                    $platzImRanking = $startPlatz + $lIdx;
                    if ($platzImRanking > $totalTeamsInGroup) continue;

                    $preview[] = [
                        'time' => $currentGlobalTime->format('H:i'),
                        'pause' => $finalPause,
                        'pause_minutes' => $finalPauseMinutes,
                        'conflicts' => 0,
                        'race_number' => $raceNumber,
                        'lane' => $laneNumber,
                        'team_id' => null,
                        'placeholder_name' => "Platz $platzImRanking der Tabelle $gruppeName",
                        'gruppe_id' => $gruppeId,
                        'is_final' => true,
                        'final_type' => $typeName
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

        $maxRaceTime = $currentGlobalTime->copy()->subMinutes($interval);

        // Siegerehrung hinzufügen
        if ($awardCeremonyTimeStr) {
            $awardCeremonyTime = \Carbon\Carbon::parse($awardCeremonyTimeStr);
            $earliestCeremony = $maxRaceTime->copy()->addMinutes($minTimeBeforeCeremony);
            if ($awardCeremonyTime->lt($earliestCeremony)) {
                $awardCeremonyTime = $earliestCeremony;
            }

            $preview[] = [
                'time' => $awardCeremonyTime->format('H:i'),
                'is_award_ceremony' => true,
                'is_final' => false,
                'lane' => '-',
                'conflicts' => 0,
                'pause' => '-',
                'race_number' => 0,
                'gruppe_id' => 0,
                'team_id' => null
            ];

            // Veröffentlichungszeit: 1 Stunde nach Siegerehrung
            $finalePublishTime = $awardCeremonyTime->copy()->addHour();
            $finalePublishTimeStr = $finalePublishTime->format('H:i');
        }

        // Gegner-Zusammenfassung erstellen
        $teamOpponents = [];
        foreach ($opponentHistory as $teamId => $opponents) {
            $opponentsList = [];
            foreach ($opponents as $oppId => $count) {
                if (isset($teamNames[$oppId])) {
                    $oppName = is_array($teamNames[$oppId]) ? ($teamNames[$oppId]['name'] ?? 'Unbekannt') : $teamNames[$oppId];
                    $opponentsList[] = $oppName . ($count > 1 ? " ({$count}x)" : "");
                }
            }

            $teamInfo = $teamNames[$teamId] ?? null;
            $teamName = is_array($teamInfo) ? ($teamInfo['name'] ?? "Unbekannt ($teamId)") : ($teamInfo ?? "Unbekannt ($teamId)");
            $gruppeId = RegattaTeam::where('id', $teamId)->value('gruppe_id');
            $gruppeName = $gruppeNames[$gruppeId] ?? 'Unbekannt';

            $teamOpponents[$gruppeName][$teamName] = $opponentsList;
        }

        // Sortiere nach Gruppennamen und dann nach Teamnamen
        ksort($teamOpponents);
        foreach ($teamOpponents as $gruppeName => &$teams) {
            ksort($teams);
        }

        $params = [
            'start_time' => $startTime,
            'interval' => $interval,
            'wertungsart' => $wertungsart,
            'heats_count' => $heatsCount,
            'min_pause' => $minPause,
            'pause_after_heats' => $pauseAfterHeats,
            'finals_start_time' => $finalsStartTimeStr,
            'finals_count' => $finalsCount,
            'award_ceremony_time' => $awardCeremonyTimeStr,
            'min_time_before_ceremony' => $minTimeBeforeCeremony,
            'finale_publish_time' => $finalePublishTimeStr,
            'teamOpponents' => $teamOpponents,
            'maxHeatEndTime' => $maxHeatEndTime->format('H:i'),
            'gruppeNames' => $gruppeNames,
            'teamNames' => $teamNames,
        ];

        DB::transaction(function() use ($regattaId, $params, $preview) {
            // Alten Draft löschen
            $oldDraft = RafflePlan::where('event_id', $regattaId)->where('is_draft', true)->first();
            if ($oldDraft) {
                $oldDraft->delete();
            }

            // Neuen Draft erstellen
            $draft = RafflePlan::create([
                'event_id' => $regattaId,
                'version_name' => 'DRAFT',
                'start_time' => $params['start_time'],
                'interval' => $params['interval'],
                'min_pause' => $params['min_pause'],
                'final_pause' => $params['pause_after_heats'],
                'final_start_time' => $params['finals_start_time'],
                'award_ceremony_time' => $params['award_ceremony_time'],
                'min_award_pause' => $params['min_time_before_ceremony'],
                'heat_count' => $params['heats_count'],
                'params' => $params,
                'is_draft' => true,
                'user_id' => auth()->id(),
            ]);

            foreach ($preview as $item) {
                $lane = $item['lane'] ?? null;
                if (!is_numeric($lane)) $lane = null;

                RafflePlanItem::create([
                    'raffle_plan_id' => $draft->id,
                    'race_number' => $item['race_number'],
                    'gruppe_id' => is_numeric($item['gruppe_id']) ? $item['gruppe_id'] : null,
                    'time' => $item['time'],
                    'lane' => $lane,
                    'team_id' => $item['team_id'] ?? null,
                    'is_final' => $item['is_final'] ?? false,
                    'final_type' => $item['final_type'] ?? null,
                    'placeholder_name' => $item['placeholder_name'] ?? null,
                    'heat_index' => $item['heat_index'] ?? null,
                    'pause_minutes' => $item['pause_minutes'] ?? null,
                    'org_intervals' => $item['org_intervals'] ?? null,
                ]);
            }
        });

        return redirect()->route('regattaRaffle.index')->with('success', 'Vorschlag generiert und als Entwurf gespeichert.');
    }

    /**
     * Verschiebt ein Rennen in der Reihenfolge nach oben oder unten.
     */
    public function moveRace(Request $request)
    {
        $direction = $request->input('direction'); // 'up' oder 'down'
        $raceNumber = (int)$request->input('race_number');
        $regattaId = Session::get('regattaSelectId');

        // Versuche regattaId aus dem Request zu holen falls Session leer
        if (!$regattaId) {
             $regattaId = $request->input('regatta_id');
        }

        $draft = RafflePlan::where('event_id', $regattaId)->where('is_draft', true)->first();

        if (!$draft || $raceNumber === null) return back();

        // Alle Items laden, sortiert nach race_number
        $items = $draft->items()->orderBy('race_number')->orderBy('lane')->get();
        $grouped = $items->groupBy('race_number');

        // Wir ignorieren die Siegerehrung (0) beim Verschieben der Rennen,
        // oder wir behandeln sie als normales Element in der Liste.
        // Da die Siegerehrung oft am Ende steht, nehmen wir alle Keys.
        $keys = $grouped->keys()->sort()->values()->toArray();
        $index = array_search($raceNumber, $keys);

        if ($index === false) return back();

        if ($direction === 'up' && $index > 0) {
            $prevKey = $keys[$index - 1];
            $keys[$index - 1] = $raceNumber;
            $keys[$index] = $prevKey;
        } elseif ($direction === 'down' && $index < count($keys) - 1) {
            $nextKey = $keys[$index + 1];
            $keys[$index + 1] = $raceNumber;
            $keys[$index] = $nextKey;
        } else {
            return back();
        }

        DB::transaction(function() use ($keys, $grouped) {
            // Wir weisen neue fortlaufende Nummern zu
            // Aber die Siegerehrung (0) soll 0 bleiben.

            $newRaceNumber = 1;
            foreach ($keys as $oldRaceNumber) {
                if ($oldRaceNumber == 0) {
                    foreach ($grouped[$oldRaceNumber] as $item) {
                        $item->race_number = 0;
                        $item->save();
                    }
                    continue;
                }

                foreach ($grouped[$oldRaceNumber] as $item) {
                    $item->race_number = $newRaceNumber;
                    $item->save();
                }
                $newRaceNumber++;
            }
        });

        // Automatische Neuberechnung der Zeiten
        $request->merge([
            'start_time' => $draft->start_time,
            'interval' => $draft->interval,
            'finals_start_time' => $draft->final_start_time,
            'pause_after_heats' => $draft->final_pause,
            'award_ceremony_time' => $draft->award_ceremony_time,
            'min_time_before_ceremony' => $draft->min_award_pause,
        ]);
        $this->recalculateTimes($request);

        // Bestimme die neue race_number für die Weiterleitung (Sprungmarke)
        // In der Transaktion oben werden die Nummern neu vergeben:
        // 0 bleibt 0, alle anderen werden fortlaufend ab 1 vergeben.
        $finalRaceNumber = 0;
        $counter = 1;
        foreach ($keys as $k) {
            if ($k === $raceNumber) {
                $finalRaceNumber = ($k == 0) ? 0 : $counter;
                break;
            }
            if ($k != 0) $counter++;
        }

        // Wir hängen den Anker an die vorherige URL an.
        // Falls die URL bereits einen Anker hat, entfernen wir ihn zuerst.
        $url = url()->previous();
        if (($pos = strpos($url, '#')) !== false) {
            $url = substr($url, 0, $pos);
        }

        return redirect($url . "#race-" . $finalRaceNumber)
            ->with('success', 'Rennreihenfolge und Startzeiten angepasst.');
    }

    /**
     * Berechnet die Startzeiten basierend auf der aktuellen Reihenfolge neu.
     */
    public function recalculateTimes(Request $request)
    {
        $regattaId = Session::get('regattaSelectId');
        $draft = RafflePlan::where('event_id', $regattaId)->where('is_draft', true)->first();

        if (!$draft) return back()->with('error', 'Kein Entwurf vorhanden.');

        $startTime = $request->input('start_time');
        $interval = $request->input('interval', 10);
        $finalsStartTimeStr = $request->input('finals_start_time');
        $pauseAfterHeats = $request->input('pause_after_heats', 30);
        $awardCeremonyTimeStr = $request->input('award_ceremony_time');
        $minTimeBeforeCeremony = $request->input('min_time_before_ceremony', 30);
        $finalePublishTimeStr = $request->input('finale_publish_time');

        $items = $draft->items()->orderBy('race_number')->orderBy('lane')->get();

        $currentGlobalTime = \Carbon\Carbon::parse($startTime);
        $finalsStartTime = \Carbon\Carbon::parse($finalsStartTimeStr);

        // Gruppiere nach race_number
        $raceGroups = $items->groupBy('race_number');

        $maxHeatEndTime = $currentGlobalTime->copy();
        $maxHeatEndTimePerGroup = [];
        $lastStartTimes = []; // Trackt die letzte Startzeit pro Team
        $isFirstFinal = true;
        $ceremonyItems = [];

        // Wir brauchen Team-Namen für die Pausenberechnung bei Organisationen (optional, falls gewünscht)
        // Aber hier berechnen wir pause_minutes für das Team selbst.

        foreach ($raceGroups as $rn => $lanes) {
            // Check if it's a ceremony (race_number 0 usually)
            if ($rn == 0) {
                $ceremonyItems = $lanes;
                continue;
            }

            $isFinal = $lanes[0]->is_final;

            if ($isFinal && $isFirstFinal) {
                $earliestFinalStart = $maxHeatEndTime->copy()->addMinutes($pauseAfterHeats);
                if ($finalsStartTime->lt($earliestFinalStart)) {
                    $currentGlobalTime = $earliestFinalStart;
                } else {
                    $currentGlobalTime = $finalsStartTime->copy();
                }
                $isFirstFinal = false;
            }

            $timeStr = $currentGlobalTime->format('H:i');
            foreach ($lanes as $lane) {
                $lane->time = $timeStr;

                if ($isFinal) {
                    // Finale: Abstand zum letzten Vorlauf der Gruppe
                    if (isset($maxHeatEndTimePerGroup[$lane->gruppe_id])) {
                        $diff = $currentGlobalTime->diffInMinutes($maxHeatEndTimePerGroup[$lane->gruppe_id]);
                        $lane->pause_minutes = (int)$diff;
                    } else {
                        $lane->pause_minutes = null;
                    }
                } else {
                    // Vorläufe: Abstand zum letzten Start des Teams
                    if ($lane->team_id && isset($lastStartTimes[$lane->team_id])) {
                        $diff = $currentGlobalTime->diffInMinutes($lastStartTimes[$lane->team_id]);
                        $lane->pause_minutes = (int)$diff;
                    } else {
                        $lane->pause_minutes = null;
                    }

                    if ($lane->team_id) {
                        $lastStartTimes[$lane->team_id] = $currentGlobalTime->copy();
                    }
                }
                $lane->save();
            }

            if (!$isFinal) {
                $maxHeatEndTime = $currentGlobalTime->copy();
                foreach ($lanes as $lane) {
                    $maxHeatEndTimePerGroup[$lane->gruppe_id] = $currentGlobalTime->copy();
                }
            }
            $currentGlobalTime->addMinutes($interval);
        }

        $maxRaceTime = $currentGlobalTime->copy()->subMinutes($interval);

        if ($awardCeremonyTimeStr) {
            $ceremonyTime = \Carbon\Carbon::parse($awardCeremonyTimeStr);
            $earliestCeremony = $maxRaceTime->copy()->addMinutes($minTimeBeforeCeremony);
            if ($ceremonyTime->lt($earliestCeremony)) {
                $ceremonyTime = $earliestCeremony;
            }

            if (count($ceremonyItems) > 0) {
                foreach ($ceremonyItems as $cItem) {
                    $cItem->update([
                        'time' => $ceremonyTime->format('H:i'),
                        'race_number' => 0,
                        'gruppe_id' => 0
                    ]);
                }
            } else {
                RafflePlanItem::create([
                    'raffle_plan_id' => $draft->id,
                    'race_number' => 0,
                    'gruppe_id' => 0,
                    'time' => $ceremonyTime->format('H:i'),
                    'lane' => null,
                    'is_final' => false,
                ]);
            }

            $finalePublishTime = $ceremonyTime->copy()->addHour();
            $finalePublishTimeStr = $finalePublishTime->format('H:i');
        }

        // Params aktualisieren
        $params = $draft->params;
        $params['start_time'] = $startTime;
        $params['interval'] = $interval;
        $params['finals_start_time'] = $finalsStartTimeStr;
        $params['pause_after_heats'] = $pauseAfterHeats;
        $params['award_ceremony_time'] = $awardCeremonyTimeStr;
        $params['min_time_before_ceremony'] = $minTimeBeforeCeremony;
        $params['finale_publish_time'] = $finalePublishTimeStr;
        $params['maxHeatEndTime'] = $maxHeatEndTime->format('H:i');
        $draft->update(['params' => $params]);

        return back()->with('success', 'Startzeiten im Entwurf aktualisiert.');
    }

    /**
     * Berechnet die Bahnverteilung basierend auf dem Seeding.
     * Stärkste Teams in die Mitte, schwächere nach außen.
     */
    private function calculateSeededLanes($lanesCount)
    {
        // Logik für Drachenboot:
        // 4 Bahnen: 1.Platz -> B2, 2.Platz -> B3, 3.Platz -> B1, 4.Platz -> B4
        // Allgemein: Mitte finden, dann abwechselnd rechts/links
        $lanes = [];
        $center = ceil($lanesCount / 2);

        $currentLane = $center;
        $step = 1;
        $direction = 1; // 1 = rechts, -1 = links

        for ($i = 0; $i < $lanesCount; $i++) {
            $lanes[] = $currentLane;
            $currentLane = $currentLane + ($step * $direction);
            $direction *= -1;
            $step++;
        }

        // Die Reihenfolge in $lanes entspricht den Plätzen 1, 2, 3...
        // Wir wollen aber wissen, welcher Platz auf welcher Bahn landet.
        // Index 0 (Platz 1) -> Wert (Bahn)
        return $lanes;
    }

    /**
     * Speichert den aktuellen Vorschlag als Version in der Datenbank.
     */
    public function saveVersion(Request $request)
    {
        $regattaId = Session::get('regattaSelectId');
        $draft = RafflePlan::where('event_id', $regattaId)->where('is_draft', true)->first();

        if (!$draft) {
            return back()->with('error', 'Kein Entwurf zum Speichern vorhanden.');
        }

        $request->validate([
            'version_name' => 'required|string|max:255',
        ]);

        DB::transaction(function () use ($regattaId, $draft, $request) {
            $newPlan = $draft->replicate();
            $newPlan->version_name = $request->version_name;
            $newPlan->is_draft = false;
            $newPlan->save();

            foreach ($draft->items as $item) {
                $newItem = $item->replicate();
                $newItem->raffle_plan_id = $newPlan->id;
                $newItem->save();
            }
        });

        return back()->with('success', 'Entwurf erfolgreich als Version gespeichert.');
    }

    /**
     * Lädt eine gespeicherte Version und macht sie zum aktuellen Draft.
     */
    public function loadVersion($id)
    {
        $regattaId = Session::get('regattaSelectId');
        $plan = RafflePlan::where('event_id', $regattaId)->where('id', $id)->firstOrFail();

        DB::transaction(function() use ($regattaId, $plan) {
            RafflePlan::where('event_id', $regattaId)->where('is_draft', true)->delete();

            $draft = $plan->replicate();
            $draft->is_draft = true;
            $draft->version_name = 'DRAFT';
            $draft->save();

            foreach ($plan->items as $item) {
                $newItem = $item->replicate();
                $newItem->raffle_plan_id = $draft->id;
                $newItem->save();
            }
        });

        return redirect()->route('regattaRaffle.index')->with('success', 'Version wurde geladen.');
    }

    /**
     * Leert den aktuellen Entwurf in der Datenbank.
     */
    public function clearDraft()
    {
        $regattaId = Session::get('regattaSelectId');
        RafflePlan::where('event_id', $regattaId)->where('is_draft', true)->delete();
        return back()->with('success', 'Aktueller Entwurf wurde gelöscht.');
    }

    /**
     * Hilfsmethode um Namen-Caches in der Session zu aktualisieren.
     */
    private function refreshSessionCaches($regattaId)
    {
        $teams = RegattaTeam::where('regatta_id', $regattaId)->get();
        $teamNames = [];
        foreach ($teams as $t) {
            $teamNames[$t->id] = [
                'name' => $t->teamname . ' (' . ($t->verein ?: 'Kein Verein') . ')',
                'teamlink' => $t->teamlink
            ];
        }
        Session::put('raffleTeamNames', $teamNames);

        $raceTypes = RaceType::where('regatta_id', $regattaId)->get();
        $gruppeNames = [];
        foreach ($raceTypes as $rt) {
            $gruppeNames[$rt->id] = $rt->bezeichnung;
        }
        Session::put('raffleGruppeNames', $gruppeNames);
    }

    /**
     * Speichert den generierten Plan in der Datenbank (Finalisierung).
     */
    public function store(Request $request)
    {
        $regattaId = Session::get('regattaSelectId');
        $draft = RafflePlan::where('event_id', $regattaId)->where('is_draft', true)->first();

        if (!$draft) {
            return back()->with('error', 'Kein Entwurf zum Finalisieren vorhanden.');
        }

        $items = $draft->items()->orderBy('race_number')->orderBy('lane')->get();
        $preview = $this->hydratePreview($items->toArray(), $draft);
        $params = $draft->params;

        \DB::transaction(function () use ($regattaId, $preview, $params) {
            $userId = auth()->id();

            // Bestehende Tabellen, Rennen und Bahnen für dieses Event löschen, falls vorhanden
            // Nur Tabellen löschen, die über die Automatik erstellt wurden (z.B. basierend auf Namensschema oder wir löschen alle des Events)
            // Laut UI "Dies überschreibt bestehende Renn-Tabellen."

            $oldTabeleIds = Tabele::where('event_id', $regattaId)->pluck('id');
            // Cascade delete sollte Bahnen und Rennen mitlöschen, falls definiert,
            // ansonsten manuell löschen um sicher zu gehen.
            Race::where('event_id', $regattaId)->delete();
            Tabele::where('event_id', $regattaId)->delete();
            // Lanes haben SoftDeletes, wir löschen sie permanent für sauberen Stand
            Lane::where('regatta_id', $regattaId)->forceDelete();

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

    /**
     * Ermittelt die Teamlink-IDs und Platzierungen der Teams, die bei der letzten Regatta der gleichen Gruppe in einem Finale waren.
     * Die Platzierung wird basierend auf Punkten oder Zeit berechnet.
     */
    private function getFinalTeamlinks($currentRegattaId)
    {
        if (!$currentRegattaId) return [];

        $currentEvent = Event::find($currentRegattaId);
        if (!$currentEvent || !$currentEvent->eventGroup_id) {
            return [];
        }

        // Finde das letzte Event derselben Gruppe (zeitlich vor dem aktuellen)
        $lastEvent = Event::where('eventGroup_id', $currentEvent->eventGroup_id)
            ->where('id', '!=', $currentRegattaId)
            ->where('datumvon', '<', $currentEvent->datumvon)
            ->orderBy('datumvon', 'desc')
            ->first();

        if (!$lastEvent) {
            return [];
        }

        // Finde alle Final-Tabellen dieses Events
        $finalTables = Tabele::where('event_id', $lastEvent->id)
            ->where('finale', '>', 0)
            ->get();

        $results = [];

        foreach ($finalTables as $table) {
            $data = Tabledata::join('regatta_teams', 'tabledatas.mannschaft_id', '=', 'regatta_teams.id')
                ->where('tabledatas.tabele_id', $table->id)
                ->where('regatta_teams.teamlink', '>', 0)
                ->select('tabledatas.*', 'regatta_teams.teamlink')
                ->get();

            if ($table->wertungsart == 2) {
                // Zeit-Wertung: Kleinste Zeit gewinnt
                $sorted = $data->sort(function ($a, $b) {
                    if ($a->zeit === $b->zeit) {
                        return $a->hundert <=> $b->hundert;
                    }
                    return $a->zeit <=> $b->zeit;
                });
            } else {
                // Punkt-Wertung (Standard): Höchste Punkte gewinnen
                $sorted = $data->sortByDesc('punkte');
            }

            $rank = 1;
            foreach ($sorted as $row) {
                // Falls ein Teamlink in mehreren Finalen auftaucht (unwahrscheinlich), gewinnt die bessere Platzierung
                if (!isset($results[$row->teamlink]) || $rank < $results[$row->teamlink]['platz']) {
                    $results[$row->teamlink] = [
                        'platz' => $rank,
                        'tabelle' => $table->ueberschrift
                    ];
                }
                $rank++;
            }
        }

        return $results;
    }
}
