<?php

namespace App\Http\Controllers;

use App\Models\Race;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class RaceController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function aktiv($raceId)
    {
        Race::find($raceId)->update([
            'visible'          => '1',
            'bearbeiter_id'    => Auth::id(),
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Rennen wurde sichtbar geschaltet.');
    }

    public function inaktiv($raceId)
    {
        Race::find($raceId)->update([
            'visible'          => '0',
            'bearbeiter_id'    => Auth::id(),
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Rennen wurde unsichtbar geschaltet.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index()
    {
        $races = Race::where([
            'event_id' => Session::get('regattaSelectId')
        ])
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->paginate(5);

        return view('regattaManagement.race.index')->with([
            'races'  => $races,
            'status' => 0
        ]);
    }

    public function indexProgram()
    {
        $races = Race::where([
            'event_id'      => Session::get('regattaSelectId'),
            'programmDatei' => Null,
        ])
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->paginate(5);

        return view('regattaManagement.race.index')->with([
            'races'  => $races,
            'status' => 1
        ]);
    }

    public function indexProgramAll()
    {
        $races = Race::where([
            ['event_id' , Session::get('regattaSelectId')],
            ['programmDatei' ,'!=' , Null]
        ])
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->paginate(5);

        return view('regattaManagement.race.index')->with([
            'races'  => $races,
            'status' => 1
        ]);
    }

    public function indexResult()
    {
        $races = Race::where([
            ['event_id' , Session::get('regattaSelectId')],
            ['ergebnisDatei' , Null]
        ])
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->paginate(5);

        return view('regattaManagement.race.index')->with([
            'races'  => $races,
            'status' => 2
        ]);
    }

    public function indexResultAll()
    {
        $races = Race::where([
            ['event_id' , Session::get('regattaSelectId')],
            ['ergebnisDatei' , '!=' , Null]
        ])
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->paginate(5);

        return view('regattaManagement.race.index')->with([
            'races'  => $races,
            'status' => 2
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $raceLevel = Race::where('event_id' , Session::get('regattaSelectId'))
            ->orderby('level' , 'desc')
            ->limit(1)
            ->first();

        if(isset($raceLevel->level)){
          $levelMax = $raceLevel->level;
        }
        else{
         $levelMax = 1;
        }

        return view('regattaManagement.race.create' , compact('levelMax'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request) {
        $request->validate([
                  'rennBezeichnung'  => 'required|max:50',
                  'rennDatum'        => 'required|date',
                  'rennUhrzeit'      => 'required|date_format:H:i',    //'date_format:H:i|after:time_start',
            ]
        );

        $race= new Race([
                'event_id'           => Session::get('regattaSelectId'),
                'nummer'             => $request->nummer,
                'rennBezeichnung'    => $request->rennBezeichnung,
                'rennDatum'          => $request->rennDatum,
                'rennUhrzeit'        => $request->rennUhrzeit,
                'verspaetungUhrzeit' => $request->rennUhrzeit,
                'level'              => $request->regattaLevel,
                'visible'            => "1",
                'bearbeiter_id'      => Auth::id(),
                'autor_id'           => Auth::id(),
                'updated_at'         => Carbon::now(),
                'created_at'         => Carbon::now()
            ]
        );
        $race->save();

        if(Session::has('regattaSelectRaceTime')) {
            $to = \Carbon\Carbon::createFromFormat('H:i', Session::get('regattaSelectRaceTime'));
            $from = \Carbon\Carbon::createFromFormat('H:i', $request->rennUhrzeit);
            $diff_in_minutes = $to->diffInMinutes($from);
            Session::put('regattaSelectRaceTimeDiff' , $diff_in_minutes);
            $Teile=explode(":",$request->rennUhrzeit);
            $stunde1=$Teile[0];
            $minute1=$Teile[1];
            if ($minute1+$diff_in_minutes<60){
                $minute1=$minute1+$diff_in_minutes;
            }
            else{
                $minute1=$minute1+$diff_in_minutes;
                $minuteSchleife=floor($minute1/60);
                for ($i = 0; $i < $minuteSchleife; $i++){
                    $minute1=$minute1-60;
                    $stunde1=$stunde1+1;
                }
            }

            if ($minute1==0)
            {
                $minute1="00";
            }

            if (strlen($minute1)==1)
            {
                $minute1="0".$minute1;
            }

            $timeNew="$stunde1:$minute1";
        }
        else{
            $timeNew=$request->rennUhrzeit;
        }

        Session::put('regattaSelectRaceDate'    , $request->rennDatum);
        Session::put('regattaSelectRaceTime'    , $request->rennUhrzeit);
        Session::put('regattaSelectRaceTimeNew' , $timeNew);

        return redirect('/Rennen/neu')->with([
                'success'         => 'Das Rennen <b>' . $request->rennBezeichnung . '</b> wurde angelegt.'
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Race  $race
     * @return \Illuminate\Http\Response
     */
    public function show(Race $race)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Race  $race
     * @return \Illuminate\Http\Response
     */
    public function edit($raceid)
    {
        $race = Race::find($raceid);

        $raceLevel = Race::where('event_id' , Session::get('regattaSelectId'))
            ->orderby('level' , 'desc')
            ->limit(1)
            ->first();

        return view('regattaManagement.race.edit')->with([
            'race'          => $race,
            'levelMax'      => $raceLevel->level,
        ]);
    }

    public function editProgram($race_id)
    {
        $race = Race::find($race_id);

        $seorch=$race->programmDatei;
        $raceDocuments = Race::where('event_id' , Session::get('regattaSelectId'))
            ->where('id' , '!=' , $race_id)
            ->where('level' , $race->level)
            ->where(function ($query) use ($seorch){
                $query->where('programmDatei' , NULL)
                      ->orwhere('programmDatei' , $seorch);
            })
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->get();

        return view('regattaManagement.race.editProgram')->with([
            'race'          => $race,
            'raceDocuments' => $raceDocuments
        ]);
    }

    public function editResult($race_id)
    {
        $race = Race::find($race_id);

        $seorch=$race->ergebnisDatei;
        $raceDocuments = Race::where('event_id' , Session::get('regattaSelectId'))
            ->where('id' , '!=' , $race_id)
            ->where('level' , $race->level)
            ->where(function ($query) use ($seorch){
                $query->where('ergebnisDatei' , NULL)
                    ->orwhere('ergebnisDatei' , $seorch);
            })
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->get();

        return view('regattaManagement.race.editResult')->with([
            'race'          => $race,
            'raceDocuments' => $raceDocuments
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Race  $race
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $race_id)
    {
        $request->validate([
                'rennBezeichnung'   => 'required|max:50',
                'rennDatum'         => 'required|date',
                'rennUhrzeit'       => 'required|date_format:H:i',    //'date_format:H:i|after:time_start',
            ]
        );

        Race::find($race_id)->update([
                'nummer'             => $request->nummer,
                'rennBezeichnung'    => $request->rennBezeichnung,
                'rennDatum'          => $request->rennDatum,
                'rennUhrzeit'        => $request->rennUhrzeit,
                'verspaetungUhrzeit' => $request->rennUhrzeit,
                'level'              => $request->regattaLevel,
                'bearbeiter_id'      => Auth::id(),
                'updated_at'         => Carbon::now()
            ]
        );

        return redirect('/Rennen/alle')->with([
                'success' => 'Die Daten vom Rennen <b>' . $request->rennBezeichnung . '</b> wurden geändert.'
            ]
        );
    }

    public function updateProgram(Request $request, $race_id)
    {
         Race::find($race_id)->update([
            'beschreibung'    => $request->beschreibung,
            'bearbeiter_id'   => Auth::id(),
            'updated_at'      => Carbon::now()
        ]);

        if($request->programmDatei){
            $extension = $request->programmDatei->extension();
            $newDocumentName = 'programm' . $race_id . '_' . str::random(4) . '.' . $extension;
            $fileProgrammDatei=$request->file('programmDatei')->getClientOriginalName();
            Storage::disk('public')->putFileAs(
                'raceDokumente/',
                $request->programmDatei,
                $newDocumentName
            );

            $oldDocumentFile = Race::find($race_id);
            if(isset($oldDocumentFile->programmDatei)){
                Storage::disk('public')->delete('raceDokumente/'.$oldDocumentFile->programmDatei);
            }

            Race::find($race_id)->update([
                'programmDatei'     => $newDocumentName,
                'fileProgrammDatei' => $fileProgrammDatei,
                'bearbeiter_id'     => Auth::id(),
                'updated_at'        => Carbon::now()
            ]);

            $raceDocIds=$request->raceDocId;
            $i=0;
            foreach ($raceDocIds as $raceDocId){
                ++$i;
                Race::find($raceDocIds[$i])->update([
                    'programmDatei'     => $newDocumentName,
                    'fileProgrammDatei' => $fileProgrammDatei,
                    'bearbeiter_id'     => Auth::id(),
                    'updated_at'        => Carbon::now()
                ]);
            }
        }

        return redirect('/Rennen/Programm')->with(
            [
                'success'  => 'Die Daten des Rennens wurden gespeichert.'
            ]
        );
    }

    public function updateResult(Request $request, $race_id)
    {
        Race::find($race_id)->update([
            'ergebnisBeschreibung' => $request->ergebnisBeschreibung,
            'bearbeiter_id'        => Auth::id(),
            'updated_at'           => Carbon::now()
        ]);

        if($request->ergebnisDatei){
            $extension = $request->ergebnisDatei->extension();
            $newDocumentName = 'ergebnis' . $race_id . '_' . str::random(4) . '.' . $extension;
            $fileErgebnisDatei=$request->file('ergebnisDatei')->getClientOriginalName();
            Storage::disk('public')->putFileAs(
                'raceDokumente/',
                $request->ergebnisDatei,
                $newDocumentName
            );

            $oldDocumentFile = Race::find($race_id);
            if(isset($oldDocumentFile->ergebnisDatei)){
                Storage::disk('public')->delete('raceDokumente/'.$oldDocumentFile->ergebnisDatei);
            }

            Race::find($race_id)->update([
                'ergebnisDatei'     => $newDocumentName,
                'fileErgebnisDatei' => $fileErgebnisDatei,
                'bearbeiter_id'     => Auth::id(),
                'updated_at'        => Carbon::now()
            ]);

            $raceDocIds=$request->raceDocId;
            $i=0;
            foreach ($raceDocIds as $raceDocId){
                ++$i;
                Race::find($raceDocIds[$i])->update([
                    'ergebnisDatei'     => $newDocumentName,
                    'fileErgebnisDatei' => $fileErgebnisDatei,
                    'bearbeiter_id'     => Auth::id(),
                    'updated_at'        => Carbon::now()
                ]);
            }
        }

        return redirect('/Rennen/Ergebnisse')->with(
            [
                'success'  => 'Die Daten des Rennens wurden gespeichert.'
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Race  $race
     * @return \Illuminate\Http\Response
     */
    public function destroy(Race $race)
    {
        //
    }

    public function deleteProgram($race_Id)
    {
        $deleteDocumentFile = Race::find($race_Id);

        Race::find($race_Id)->update(
            [
                'programmDatei'     => Null,
                'fileProgrammDatei' => Null,
                'bearbeiter_id'     => Auth::id(),
                'updated_at'        => Carbon::now()
            ]);

        DB::table('races')
            ->where('programmDatei' , $deleteDocumentFile->programmDatei)
            ->update([
                'programmDatei'     => Null,
                'fileProgrammDatei' => Null,
                'bearbeiter_id'     => Auth::id(),
                'updated_at'        => Carbon::now()
            ]);

        if(isset($deleteDocumentFile->programmDatei)){
            Storage::disk('public')->delete('raceDokumente/'.$deleteDocumentFile->programmDatei);
        }

        $document = Race::find($race_Id);
        return redirect('/Rennen/Programm')->with(
            [
                'success'  => 'Das Programm <b>' . $document->programmDatei . '</b> wurde gelöscht.'
            ]
        );
    }

    public function deleteResult($race_Id)
    {
        $deleteDocumentFile = Race::find($race_Id);

        Race::find($race_Id)->update(
            [
                'ergebnisDatei'     => Null,
                'fileErgebnisDatei' => Null,
                'bearbeiter_id'     => Auth::id(),
                'updated_at'        => Carbon::now()
            ]);

        DB::table('races')
            ->where('ergebnisDatei' , $deleteDocumentFile->ergebnisDatei)
            ->update([
                'ergebnisDatei'     => Null,
                'fileErgebnisDatei' => Null,
                'bearbeiter_id'     => Auth::id(),
                'updated_at'        => Carbon::now()
            ]);

        if(isset($deleteDocumentFile->ergebnisDatei)){
            Storage::disk('public')->delete('raceDokumente/'.$deleteDocumentFile->ergebnisDatei);
        }

        $document = Race::find($race_Id);
        return redirect('Rennen/Ergebnis/'.$race_Id)->with(
            [
              'success'  => 'Das Ergebnisdokument  <b>' . $document->ergebnisDatei . '</b> wurde gelöscht.'
            ]
        );
    }

}
