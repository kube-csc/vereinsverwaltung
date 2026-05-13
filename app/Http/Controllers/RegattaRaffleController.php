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
            'previewData' => Session::get('rafflePreview')
        ]);
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

        $teamsByGroup = RegattaTeam::where('regatta_id', $regattaId)
            ->where('status', 'Neuanmeldung')
            ->get()
            ->groupBy('gruppe_id');

        $preview = [];
        $currentTime = \Carbon\Carbon::createFromFormat('H:i', $startTime);
        $raceNumber = 1;
        $lastStartTimes = []; // Trackt den letzten Startzeitpunkt jedes Teams

        // Wir gehen alle gewünschten Vorläufe (Heats) durch
        for ($h = 1; $h <= $heatsCount; $h++) {
            foreach ($teamsByGroup as $gruppeId => $gruppeTeams) {
                $raceType = RaceType::find($gruppeId);
                $lanesCount = $raceType->bahnen ?? 4;

                // Teams sortieren nach letztem Startzeitpunkt (um Pause zu maximieren)
                // Teams, die noch nie gestartet sind oder am längsten her, kommen zuerst.
                $sortedTeams = $gruppeTeams->shuffle()->sortBy(function($team) use ($lastStartTimes) {
                    return $lastStartTimes[$team->id] ?? -1;
                });

                $heats = [];
                foreach ($sortedTeams as $team) {
                    $placed = false;
                    foreach ($heats as &$heat) {
                        if (count($heat) < $lanesCount) {
                            $clubExists = false;
                            foreach ($heat as $heatTeam) {
                                if ($heatTeam->plz == $team->plz && $heatTeam->ort == $team->ort) {
                                    $clubExists = true;
                                    break;
                                }
                            }
                            if (!$clubExists) {
                                $heat[] = $team;
                                $placed = true;
                                break;
                            }
                        }
                    }
                    if (!$placed) {
                        $heats[] = [$team];
                    }
                }

                foreach ($heats as $heatTeams) {
                    foreach ($heatTeams as $index => $team) {
                        $pauseStr = '-';
                        if (isset($lastStartTimes[$team->id])) {
                            $diff = $currentTime->diffInMinutes(\Carbon\Carbon::createFromFormat('H:i', $lastStartTimes[$team->id]));
                            $pauseStr = $diff . ' Min';
                        }

                        $preview[] = [
                            'time' => $currentTime->format('H:i'),
                            'pause' => $pauseStr,
                            'race_number' => $raceNumber,
                            'lane' => $index + 1,
                            'team_id' => $team->id,
                            'team_name' => $team->teamname,
                            'gruppe_id' => $gruppeId,
                            'gruppe_name' => $raceType->typ ?? 'Unbekannt',
                            'wertungsart' => $wertungsart,
                            'heat_index' => $h
                        ];
                        $lastStartTimes[$team->id] = $currentTime->format('H:i');
                    }
                    $currentTime->addMinutes($interval);
                    $raceNumber++;
                }
            }
        }

        Session::put('rafflePreview', $preview);
        Session::put('raffleParams', $request->only(['start_time', 'interval', 'wertungsart', 'heats_count']));

        return redirect()->route('regattaRaffle.index')->with('success', 'Vorschlag generiert.');
    }

    /**
     * Speichert den generierten Plan in der Datenbank.
     */
    public function store(Request $request)
    {
        $regattaId = Session::get('regattaSelectId');
        $preview = Session::get('rafflePreview');
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
                    $tabele->ueberschrift = $heatIndex . '. Vorlauf ' . $firstLane['gruppe_name'];
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
                $race->rennBezeichnung = 'Lauf ' . $raceNum;
                $race->bahnen = count($laneData);
                $race->autor_id = $userId;
                $race->bearbeiter_id = $userId;
                $race->save();

                foreach ($laneData as $lane) {
                    $laneModel = new \App\Models\Lane();
                    $laneModel->regatta_id = $regattaId;
                    $laneModel->rennen_id = $race->id;
                    $laneModel->tabele_id = $tabeleIds[$key];
                    $laneModel->mannschaft_id = $lane['team_id'];
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
