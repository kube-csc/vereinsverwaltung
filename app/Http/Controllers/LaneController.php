<?php

namespace App\Http\Controllers;

use App\Models\Lane;
use App\Models\Race;
use App\Models\RegattaTeam;
use App\Models\Tabele;
use App\Models\Tabledata;
use App\Models\Pointsystem;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class LaneController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
     //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function show($id)
    {
        $race = Race::find($id);

        if ($race->status != 4) {
            $lanes = Lane::where('rennen_id', $id)->orderBy('bahn')->get();
        }

        $platzRennen=0;
        $highhour=0;
        if($race->status == 4) {
            $lanes = Lane::where('rennen_id',$id)->orderBy('platz')->get();
            foreach ($lanes as $lane) {
                if ($lane->platz > 0) {
                    $platzRennen = 1;
                }
            }
            if($platzRennen > 0) {
                // Separate lanes with platz 0 and non-zero platz
                $lanesWithPlatz = $lanes->filter(function ($lane) {
                    return $lane->platz > 0;
                });

                $lanesWithPlatzZero = $lanes->filter(function ($lane) {
                    return $lane->platz == 0;
                });

                // Merge the two collections, placing lanes with platz 0 at the end
                $lanes = $lanesWithPlatz->merge($lanesWithPlatzZero);
            }

            if($platzRennen == 0) {
                $lanes = Lane::where('rennen_id',$id)->orderBy('zeit')->orderBy('hundert')->get();
                foreach($lanes as $lane) {
                    $highhour=substr($lane->zeit,0,2);
                }
                $platzRennen = 2;
            }

        }

        $tabele = Tabele::find( $race->tabele_id);

        return view('regattaManagement.lane.show')->with(
            [
                'lanes' => $lanes,
                'race' => $race,
                'tabele' => $tabele,
                'platzRennen' => $platzRennen,
                'highhour' => $highhour,

                //'success'  => 'Das Ergebnisdokument  <b>' . $document->ergebnisDatei . '</b> wurde gelöscht.'
            ]
        );
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
      //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response|\Illuminate\View\View
     */
    public function editDraw($id)
    {
        $race = Race::find($id);

        // Vorheriges Rennen
        $previousRace = Race::where('event_id', Session::get('regattaSelectId'))
            ->where('id', '!=', $id)
            ->where('rennDatum', $race->rennDatum)
            ->where('rennUhrzeit', '<=', $race->rennUhrzeit)
            ->where('status', '>', 0)
            ->where('status', '<=', 2)
            ->orderBy('rennUhrzeit', 'desc')
            ->first();

        // Nächstes Rennen
        $nextRace = Race::where('event_id', Session::get('regattaSelectId'))
            ->where('id', '!=', $id)
            ->where('rennDatum', $race->rennDatum)
            ->where('rennUhrzeit', '>=', $race->rennUhrzeit)
            ->where('status', '>', 0)
            ->where('status', '<=', 2)
            ->orderBy('rennUhrzeit', 'asc')
            ->first();

        $lanes = Lane::where('rennen_id',$id)->get();

        if ($lanes->isEmpty()) {
            for ($i = 1; $i <= $race->bahnen; $i++) {
                 if($race->tabele_id != Null) {
                    $lane = new Lane([
                            'regatta_id' => Session::get('regattaSelectId'),
                            'rennen_id' => $id,
                            'tabele_id' => $race->tabele_id,
                            'bahn' => $i,
                            'zeit' => '00:00:00',
                            'hundert' => 0,
                            'punkte' => 0,
                            'platz' => 0,
                            'bearbeiter_id' => Auth::id(),
                            'autor_id' => Auth::id(),
                            'updated_at' => Carbon::now(),
                            'created_at' => Carbon::now()
                        ]);
                }
                $lane->save();
            }
            $lanes = Lane::where('rennen_id',$id)->get();
        }

        $tabele = Tabele::where('id', $race->tabele_id)->first();

        if($race->mix == Null && $tabele->gruppe_id>0) {
            $teams = RegattaTeam::where('gruppe_id', $tabele->gruppe_id)
                     ->orderBy('teamname')
                     ->get();
        }
        else {
            $teams = RegattaTeam::where('regatta_id', Session::get('regattaSelectId'))
                     ->orderBy('teamname')
                     ->get();
        }

        $tabeleAlls = Tabele::where('event_id', Session::get('regattaSelectId'))
                            ->orderBy('tabelleLevelVon')
                            ->orderBy('tabelleLevelBis')
                            ->orderBy('ueberschrift')
                            ->get();

        return view('regattaManagement.lane.editDraw')->with(
            [
                'lanes'        => $lanes,
                'race'         => $race,
                'previousRace' => $previousRace,
                'nextRace'     => $nextRace,
                'tabele'       => $tabele,
                'teams'        => $teams,
                'tabeleAlls'   => $tabeleAlls
            ]
        );
    }

    public function editSetDraw($id)
    {
        $race = Race::find($id);

        // Vorheriges Rennen
        $previousRace = Race::where('event_id', Session::get('regattaSelectId'))
            ->where('id', '!=', $id)
            ->where('rennDatum', $race->rennDatum)
            ->where('rennUhrzeit', '<=', $race->rennUhrzeit)
            ->where('status', '>', 0)
            ->where('status', '<=', 2)
            ->orderBy('rennUhrzeit', 'desc')
            ->first();

        // Nächstes Rennen
        $nextRace = Race::where('event_id', Session::get('regattaSelectId'))
            ->where('id', '!=', $id)
            ->where('rennDatum', $race->rennDatum)
            ->where('rennUhrzeit', '>=', $race->rennUhrzeit)
            ->where('status', '>', 0)
            ->where('status', '<=', 2)
            ->orderBy('rennUhrzeit', 'asc')
            ->first();

        $lanes = Lane::where('rennen_id',$id)->get();

        if($lanes->isEmpty()) {
            for($i = 1; $i <= $race->bahnen; $i++) {
                if($race->tabele_id != Null) {
                    $lane = new Lane([
                        'regatta_id'    => Session::get('regattaSelectId'),
                        'rennen_id'     => $id,
                        'tabele_id'     => $race->tabele_id,
                        'bahn'          => $i,
                        'zeit'          => '00:00:00',
                        'hundert'       => 0,
                        'punkte'        => 0,
                        'platz'         => 0,
                        'tabelevor_id'  => 0,
                        'platzvor'      => 0,
                        'bearbeiter_id' => Auth::id(),
                        'autor_id'      => Auth::id(),
                        'updated_at'    => Carbon::now(),
                        'created_at'    => Carbon::now()
                    ]);
                }
                $lane->save();
            }
            $lanes = Lane::where('rennen_id',$id)->get();
        }

        $tabele = Tabele::where('id', $race->tabele_id)->first();

        if($race->mix == Null && $tabele->gruppe_id>0) {
            $teamCount = RegattaTeam::where('gruppe_id', $tabele->gruppe_id)->count();
        }
        else {
            $teamCount = RegattaTeam::where('regatta_id', Session::get('regattaSelectId'))->count();
        }

        $tabeleVorRennens = Tabele::where('event_id', Session::get('regattaSelectId'))
            ->where('tabelleLevelBis', '<', $race->level)
            ->orderBy('tabelleLevelVon')
            ->orderBy('tabelleLevelBis')
            ->orderBy('ueberschrift')
            ->get();

        $tabeleAlls = Tabele::where('event_id', Session::get('regattaSelectId'))
            ->where('tabelleLevelBis', '>=', $race->level)
            ->orderBy('tabelleLevelVon')
            ->orderBy('tabelleLevelBis')
            ->orderBy('ueberschrift')
            ->get();

        return view('regattaManagement.lane.editSetDraw')->with(
            [
                'lanes'            => $lanes,
                'race'             => $race,
                'previousRace'     => $previousRace,
                'nextRace'         => $nextRace,
                'tabele'           => $tabele,
                'teamsCount'       => $teamCount,
                'tabeleAlls'       => $tabeleAlls,
                'tabeleVorRennens' => $tabeleVorRennens
            ]
        );
    }

    public function editResult($id)
    {
        $race = Race::find($id);

        // Vorheriges Rennen
        $previousRace = Race::where('event_id', Session::get('regattaSelectId'))
            ->where('id', '!=', $id)
            ->where('rennDatum', $race->rennDatum)
            ->where('rennUhrzeit', '<=', $race->rennUhrzeit)
            ->where('status', '>=', 3)
            ->where('status', '<=', 4)
            ->orderBy('rennUhrzeit', 'desc')
            ->first();

        // Nächstes Rennen
        $nextRace = Race::where('event_id', Session::get('regattaSelectId'))
            ->where('id', '!=', $id)
            ->where('rennDatum', $race->rennDatum)
            ->where('rennUhrzeit', '>=', $race->rennUhrzeit)
            ->where('status', '>=', 3)
            ->where('status', '<=', 4)
            ->orderBy('rennUhrzeit', 'asc')
            ->first();

        $lanes = Lane::where('rennen_id',$id)->get();

        $tabele = Tabele::find($race->tabele_id);

        if($race->status == 2 && $race->rennDatum == Carbon::now()->toDateString() && $race->rennzeit == 0){
            $ractetime1 = Carbon::now();
            $ractetime2 = $ractetime1->subMinute(3);
            $ractetime  = $ractetime2->toTimeString();
        }
        else{
            $ractetime=$race->verspaetungUhrzeit;
        }

        return view('regattaManagement.lane.editResult')->with(
            [
                'lanes'        => $lanes,
                'race'         => $race,
                'previousRace' => $previousRace,
                'nextRace'     => $nextRace,
                'tabele'       => $tabele,
                'ractetime'    => $ractetime
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $raceId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, int $raceId)
    {
        $changeCount=0;
        foreach ($request->laneId as $index => $laneId) {
            $lane = Lane::find($laneId);
            if ($lane->mannschaft_id != $request->mannschaftId[$index] || $lane->tabele_id != $request->tabeleId[$index]) {
                $altMannschaftId = $lane->mannschaft_id;
                $altTabelleId = $lane->tabele_id;

                if($lane->mannschaft_id != $request->mannschaftId[$index]){
                    $lane->mannschaft_id = $request->mannschaftId[$index];
                    $changeCount=1;
                }

                if($lane->tabele_id != $request->tabeleId[$index]) {
                    $lane->tabele_id = $request->tabeleId[$index];
                    $changeCount=1;
                }

                if($changeCount == 1){
                    $lane->platz = 0;
                    $lane->bearbeiter_id = Auth::id();
                    $lane->updated_at    = Carbon::now();
                    $lane->save();

                    $mannschaftCount = Lane::where('regatta_id', Session::get('regattaSelectId'))
                        ->where('tabele_id', $altTabelleId)
                        ->where('mannschaft_id', $altMannschaftId)
                        ->count();

                    if($mannschaftCount == 0){
                        $tabledata = Tabledata::where('regatta_id', Session::get('regattaSelectId'))
                            ->where('tabele_id', $altTabelleId)
                            ->where('mannschaft_id', $altMannschaftId)
                            ->delete();
                    }
                }
            }
        }

        $race = Race::find($raceId);

        $laneTableBeforCount = Lane::where('rennen_id', $raceId)
            ->where('tabelevor_id', '>', 0)
            ->where('platzvor', '>', 0)
            ->count();

        $teamLaneCount = Lane::where('rennen_id', $raceId)
            ->where('mannschaft_id', '>', 0)
            ->count();

        if($changeCount == 1) {
            if($teamLaneCount == 0) {
                if($laneTableBeforCount == $race->bahnen) {
                    $race->status = 1;
                }
                else{
                    $race->status = 0;
                }
            }
            else {
                  $race->status = 2;
            }

            $race->save();
            $success = 'Das Rennen wurde gesetzt.';

            $tabellenIds = Lane::where('rennen_id', $raceId)
                ->where('mannschaft_id', '>', 0)
                ->pluck('tabele_id')
                ->unique();

            foreach ($tabellenIds as $tabellenId) {

                $raceCount = Race::whereHas('lanes', function ($query) use ($tabellenId) {
                    $query->where('tabele_id', $tabellenId);
                })->where('status', '<', 2)->count();

                if($raceCount == 0) {

                   $laneErsteMannschaftId = Lane::where('tabele_id', $tabellenId)
                                                ->where('mannschaft_id', '>', 0)
                                                ->first();

                   if($laneErsteMannschaftId) {
                        $laneMaxRennen = Lane::where('tabele_id', $tabellenId)
                            ->where('mannschaft_id', $laneErsteMannschaftId->mannschaft_id)
                            ->count();

                        $tabelle = Tabele::find($tabellenId);
                        if ($laneMaxRennen != $tabelle->maxrennen) {
                            $tabelle->maxrennen = $laneMaxRennen;
                            $tabelle->save();
                        }
                   }
                }
            }
        }
        else {
            if($laneTableBeforCount == $race->bahnen) {
                $race->status = 1;
                $race->save();
            }
            if($teamLaneCount > 0 ){
                $race->status = 2;
                $race->save();
            }

            $success = 'Es gab keine Änderung.';
        }

        return redirect('/Rennen/Programm')->with([
                'success' => $success
            ]
        );
    }

    /**
     * updateSetDraw the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param int $raceId
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function updateSetDraw(Request $request, int $raceId)
    {
        $changeCount=0;
        foreach ($request->laneId as $index => $laneId) {
            $lane = Lane::find($laneId);
            if ($lane->tabelevor_id != $request->tabelevorId[$index] || $lane->platzvor != $request->platzvor[$index] || $lane->tabele_id != $request->tabeleId[$index]) {

                if($lane->tabelevor_id != $request->tabelevorId[$index]){
                    $lane->tabelevor_id = $request->tabelevorId[$index];
                    $changeCount=1;
                }

                if($lane->platzvor != $request->platzvor[$index]){
                    $lane->platzvor = $request->platzvor[$index];
                    $changeCount=1;
                }

                if($lane->tabele_id != $request->tabeleId[$index]) {
                    $lane->tabele_id = $request->tabeleId[$index];
                    $changeCount=1;
                }

                if($changeCount == 1){
                    $lane->mannschaft_id = Null;
                    $lane->platz = 0;
                    $lane->bearbeiter_id = Auth::id();
                    $lane->updated_at    = Carbon::now();
                    $lane->save();
                }
            }
        }

        $race = Race::find($raceId);

        $laneTableBeforCount = Lane::where('rennen_id', $raceId)
            ->where('tabelevor_id', '>', 0)
            ->where('platzvor', '>', 0)
            ->count();

        if($changeCount == 1){
            $teamLaneCount = Lane::where('rennen_id', $raceId)
                ->where('mannschaft_id', '>', 0)
                ->count();

            if($teamLaneCount == 0){
                if($laneTableBeforCount == $race->bahnen) {
                    $race->status = 1;
                }
                else{
                    $race->status = 0;
                }
            }
            else{
                $race->status = 2;
            }
            $race->save();
            $success = 'Das Rennen wurde gesetzt.';
        }
        else{
            if($laneTableBeforCount == $race->bahnen) {
                $race->status = 1;
                $race->save();
            }
            $success = 'Es gab keine Änderung.';
        }

        return redirect('/Rennen/Programm')->with([
                'success' => $success
            ]
        );
    }

    public function updateResult(Request $request, $raceId)
    {
        $request->validate([
                'zeit'   => 'min:0|max:10'
            ]
        );

        $race   = Race::find($raceId);
        $tabele = Tabele::find($race->tabele_id);

        $changeCount=0;
        $platzCount=0;

        foreach ($request->laneId as $index => $laneId) {
            $lane = Lane::find($laneId);
            $platzAlt[$laneId] = $lane->platz;
            $aenderung[$laneId] = 0;
            if (($lane && $lane->mannschaft_id) && (($lane->platz != $request->platz[$index]) || ($request->newCalculate == 1))) {
                $lane->platz = $request->platz[$index];
                $lane->bearbeiter_id = Auth::id();
                $lane->updated_at    = Carbon::now();
                $lane->save();

                $aenderung[$laneId] = 1;
                $changeCount = 1;
                if ($request->platz[$index] > 0) {
                    $platzCount = 1;
                }
            }
        }

        $laneRaces = Lane::where('rennen_id', $raceId)
            ->orderBy('platz')
            ->get();

        foreach ($laneRaces as $lane) {
            $punkteAlt[$lane->id] = $lane->punkte;
            if($aenderung[$lane->id] == 1){

                // Wenn es sich um ein Mix Rennen handelt, soll für jede Bahn die Tabelle aufgerufen werden
                if($race->mix == 1 && $lane->tabele_id >0)
                {
                  $tabele = Tabele::find($lane->tabele_id);
                }
                $tabelleBuchholzwertungaktiv[$lane->tabele_id]=$tabele->buchholzwertungaktiv;
                $tabelleWertungsart[$lane->tabele_id]=$tabele->wertungsart;
                if($lane->platz>0) {
                    if ($tabele->wertungsart == 1) {
                        if ($tabele->getrenntewertung == 0) {
                           $pointsystem = Pointsystem::where('system_id', $tabele->system_id)
                                ->where('platz', $lane->platz)
                                ->first();
                        }
                        else {
                            if (isset($platz[$lane->tabele_id])) {
                                $platz[$lane->tabele_id] = $platz[$lane->tabele_id] + 1;
                            }
                            else {
                                $platz[$lane->tabele_id] = 1;
                            }
                            $pointsystem = Pointsystem::where('system_id', $tabele->system_id)
                                ->where('platz', $platz[$lane->tabele_id])
                                ->first();
                        }
                        $punkte = optional($pointsystem)->punkte ?? 0;
                    }

                    if ($tabele->wertungsart == 3) {
                        if ($tabele->getrenntewertung == 0) {
                            $punkte = $race->bahnen - $lane->platz + 1;
                        }
                        else {
                            if (isset($platz[$lane->tabele_id])) {
                                $platz[$lane->tabele_id] = $platz[$lane->tabele_id] + 1;
                            } else {
                                $platz[$lane->tabele_id] = 1;
                            }
                            $punkte = $race->bahnen - $platz[$lane->tabele_id];
                        }
                    }
                }
                else{
                    $punkte=0;
                }

                $tabledata = Tabledata::where('regatta_id', Session::get('regattaSelectId'))
                                      ->where('tabele_id', $tabele->id)
                                      ->where('mannschaft_id', $lane->mannschaft_id)
                                      ->whereNull('deleted_at')
                                      ->first();

                if ($tabledata) {
                    if($platzAlt[$lane->id] == 0) {
                        $tabledata->punkte       += $punkte;
                        $tabledata->rennanzahl   += 1;
                        $tabledata->bearbeiter_id = Auth::id();
                        $tabledata->autor_id      = Auth::id();
                        $tabledata->updated_at    = Carbon::now();
                        $tabledata->save();

                        $laneRacesSave = Lane::find($lane->id);
                        $laneRacesSave->punkte = $punkte;
                        $laneRacesSave->save();
                    }
                    else{
                        $tabledata->punkte = $tabledata->punkte+$punkte-$punkteAlt[$lane->id];
                        if($punkte == 0){
                            $tabledata->rennanzahl  = $tabledata->rennanzahl-1;
                        }
                        else{
                            if($race->status < 4) {
                                $tabledata->rennanzahl += 1;
                            }
                        }

                        $tabledata->bearbeiter_id = Auth::id();
                        $tabledata->updated_at    = Carbon::now();
                        $tabledata->save();

                        $laneRacesSave = Lane::find($lane->id);
                        $laneRacesSave->punkte = $punkte;
                        $laneRacesSave->save();
                    }
                }
                else {
                     $tabledata = new Tabledata([
                        'regatta_id'    => Session::get('regattaSelectId'),
                        'tabele_id'     => $tabele->id,
                        'mannschaft_id' => $lane->mannschaft_id,
                        'rennanzahl'    => 1,
                        'punkte'        => $punkte,
                        'bearbeiter_id' => Auth::id(),
                        'autor_id'      => Auth::id(),
                        'updated_at'    => Carbon::now(),
                        'created_at'    => Carbon::now()
                    ]);
                    $tabledata->save();

                    $laneRacesSave = Lane::find($lane->id);
                    $laneRacesSave->punkte = $punkte;
                    $laneRacesSave->save();
                }
            }
        }

        if($changeCount == 1) {
            $tabelleIds = Lane::where('rennen_id', $raceId)->pluck('tabele_id')->unique();
            foreach ($tabelleIds as $tabelleId) {
                if($tabelleBuchholzwertungaktiv[$tabelleId] == 1 && $tabelleWertungsart[$tabelleId] != 3){

                    //Ermittel gegen welche Mannschaften die Mannschaft bei der die Buchholzzahl ermittel wird
                    $lanes = Lane::where('tabele_id', $tabelleId)->get();

                    // Welche Mannschaften sind in der Tabelle vorhanden
                    $teamIds = $lanes->pluck('mannschaft_id')->unique();

                    foreach ($teamIds as $teamId) {
                        $rennenIds = $lanes->where('mannschaft_id', $teamId)->pluck('rennen_id')->unique();

                        // Ermittel die Mannschaften gegen die Mannschaft in den Rennen $rennenIds gefahren ist
                        $opponentIds = $lanes->whereIn('rennen_id', $rennenIds)
                            ->where('mannschaft_id', '!=' , $teamId)
                            ->where('platz', '>', 0)
                            ->pluck('mannschaft_id');

                        $buchholzScore = 0;
                        foreach ($opponentIds as $opponentId) {
                            $opponent = RegattaTeam::find($opponentId);

                            $buchholzPunkte = Tabledata::where('mannschaft_id', $opponentId)
                                ->where('tabele_id', $tabelleId)
                                ->first();

                            if ($buchholzPunkte) {
                                $buchholzScore += $buchholzPunkte->punkte;
                            }
                        }

                        $tablledata = Tabledata::where('tabele_id', $tabelleId)
                            ->where('mannschaft_id', $teamId)
                            ->first();
                        if ($tablledata) {
                            $tablledata->buchholzzahl = $buchholzScore;
                            $tablledata->save();
                        }
                    }
                }
            }
        }

        Session::put('regattaZeit' , $request->zeit);
        Session::put('regattaZeitMinAbstand' , $request->zeitMinAbstand);

        if($request->rennzeit==Null) {
            $request->rennzeit = 0;
        }

        if ($changeCount == 1 && $platzCount == 1) {
            Race::find($raceId)->update([
                'verspaetungUhrzeit'   => $request->rennUhrzeit,
                'rennzeit'             => $request->rennzeit,
                'status'               => 4,
                'bearbeiter_id'        => Auth::id(),
                'updated_at'           => Carbon::now(),
                'liveStream'           => false
            ]);

            $berechnung=$this->timeVerschiebung($raceId, $request->rennUhrzeit, $request->zeit, $request->zeitMinAbstand);
        }

        if ($changeCount == 1 && $platzCount == 0) {
            Race::find($raceId)->update([
                'verspaetungUhrzeit'   => $request->rennUhrzeit,
                'rennzeit'             => $request->rennzeit,
                'status'               => 2,
                'bearbeiter_id'        => Auth::id(),
                'updated_at'           => Carbon::now(),
            ]);

            $berechnung=$this->timeVerschiebung($raceId, $request->rennUhrzeit, $request->zeit, $request->zeitMinAbstand);
        }

        return redirect('/Rennen/Programm')->with([
                'success' => 'Die Platzierungen vom Rennen wurde eingeben.'
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function timeVerschiebung($race_id, $rennUhrzeit, $zeit, $zeitMinAbstand){
        $raceLevel = Race::find($race_id);

        $time1=explode(":" , $raceLevel->rennUhrzeit);
        $time2=explode(":" , $rennUhrzeit);

        $difftime=($time2[0]*60+$time2[1])-($time1[0]*60+$time1[1]);

        $hour=$difftime/60;
        $houradd=(int)$hour;
        $minuteadd=$difftime-$houradd*60;
        $hourVerspeatet  =$time1[0]+$houradd;
        $minuteVerspeatet=$time1[1]+$minuteadd;
        if($minuteVerspeatet>=60){
            ++$hourVerspeatet;
            $minuteVerspeatet=$minuteVerspeatet-60;
        }
        $timeVerspaetung=$hourVerspeatet.":".$minuteVerspeatet.":00";

        $raceTimes = Race::where('event_id', Session::get('regattaSelectId'))
            ->where('id', '!=', $race_id)
            ->where('rennUhrzeit', '>', $raceLevel->rennUhrzeit)
            ->where('rennDatum', $raceLevel->rennDatum)
            ->orderby('rennUhrzeit')
            ->get();

        $rennzeitStop=0;
        foreach ($raceTimes as $raceTime) {
            if($raceTime->rennzeit==1){
                $rennzeitStop=1;
            }
            if($rennzeitStop==0) {
                $time3 = explode(":", $raceTime->rennUhrzeit);
                $difftimeRace = ($time3[0] * 60 + $time3[1]) - ($time1[0] * 60 + $time1[1]);

                $time1[0] = $time3[0];
                $time1[1] = $time3[1];

                if ($difftimeRace > $zeitMinAbstand) {
                    $diffMin = $difftimeRace - $zeitMinAbstand;
                    $minuteadd = $minuteadd - $diffMin;
                }

                $hourVerspeatet   = $time3[0] + $houradd;
                $minuteVerspeatet = $time3[1] + $minuteadd;
                if ($minuteVerspeatet >= 60) {
                    ++$hourVerspeatet;
                    $minuteVerspeatet = $minuteVerspeatet - 60;
                }
                $timeVerspaetung = $hourVerspeatet . ":" . $minuteVerspeatet . ":00";

                if ($minuteadd > 0) {
                    Race::find($raceTime->id)->update([
                        'verspaetungUhrzeit' => $timeVerspaetung,
                        'bearbeiter_id' => Auth::id(),
                        'updated_at' => Carbon::now()
                    ]);
                    $minuteadd = $minuteadd - $zeit;
                    if ($minuteadd < 0) {
                        $minuteadd = 0;
                    }
                }
                else {
                    Race::find($raceTime->id)->update([
                        'verspaetungUhrzeit' => $raceTime->rennUhrzeit,
                        'bearbeiter_id'      => Auth::id(),
                        'updated_at'         => Carbon::now()
                    ]);
                }
            }
        }
    }

    public function newLane($race_id)
    {
        $race = Race::find($race_id);
        $lanesCount = Lane::where('rennen_id',$race_id)->count();

        $lane = Lane::onlyTrashed()->where('rennen_id', $race_id)->orderBy('bahn')->first();
        if (!$lane) {
            // Suche erneut ohne rennen_id
            $lane = Lane::onlyTrashed()->orderBy('bahn')->first();
        }
        if ($lane && $lane->trashed()) {
            $lane->regatta_id    = Session::get('regattaSelectId');
            $lane->rennen_id     = $race_id;
            $lane->tabele_id     = $race->tabele_id;
            $lane->bahn          = $lanesCount + 1;
            $lane->zeit          = '00:00:00';
            $lane->hundert       = 0;
            $lane->punkte        = 0;
            $lane->platz         = 0;
            $lane->tabelevor_id  = 0;
            $lane->platzvor      = 0;
            $lane->bearbeiter_id = Auth::id();
            $lane->autor_id      = Auth::id();
            $lane->updated_at    = Carbon::now();
            $lane->created_at    = Carbon::now();
            $lane->restore();
            $lane->save();
        }
        else {
            $lane = new Lane([
                'regatta_id' => Session::get('regattaSelectId'),
                'rennen_id' => $race_id,
                'tabele_id' => $race->tabele_id,
                'bahn' => $lanesCount + 1,
                'zeit' => '00:00:00',
                'hundert' => 0,
                'punkte' => 0,
                'platz' => 0,
                'tabelevor_id' => 0,
                'platzvor' => 0,
                'bearbeiter_id' => Auth::id(),
                'autor_id' => Auth::id(),
                'updated_at' => Carbon::now(),
                'created_at' => Carbon::now()
            ]);
            $lane->save();
        }

        $race->bahnen = $lanesCount + 1;
        $race->save();

        return redirect()->back()->with('race_id', $race_id);
    }

    public function deleteLast($race_id)
    {
        $lastLane = Lane::where('rennen_id', $race_id)->orderByDesc('bahn')->first();
        if ($lastLane && is_null($lastLane->mannschaft_id)) {
            $lastLane->delete();
            // Bahnen-Anzahl im Race anpassen
            $race = Race::find($race_id);
            $race->bahnen = Lane::where('rennen_id', $race_id)->count();
            $race->save();
        }
        return redirect()->back()->with('race_id', $race_id);
    }
}
