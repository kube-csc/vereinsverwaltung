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
        $minPauseOrg = $request->input('min_pause_org', 10);
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

            $isSmall = ($totalTeams <= $lanesCount);

            // Der User möchte, dass Teams möglichst alle gegeneinander fahren.
            // Wir berechnen die Bahnenbelegung so, dass wir die Meldezahl bestmöglich ausnutzen.
            $effectiveLanes = $lanesCount;
            if ($isSmall) {
                // Wenn weniger Teams als Bahnen da sind, nehmen wir die Anzahl der Teams als Bahnenmaßstab
                // damit ceil(totalTeams / totalTeams) = 1 Heat ergibt.
                $effectiveLanes = max(1, $totalTeams);
            }

            $neededHeatsPerRound = ceil($totalTeams / $effectiveLanes);
            $groupsMeta[$gruppeId] = [
                'lanes_count' => ($totalTeams <= $lanesCount) ? $totalTeams : $lanesCount,
                'heats_per_round' => $neededHeatsPerRound,
                'is_small' => $isSmall,
                'total_teams' => $totalTeams,
                'orig_lanes' => $lanesCount
            ];
        }

        // Rundenweise abwechselnd sammeln
        for ($h = 1; $h <= $heatsCount; $h++) {
            // Bestimme die Reihenfolge der Gruppen für diese Runde
            // 1. Gruppen mit mehr Teams als Bahnen (is_small = false)
            //    Innerhalb dieser: aufsteigend nach Teamanzahl
            // 2. Gruppen mit Teams <= Bahnen (is_small = true)
            $sortedGroupIds = array_keys($groupsMeta);
            usort($sortedGroupIds, function($a, $b) use ($groupsMeta) {
                $metaA = $groupsMeta[$a];
                $metaB = $groupsMeta[$b];

                if ($metaA['is_small'] !== $metaB['is_small']) {
                    return $metaA['is_small'] ? 1 : -1;
                }

                return $metaA['total_teams'] <=> $metaB['total_teams'];
            });

            // Wir suchen das Maximum an Läufen in dieser Runde über alle Gruppen
            $maxHeatsInRound = 0;
            foreach ($groupsMeta as $meta) {
                $maxHeatsInRound = max($maxHeatsInRound, $meta['heats_per_round']);
            }

            for ($i = 0; $i < $maxHeatsInRound; $i++) {
                foreach ($sortedGroupIds as $gruppeId) {
                    $meta = $groupsMeta[$gruppeId];
                    // Nur hinzufügen, wenn diese Gruppe in dieser Runde noch einen Lauf braucht
                    if ($i < $meta['heats_per_round']) {
                        $allHeats[] = [
                            'gruppe_id' => $gruppeId,
                            'heat_index' => $h, // Korrektur: h ist die Runde (1 bis heatsCount)
                            'round_heat_index' => $i + 1, // Index des Heats innerhalb dieser Runde
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
        $heatIndexInAllHeats = 0;
        while ($heatIndexInAllHeats < count($allHeats)) {
            $heatData = &$allHeats[$heatIndexInAllHeats];
            $gruppeId = $heatData['gruppe_id'];
            $hIndex = $heatData['heat_index'];
            $rHeatIndex = $heatData['round_heat_index'];
            $lanesCount = $heatData['lanes_count'];

            // Sonderfall: Wenn die Gruppe <= Bahnen hat, sollen alle in einem Lauf starten.
            $raceType = RaceType::find($gruppeId);
            $origLanesCount = $raceType->bahnen ?? 4;
            $gruppeTeamsTotal = RegattaTeam::where('regatta_id', $regattaId)->where('gruppe_id', $gruppeId)->where('status', 'Neuanmeldung')->count();
            $isSmallGroup = ($gruppeTeamsTotal <= $origLanesCount);

            $availableTeamIds = $teamsRemainingPerHeat[$gruppeId][$hIndex];

            // Prüfen ob Pause für Teams in diesem Heat ausreicht
            $hasPauseConflict = false;
            foreach ($availableTeamIds as $tid) {
                if (isset($lastStartTimes[$tid]) && $currentGlobalTime->diffInMinutes($lastStartTimes[$tid]) < $minPause) {
                    $hasPauseConflict = true;
                    break;
                }
                $orgId = $orgAssignments[$tid] ?? null;
                if ($orgId && isset($lastOrgStartTime[$orgId])) {
                    $diff = $currentGlobalTime->diffInMinutes($lastOrgStartTime[$orgId]);
                    if ($lastOrgTeamId[$orgId] != $tid && $diff < $minPauseOrg) {
                        $hasPauseConflict = true;
                        break;
                    }
                }
            }

            // Wenn es eine kleine Gruppe ist und ein Pausenkonflikt besteht,
            // versuchen wir diesen Heat zu überspringen (als Puffer nutzen)
            if ($isSmallGroup && $hasPauseConflict) {
                // Suchen nach einem späteren Heat in allHeats, der KEIN Pausenkonflikt hat
                $foundAlternative = false;
                for ($nextIdx = $heatIndexInAllHeats + 1; $nextIdx < count($allHeats); $nextIdx++) {
                    $nextHeat = $allHeats[$nextIdx];
                    if (isset($nextHeat['teams'])) continue; // Schon geplant

                    $nextGruppeId = $nextHeat['gruppe_id'];
                    $nextHIndex = $nextHeat['heat_index'];
                    $nextAvailableTeams = $teamsRemainingPerHeat[$nextGruppeId][$nextHIndex];

                    $nextHasPauseConflict = false;
                    foreach ($nextAvailableTeams as $ntid) {
                        if (isset($lastStartTimes[$ntid]) && $currentGlobalTime->diffInMinutes($lastStartTimes[$ntid]) < $minPause) {
                            $nextHasPauseConflict = true;
                            break;
                        }
                        $norgId = $orgAssignments[$ntid] ?? null;
                        if ($norgId && isset($lastOrgStartTime[$norgId])) {
                            $ndiff = $currentGlobalTime->diffInMinutes($lastOrgStartTime[$norgId]);
                            if ($lastOrgTeamId[$norgId] != $ntid && $ndiff < $minPauseOrg) {
                                $nextHasPauseConflict = true;
                                break;
                            }
                        }
                    }

                    if (!$nextHasPauseConflict) {
                        // Tausche aktuellen Heat mit alternativem Heat
                        $temp = $allHeats[$heatIndexInAllHeats];
                        $allHeats[$heatIndexInAllHeats] = $allHeats[$nextIdx];
                        $allHeats[$nextIdx] = $temp;
                        $foundAlternative = true;
                        break;
                    }
                }

                if ($foundAlternative) {
                    // Wir haben getauscht, also müssen wir die Daten für den neuen Heat an dieser Position neu laden
                    $heatData = &$allHeats[$heatIndexInAllHeats];
                    $gruppeId = $heatData['gruppe_id'];
                    $hIndex = $heatData['heat_index'];
                    $rHeatIndex = $heatData['round_heat_index'];
                    $lanesCount = $heatData['lanes_count'];
                    $availableTeamIds = $teamsRemainingPerHeat[$gruppeId][$hIndex];
                }
            }

            $selectedTeams = [];

            // Bestimme die Anzahl der Teams für diesen Heat
            // Wir müssen die verbleibenden Teams gleichmäßig auf die verbleibenden Heats dieser Runde verteilen
            $heatsRemainingInThisRound = 0;
            foreach($allHeats as $idx => $fHeat) {
                if ($idx >= $heatIndexInAllHeats && $fHeat['gruppe_id'] == $gruppeId && $fHeat['heat_index'] == $hIndex && !isset($fHeat['teams'])) {
                    $heatsRemainingInThisRound++;
                }
            }

            $numTeamsToPick = ceil(count($availableTeamIds) / max(1, $heatsRemainingInThisRound));

            if ($gruppeTeamsTotal <= $origLanesCount) {
                $numTeamsToPick = count($availableTeamIds);
            }

            $numTeamsToPick = min($numTeamsToPick, $lanesCount);

            // OPTIMIERUNG: Wenn wir hier Teams haben, die die Mindestpause unterschreiten würden,
            // versuchen wir sie in einen späteren Heat derselben Runde zu schieben, falls möglich.
            $tooEarlyTeams = [];
            $safeTeams = [];

            foreach ($availableTeamIds as $tid) {
                $isTooEarlyTeam = isset($lastStartTimes[$tid]) && $currentGlobalTime->diffInMinutes($lastStartTimes[$tid]) < $minPause;

                $orgId = $orgAssignments[$tid] ?? null;
                $isTooEarlyOrg = false;
                if ($orgId && isset($lastOrgStartTime[$orgId])) {
                    $diff = $currentGlobalTime->diffInMinutes($lastOrgStartTime[$orgId]);
                    if ($lastOrgTeamId[$orgId] != $tid && $diff < $minPauseOrg) {
                        $isTooEarlyOrg = true;
                    }
                }

                if ($isTooEarlyTeam || $isTooEarlyOrg) {
                    $tooEarlyTeams[] = $tid;
                } else {
                    $safeTeams[] = $tid;
                }
            }

            // Wenn wir genug "sichere" Teams haben, nehmen wir bevorzugt diese.
            // Aber Vorsicht: Wir dürfen nicht zu viele Teams aufschieben, sonst fehlen sie später.
            // Nur wenn es noch zukünftige Heats in dieser Runde gibt.
            if (count($tooEarlyTeams) > 0 && $heatsRemainingInThisRound > 1) {
                // Wir prüfen wie viele Teams wir maximal aufschieben können
                $maxToDeffer = count($availableTeamIds) - $numTeamsToPick;
                $canDeffer = min(count($tooEarlyTeams), $maxToDeffer);

                if ($canDeffer > 0) {
                    // Wir nehmen die ersten $canDeffer Teams aus tooEarlyTeams und schieben sie ans Ende von availableTeamIds
                    $toDeffer = array_splice($tooEarlyTeams, 0, $canDeffer);

                    // Sicherstellen, dass safeTeams und tooEarlyTeams keine Duplikate enthalten
                    $availableTeamIds = array_values(array_unique(array_merge($safeTeams, $tooEarlyTeams, $toDeffer)));
                }
            }

            // Sicherstellen, dass wir nicht mehr Teams wählen, als noch verfügbar sind
            $numTeamsToPick = min($numTeamsToPick, count($availableTeamIds));

            for ($p = 0; $p < $numTeamsToPick; $p++) {
                $bestTeamId = null;
                $minScore = PHP_INT_MAX;

                // Wir versuchen hier, das beste Team für diesen Slot zu finden.
                // Da wir später noch eine globale Optimierung (Swap-Loop) machen,
                // reicht hier ein solides Initial-Ranking.
                // WICHTIG: Nur Teams wählen, die noch nicht in selectedTeams sind
                // UND auch noch nicht in diesem Lauf (race_number) im $preview vorkommen!
                $alreadySelectedIdsInHeat = collect($selectedTeams)->pluck('id')->toArray();
                $alreadyInPreviewForThisRace = collect($preview)
                    ->where('race_number', $raceNumber)
                    ->pluck('team_id')
                    ->filter()
                    ->toArray();

                $excludedIds = array_unique(array_merge($alreadySelectedIdsInHeat, $alreadyInPreviewForThisRace));
                $candidatesForThisSlot = array_values(array_diff($availableTeamIds, $excludedIds));

                foreach ($candidatesForThisSlot as $teamId) {
                    $conflictScore = 0;
                    foreach ($selectedTeams as $selTeam) {
                        $conflictScore += ($opponentHistory[$teamId][$selTeam->id] ?? 0) * 1000;
                    }

                    $totalHistoryConflicts = 0;
                    if (isset($opponentHistory[$teamId])) {
                        foreach($opponentHistory[$teamId] as $oppId => $count) {
                            $totalHistoryConflicts += $count;
                        }
                    }

                    $pauseInMinutes = 9999;
                    if (isset($lastStartTimes[$teamId])) {
                        $pauseInMinutes = $currentGlobalTime->diffInMinutes($lastStartTimes[$teamId]);
                        if ($pauseInMinutes < $minPause) {
                            $conflictScore += ($minPause - $pauseInMinutes) * 10000; // Massiv erhöht für Vor-Selektion
                        }
                    }

                    $orgId = $orgAssignments[$teamId] ?? null;
                    $orgMalus = 0;
                    if ($orgId && isset($lastOrgStartTime[$orgId])) {
                        $diff = $currentGlobalTime->diffInMinutes($lastOrgStartTime[$orgId]);
                        if ($lastOrgTeamId[$orgId] != $teamId && $diff < $minPauseOrg) {
                            $orgMalus = 50000 * ($minPauseOrg - $diff); // Massiv erhöht für Vor-Selektion
                        }
                    }

                    $score = $conflictScore + ($totalHistoryConflicts * 10) - ($pauseInMinutes * 2) + $orgMalus;

                    if ($score < $minScore) {
                        $minScore = $score;
                        $bestTeamId = $teamId;
                    }
                }

                if ($bestTeamId) {
                    $teamObj = $teamsByGroup[$gruppeId]->firstWhere('id', $bestTeamId);
                    $selectedTeams[] = $teamObj;
                    $availableTeamIds = array_values(array_diff($availableTeamIds, [$bestTeamId]));

                    // WICHTIG: Team auch aus den anderen Kategorien (tooEarlyTeams, safeTeams) entfernen,
                    // falls es dort noch existiert, um Doppel-Zuweisung durch fehlerhafte availableTeamIds-Updates zu vermeiden.
                    $safeTeams = array_values(array_diff($safeTeams, [$bestTeamId]));
                    $tooEarlyTeams = array_values(array_diff($tooEarlyTeams, [$bestTeamId]));
                } else {
                    // Fallback: Falls kein bestTeamId gefunden wurde (sollte nicht passieren),
                    // brechen wir das Picking für diesen Heat ab.
                    break;
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
                    'heat_index' => $heatData['heat_index'], // Dies ist nun die Runde (1 bis heatsCount)
                    'heat_level' => 'Vorlauf ' . $heatData['heat_index'],
                    'round_heat_index' => $heatData['round_heat_index'],
                    'is_final' => false
                ];
            }
            $raceNumber++;
            $currentGlobalTime->addMinutes($interval);
            $heatIndexInAllHeats++;
        }

        // --- NACHBESSERUNG KONFLIKTE (Opponent History) ---
        // Wenn wir Teams getauscht haben, müssen wir die opponentHistory und conflicts in $preview neu berechnen
        // da die initiale Berechnung während der sequentiellen Generierung stattfand.
        $opponentHistory = [];
        $heatsGrouped = collect($preview)->where('is_final', false)->where('race_number', '>', 0)->groupBy('race_number');
        foreach ($heatsGrouped as $rNum => $raceItems) {
            $teamIds = $raceItems->pluck('team_id')->filter()->toArray();
            foreach ($teamIds as $tA) {
                foreach ($teamIds as $tB) {
                    if ($tA != $tB) {
                        $opponentHistory[$tA][$tB] = ($opponentHistory[$tA][$tB] ?? 0) + 1;
                    }
                }
            }
        }

        foreach ($preview as &$pItem) {
            if ($pItem['is_final'] || $pItem['race_number'] == 0 || !$pItem['team_id']) continue;
            $tA = $pItem['team_id'];
            $currentRaceTeams = collect($preview)->where('race_number', $pItem['race_number'])->pluck('team_id')->toArray();
            $conflicts = 0;
            foreach ($currentRaceTeams as $tB) {
                if ($tB && $tA != $tB) {
                    // Konflikt = wie oft haben sie INGESAMT gegeneinander gespielt (bis zu diesem Punkt im NEUEN Plan)
                    // Eigentlich wollen wir hier die Wiederholungen sehen.
                    // Wir zählen einfach die Historie bis zu diesem Rennen neu.
                }
            }
        }
        // Vereinfacht: Wir aktualisieren nur die conflicts Anzeige basierend auf der finalen History
        foreach ($preview as &$pItem) {
            if ($pItem['is_final'] || $pItem['race_number'] == 0 || !$pItem['team_id']) continue;
            $tA = $pItem['team_id'];
            $currentRaceTeams = collect($preview)->where('race_number', $pItem['race_number'])->pluck('team_id')->toArray();
            $conflicts = 0;
            foreach ($currentRaceTeams as $tB) {
                if ($tB && $tA != $tB) {
                    // Wir zählen wie oft sie im gesamten Vorlauf-Plan gegeneinander antreten
                    $conflicts += ($opponentHistory[$tA][$tB] ?? 1) - 1;
                }
            }
            $pItem['conflicts'] = $conflicts;
        }
        // --- ENDE NACHBESSERUNG KONFLIKTE ---

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
                // $f=0 ist das sportlich schwächste Finale (z.B. C-Finale), $f=max ist das A-Finale
                // Da wir sie später nach Leistungsstärke aufsteigend planen wollen:
                $finalLetter = chr(65 + ($actualFinalsCount - 1 - $f));
                $finalTypeName = $finalLetter . '-Finale';

                if (!isset($finalsByType[$finalTypeName])) {
                    $finalsByType[$finalTypeName] = [];
                }

                $finalsByType[$finalTypeName][] = [
                    'gruppe_id' => $gruppeId,
                    'lanes_count' => $lanesCount,
                    'total_teams' => $totalTeamsInGroup,
                    'f_index' => $f, // Index 0 ist das schwächste geplante Finale der Gruppe
                    'final_type' => $finalTypeName,
                    'actual_finals_count' => $actualFinalsCount
                ];
            }
        }

        // Sortiere Finals-Typen alphabetisch aufsteigend von schwach nach stark
        // Falls wir E, D, C, B, A haben:
        // ksort sortiert A, B, C, D, E.
        // krsort sortiert E, D, C, B, A.
        // Der User möchte: "Die Reihenfolge erfolgt aufsteigend nach Leistungsstärke (z. B. erst alle E-Finals aller Gruppen, dann alle D-Finals, C-Finals, B-Finals)."
        // Also krsort.
        krsort($finalsByType);

        foreach ($finalsByType as $typeName => $finals) {
            // Innerhalb eines Typs (z.B. alle B-Finals) sortieren wir die Gruppen
            // nach Meldezahl (kleinere Gruppen zuerst), um eine Steigerung zu haben
            usort($finals, function($a, $b) {
                return $a['total_teams'] <=> $b['total_teams'];
            });

            foreach ($finals as $fData) {
                $gruppeId = $fData['gruppe_id'];
                $lanesCount = $fData['lanes_count'];
                $totalTeamsInGroup = $fData['total_teams'];
                $f = $fData['f_index'];
                $actualFinalsCount = $fData['actual_finals_count'];
                $gruppeName = $gruppeNames[$gruppeId] ?? 'Unbekannt';

                $startPlatz = ($actualFinalsCount - 1 - $f) * $lanesCount + 1;
                // $startPlatz: Bei A-Finale (f=max) soll es 1 sein.
                // $actualFinalsCount ist die Anzahl der Finals für DIESE Gruppe.
                // Wenn wir 3 Finals haben (C, B, A):
                // f=0 (C-Finale): (3-1-0)*4 + 1 = 9 -> Plätze 9-12
                // f=1 (B-Finale): (3-1-1)*4 + 1 = 5 -> Plätze 5-8
                // f=2 (A-Finale): (3-1-2)*4 + 1 = 1 -> Plätze 1-4
                // Das ist korrekt für die sportliche Zuordnung.
                // Das stärkste Team (Platz 1) soll zuletzt im Finale gesetzt werden (auf die Mittelbahn).
                // Da calculateSeededLanes die Bahnen von "stark" nach "schwach" ausgibt,
                // müssen wir die Platzierungen so zuordnen, dass die niedrigen Platzierungen (starke Teams)
                // auf die vorderen (besonderen) Bahnen laut Seeding kommen.

                // Korrektur: Der User wünscht, dass die "starken Teams später gesetzt werden".
                // In einem Finale (z.B. A-Finale) ist die Setzung der Teams auf die Bahnen bereits das "Rennen".
                // Wenn er meint, dass die Platzierungen 1, 2, 3 später (auf die besseren Bahnen) kommen sollen:
                // calculateSeededLanes gibt z.B. [3, 2, 4, 1] zurück.
                // Wir wollen: Bahn 3 -> Platz 1, Bahn 2 -> Platz 2, Bahn 4 -> Platz 3, Bahn 1 -> Platz 4.
                // Aktuell: $platzImRanking = $startPlatz + $lIdx; wobei $lIdx 0, 1, 2, 3 ist.
                // Das bedeutet:
                // $lIdx=0 (Bahn 3) -> Platz 1
                // $lIdx=1 (Bahn 2) -> Platz 2
                // ...
                // Das ist sportlich korrekt (Seeding).

                // Aber der User sagt: "Die setzung der temas für die Finale ist falscherum die Startsen sollen im Spätern verlauf gesetzt werden."
                // Wenn "Startsen" die starken Teams sind (Platz 1, 2...), und er möchte, dass diese in der Liste/Anzeige
                // oder in der Logik "später" kommen (z.B. höhere Bahnnummern oder einfach umgekehrte Reihenfolge im Loop),
                // dann müssen wir $laneAssignment umkehren, damit die schwächeren Teams zuerst (auf die äußeren Bahnen)
                // und die starken Teams zuletzt (auf die Mittelbahnen) im Loop verarbeitet werden.

                // Bahnverteilung in Finals: Immer bei Bahn 1 anfangen (User-Wunsch).
                // Wir belegen die Bahnen 1, 2, 3... bis zur Anzahl der Teams in diesem Finale.
                // Um die Anforderung "stärkste Teams zuletzt setzen" zu erfüllen,
                // durchlaufen wir die Plätze von schwach nach stark.
                $teamsInThisFinal = min($lanesCount, $totalTeamsInGroup - $startPlatz + 1);
                $laneAssignment = [];
                for ($i = 1; $i <= $teamsInThisFinal; $i++) {
                    $laneAssignment[] = $i;
                }
                // Wir drehen es um, damit wir mit der höchsten Bahn (schwächstes Team) anfangen
                $laneAssignment = array_reverse($laneAssignment);

                // Berechne Abstand zum letzten Vorlauf dieser Gruppe
                $finalPause = '-';
                $finalPauseMinutes = null;
                if (isset($maxHeatEndTimePerGroup[$gruppeId])) {
                    $diff = $currentGlobalTime->diffInMinutes($maxHeatEndTimePerGroup[$gruppeId]);
                    $finalPause = $diff . ' (Abst.)';
                    $finalPauseMinutes = (int)$diff;
                }

                foreach ($laneAssignment as $lIdx => $laneNumber) {
                    // Da wir $laneAssignment umgedreht haben (z.B. [4, 3, 2, 1]),
                    // entspricht $lIdx 0 der Bahn 4 (schwächstes Team).
                    // Der Platz im Ranking für das schwächste Team ist: $startPlatz + $teamsInThisFinal - 1
                    $reverseIdx = ($teamsInThisFinal - 1) - $lIdx;
                    $platzImRanking = $startPlatz + $reverseIdx;

                    if ($platzImRanking > $totalTeamsInGroup) continue;

                    // Sicherheitscheck: Verhindere doppelte Bahnen im gleichen Rennen
                    $alreadyAssigned = false;
                    foreach ($preview as $p) {
                        if ($p['race_number'] === $raceNumber && $p['lane'] === $laneNumber) {
                            $alreadyAssigned = true;
                            break;
                        }
                    }
                    if ($alreadyAssigned) continue;

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
                        'final_type' => $typeName,
                        'heat_level' => $typeName
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

        // Zähle Heats pro Team für Level-Bestimmung
        $teamHeatCounts = [];
        foreach ($preview as $item) {
            if (!$item['is_final'] && $item['team_id']) {
                $teamHeatCounts[$item['team_id']] = ($teamHeatCounts[$item['team_id']] ?? 0) + 1;
            }
        }

        // Level (Laufanzahl) zu Items hinzufügen
        foreach ($preview as &$item) {
            if ($item['team_id']) {
                $item['level'] = $teamHeatCounts[$item['team_id']] ?? 0;
            } else {
                $item['level'] = 0;
            }
        }

        $maxRaceTime = $currentGlobalTime->copy()->subMinutes($interval);

        // --- GLOBALE OPTIMIERUNG (Swap-Loop) ---
        // Wenn Teams unter der Mindestpause sind, versuchen wir sie mit Teams aus späteren Heats
        // der gleichen Gruppe UND des gleichen Levels zu tauschen.
        $swapCount = 0;
        $swapAttempts = 0;
        $swapLogs = [];
        $noSwapFoundLogs = [];
        for ($iteration = 1; $iteration <= 50; $iteration++) {
            $hasConflict = false;

            // Wir sortieren die Vorläufe chronologisch für die Analyse
            $heats = collect($preview)->where('is_final', false)->where('race_number', '>', 0)->sortBy('race_number');

            foreach ($heats as $index => $item) {
                if (!$item['team_id']) continue;

                $teamId = $item['team_id'];
                $tid = $teamId;
                $currentRaceTime = \Carbon\Carbon::parse($item['time']);

                // Finde vorherigen Start des Teams
                $prevStart = collect($preview)
                    ->where('team_id', $teamId)
                    ->filter(fn($i) => $i['race_number'] < $item['race_number'])
                    ->sortByDesc('race_number')
                    ->first();

                $isTooEarlyTeam = $prevStart && $currentRaceTime->diffInMinutes(\Carbon\Carbon::parse($prevStart['time'])) < $minPause;

                // Org Konflikt
                $orgId = $orgAssignments[$teamId] ?? null;
                $isTooEarlyOrg = false;
                if ($orgId) {
                    $prevOrgStart = collect($preview)
                        ->where('is_final', false)
                        ->where('race_number', '>', 0)
                        ->filter(function($i) use ($orgAssignments, $orgId, $item, $tid) {
                            return ($orgAssignments[$i['team_id']] ?? null) == $orgId
                                && $i['team_id'] != $tid
                                && $i['race_number'] < $item['race_number'];
                        })
                        ->sortByDesc('race_number')
                        ->first();

                    if ($prevOrgStart) {
                        if ($currentRaceTime->diffInMinutes(\Carbon\Carbon::parse($prevOrgStart['time'])) < $minPauseOrg) {
                            $isTooEarlyOrg = true;
                        }
                    }
                }

                if ($isTooEarlyTeam || $isTooEarlyOrg) {
                    $hasConflict = true;
                    $swapAttempts++;

                    // Suche Tauschpartner in späteren Heats der gleichen Gruppe und gleichen Levels
                    $currentGruppeId = $item['gruppe_id'];
                    $currentRaceNumber = $item['race_number'];
                    $currentLevel = $item['level'] ?? 0;

                    $candidates = collect($preview)
                        ->where('is_final', false)
                        ->where('gruppe_id', $currentGruppeId)
                        ->where('level', $currentLevel)
                        ->where('heat_level', $item['heat_level']) // Nur gleiches Heat-Level (z.B. Vorlauf 1)
                        ->where('race_number', '>', $currentRaceNumber);

                    $bestSwapItem = null;
                    $maxSwapPause = -1;

                    foreach ($candidates as $cand) {
                        if (!$cand['team_id']) continue;

                        // Berechne Pause des Kandidaten an seiner jetzigen Stelle
                        $candTime = \Carbon\Carbon::parse($cand['time']);
                        $candPrevStart = collect($preview)
                            ->where('team_id', $cand['team_id'])
                            ->filter(fn($i) => $i['race_number'] < $cand['race_number'])
                            ->sortByDesc('race_number')
                            ->first();

                        $candPause = $candPrevStart ? $candTime->diffInMinutes(\Carbon\Carbon::parse($candPrevStart['time'])) : 9999;

                        // Wir suchen den mit der höchsten Pause
                        if ($candPause > $maxSwapPause) {
                            // Zusätzliche Prüfung: Wäre der Tausch für beide "sicher"?
                            // Ein Team darf nicht in ein Rennen getauscht werden, in dem es schon ist.
                            $raceOfTarget = collect($preview)->where('race_number', $item['race_number'])->pluck('team_id')->filter()->toArray();
                            $raceOfCand = collect($preview)->where('race_number', $cand['race_number'])->pluck('team_id')->filter()->toArray();

                            if (in_array($cand['team_id'], $raceOfTarget) || in_array($item['team_id'], $raceOfCand)) {
                                continue;
                            }

                            // SICHERHEITSCHECK: Verhindere Doppelbelegung (Team darf nicht zweimal im gleichen Rennen sein)
                            // Das Team, das von cand kommt, darf nicht bereits in item's Rennen sein (außer an der Stelle, die getauscht wird)
                            // Und das Team, das von item kommt, darf nicht bereits in cand's Rennen sein.
                            // Da wir oben bereits $raceOfTarget und $raceOfCand geplückt haben, prüfen wir hier gegen diese Listen.
                            if (in_array($cand['team_id'], array_diff($raceOfTarget, [$item['team_id']])) ||
                                in_array($item['team_id'], array_diff($raceOfCand, [$cand['team_id']]))) {
                                continue;
                            }

                            // Prüfung: Würde der Tausch beim Kandidaten einen Konflikt verursachen?
                            // (Einfache Prüfung der Pause des Kandidaten am neuen Platz)
                            $newTimeForCand = \Carbon\Carbon::parse($item['time']);
                            $candPrevStartNew = collect($preview)
                                ->where('team_id', $cand['team_id'])
                                ->filter(fn($i) => $i['race_number'] < $item['race_number'])
                                ->sortByDesc('race_number')
                                ->first();
                            $newPauseForCand = $candPrevStartNew ? $newTimeForCand->diffInMinutes(\Carbon\Carbon::parse($candPrevStartNew['time'])) : 9999;

                            if ($newPauseForCand < $minPause) {
                                continue;
                            }

                            // Prüfung: Würde der Tausch für Team A (das aktuelle Team) einen neuen Konflikt verursachen?
                            // Team A zieht von Lauf A (item) nach Lauf B (cand)
                            $newTimeForA = \Carbon\Carbon::parse($cand['time']);
                            $prevStartANew = collect($preview)
                                ->where('team_id', $item['team_id'])
                                ->filter(fn($i) => $i['race_number'] < $cand['race_number'])
                                ->sortByDesc('race_number')
                                ->first();
                            $newPauseForA = $prevStartANew ? $newTimeForA->diffInMinutes(\Carbon\Carbon::parse($prevStartANew['time'])) : 9999;

                            if ($newPauseForA < $minPause) {
                                continue;
                            }

                            // SICHERHEITS-CHECK: Ist das aktuelle Team (teamId) bereits im Ziel-Lauf (Lauf B) vorhanden?
                            // Oder ist der Tauschpartner (cand['team_id']) bereits im Quell-Lauf (Lauf A) vorhanden?
                            $teamsInRaceA = collect($preview)->where('race_number', $item['race_number'])->pluck('team_id')->toArray();
                            $teamsInRaceB = collect($preview)->where('race_number', $cand['race_number'])->pluck('team_id')->toArray();

                            if (in_array($teamId, $teamsInRaceB) || (isset($cand['team_id']) && in_array($cand['team_id'], $teamsInRaceA))) {
                                continue;
                            }

                            $maxSwapPause = $candPause;
                            $bestSwapItem = $cand;
                        }
                    }

                    if ($bestSwapItem) {
                        // Tausche team_id in $preview
                        $idxA = null;
                        $idxB = null;
                        foreach ($preview as $k => $v) {
                            if ($v['race_number'] == $item['race_number'] && $v['lane'] == $item['lane']) $idxA = $k;
                            if ($v['race_number'] == $bestSwapItem['race_number'] && $v['lane'] == $bestSwapItem['lane']) $idxB = $k;
                        }

                        if ($idxA !== null && $idxB !== null) {
                            $teamIdA = $preview[$idxA]['team_id'];
                            $teamIdB = $preview[$idxB]['team_id'];

                            // Sicherheits-Check: Tauschpartner müssen existieren und verschieden sein
                            if ($teamIdA && $teamIdB && $teamIdA != $teamIdB) {
                                $teamNameA = is_array($teamNames[$teamIdA]) ? ($teamNames[$teamIdA]['name'] ?? $teamIdA) : $teamNames[$teamIdA];
                                $teamNameB = is_array($teamNames[$teamIdB]) ? ($teamNames[$teamIdB]['name'] ?? $teamIdB) : $teamNames[$teamIdB];

                                // --- Pausen VOR dem Tausch ermitteln ---
                                // Team A (in Lauf A)
                                $prevStartA_old = collect($preview)
                                    ->where('team_id', $teamIdA)
                                    ->filter(fn($i) => $i['race_number'] < $preview[$idxA]['race_number'])
                                    ->sortByDesc('race_number')
                                    ->first();
                                $pauseA_old = $prevStartA_old ? $currentRaceTime->diffInMinutes(\Carbon\Carbon::parse($prevStartA_old['time'])) : '-';
                                $org_pauseA_old = '-';
                                if (isset($orgAssignments[$teamIdA])) {
                                    $oIdA = $orgAssignments[$teamIdA];
                                    $pOrgStartA_old = collect($preview)
                                        ->where('is_final', false)
                                        ->where('race_number', '>', 0)
                                        ->filter(function($i) use ($orgAssignments, $oIdA, $preview, $idxA, $teamIdA) {
                                            return ($orgAssignments[$i['team_id']] ?? null) == $oIdA
                                                && $i['team_id'] != $teamIdA
                                                && $i['race_number'] < $preview[$idxA]['race_number'];
                                        })
                                        ->sortByDesc('race_number')
                                        ->first();
                                    $org_pauseA_old = $pOrgStartA_old ? $currentRaceTime->diffInMinutes(\Carbon\Carbon::parse($pOrgStartA_old['time'])) : '-';
                                }

                                // Team B (in Lauf B)
                                $candTime = \Carbon\Carbon::parse($bestSwapItem['time']);
                                $prevStartB_old = collect($preview)
                                    ->where('team_id', $teamIdB)
                                    ->filter(fn($i) => $i['race_number'] < $preview[$idxB]['race_number'])
                                    ->sortByDesc('race_number')
                                    ->first();
                                $pauseB_old = $prevStartB_old ? $candTime->diffInMinutes(\Carbon\Carbon::parse($prevStartB_old['time'])) : '-';

                                $org_pauseB_old = '-';
                                if (isset($orgAssignments[$teamIdB])) {
                                    $oIdB = $orgAssignments[$teamIdB];
                                    $pOrgStartB_old = collect($preview)
                                        ->where('is_final', false)
                                        ->where('race_number', '>', 0)
                                        ->filter(function($i) use ($orgAssignments, $oIdB, $preview, $idxB, $teamIdB) {
                                            return ($orgAssignments[$i['team_id']] ?? null) == $oIdB
                                                && $i['team_id'] != $teamIdB
                                                && $i['race_number'] < $preview[$idxB]['race_number'];
                                        })
                                        ->sortByDesc('race_number')
                                        ->first();
                                    $org_pauseB_old = $pOrgStartB_old ? $candTime->diffInMinutes(\Carbon\Carbon::parse($pOrgStartB_old['time'])) : '-';
                                }

                                // --- Tausche team_id in $preview ---
                                $preview[$idxA]['team_id'] = $teamIdB;
                                $preview[$idxB]['team_id'] = $teamIdA;

                                // --- Pausen NACH dem Tausch berechnen ---
                                $newTimeA = \Carbon\Carbon::parse($preview[$idxA]['time']);
                                $newTimeB = \Carbon\Carbon::parse($preview[$idxB]['time']);

                                // Team A (jetzt in Lauf B)
                                $prevStartA_new = collect($preview)
                                    ->where('team_id', $teamIdA)
                                    ->filter(fn($i) => $i['race_number'] < $preview[$idxB]['race_number'])
                                    ->sortByDesc('race_number')
                                    ->first();
                                $pauseA_new = $prevStartA_new ? $newTimeB->diffInMinutes(\Carbon\Carbon::parse($prevStartA_new['time'])) : '-';

                                // Team B (jetzt in Lauf A)
                                $prevStartB_new = collect($preview)
                                    ->where('team_id', $teamIdB)
                                    ->filter(fn($i) => $i['race_number'] < $preview[$idxA]['race_number'])
                                    ->sortByDesc('race_number')
                                    ->first();
                                $pauseB_new = $prevStartB_new ? $newTimeA->diffInMinutes(\Carbon\Carbon::parse($prevStartB_new['time'])) : '-';

                                // Org Pausen nach dem Tausch
                                $org_pauseA_new = '-';
                                if (isset($orgAssignments[$teamIdA])) {
                                    $oIdA = $orgAssignments[$teamIdA];
                                    $pOrgStartA = collect($preview)
                                        ->where('is_final', false)
                                        ->where('race_number', '>', 0)
                                        ->filter(function($i) use ($orgAssignments, $oIdA, $preview, $idxB, $teamIdA) {
                                            return ($orgAssignments[$i['team_id']] ?? null) == $oIdA
                                                && $i['team_id'] != $teamIdA
                                                && $i['race_number'] < $preview[$idxB]['race_number'];
                                        })
                                        ->sortByDesc('race_number')
                                        ->first();
                                    $org_pauseA_new = $pOrgStartA ? $newTimeB->diffInMinutes(\Carbon\Carbon::parse($pOrgStartA['time'])) : '-';
                                }

                                $org_pauseB_new = '-';
                                if (isset($orgAssignments[$teamIdB])) {
                                    $oIdB = $orgAssignments[$teamIdB];
                                    $pOrgStartB = collect($preview)
                                        ->where('is_final', false)
                                        ->where('race_number', '>', 0)
                                        ->filter(function($i) use ($orgAssignments, $oIdB, $preview, $idxA, $teamIdB) {
                                            return ($orgAssignments[$i['team_id']] ?? null) == $oIdB
                                                && $i['team_id'] != $teamIdB
                                                && $i['race_number'] < $preview[$idxA]['race_number'];
                                        })
                                        ->sortByDesc('race_number')
                                        ->first();
                                    $org_pauseB_new = $pOrgStartB ? $newTimeA->diffInMinutes(\Carbon\Carbon::parse($pOrgStartB['time'])) : '-';
                                }

                                $swapLogs[] = [
                                    'type' => 'Pause',
                                    'raceA' => $item['race_number'],
                                    'raceB' => $bestSwapItem['race_number'],
                                    'heat_levelA' => $item['heat_level'] ?? '-',
                                    'heat_levelB' => $bestSwapItem['heat_level'] ?? '-',
                                    'teamA' => $teamNameA,
                                    'teamB' => $teamNameB,
                                    'pauseA_old' => $pauseA_old,
                                    'pauseA_new' => $pauseA_new,
                                    'pauseB_old' => $pauseB_old,
                                    'pauseB_new' => $pauseB_new,
                                    'org_pauseA_old' => $org_pauseA_old,
                                    'org_pauseA_new' => $org_pauseA_new,
                                    'org_pauseB_old' => $org_pauseB_old,
                                    'org_pauseB_new' => $org_pauseB_new,
                                    'reason' => ($isTooEarlyTeam ? 'Team-Pause' : 'Org-Pause') . ' unterschritten',
                                    'level' => $currentLevel
                                ];

                                $swapCount++;
                                break 2;
                            }
                        }
                    } else {
                        // Kein Tauschpartner gefunden
                        $teamName = is_array($teamNames[$teamId]) ? ($teamNames[$teamId]['name'] ?? $teamId) : $teamNames[$teamId];
                        $noSwapFoundLogs[] = [
                            'race' => $item['race_number'],
                            'heat_level' => $item['heat_level'] ?? '-',
                            'team' => $teamName,
                            'level' => $currentLevel,
                            'reason' => $isTooEarlyTeam ? 'Team-Pause' : 'Org-Pause',
                            'pause' => $prevStart ? $currentRaceTime->diffInMinutes(\Carbon\Carbon::parse($prevStart['time'])) : '-',
                            'org_pause' => $prevOrgStart ? $currentRaceTime->diffInMinutes(\Carbon\Carbon::parse($prevOrgStart['time'])) : '-',
                            'min_needed' => $isTooEarlyTeam ? $minPause : $minPauseOrg
                        ];
                    }
                }
            }

            if (!$hasConflict) break; // Fertig!

            // Pausen neu berechnen für die nächste Runde der Analyse
            $lastStartsLocal = [];
            $lastOrgStartsLocal = [];
            $lastOrgTeamLocal = [];

            // Erst sortieren
            usort($preview, function($a, $b) {
                if ($a['time'] != $b['time']) return strcmp($a['time'], $b['time']);
                if ($a['race_number'] != $b['race_number']) return $a['race_number'] - $b['race_number'];
                return $a['lane'] - $b['lane'];
            });

            foreach ($preview as &$pItem) {
                if (!$pItem['team_id']) continue;
                $tId = $pItem['team_id'];
                $currT = \Carbon\Carbon::parse($pItem['time']);

                if (isset($lastStartsLocal[$tId])) {
                    $diff = $currT->diffInMinutes($lastStartsLocal[$tId]);
                    $pItem['pause'] = $diff;
                    $pItem['pause_minutes'] = (int)$diff;
                } else {
                    $pItem['pause'] = '-';
                    $pItem['pause_minutes'] = null;
                }
                $lastStartsLocal[$tId] = $currT;

                // Org Pause
                $oId = $orgAssignments[$tId] ?? null;
                if ($oId) {
                    if (isset($lastOrgStartsLocal[$oId]) && $lastOrgTeamLocal[$oId] != $tId) {
                        $pItem['org_pause'] = $currT->diffInMinutes($lastOrgStartsLocal[$oId]);
                    } else {
                        $pItem['org_pause'] = '-';
                    }
                    $lastOrgStartsLocal[$oId] = $currT;
                    $lastOrgTeamLocal[$oId] = $tId;
                }
            }
        }
        // --- ENDE GLOBALE OPTIMIERUNG ---

            // --- FINALE VALIDIERUNG: KEINE DOPPELBELEGUNGEN ---
            $validationRaces = collect($preview)->where('race_number', '>', 0)->groupBy('race_number');
            foreach ($validationRaces as $rNum => $rItems) {
                $tIds = $rItems->pluck('team_id')->filter()->toArray();
                if (count($tIds) !== count(array_unique($tIds))) {
                    $duplicates = array_count_values($tIds);
                    foreach ($duplicates as $dTid => $count) {
                        if ($count > 1) {
                            $dName = is_array($teamNames[$dTid] ?? $dTid) ? (($teamNames[$dTid]['name'] ?? $dTid)) : ($teamNames[$dTid] ?? $dTid);
                            $noSwapFoundLogs[] = [
                                'type' => 'Validierung',
                                'raceA' => $rNum,
                                'teamA' => $dName,
                                'reason' => 'KRITISCHER FEHLER: Team ' . $count . 'x in Lauf ' . $rNum . ' (Initial oder Optimierung)',
                                'level' => 0,
                                'pauseA_old' => '-',
                                'pauseA_new' => '-',
                                'org_pauseA_old' => '-',
                                'org_pauseA_new' => '-'
                            ];

                            // REPARATUR: Wenn Team doppelt im gleichen Lauf, entferne das Duplikat
                            $foundFirst = false;
                            foreach ($preview as $idx => $item) {
                                if ($item['race_number'] == $rNum && $item['team_id'] == $dTid) {
                                    if (!$foundFirst) {
                                        $foundFirst = true;
                                    } else {
                                        $preview[$idx]['team_id'] = null;
                                        $preview[$idx]['conflicts'] = 0;
                                        $preview[$idx]['pause'] = '-';
                                    }
                                }
                            }
                        }
                    }
                }
            }

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
            'min_pause_org' => $minPauseOrg,
            'pause_after_heats' => $pauseAfterHeats,
            'finals_start_time' => $finalsStartTimeStr,
            'finals_count' => $finalsCount,
            'award_ceremony_time' => $awardCeremonyTimeStr,
            'min_time_before_ceremony' => $minTimeBeforeCeremony,
            'finale_publish_time' => $finalePublishTimeStr,
            'swapCount' => $swapCount,
            'swapAttempts' => $swapAttempts,
            'swapLogs' => collect($swapLogs)->unique()->toArray(),
            'noSwapFoundLogs' => collect($noSwapFoundLogs)->unique()->toArray(),
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
                    'conflicts' => $item['conflicts'] ?? 0,
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
            'min_pause' => $draft->min_pause,
            'min_pause_org' => $draft->params['min_pause_org'] ?? 10,
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
        $minPause = $request->input('min_pause', 20);
        $minPauseOrg = $request->input('min_pause_org', 10);
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

                // Konflikte berechnen
                $conflictCount = 0;
                if (!$isFinal && $lane->team_id) {
                    foreach ($lanes as $otherLane) {
                        if ($otherLane->id !== $lane->id && $otherLane->team_id) {
                            if (isset($opponentHistory[$lane->team_id][$otherLane->team_id])) {
                                $conflictCount += $opponentHistory[$lane->team_id][$otherLane->team_id];
                            }
                        }
                    }
                    $lane->conflicts = $conflictCount;
                }

                $lane->save();
            }

            // Historie nach jedem Lauf aktualisieren
            if (!$isFinal) {
                foreach ($lanes as $laneA) {
                    foreach ($lanes as $laneB) {
                        if ($laneA->id !== $laneB->id && $laneA->team_id && $laneB->team_id) {
                            $opponentHistory[$laneA->team_id][$laneB->team_id] = ($opponentHistory[$laneA->team_id][$laneB->team_id] ?? 0) + 1;
                        }
                    }
                }
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
            // Lanes haben SoftDeletes, wir löschen sie permanent für sauberen Stand
            Lane::where('regatta_id', $regattaId)->delete();
            Tabledata::whereIn('tabele_id', $oldTabeleIds)->delete();
            Race::where('event_id', $regattaId)->delete();
            Tabele::where('event_id', $regattaId)->delete();

            $event = \App\Models\Event::find($regattaId);
            $rennDatum = $event->datumvon;

            // Erste Startzeit ermitteln (für veroeffentlichungUhrzeit bei Nicht-Finals)
            $firstRaceTime = collect($preview)->sortBy('race_number')->first()['time'] ?? '00:00';

            // Tabellen erstellen: Gruppiert nach Gruppe (für Vorläufe) und Gruppe + Final-Typ (für Endläufe)
            $groupedForTables = collect($preview)->where('gruppe_id', '!=', 0)->groupBy(function ($item) {
                if ($item['is_final']) {
                    return $item['gruppe_id'] . '_final_' . $item['final_type'];
                }
                return $item['gruppe_id'] . '_vorlauf';
            });

            $tabeleIds = [];
            foreach ($groupedForTables as $key => $laneData) {
                $firstLane = $laneData->first();
                $gruppeId = $firstLane['gruppe_id'];
                $heatIndex = $firstLane['heat_index'] ?? 1;
                $finale = $firstLane['is_final'];

                $tabele = new \App\Models\Tabele();
                $tabele->event_id = $regattaId;
                $tabele->gruppe_id = $gruppeId;
                $tabele->tabelleDatumVon = $rennDatum;

                if ($firstLane['is_final']) {
                    $tabele->ueberschrift = $firstLane['final_type'] . ' ' . $firstLane['gruppe_name'];
                    $tabele->finale = ($firstLane['final_type'] === 'A-Finale') ? 1 : 0;
                    $tabele->tabelleLevelVon = $params['heats_count']+1 ?? 2;
                    $tabele->tabelleLevelBis = $params['heats_count']+1 ?? 2;
                    $tabele->wertungsart = $params['wertungsart'] ?? 1;
                    $tabele->tabelleVisible = 0;
                    $tabele->finaleAnzeigen = $params['finale_publish_time'].':00' ?? '00:00:00';
                } else {
                    $tabele->ueberschrift = 'Vorlauf ' . $firstLane['gruppe_name'];
                    $tabele->finale = 0;
                    $tabele->tabelleLevelVon = 1;
                    $tabele->tabelleLevelBis = $params['heats_count'] ?? 1;
                    $tabele->wertungsart = 2;
                    $tabele->tabelleVisible = 1;
                    $tabele->finaleAnzeigen = $firstRaceTime;
                }

                $tabele->wertungsart = $params['wertungsart'] ?? 1;
                $tabele->autor_id = $userId;
                $tabele->bearbeiter_id = $userId;
                $tabele->save();

                $tabeleIds[$key] = $tabele->id;
            }

            // Rennen erstellen: Gruppiert nach race_number
            $groupedByRace = collect($preview)->where('gruppe_id', '!=', 0)->groupBy('race_number');

            foreach ($groupedByRace as $raceNum => $laneData) {
                $firstLane = $laneData->first();
                $gruppeId = $firstLane['gruppe_id'];
                $tableKey = $firstLane['is_final']
                    ? $gruppeId . '_final_' . $firstLane['final_type']
                    : $gruppeId . '_vorlauf';

                $race = new \App\Models\Race();
                $race->event_id = $regattaId;
                $race->tabele_id = $tabeleIds[$tableKey] ?? null;
                $race->gruppe_id = $gruppeId;
                $race->rennDatum = $rennDatum;
                $race->rennUhrzeit = $firstLane['time'];

                // Veröffentlichungsuhrzeit: Bei Finals 'finale_publish_time', sonst die erste Startzeit der Regatta
                if ($firstLane['is_final']) {
                    $race->veroeffentlichungUhrzeit = $params['finale_publish_time'] ?? '00:00';
                } else {
                    $race->veroeffentlichungUhrzeit = $firstRaceTime;
                }

                $race->level = $firstLane['heat_index'] ?? 1;

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
                    $laneModel->tabele_id = $tabeleIds[$tableKey] ?? null;
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
