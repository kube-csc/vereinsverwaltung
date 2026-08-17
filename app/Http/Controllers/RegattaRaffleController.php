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
    private function resolveLaneCount(?RaceType $raceType, array &$configWarnings = []): int
    {
        $lanesCount = (int) ($raceType->bahnen ?? 0);

        if ($lanesCount > 0) {
            return $lanesCount;
        }

        $warningKey = 'race_type_' . ($raceType->id ?? 'unknown');
        if (!isset($configWarnings[$warningKey])) {
            $configWarnings[$warningKey] = sprintf(
                'Bootsklasse "%s" (#%s) hat keine gültige Bahnenzahl hinterlegt. Für die Rennplanung wird automatisch der Fallback-Wert 4 verwendet.',
                $raceType->typ ?? 'Unbekannt',
                $raceType->id ?? '?'
            );
        }

        return 4;
    }

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
        $configWarnings = [];
        foreach ($teamsByGroup as $gruppeId => $gruppeTeams) {
            $raceType = $raceTypes->firstWhere('id', $gruppeId);
            $lanesCount = $this->resolveLaneCount($raceType, $configWarnings);
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

        $pointsystemIds = \App\Models\Pointsystem::query()
            ->select('system_id')
            ->distinct()
            ->orderBy('system_id')
            ->pluck('system_id');

        $pointsystemsBySystem = \App\Models\Pointsystem::query()
            ->orderBy('system_id')
            ->orderBy('platz')
            ->get()
            ->groupBy('system_id')
            ->map(function ($rows) {
                return $rows->map(function ($row) {
                    return [
                        'platz' => (int)$row->platz,
                        'punkte' => (int)$row->punkte,
                    ];
                })->values();
            });

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
            'draft' => $draft,
            'configWarnings' => array_values($draft->params['configWarnings'] ?? $configWarnings),
            'pointsystemIds' => $pointsystemIds,
            'pointsystemsBySystem' => $pointsystemsBySystem,
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

        foreach ($preview as $previewIndex => $row) {
            // Markiere Siegerehrung/Mittagspause basierend auf race_number und gruppe_id
            if ((isset($row['race_number']) && $row['race_number'] == 0) && (isset($row['gruppe_id']) && $row['gruppe_id'] == 0)) {
                $isAwardFlag = !empty($row['is_award_ceremony']) || $this->isAwardCeremonyPlanItem($row);
                $isPauseFlag = !empty($row['is_extra_pause']) || $this->isPausePlanItem($row);

                if ($isPauseFlag && !$isAwardFlag) {
                    $preview[$previewIndex]['is_extra_pause'] = true;
                    $preview[$previewIndex]['is_award_ceremony'] = false;
                    $preview[$previewIndex]['gruppe_name'] = (($row['placeholder_name'] ?? null) && str_contains(strtoupper((string)$row['placeholder_name']), 'MITTAGSPAUSE'))
                        ? 'Mittagspause'
                        : '--- PAUSENBLOCK NACH DEN VORLÄUFEN ---';
                } elseif ($isAwardFlag) {
                    $preview[$previewIndex]['is_award_ceremony'] = true;
                    $preview[$previewIndex]['is_extra_pause'] = false;
                    $preview[$previewIndex]['gruppe_name'] = 'Siegerehrung';
                    $preview[$previewIndex]['heat_index'] = null;
                } else {
                    // Fallback: unbekannte 0-Items behandeln wir defensiv als Pause.
                    $preview[$previewIndex]['is_award_ceremony'] = false;
                    $preview[$previewIndex]['is_extra_pause'] = true;
                    $preview[$previewIndex]['gruppe_name'] = '--- PAUSENBLOCK NACH DEN VORLÄUFEN ---';
                }
                // Wichtig: Wir behalten race_number 0 für die UI-Zuordnung
            } else {
                // Wenn es kein 0-Item ist, aber vielleicht das Flag trägt (aus DB geladen)
                if (!empty($row['is_extra_pause']) || $this->isPausePlanItem($row)) {
                    $preview[$previewIndex]['is_award_ceremony'] = false;
                    $preview[$previewIndex]['gruppe_name'] = (($row['placeholder_name'] ?? null) && str_contains(strtoupper((string)$row['placeholder_name']), 'MITTAGSPAUSE'))
                        ? 'Mittagspause'
                        : '--- PAUSENBLOCK NACH DEN VORLÄUFEN ---';
                    $preview[$previewIndex]['race_number'] = 0;
                } elseif (!empty($row['is_award_ceremony']) || $this->isAwardCeremonyPlanItem($row)) {
                    $preview[$previewIndex]['is_extra_pause'] = false;
                    $preview[$previewIndex]['gruppe_name'] = 'Siegerehrung';
                    $preview[$previewIndex]['race_number'] = 0; // Sicherstellen, dass es als 0 behandelt wird
                    $preview[$previewIndex]['heat_index'] = null;
                } else {
                    $preview[$previewIndex]['is_award_ceremony'] = false;
                    $preview[$previewIndex]['is_extra_pause'] = false;
                    $preview[$previewIndex]['gruppe_name'] = $gruppeNames[$row['gruppe_id']] ?? 'Unbekannt';
                }
            }
            if (isset($row['is_final']) && $row['is_final']) {
                $preview[$previewIndex]['team_name'] = $row['placeholder_name'] ?? 'Platzhalter';
                $preview[$previewIndex]['has_pokal'] = false;
                $preview[$previewIndex]['last_final_platz'] = null;
            } else {
                $teamInfo = $teamNames[$row['team_id'] ?? null] ?? null;
                $preview[$previewIndex]['team_name'] = is_array($teamInfo) ? ($teamInfo['name'] ?? 'Unbekannt') : ($teamInfo ?? 'Unbekannt');
                $teamlink = is_array($teamInfo) ? ($teamInfo['teamlink'] ?? 0) : 0;
                $preview[$previewIndex]['has_pokal'] = ($teamlink > 0 && isset($finalTeamlinks[$teamlink]));
                $preview[$previewIndex]['last_final_platz'] = $preview[$previewIndex]['has_pokal'] ? $finalTeamlinks[$teamlink] : null;
            }
        }

        return $preview;
    }

    private function getPlanItemValue($item, string $key)
    {
        if (is_array($item)) {
            return $item[$key] ?? null;
        }

        return $item->{$key} ?? null;
    }

    private function buildPlanItemText($item): string
    {
        $gruppeName = strtoupper(trim((string)($this->getPlanItemValue($item, 'gruppe_name') ?? '')));
        $placeholder = strtoupper(trim((string)($this->getPlanItemValue($item, 'placeholder_name') ?? '')));

        return trim($gruppeName . ' ' . $placeholder);
    }

    private function isPausePlanItem($item): bool
    {
        $text = $this->buildPlanItemText($item);
        $raceNumber = (int)($this->getPlanItemValue($item, 'race_number') ?? 0);
        $gruppeId = (int)($this->getPlanItemValue($item, 'gruppe_id') ?? 0);

        if (str_contains($text, 'SIEGEREHRUNG')) {
            return false;
        }

        if (str_contains($text, 'MITTAGSPAUSE') || str_contains($text, 'PAUSENBLOCK NACH DEN VORL')) {
            return true;
        }

        return $raceNumber === 0 && $gruppeId === 0;
    }

    private function isAwardCeremonyPlanItem($item): bool
    {
        $text = $this->buildPlanItemText($item);
        return str_contains($text, 'SIEGEREHRUNG');
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
        $minPauseOrg = $request->input('min_pause_org', 40);
        $pauseAfterHeats = $request->input('pause_after_heats', 30);

        // Zusätzliche Pausenoptionen
        $pauseType = $request->input('pause_type', 'none');
        $pauseTrigger = $request->input('pause_trigger');
        $pauseDuration = (int)$request->input('pause_duration', 30);
        $appliedPauseTriggers = [];

        $finalsStartTimeStr = $request->input('finals_start_time', '14:00');
        $awardCeremonyTimeStr = $request->input('award_ceremony_time', '18:00');
        $minTimeBeforeCeremony = $request->input('min_time_before_ceremony', 30);
        $finalePublishTimeStr = trim((string) $request->input('finale_publish_time', '')); // Manuelle Eingabe falls vorhanden

        $finalsCount = $request->input('finals_count', 1);
        $cupsCount = max(1, (int) $request->input('cups_count', 1));
        $buchholzwertung = $request->input('buchholzwertung', 0);
        $tabelleSystem = $request->input('tabelleSystem');

        // Bei Zeitwertung (2) immer Buchholzwertung deaktivieren (Rennen mit Einzelwertung/Zeitwertung)
        if ($wertungsart == 2) {
            $buchholzwertung = 0;
            $tabelleSystem = null; // Kein Punktesystem bei Zeitwertung
        }

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
        $configWarnings = [];

        // Vorläufe sammeln (Rundenweise abwechselnd nach Gruppen)
        $allHeats = [];
        $gruppeNames = []; // Cache für Gruppennamen
        $teamNames = [];   // Cache für Teamnamen

        // Zuerst Metadaten sammeln
        $groupsMeta = [];
        foreach ($teamsByGroup as $gruppeId => $gruppeTeams) {
            $raceType = RaceType::find($gruppeId);
            $lanesCount = $this->resolveLaneCount($raceType, $configWarnings);
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
            $origLanesCount = max(1, (int) ($groupsMeta[$gruppeId]['orig_lanes'] ?? $lanesCount));
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
                    if ((int)($nextHeat['heat_index'] ?? 0) !== (int)$hIndex) continue; // Blockgrenze nicht überschreiten

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

            // Zusätzliche Pause anwenden, falls konfiguriert
            if ($pauseType !== 'none') {
                if ($pauseType === 'time' && !empty($pauseTrigger)) {
                    $triggerString = $pauseTrigger;
                    if (is_numeric($triggerString) && strlen($triggerString) <= 2) {
                        $triggerString .= ':00';
                    }
                    try {
                        $triggerTime = \Carbon\Carbon::parse($triggerString);
                        if (!in_array('time_' . $triggerTime->format('H:i'), $appliedPauseTriggers) && $currentGlobalTime->format('H:i') >= $triggerTime->format('H:i')) {
                            $currentGlobalTime->addMinutes($pauseDuration);
                            $appliedPauseTriggers[] = 'time_' . $triggerTime->format('H:i');
                            $preview[] = [
                                'time' => $currentGlobalTime->copy()->subMinutes($pauseDuration)->format('H:i'),
                                'is_extra_pause' => true,
                                'is_award_ceremony' => false,
                                'pause_duration' => $pauseDuration,
                                'is_final' => false,
                                'race_number' => 0,
                                'gruppe_id' => 0,
                                'heat_index' => $h,
                                'gruppe_name' => 'Mittagspause',
                                'placeholder_name' => 'Mittagspause (' . $pauseDuration . ' Min)'
                            ];
                        }
                    } catch (\Exception $e) {
                        // Ignorieren bei Fehlformatierung
                    }
                } elseif (($pauseType === 'heat' || $pauseType === 'race') && !empty($pauseTrigger)) {
                    $triggerNumbers = array_values(array_unique(array_filter(array_map(
                        fn($v) => (int)trim($v),
                        explode(',', (string)$pauseTrigger)
                    ), fn($v) => $v > 0)));

                    $nextHeatData = $allHeats[$heatIndexInAllHeats + 1] ?? null;
                    $isEndOfHeatBlock = !$nextHeatData || (int)($nextHeatData['heat_index'] ?? 0) !== (int)$hIndex;
                    $currentRaceNumber = (int)($raceNumber - 1);

                    $isHeatTrigger = $pauseType === 'heat'
                        && $isEndOfHeatBlock
                        && in_array((int)$hIndex, $triggerNumbers, true);
                    $isRaceTrigger = $pauseType === 'race'
                        && in_array($currentRaceNumber, $triggerNumbers, true);

                    $triggerId = $isHeatTrigger ? ('heat_' . $hIndex) : ('race_' . $currentRaceNumber);

                    if (($isHeatTrigger || $isRaceTrigger) && !in_array($triggerId, $appliedPauseTriggers, true)) {
                        $currentGlobalTime->addMinutes($pauseDuration);
                        $appliedPauseTriggers[] = $triggerId;
                        $preview[] = [
                            'time' => $currentGlobalTime->copy()->subMinutes($pauseDuration)->format('H:i'),
                            'is_extra_pause' => true,
                            'is_award_ceremony' => false,
                            'pause_duration' => $pauseDuration,
                            'is_final' => false,
                            'race_number' => 0,
                            'gruppe_id' => 0,
                            'heat_index' => $h,
                            'gruppe_name' => 'Mittagspause',
                            'placeholder_name' => 'Mittagspause (' . $pauseDuration . ' Min)'
                        ];
                    }
                }
            }

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

        // Vereinfacht: Wir aktualisieren nur die conflicts Anzeige basierend auf der finalen History
        foreach ($preview as $previewIndex => $pItem) {
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
            $preview[$previewIndex]['conflicts'] = $conflicts;
        }
        // --- ENDE NACHBESSERUNG KONFLIKTE ---

        $pauseBlockLabel = '--- PAUSENBLOCK NACH DEN VORLÄUFEN ---';
        $maxPrelimHeatIndex = collect($allHeats)->max('heat_index') ?? (int)$heatsCount;
        $finalHeatIndex = (int)$maxPrelimHeatIndex + 1;

        // Finale generieren
        $finalsStartTime = \Carbon\Carbon::parse($finalsStartTimeStr);
        // Erstes Finale darf nie vor den beiden Grenzen starten:
        // 1) Startzeit Finals
        // 2) letzter Vorlauf + Pause nach Vorläufen
        $earliestFinalsStart = $maxHeatEndTime->copy()->addMinutes((int)$pauseAfterHeats);
        if ($finalsStartTime->lt($earliestFinalsStart)) {
            $finalsStartTime = $earliestFinalsStart;
        }

        // Pausenblock wird nicht mehr als Datensatz in der DB gespeichert,
        // sondern nur bei der Ausgabe berechnet/angezeigt.

        $currentGlobalTime = $finalsStartTime->copy();

        // Finale sammeln (gruppiert nach Typ: A, B, C...)
        // Mehr-Cup-Logik: Die gemeinsame Vorlauf-Gesamtwertung einer Gruppe wird in Cups aufgeteilt.
        // Cup 1 = stärkster Cup, höherer Cup-Index = schwächerer Cup.
        $finalsByType = [];
        foreach ($teamsByGroup as $gruppeId => $gruppeTeams) {
            $raceType = RaceType::find($gruppeId);
            $lanesCount = $this->resolveLaneCount($raceType, $configWarnings);
            $totalTeamsInGroup = $gruppeTeams->count();

            $actualCupsCount = min($cupsCount, max(1, $totalTeamsInGroup));
            $baseCupSize = intdiv($totalTeamsInGroup, $actualCupsCount);
            $cupRemainder = $totalTeamsInGroup % $actualCupsCount;

            $cupStartRank = 1;
            for ($cupNumber = 1; $cupNumber <= $actualCupsCount; $cupNumber++) {
                // Restplätze gehen an stärkere Cups zuerst (Cup 1, dann Cup 2, ...)
                $cupSize = $baseCupSize + ($cupNumber <= $cupRemainder ? 1 : 0);
                if ($cupSize <= 0) {
                    continue;
                }

                $maxSaneFinalsInCup = (int) ceil($cupSize / $lanesCount);
                $actualFinalsCount = min($finalsCount, $maxSaneFinalsInCup);

                for ($f = 0; $f < $actualFinalsCount; $f++) {
                    // $f=0 ist das sportlich schwächste Finale der jeweiligen Cup-Wertung.
                    $finalLetter = chr(65 + ($actualFinalsCount - 1 - $f));
                    $baseFinalTypeName = $finalLetter . '-Finale';
                    $finalTypeName = $baseFinalTypeName . ' Cup ' . $cupNumber;

                    if (!isset($finalsByType[$baseFinalTypeName])) {
                        $finalsByType[$baseFinalTypeName] = [];
                    }

                    $finalsByType[$baseFinalTypeName][] = [
                        'gruppe_id' => $gruppeId,
                        'lanes_count' => $lanesCount,
                        'total_teams' => $totalTeamsInGroup,
                        'f_index' => $f,
                        'final_type' => $finalTypeName,
                        'final_type_base' => $baseFinalTypeName,
                        'actual_finals_count' => $actualFinalsCount,
                        'cup_number' => $cupNumber,
                        'cup_size' => $cupSize,
                        'cup_start_rank' => $cupStartRank,
                    ];
                }

                $cupStartRank += $cupSize;
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
            // Innerhalb eines Typs (z.B. alle B-Finals): zuerst schwächster Cup, dann stärkere Cups.
            usort($finals, function($a, $b) {
                $cupSort = ($b['cup_number'] ?? 1) <=> ($a['cup_number'] ?? 1);
                if ($cupSort !== 0) {
                    return $cupSort;
                }
                // Stabile Zweitsortierung für identischen Cup (deterministisch)
                return ($a['gruppe_id'] ?? 0) <=> ($b['gruppe_id'] ?? 0);
            });

            foreach ($finals as $fData) {
                $gruppeId = $fData['gruppe_id'];
                $lanesCount = $fData['lanes_count'];
                $totalTeamsInGroup = $fData['total_teams'];
                $f = $fData['f_index'];
                $actualFinalsCount = $fData['actual_finals_count'];
                $cupStartRank = $fData['cup_start_rank'] ?? 1;
                $cupSize = $fData['cup_size'] ?? $totalTeamsInGroup;
                $gruppeName = $gruppeNames[$gruppeId] ?? 'Unbekannt';

                $startPlatz = ($actualFinalsCount - 1 - $f) * $lanesCount + 1;
                // $startPlatz: Bei A-Finale (f=max) soll es 1 sein.
                // $actualFinalsCount ist die Anzahl der Finals für DIESE Gruppe.
                // Wenn wir 3 Finals haben (C, B, A):
                // f=0 (C-Finale): (3-1-0)*4 + 1 = 9 -> Plätze 9-12
                // f=1 (B-Finale): (3-1-1)*4 + 1 = 5 -> Plätze 5-8
                // f=2 (A-Finale): (3-1-2)*4 + 1 = 1 -> Plätze 1-4
                // Das ist korrekt für die sportliche Zuordnung.
                // Finals werden nach Seeding gesetzt: starke Platzierungen in die Mitte,
                // nach außen hin schwächer (Center-Out).
                $teamsInThisFinal = min($lanesCount, $cupSize - $startPlatz + 1);

                $laneAssignment = array_slice($this->calculateSeededLanes($lanesCount), 0, max(0, $teamsInThisFinal));

                // Berechne Abstand zum letzten Vorlauf dieser Gruppe
                $finalPause = '-';
                $finalPauseMinutes = null;
                if (isset($maxHeatEndTimePerGroup[$gruppeId])) {
                    $diff = $currentGlobalTime->diffInMinutes($maxHeatEndTimePerGroup[$gruppeId]);
                    $finalPause = $diff . ' (Abst.)';
                    $finalPauseMinutes = (int)$diff;
                }

                foreach ($laneAssignment as $lIdx => $laneNumber) {
                    // Niedrige Platzierung = starkes Team.
                    // $lIdx folgt der Seeding-Reihenfolge aus calculateSeededLanes().
                    $platzImCupRanking = $startPlatz + $lIdx;
                    $platzImRanking = $cupStartRank + $platzImCupRanking - 1;

                    if ($platzImCupRanking > $cupSize || $platzImRanking > $totalTeamsInGroup) continue;

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
                        'source_tabele_id' => null,
                        'source_place' => $platzImRanking,
                        'gruppe_id' => $gruppeId,
                        'is_final' => true,
                        'final_type' => $fData['final_type'],
                        'heat_index' => $finalHeatIndex,
                        'heat_level' => $fData['final_type']
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
        foreach ($preview as $previewRow) {
            if (!$previewRow['is_final'] && isset($previewRow['team_id']) && $previewRow['team_id']) {
                $teamHeatCounts[$previewRow['team_id']] = ($teamHeatCounts[$previewRow['team_id']] ?? 0) + 1;
            }
        }

        // Level (Laufanzahl) zu Items hinzufügen
        foreach ($preview as $previewIndex => $previewLevelItem) {
            if (isset($previewLevelItem['team_id']) && $previewLevelItem['team_id']) {
                $preview[$previewIndex]['level'] = $teamHeatCounts[$previewLevelItem['team_id']] ?? 0;
            } else {
                $preview[$previewIndex]['level'] = 0;
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

            foreach ($heats as $index => $heatItem) {
                $item = $heatItem;
                if (!isset($item['team_id']) || !$item['team_id']) continue;

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
                        if (!isset($cand['team_id']) || !$cand['team_id']) continue;

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
            $aTime = (string)($a['time'] ?? '00:00');
            $bTime = (string)($b['time'] ?? '00:00');
            if ($aTime !== $bTime) return strcmp($aTime, $bTime);

            $aRaceNumber = (int)($a['race_number'] ?? 0);
            $bRaceNumber = (int)($b['race_number'] ?? 0);
            // Mittagspausen haben oft race_number > 0 jetzt, aber Siegerehrung ist 0.
            // Wir wollen 0 (Siegerehrung) immer ganz hinten haben bei gleicher Zeit.
            if ($aRaceNumber === 0) return 1;
            if ($bRaceNumber === 0) return -1;
            if ($aRaceNumber !== $bRaceNumber) return $aRaceNumber - $bRaceNumber;

            $aLane = (int)($a['lane'] ?? 0);
            $bLane = (int)($b['lane'] ?? 0);
            return $aLane - $bLane;
        });

            foreach ($preview as $previewIndex => $pItem) {
                if (!isset($pItem['team_id']) || !$pItem['team_id']) continue;
                $tId = $pItem['team_id'];
                $currT = \Carbon\Carbon::parse($pItem['time']);

                if (isset($lastStartsLocal[$tId])) {
                    $diff = $currT->diffInMinutes($lastStartsLocal[$tId]);
                    $preview[$previewIndex]['pause'] = $diff;
                    $preview[$previewIndex]['pause_minutes'] = (int)$diff;
                } else {
                    $preview[$previewIndex]['pause'] = '-';
                    $preview[$previewIndex]['pause_minutes'] = null;
                }
                $lastStartsLocal[$tId] = $currT;

                // Org Pause
                $oId = $orgAssignments[$tId] ?? null;
                if ($oId) {
                    if (isset($lastOrgStartsLocal[$oId]) && $lastOrgTeamLocal[$oId] != $tId) {
                        $preview[$previewIndex]['org_pause'] = $currT->diffInMinutes($lastOrgStartsLocal[$oId]);
                    } else {
                        $preview[$previewIndex]['org_pause'] = '-';
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
                $tIds = $rItems->pluck('team_id')->filter(function($id) {
                    return !is_null($id);
                })->toArray();
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
                                if (isset($item['race_number']) && $item['race_number'] == $rNum && isset($item['team_id']) && $item['team_id'] == $dTid) {
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

        // Veröffentlichungszeit: Falls nicht manuell gesetzt, 1 Stunde nach Siegerehrung als Fallback
        if ($finalePublishTimeStr === '' && $awardCeremonyTimeStr) {
            $awardCeremonyTime = \Carbon\Carbon::parse($awardCeremonyTimeStr);
            $finalePublishTime = $awardCeremonyTime->copy()->addHour();
            $finalePublishTimeStr = $finalePublishTime->format('H:i');
        }

        // Gegner-Zusammenfassung erstellen
        $teamOpponents = [];
        $teamRaceCount = []; // Zählt, wie oft jedes Team in der Planung vorkommt

        // Zähle die Häufigkeit pro Team
        foreach ($preview as $item) {
            if (isset($item['team_id']) && $item['team_id']) {
                $teamRaceCount[$item['team_id']] = ($teamRaceCount[$item['team_id']] ?? 0) + 1;
            }
        }

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

            // Struktur mit Team-Häufigkeit erweitern
            $teamOpponents[$gruppeName][$teamName] = [
                'opponents' => $opponentsList,
                'race_count' => $teamRaceCount[$teamId] ?? 0
            ];
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
            'buchholzwertung' => $buchholzwertung,
            'heats_count' => $heatsCount,
            'min_pause' => $minPause,
            'min_pause_org' => $minPauseOrg,
            'pause_after_heats' => $pauseAfterHeats,
            'finals_start_time' => $finalsStartTimeStr,
            'finals_count' => $finalsCount,
            'cups_count' => $cupsCount,
            'award_ceremony_time' => $awardCeremonyTimeStr,
            'min_time_before_ceremony' => $minTimeBeforeCeremony,
            'finale_publish_time' => $finalePublishTimeStr,
            'tabelleSystem' => $tabelleSystem,
            'pause_type' => $pauseType,
            'pause_trigger' => $pauseTrigger,
            'pause_duration' => $pauseDuration,
            'configWarnings' => array_values($configWarnings),
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
                    'race_number' => is_numeric($item['race_number'] ?? null) ? (int)$item['race_number'] : null,
                    'gruppe_id' => is_numeric($item['gruppe_id']) ? $item['gruppe_id'] : null,
                    'time' => $item['time'],
                    'lane' => $lane,
                    'team_id' => $item['team_id'] ?? null,
                    'is_final' => $item['is_final'] ?? false,
                    'final_type' => $item['final_type'] ?? null,
                    'placeholder_name' => $item['placeholder_name'] ?? null,
                    'source_tabele_id' => $item['source_tabele_id'] ?? null,
                    'source_place' => $item['source_place'] ?? null,
                    'heat_index' => $item['heat_index'] ?? null,
                    'pause_minutes' => $item['pause_minutes'] ?? null,
                    'conflicts' => $item['conflicts'] ?? 0,
                    'org_intervals' => $item['org_intervals'] ?? null,
                ]);
            }

            // Erstelle eine Siegerehrung-Eintrag als DB-Item, wenn eine Zeit angegeben ist
            $awardCeremonyTimeStr = $params['award_ceremony_time'] ?? null;
            if ($awardCeremonyTimeStr) {
                $ceremonyTime = \Carbon\Carbon::parse($awardCeremonyTimeStr);
                $draft->items()->create([
                    'race_number' => 0,
                    'gruppe_id' => 0,
                    'time' => $ceremonyTime->format('H:i'),
                    'lane' => null,
                    'team_id' => null,
                    'is_final' => false,
                    'final_type' => null,
                    'placeholder_name' => 'Siegerehrung',
                    'source_tabele_id' => null,
                    'source_place' => null,
                    'heat_index' => null,
                    'pause_minutes' => null,
                    'conflicts' => 0,
                    'org_intervals' => null,
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
        $swapRaceNumber = null;

        // Versuche regattaId aus dem Request zu holen falls Session leer
        if (!$regattaId) {
             $regattaId = $request->input('regatta_id');
        }

        $draft = RafflePlan::where('event_id', $regattaId)->where('is_draft', true)->first();

        if (!$draft || $raceNumber === null) return back();

        // Alle Items laden, sortiert nach race_number
        $items = $draft->items()->orderBy('race_number')->orderBy('lane')->get();
        $grouped = $items->groupBy('race_number');

        // Spezialfall: Mittagspause verschieben -> Zeiten direkt mit dem Nachbar-Block tauschen.
        if ($request->input('move_type') === 'pause') {
            $pauseTime = substr((string)$request->input('race_time', ''), 0, 5);
            if ($pauseTime === '') {
                return back()->with('error', 'Pause-Zeit nicht gefunden.');
            }

            $blocks = [];
            foreach ($items as $item) {
                if ($this->isPausePlanItem($item) && !$this->isAwardCeremonyPlanItem($item)) {
                    $type = 'pause';
                    $key = 'special-pause-' . substr((string)$item->time, 0, 5);
                } elseif ($this->isAwardCeremonyPlanItem($item)) {
                    $type = 'award';
                    $key = 'special-award-' . substr((string)$item->time, 0, 5);
                } else {
                    $key = 'race-' . (int)$item->race_number;
                    $type = 'race';
                }

                if (!isset($blocks[$key])) {
                    $blocks[$key] = [
                        'type' => $type,
                        'time' => substr((string)$item->time, 0, 5),
                        'items' => [],
                    ];
                }
                $blocks[$key]['items'][] = $item;
            }

            $blocks = array_values($blocks);
            usort($blocks, function ($a, $b) {
                $aParts = explode(':', $a['time'] ?: '00:00');
                $bParts = explode(':', $b['time'] ?: '00:00');
                $aMinutes = ((int)($aParts[0] ?? 0) * 60) + (int)($aParts[1] ?? 0);
                $bMinutes = ((int)($bParts[0] ?? 0) * 60) + (int)($bParts[1] ?? 0);

                if ($aMinutes !== $bMinutes) {
                    return $aMinutes <=> $bMinutes;
                }

                // Bei gleicher Zeit soll die Siegerehrung immer hinter regulären Blöcken stehen.
                $aPenalty = $a['type'] === 'award' ? 1 : 0;
                $bPenalty = $b['type'] === 'award' ? 1 : 0;
                return $aPenalty <=> $bPenalty;
            });

            $index = null;
            foreach ($blocks as $i => $block) {
                if ($block['type'] === 'pause' && $block['time'] === $pauseTime) {
                    $index = $i;
                    break;
                }
            }

            if ($index === null) {
                return back()->with('error', 'Mittagspause konnte nicht gefunden werden.');
            }

            if ($direction === 'up' && $index > 0) {
                $swapIndex = $index - 1;
            } elseif ($direction === 'down' && $index < count($blocks) - 1) {
                $swapIndex = $index + 1;
            } else {
                return back();
            }

            $currentTime = $blocks[$index]['time'];
            $swapTime = $blocks[$swapIndex]['time'];
            $pauseDuration = (int)($draft->params['pause_duration'] ?? 30);

            $interval = (int)($draft->interval ?? 10);

            DB::transaction(function () use ($blocks, $index, $swapIndex, $currentTime, $swapTime, $pauseDuration, $direction, $interval) {
                // Prüfe, ob der aktuelle Block eine Pause ist
                $currentBlockIsPause = $blocks[$index]['type'] === 'pause';
                $swapBlockIsPause = $blocks[$swapIndex]['type'] === 'pause';

                if ($currentBlockIsPause) {
                    if ($direction === 'down') {
                        // Pause nach unten: Pause bekommt alte Pausenzeit + Intervall,
                        // das getauschte Rennen übernimmt die alte Pausenzeit.
                        foreach ($blocks[$index]['items'] as $item) {
                            $newTime = \Carbon\Carbon::createFromFormat('H:i', $currentTime)
                                ->addMinutes($interval);
                            $item->time = $newTime->format('H:i');
                            $item->save();
                        }
                        foreach ($blocks[$swapIndex]['items'] as $item) {
                            $item->time = $currentTime;
                            $item->save();
                        }
                    } else {
                        // Pause nach oben: Pause übernimmt die Zeit des Rennens,
                        // das getauschte Rennen bekommt die Pausenlänge auf die alte Rennzeit.
                        foreach ($blocks[$index]['items'] as $item) {
                            $item->time = $swapTime;
                            $item->save();
                        }
                        foreach ($blocks[$swapIndex]['items'] as $item) {
                            $newTime = \Carbon\Carbon::createFromFormat('H:i', $swapTime)
                                ->addMinutes($pauseDuration);
                            $item->time = $newTime->format('H:i');
                            $item->save();
                        }
                    }
                } elseif ($swapBlockIsPause) {
                    // Swap-Block ist Pause: Swap-Block übernimmt currentTime,
                    // Aktuelle Block (Rennen) bekommt currentTime + Pausenlänge
                    foreach ($blocks[$index]['items'] as $item) {
                        $newTime = \Carbon\Carbon::createFromFormat('H:i', $currentTime)
                            ->addMinutes($pauseDuration);
                        $item->time = $newTime->format('H:i');
                        $item->save();
                    }
                    foreach ($blocks[$swapIndex]['items'] as $item) {
                        $item->time = $currentTime;
                        $item->save();
                    }
                } else {
                    // Niemand ist Pause: einfacher Tausch
                    foreach ($blocks[$index]['items'] as $item) {
                        $item->time = $swapTime;
                        $item->save();
                    }
                    foreach ($blocks[$swapIndex]['items'] as $item) {
                        $item->time = $currentTime;
                        $item->save();
                    }
                }
            });

            $this->recalculateDerivedValuesFromCurrentTimes($draft);

            $url = url()->previous();
            if (($pos = strpos($url, '#')) !== false) {
                $url = substr($url, 0, $pos);
            }

            return redirect($url . '#pause-' . str_replace(':', '', $swapTime))->with('success', 'Mittagspause verschoben: Startzeiten getauscht, Team-Abstände und Konflikte neu berechnet.');
        }

        // Normales Verschieben betrifft nur echte Rennen (race_number > 0).
        // Spezialblöcke mit race_number = 0 (Pause/Siegerehrung) bleiben unverändert.
        if ($raceNumber <= 0) {
            return back()->with('error', 'Spezialblöcke (race_number 0) können hier nicht als Rennen verschoben werden.');
        }

        $keys = collect($grouped->keys())
            ->map(fn($k) => (int)$k)
            ->filter(fn($k) => $k > 0)
            ->sort()
            ->values()
            ->toArray();
        $index = array_search($raceNumber, $keys);

        if ($index === false) return back();

        if ($direction === 'up' && $index > 0) {
            $prevKey = $keys[$index - 1];
            $swapRaceNumber = $prevKey;
            $keys[$index - 1] = $raceNumber;
            $keys[$index] = $prevKey;
        } elseif ($direction === 'down' && $index < count($keys) - 1) {
            $nextKey = $keys[$index + 1];
            $swapRaceNumber = $nextKey;
            $keys[$index + 1] = $raceNumber;
            $keys[$index] = $nextKey;
        } else {
            return back();
        }

        $movingGroup = $grouped[$raceNumber] ?? collect();
        $swapGroup = $grouped[$swapRaceNumber] ?? collect();
        $movingIsPause = $movingGroup->isNotEmpty() && $this->isPausePlanItem($movingGroup->first()) && !$this->isAwardCeremonyPlanItem($movingGroup->first());
        $swapIsPause = $swapGroup->isNotEmpty() && $this->isPausePlanItem($swapGroup->first()) && !$this->isAwardCeremonyPlanItem($swapGroup->first());
        $isPauseSwap = $movingIsPause || $swapIsPause;
        $movingTime = $movingGroup->first()->time ?? null;
        $swapTime = $swapGroup->first()->time ?? null;
        $pauseDuration = (int)($draft->params['pause_duration'] ?? 30);

        DB::transaction(function() use ($keys, $grouped, $isPauseSwap, $movingGroup, $swapGroup, $movingTime, $swapTime, $movingIsPause, $swapIsPause, $pauseDuration) {
            // Wir weisen neue fortlaufende Nummern zu
            // Nur für echte Rennen > 0. race_number 0 bleibt unverändert.

            $newRaceNumber = 1;
            foreach ($keys as $oldRaceNumber) {
                foreach ($grouped[$oldRaceNumber] as $item) {
                    $item->race_number = $newRaceNumber;
                    $item->save();
                }
                $newRaceNumber++;
            }

            // Beim Verschieben einer Pause: Pause übernimmt Zeit des Renns,
            // Rennen bekommt: alte_rennzeit + Pausenlänge
            if ($isPauseSwap && $movingTime && $swapTime) {
                if ($movingIsPause) {
                    // Bewegte Group ist die Pause
                    foreach ($movingGroup as $item) {
                        $item->time = $swapTime;  // Pause übernimmt Zeit des Rennens
                        $item->save();
                    }
                    foreach ($swapGroup as $item) {
                        // Rennen bekommt alte_rennzeit + Pausenlänge
                        $newTime = \Carbon\Carbon::createFromFormat('H:i', $swapTime)
                            ->addMinutes($pauseDuration);
                        $item->time = $newTime->format('H:i');
                        $item->save();
                    }
                } else {
                    // Bewegte Group ist ein Rennen, Swap-Group ist die Pause
                    foreach ($movingGroup as $item) {
                        // Rennen bekommt alte_rennzeit + Pausenlänge
                        $newTime = \Carbon\Carbon::createFromFormat('H:i', $movingTime)
                            ->addMinutes($pauseDuration);
                        $item->time = $newTime->format('H:i');
                        $item->save();
                    }
                    foreach ($swapGroup as $item) {
                        $item->time = $movingTime;  // Pause übernimmt Zeit des Rennens
                        $item->save();
                    }
                }
            }
        });

        // Bei Pausen-Tausch: Neuberechnung notwendig, da sich die Zeiten der Rennen ändern können
        if (!$isPauseSwap) {
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
        } else {
            // Pause-Tausch: Alle Zeiten ab der Pause neu berechnen
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
        }

        // Bestimme die neue race_number für die Weiterleitung (Sprungmarke)
        // In der Transaktion oben werden nur Rennen > 0 neu vergeben.
        $finalRaceNumber = 0;
        $counter = 1;
        foreach ($keys as $k) {
            if ($k === $raceNumber) {
                $finalRaceNumber = $counter;
                break;
            }
            $counter++;
        }

        // Wir hängen den Anker an die vorherige URL an.
        // Falls die URL bereits einen Anker hat, entfernen wir ihn zuerst.
        $url = url()->previous();
        if (($pos = strpos($url, '#')) !== false) {
            $url = substr($url, 0, $pos);
        }

        return redirect($url . "#race-" . $finalRaceNumber)
            ->with('success', $isPauseSwap ? 'Pause verschoben: Uhrzeiten getauscht, Team-Abstände und Konflikte neu berechnet.' : 'Rennreihenfolge und Startzeiten angepasst.');
    }

    /**
     * Berechnet abgeleitete Werte (Team-Pausen, Konflikte, maxHeatEndTime) neu,
     * ohne bestehende Startzeiten zu verändern.
     */
    private function recalculateDerivedValuesFromCurrentTimes(RafflePlan $draft): void
    {
        $items = $draft->items()->orderBy('race_number')->orderBy('lane')->get();
        $raceGroups = $items->groupBy('race_number')->sortBy(function ($lanes) {
            $first = $lanes->first();
            $timeStr = substr((string)($first->time ?? '00:00'), 0, 5);
            $parts = explode(':', $timeStr);
            $minutes = ((int)($parts[0] ?? 0) * 60) + (int)($parts[1] ?? 0);
            $raceNumber = (int)($first->race_number ?? 0);
            $awardPenalty = !empty($first->is_award_ceremony) ? 100000 : 0;

            return ($minutes * 1000000) + $awardPenalty + max($raceNumber, 0);
        });

        $lastStartTimes = [];
        $opponentHistory = [];
        $maxHeatEndTimePerGroup = [];
        $maxHeatEndTime = null;

        foreach ($raceGroups as $lanes) {
            $first = $lanes->first();
            $isPause = !empty($first->is_extra_pause) && empty($first->is_award_ceremony);
            $isAward = !empty($first->is_award_ceremony);
            $isFinal = !empty($first->is_final);

            if ($isPause || $isAward) {
                foreach ($lanes as $lane) {
                    $lane->pause_minutes = null;
                    $lane->conflicts = 0;
                    $lane->save();
                }
                continue;
            }

            $time = \Carbon\Carbon::parse($first->time ?? '00:00');

            foreach ($lanes as $lane) {
                if ($isFinal) {
                    if (isset($lane->gruppe_id) && isset($maxHeatEndTimePerGroup[$lane->gruppe_id])) {
                        $lane->pause_minutes = (int)$time->diffInMinutes($maxHeatEndTimePerGroup[$lane->gruppe_id]);
                    } else {
                        $lane->pause_minutes = null;
                    }
                } else {
                    if (!empty($lane->team_id) && isset($lastStartTimes[$lane->team_id])) {
                        $lane->pause_minutes = (int)$time->diffInMinutes($lastStartTimes[$lane->team_id]);
                    } else {
                        $lane->pause_minutes = null;
                    }

                    if (!empty($lane->team_id)) {
                        $lastStartTimes[$lane->team_id] = $time->copy();
                    }
                }

                $conflictCount = 0;
                if (!$isFinal && !empty($lane->team_id)) {
                    foreach ($lanes as $otherLane) {
                        if ($otherLane->id !== $lane->id && !empty($otherLane->team_id)) {
                            $conflictCount += ($opponentHistory[$lane->team_id][$otherLane->team_id] ?? 0);
                        }
                    }
                }

                $lane->conflicts = $conflictCount;
                $lane->save();
            }

            if (!$isFinal) {
                $maxHeatEndTime = $time->copy();

                foreach ($lanes as $lane) {
                    if (isset($lane->gruppe_id)) {
                        $maxHeatEndTimePerGroup[$lane->gruppe_id] = $time->copy();
                    }
                }

                foreach ($lanes as $laneA) {
                    foreach ($lanes as $laneB) {
                        if ($laneA->id !== $laneB->id && !empty($laneA->team_id) && !empty($laneB->team_id)) {
                            $opponentHistory[$laneA->team_id][$laneB->team_id] = ($opponentHistory[$laneA->team_id][$laneB->team_id] ?? 0) + 1;
                        }
                    }
                }
            }
        }

        if ($maxHeatEndTime) {
            $params = $draft->params ?? [];
            $params['maxHeatEndTime'] = $maxHeatEndTime->format('H:i');
            $draft->update(['params' => $params]);
        }
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
        $minPauseOrg = $request->input('min_pause_org', 40);
        $finalsStartTimeStr = $request->input('finals_start_time');
        $pauseAfterHeats = $request->input('pause_after_heats', 30);
        $cupsCount = max(1, (int) $request->input('cups_count', ($draft->params['cups_count'] ?? 1)));

        // Zusätzliche Pausenoptionen aus Draft laden oder Request
        $pauseType = $request->input('pause_type', $draft->params['pause_type'] ?? 'none');
        $pauseTrigger = $request->input('pause_trigger', $draft->params['pause_trigger'] ?? '');
        $pauseDuration = (int)$request->input('pause_duration', $draft->params['pause_duration'] ?? 30);
        $extraPauseApplied = false;
        $appliedPauseTriggers = [];
        $pauseTriggerNumbers = array_values(array_unique(array_filter(array_map(
            fn($v) => (int)trim($v),
            explode(',', (string)$pauseTrigger)
        ), fn($v) => $v > 0)));

        $awardCeremonyTimeStr = $request->input('award_ceremony_time');
        $minTimeBeforeCeremony = $request->input('min_time_before_ceremony', 30);
        $finalePublishTimeStr = trim((string) $request->input('finale_publish_time', ''));

        $items = $draft->items()->orderBy('race_number')->orderBy('lane')->get();
        $pauseBlockLabel = '--- PAUSENBLOCK NACH DEN VORLÄUFEN ---';

        $currentGlobalTime = \Carbon\Carbon::parse($startTime);
        $finalsStartTime = \Carbon\Carbon::parse($finalsStartTimeStr);

        // Spezial-Items robust erkennen und normalisieren (Flags + Text + Fallback über Uhrzeit)
        $pauseItems = collect();
        $ceremonyItems = collect();
        foreach ($items as $item) {
            $isPause = $this->isPausePlanItem($item) && !$this->isAwardCeremonyPlanItem($item);
            $isAward = $this->isAwardCeremonyPlanItem($item);

            if ($isPause) {
                $pauseItems->push($item);
                continue;
            }

            if ($isAward) {
                $item->heat_index = null;
                $item->save();
                $ceremonyItems->push($item);
            }
        }

        // Bestehende Mittagspause-Items werden für die Recalculate-Logik neu aufgebaut.
        foreach ($pauseItems as $oldPauseItem) {
            $oldPauseItem->delete();
        }
        $pauseItems = collect();

        // Nur echte Rennen für die Zeit-Neuberechnung verwenden
        $raceGroups = $items->reject(function ($i) {
            return !empty($i->is_extra_pause)
                || !empty($i->is_award_ceremony)
                || ((int)($i->gruppe_id ?? 0) === 0 && empty($i->team_id));
        })->groupBy('race_number');

        $maxHeatEndTime = $currentGlobalTime->copy();
        $maxHeatEndTimePerGroup = [];
        $lastStartTimes = []; // Trackt die letzte Startzeit pro Team
        $isFirstFinal = true;
        $firstFinalStartTime = null;
        $maxPrelimHeatIndex = $raceGroups
            ->filter(function ($lanes) {
                return !($lanes[0]->is_final ?? false);
            })
            ->map(function ($lanes) {
                return (int)($lanes[0]->heat_index ?? 0);
            })
            ->max() ?? 0;
        $finalHeatIndex = (int)$maxPrelimHeatIndex + 1;
        // Bereits oben aus den Flags bestimmt

        // Wir brauchen Team-Namen für die Pausenberechnung bei Organisationen (optional, falls gewünscht)
        // Aber hier berechnen wir pause_minutes für das Team selbst.

        foreach ($raceGroups as $rn => $lanes) {
            $isFinal = $lanes[0]->is_final;

            if ($isFinal && $isFirstFinal) {
                // Gleiches Regelwerk wie in generatePreview():
                // erstes Finale >= max(Startzeit Finals, letzter Vorlauf + Pause nach Vorläufen)
                $earliestFinalStart = $maxHeatEndTime->copy()->addMinutes((int)$pauseAfterHeats);
                if ($finalsStartTime->lt($earliestFinalStart)) {
                    $currentGlobalTime = $earliestFinalStart;
                } else {
                    $currentGlobalTime = $finalsStartTime->copy();
                }

                $firstFinalStartTime = $currentGlobalTime->copy();

                // Pausenblock wird jetzt NICHT in der DB gespeichert, sondern nur in der View berechnet.
                // Die View-Logik berücksichtigt $maxHeatEndTime und die Finalstart-Zeit zur Anzeige.

                $isFirstFinal = false;
            }

            $timeStr = $currentGlobalTime->format('H:i');

            // Sonderfall: Mittagspause-Item (falls vorhanden) direkt in die Berechnung einfließen lassen
            // Wir prüfen bei jedem Rennen, ob wir die Mittagspause (falls zeitbasiert) einschieben müssen
            if (!$extraPauseApplied && $pauseType === 'time' && !empty($pauseTrigger)) {
                $triggerTime = \Carbon\Carbon::parse($pauseTrigger);
                if ($currentGlobalTime->format('H:i') >= $triggerTime->format('H:i')) {
                    $draft->items()->create([
                        'race_number' => 0,
                        'gruppe_id' => 0,
                        'time' => $currentGlobalTime->format('H:i'),
                        'lane' => null,
                        'team_id' => null,
                        'is_final' => false,
                        'final_type' => null,
                        'placeholder_name' => 'Mittagspause (' . $pauseDuration . ' Min)',
                        'source_tabele_id' => null,
                        'source_place' => null,
                        'heat_index' => $lanes[0]->heat_index ?? null,
                        'pause_minutes' => null,
                        'conflicts' => 0,
                        'org_intervals' => null,
                    ]);

                    $currentGlobalTime->addMinutes($pauseDuration);
                    $timeStr = $currentGlobalTime->format('H:i');
                    $extraPauseApplied = true;
                }
            }

            foreach ($lanes as $lane) {
                $lane->time = $timeStr;

                if ($isFinal) {
                    $lane->heat_index = $finalHeatIndex;
                    // Finale: Abstand zum letzten Vorlauf der Gruppe
                    if (isset($lane->gruppe_id) && isset($maxHeatEndTimePerGroup[$lane->gruppe_id])) {
                        $diff = $currentGlobalTime->diffInMinutes($maxHeatEndTimePerGroup[$lane->gruppe_id]);
                        $lane->pause_minutes = (int)$diff;
                    } else {
                        $lane->pause_minutes = null;
                    }
                } else {
                    // Vorläufe: Abstand zum letzten Start des Teams
                    if (isset($lane->team_id) && $lane->team_id && isset($lastStartTimes[$lane->team_id])) {
                        $diff = $currentGlobalTime->diffInMinutes($lastStartTimes[$lane->team_id]);
                        $lane->pause_minutes = (int)$diff;
                    } else {
                        $lane->pause_minutes = null;
                    }

                    if (isset($lane->team_id) && $lane->team_id) {
                        $lastStartTimes[$lane->team_id] = $currentGlobalTime->copy();
                    }
                }

                // Konflikte berechnen
                $conflictCount = 0;
                if (!$isFinal && isset($lane->team_id) && $lane->team_id) {
                    foreach ($lanes as $otherLane) {
                        if ($otherLane->id !== $lane->id && isset($otherLane->team_id) && $otherLane->team_id) {
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
                        if ($laneA->id !== $laneB->id && isset($laneA->team_id) && $laneA->team_id && isset($laneB->team_id) && $laneB->team_id) {
                            $opponentHistory[$laneA->team_id][$laneB->team_id] = ($opponentHistory[$laneA->team_id][$laneB->team_id] ?? 0) + 1;
                        }
                    }
                }
            }

            if (!$isFinal) {
                $maxHeatEndTime = $currentGlobalTime->copy();
                foreach ($lanes as $lane) {
                    if (isset($lane->gruppe_id)) {
                        $maxHeatEndTimePerGroup[$lane->gruppe_id] = $currentGlobalTime->copy();
                    }
                }

                // Automatische Pausenanwendung bei der Neuberechnung
                if (!$extraPauseApplied && $pauseType !== 'none') {
                    if ($pauseType === 'time' && !empty($pauseTrigger)) {
                        $triggerString = $pauseTrigger;
                        if (is_numeric($triggerString) && strlen($triggerString) <= 2) {
                            $triggerString .= ':00';
                        }
                        try {
                            $triggerTime = \Carbon\Carbon::parse($triggerString);
                            if ($currentGlobalTime->format('H:i') >= $triggerTime->format('H:i')) {
                                $currentGlobalTime->addMinutes($pauseDuration);
                                $extraPauseApplied = true;
                            }
                        } catch (\Exception $e) {
                            // Ignorieren bei Fehlformatierung
                            \Illuminate\Support\Facades\Log::debug('Ungueltiger Pause-Trigger bei Recalculate', [
                                'trigger' => $triggerString,
                                'error' => $e->getMessage(),
                            ]);
                        }
                    } elseif (($pauseType === 'heat' || $pauseType === 'race') && !empty($pauseTrigger)) {
                        // Bei Neuberechnung ist es schwerer den "letzten Heat der Runde" zu finden
                        // Wir könnten schauen ob der nächste Heat ein höheres Level hat.
                        // Aber da wir die Pause als Item in der DB haben (falls sie existiert),
                        // wird sie oben schon behandelt. Diese Logik hier ist für den Fall,
                        // dass die Pause NOCH NICHT als Item existiert aber die Parameter gesetzt sind.
                        // Da recalculateTimes meist auf existierenden Items arbeitet, ist das primär für die Konsistenz.
                    }
                }
            }
            // Mittagspause(n) nach definierten Läufen oder am Ende definierter Heat-Blöcke einfügen.
            if (!$isFinal && ($pauseType === 'heat' || $pauseType === 'race') && !empty($pauseTriggerNumbers)) {
                $nextRn = $rn + 1;
                $nextLanes = $raceGroups[$nextRn] ?? null;
                $isNextFinal = $nextLanes ? $nextLanes[0]->is_final : true;

                $currentHeatRound = $lanes[0]->heat_index ?? 0;
                $nextHeatRound = $nextLanes ? ($nextLanes[0]->heat_index ?? 0) : 0;
                $isLastHeatOfRound = ($nextHeatRound > $currentHeatRound || $isNextFinal);
                $currentRaceNumber = (int)$rn;

                $isHeatTrigger = $pauseType === 'heat'
                    && $isLastHeatOfRound
                    && in_array((int)$currentHeatRound, $pauseTriggerNumbers, true);
                $isRaceTrigger = $pauseType === 'race'
                    && in_array($currentRaceNumber, $pauseTriggerNumbers, true);
                $triggerId = $isHeatTrigger ? ('heat_' . $currentHeatRound) : ('race_' . $currentRaceNumber);

                if (($isHeatTrigger || $isRaceTrigger) && !in_array($triggerId, $appliedPauseTriggers, true)) {
                    $pauseStart = $currentGlobalTime->copy()->addMinutes($interval);
                    $draft->items()->create([
                        'race_number' => 0,
                        'gruppe_id' => 0,
                        'time' => $pauseStart->format('H:i'),
                        'lane' => null,
                        'team_id' => null,
                        'is_final' => false,
                        'final_type' => null,
                        'placeholder_name' => 'Mittagspause (' . $pauseDuration . ' Min)',
                        'source_tabele_id' => null,
                        'source_place' => null,
                        'heat_index' => $currentHeatRound ?: ($lanes[0]->heat_index ?? null),
                        'pause_minutes' => null,
                        'conflicts' => 0,
                        'org_intervals' => null,
                    ]);

                    $appliedPauseTriggers[] = $triggerId;
                    $currentGlobalTime->addMinutes($pauseDuration);
                }
            }
            $currentGlobalTime->addMinutes($interval);
        }

        $maxRaceTime = $currentGlobalTime->copy()->subMinutes($interval);

        if ($ceremonyItems->count() > 0) {
            foreach ($ceremonyItems as $cItem) {
                $cItem->delete();
            }
        }

        // Erstelle eine Siegerehrung-Eintrag als DB-Item mit is_award_ceremony Flag
        if ($awardCeremonyTimeStr) {
            $ceremonyTime = \Carbon\Carbon::parse($awardCeremonyTimeStr);
            $earliestCeremony = $maxRaceTime->copy()->addMinutes($minTimeBeforeCeremony);
            if ($ceremonyTime->lt($earliestCeremony)) {
                $ceremonyTime = $earliestCeremony;
            }

            // Nach dem Bereinigen immer frisch neu anlegen, damit keine gelöschten Models reused werden.
            $draft->items()->create([
                'race_number' => 0,
                'gruppe_id' => 0,
                'time' => $ceremonyTime->format('H:i'),
                'lane' => null,
                'team_id' => null,
                'is_final' => false,
                'final_type' => null,
                'placeholder_name' => 'Siegerehrung',
                'heat_index' => null,
                'pause_minutes' => null,
                'conflicts' => 0,
                'org_intervals' => null,
            ]);
        }

        // Berechne Veröffentlichungszeit für Finals (Fallback: 1 Stunde nach Siegerehrungszeit)
        $finalePublishTime = null;
        if ($finalePublishTimeStr === '' && $awardCeremonyTimeStr) {
            $finalePublishTime = \Carbon\Carbon::parse($awardCeremonyTimeStr)->addHour();
            $finalePublishTimeStr = $finalePublishTime->format('H:i');
        }

        // Params aktualisieren
        $params = $draft->params;
        $params['start_time'] = $startTime;
        $params['interval'] = $interval;
        $params['finals_start_time'] = $finalsStartTimeStr;
        $params['pause_after_heats'] = $pauseAfterHeats;
        $params['cups_count'] = $cupsCount;
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

        \DB::transaction(function () use ($regattaId, $preview, $params, $draft) {
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

            $normalizeTime = static function ($time, string $fallback = '00:00:00'): string {
                if (!is_string($time) || trim($time) === '') {
                    return $fallback;
                }

                $time = trim($time);
                if (preg_match('/^\d{1,2}:\d{2}$/', $time)) {
                    return $time . ':00';
                }

                if (preg_match('/^\d{1,2}:\d{2}:\d{2}$/', $time)) {
                    return $time;
                }

                return $fallback;
            };

            // Erste echte Renn-Startzeit ermitteln (für Fallbacks).
            // Spezialblöcke (Pause/Siegerehrung) dürfen hier nicht einfließen.
            $firstRaceTime = $normalizeTime(collect($preview)
                ->where('gruppe_id', '!=', 0)
                ->filter(function ($item) {
                    return empty($item['is_extra_pause']) && empty($item['is_award_ceremony']);
                })
                ->sortBy([
                    ['time', 'asc'],
                    ['race_number', 'asc'],
                ])
                ->first()['time'] ?? null);

            // Für Vorläufe je Gruppe die Startzeit des 1. Laufs bestimmen.
            $firstVorlaufTimeByGroup = collect($preview)
                ->where('gruppe_id', '!=', 0)
                ->filter(function ($item) {
                    return empty($item['is_extra_pause'])
                        && empty($item['is_award_ceremony'])
                        && empty($item['is_final']);
                })
                ->groupBy('gruppe_id')
                ->map(function ($groupItems) use ($normalizeTime, $firstRaceTime) {
                    $firstGroupHeat = collect($groupItems)
                        ->sortBy(function ($item) {
                            return sprintf(
                                '%03d_%s_%06d',
                                (int)($item['heat_index'] ?? 999),
                                (string)($item['time'] ?? '99:99:99'),
                                (int)($item['race_number'] ?? 999999)
                            );
                        })
                        ->first();

                    return $normalizeTime($firstGroupHeat['time'] ?? null, $firstRaceTime);
                })
                ->all();

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
                $tabele->getrenntewertung = 0;

                if ($firstLane['is_final']) {
                    $tabele->ueberschrift = $firstLane['final_type'] . ' ' . $firstLane['gruppe_name'];
                    $tabele->finale = str_starts_with((string) $firstLane['final_type'], 'A-Finale') ? 1 : 0;
                    $tabele->tabelleLevelVon = $params['heats_count']+1 ?? 2;
                    $tabele->tabelleLevelBis = $params['heats_count']+1 ?? 2;
                    $tabele->wertungsart = $params['wertungsart'] ?? 1;
                    $tabele->system_id = $params['tabelleSystem'] ?? null;
                    $tabele->buchholzwertungaktiv = 0; // Finals nie mit Buchholz
                    $tabele->tabelleVisible = 0;
                    $tabele->finaleAnzeigen = $normalizeTime($params['finale_publish_time'] ?? null);
                } else {
                    $tabele->ueberschrift = 'Vorlauf ' . $firstLane['gruppe_name'];
                    $tabele->finale = 0;
                    $tabele->tabelleLevelVon = 1;
                    $tabele->tabelleLevelBis = $params['heats_count'] ?? 1;
                    $tabele->wertungsart = $params['wertungsart'] ?? 1;
                    $tabele->buchholzwertungaktiv = $params['buchholzwertung'] ?? 0;
                    $tabele->system_id = $params['tabelleSystem'] ?? null;
                    $tabele->tabelleVisible = 1;
                    $tabele->finaleAnzeigen = $firstVorlaufTimeByGroup[$gruppeId] ?? $firstRaceTime;
                }

                $tabele->autor_id = $userId;
                $tabele->bearbeiter_id = $userId;
                $tabele->save();

                $tabeleIds[$key] = $tabele->id;
            }

            // Vorlauf-Quellenmap deterministisch aus tatsächlich erzeugten Tabellen ableiten.
            // Key-Format: "<gruppe_id>_vorlauf"
            $sourceTableByGroup = [];
            foreach ($tabeleIds as $tableKey => $tableId) {
                if (str_ends_with((string)$tableKey, '_vorlauf')) {
                    $gid = (int)explode('_', (string)$tableKey)[0];
                    $sourceTableByGroup[$gid] = $tableId;
                }
            }

            // Quelle für Final-Platzhalter in raffle_plan_items auflösen (source_tabele_id + source_place).
            // Dabei werden EXPLIZIT die in diesem store()-Lauf neu erzeugten Vorlauf-Tabellen verwendet.
            RafflePlanItem::where('raffle_plan_id', $draft->id)
                ->where('is_final', true)
                ->get()
                ->each(function ($item) use ($sourceTableByGroup, $regattaId) {
                    $gid = (int)($item->gruppe_id ?? 0);
                    $resolvedSourceTableId = $sourceTableByGroup[$gid] ?? null;

                    if (!$resolvedSourceTableId) {
                        $resolvedSourceTableId = Tabele::where('event_id', $regattaId)
                            ->where('gruppe_id', $gid)
                            ->where('finale', 0)
                            ->orderByDesc('id')
                            ->value('id');
                    }

                    $item->source_tabele_id = $resolvedSourceTableId;

                    if (empty($item->source_place) && !empty($item->placeholder_name)
                        && preg_match('/Platz\s+(\d+)/i', (string)$item->placeholder_name, $m)) {
                        $item->source_place = (int)$m[1];
                    }

                    $item->save();
                });

            // Rennen erstellen: Gruppiert nach race_number
            $groupedByRace = collect($preview)->where('gruppe_id', '!=', 0)->groupBy('race_number');
            $raceSequentialNumber = 1;

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
                $raceStartTime = $normalizeTime($firstLane['time'] ?? null, $firstRaceTime);
                $race->rennUhrzeit = $raceStartTime;
                $race->verspaetungUhrzeit = $raceStartTime;

                // Veröffentlichungsuhrzeit: Bei Finals 'finale_publish_time', bei Vorläufen Startzeit des 1. Laufs der Gruppe
                if ($firstLane['is_final']) {
                    $race->veroeffentlichungUhrzeit = $normalizeTime($params['finale_publish_time'] ?? null);
                } else {
                    $race->veroeffentlichungUhrzeit = $firstVorlaufTimeByGroup[$gruppeId] ?? $firstRaceTime;
                }

                $race->level = $firstLane['heat_index'] ?? 1;
                $race->nummer = (string)$raceSequentialNumber;

                if ($firstLane['is_final']) {
                    $race->rennBezeichnung = $firstLane['final_type'];
                } else {
                    $race->rennBezeichnung = ($firstLane['heat_index'] ?? 1) . '. Vorlauf';
                    $race->status = 1;
                }

                $race->bahnen = count($laneData);
                $race->autor_id = $userId;
                $race->bearbeiter_id = $userId;
                $race->save();
                $raceSequentialNumber++;

                foreach ($laneData as $lane) {
                    $laneModel = new \App\Models\Lane();
                    $laneModel->regatta_id = $regattaId;
                    $laneModel->rennen_id = $race->id;
                    $laneModel->tabele_id = $tabeleIds[$tableKey] ?? null;
                    $laneModel->mannschaft_id = $lane['team_id'] ?? null;

                    if ($firstLane['is_final']) {
                        $sourceTableId = $sourceTableByGroup[$gruppeId] ?? ($lane['source_tabele_id'] ?? null);

                        if (!$sourceTableId) {
                            $sourceTableId = Tabele::where('event_id', $regattaId)
                                ->where('gruppe_id', $gruppeId)
                                ->where('finale', 0)
                                ->orderByDesc('id')
                                ->value('id');
                        }

                        $sourcePlace = $lane['source_place'] ?? null;

                        if ($sourcePlace === null && !empty($lane['placeholder_name'])
                            && preg_match('/Platz\s+(\d+)/i', (string)$lane['placeholder_name'], $m)) {
                            $sourcePlace = (int)$m[1];
                        }

                        $laneModel->tabelevor_id = $sourceTableId ?? 0;
                        $laneModel->platzvor = $sourcePlace ?? 0;
                    } else {
                        $laneModel->tabelevor_id = 0;
                        $laneModel->platzvor = 0;
                    }

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
                ->select('tabledatas.*', 'regatta_teams.teamlink')
                ->get();

            if ($data->isEmpty()) {
                continue;
            }

            // Primär die echte gespeicherte Platzierung verwenden (tabledatas.platz),
            // damit kein Re-Ranking entsteht wenn Teams ohne teamlink herausgefiltert worden wären.
            // WICHTIG: Das Ranking muss über ALLE Teams berechnet werden (auch teamlink=0),
            //          damit nachfolgende Teams nicht fälschlich nach oben rücken.
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
                $teamlink = (int) ($row->teamlink ?? 0);

                // Rang wird für ALLE Teams hochgezählt (auch ohne teamlink),
                // damit kein fälschliches Aufrücken entsteht.
                if ($teamlink > 0) {
                    // Falls ein Teamlink in mehreren Finals auftaucht, gewinnt die bessere Platzierung.
                    if (!isset($results[$teamlink]) || $rank < $results[$teamlink]['platz']) {
                        $results[$teamlink] = [
                            'platz' => $rank,
                            'tabelle' => $table->ueberschrift,
                        ];
                    }
                }

                $rank++;
            }
        }

        return $results;
    }
}
