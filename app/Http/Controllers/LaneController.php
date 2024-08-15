<?php

namespace App\Http\Controllers;

use App\Models\Lane;
use App\Models\Race;
use App\Models\RegattaTeam;
use App\Models\Tabele;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Collection;
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
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $race = Race::find($id);

        $lanes = Lane::where('rennen_id',$id)->orderBy('bahn')->get();

        if ($race->status < 4) {
            $lanes = Lane::where('rennen_id',$id)->orderBy('bahn')->get();
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
                'highhour' => $highhour

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
     * @return \Illuminate\Http\Response
     */
    public function editDraw($id)
    {
        $race = Race::find($id);

        $lanes = Lane::where('rennen_id',$id)->get();

        if ($lanes->isEmpty()) {
            for ($i = 1; $i <= $race->bahnen; $i++) {
                if($race->mix == Null and $race->tabele_id) {
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
                else{
                    $lane = new Lane([
                        'regatta_id' => Session::get('regattaSelectId'),
                        'rennen_id' => $id,
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
            $teams = RegattaTeam::where('gruppe_id', $tabele->gruppe_id)->get();
        }
        else {
            $teams = RegattaTeam::where('regatta_id', Session::get('regattaSelectId'))->get();
        }

        return view('regattaManagement.lane.editDraw')->with(
            [
                'lanes'  => $lanes,
                'race'   => $race,
                'tabele' => $tabele,
                'teams'  => $teams
            ]
        );
    }

    public function editResult($id)
    {
        $race = Race::find($id);

        $lanes = Lane::where('rennen_id',$id)->get();

        $tabele = Tabele::find($race->tabele_id);

        if($race->status==2) {
            $ractetime1 = Carbon::now();
            $ractetime2 = $ractetime1->subMinute(3);
            $ractetime  = $ractetime2->toTimeString();
        }
        else{
            $ractetime=$race->verspaetungUhrzeit;
        }

        return view('regattaManagement.lane.editResult')->with(
            [
                'lanes'     => $lanes,
                'race'      => $race,
                'tabele'    => $tabele,
                'ractetime' => $ractetime
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $race_id)
    {
        $changeCount=0;
        foreach ($request->laneId as $index => $laneId) {
            $lane = Lane::find($laneId);
            if ($lane && $lane->mannschaft_id != $request->mannschaftId[$index]) {
                    $lane->mannschaft_id = $request->mannschaftId[$index];
                    $lane->bearbeiter_id = Auth::id();
                    $lane->updated_at = Carbon::now();
                    $lane->save();
                    $changeCount=1;
            }
        }

        if ($changeCount == 1) {
            $race = Race::find($race_id);
            $race->status = 2;
            $race->save();
        }

        return redirect('/Rennen/Programm')->with([
                'success' => 'Das Rennen wurde gesetzt.'
            ]
        );
    }

    public function updateResult(Request $request, $race_id)
    {
        $request->validate([
                'zeit'   => 'min:0|max:10'
            ]
        );

        $changeCount=0;
        $platzCount=0;
        foreach ($request->laneId as $index => $laneId) {
            $lane = Lane::find($laneId);
            if ($lane && $lane->platz != $request->platz[$index]) {
                $lane->platz = $request->platz[$index];
                $lane->bearbeiter_id = Auth::id();
                $lane->updated_at = Carbon::now();
                $lane->save();

                $changeCount=1;
                if($request->platz[$index]>0){
                    $platzCount=1;
                }
            }
        }

        Session::put('regattaZeit' , $request->zeit);
        Session::put('regattaZeitMinAbstand' , $request->zeitMinAbstand);

        if($request->rennzeit==Null){
            $request->rennzeit=0;
        }

        if ($changeCount == 1 && $platzCount==1) {
            Race::find($race_id)->update([
                //'ergebnisBeschreibung' => $request->ergebnisBeschreibung,
                'verspaetungUhrzeit'   => $request->rennUhrzeit,
                'rennzeit'             => $request->rennzeit,
                'status'               => 4,
                'bearbeiter_id'        => Auth::id(),
                'updated_at'           => Carbon::now()
            ]);

            $berechnung=$this->timeVerschiebung($race_id, $request->rennUhrzeit, $request->zeit, $request->zeitMinAbstand);
        }

        if ($changeCount == 1 && $platzCount==0) {
            Race::find($race_id)->update([
                //'ergebnisBeschreibung' => $request->ergebnisBeschreibung,
                'verspaetungUhrzeit'   => $request->rennUhrzeit,
                'rennzeit'             => $request->rennzeit,
                'status'               => 2,
                'bearbeiter_id'        => Auth::id(),
                'updated_at'           => Carbon::now()
            ]);

            $berechnung=$this->timeVerschiebung($race_id, $request->rennUhrzeit, $request->zeit, $request->zeitMinAbstand);
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
            //->where('level', $raceLevel->level)
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

                $hourVerspeatet = $time3[0] + $houradd;
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
                } else {
                    Race::find($raceTime->id)->update([
                        'verspaetungUhrzeit' => $raceTime->rennUhrzeit,
                        'bearbeiter_id' => Auth::id(),
                        'updated_at' => Carbon::now()
                    ]);
                }
            }
        }
    }
}
