<?php

namespace App\Http\Controllers;

use App\Models\Race;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class RaceController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        $races = Race::where([
            'event_id' => Session::get('regattaSelectId'),
        ])
            ->orderby('datumvon')
            ->orderby('uhrzeit')
            ->paginate(5);

        return view('regattaManagement.race.index')->with([
            'races'  => $races,
            'status' => 0
        ]);
    }

    public function indexProgram()
    {
        $races = Race::where([
            ['event_id' , Session::get('regattaSelectId')],
            ['programmDatei' , Null],
        ])
            ->orderby('datumvon')
            ->orderby('uhrzeit')
            ->paginate(5);

        return view('regattaManagement.race.index')->with([
            'races'  => $races,
            'status' => 1
        ]);
    }

    public function indexProgramAll()
    {
        $races = Race::where([
            ['event_id' , Session::get('regattaSelectId')]
        ])
            ->orderby('datumvon')
            ->orderby('uhrzeit')
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
            ['ergebnisDatei' , Null],
        ])
            ->orderby('datumvon')
            ->orderby('uhrzeit')
            ->paginate(5);

        return view('regattaManagement.race.index')->with([
            'races'  => $races,
            'status' => 2
        ]);
    }

    public function indexResultAll()
    {
        $races = Race::where([
            ['event_id' , Session::get('regattaSelectId')]
        ])
            ->orderby('datumvon')
            ->orderby('uhrzeit')
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
        return view('regattaManagement.race.create');
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
                  'datumvon'         => 'required|date',
                  'uhrzeit'          => 'required|date_format:H:i',    //'date_format:H:i|after:time_start',
            ]
        );

        $race= new Race([
                'event_id'         => Session::get('regattaSelectId'),
                'nummer'           => $request->nummer,
                'rennBezeichnung'  => $request->rennBezeichnung,
                'datumvon'         => $request->datumvon,
                'uhrzeit'          => $request->uhrzeit,
                'bearbeiter_id'    => Auth::user()->id,
                'autor_id'         => Auth::user()->id,
                'updated_at'       => Carbon::now(),
                'created_at'       => Carbon::now()
            ]
        );
        $race->save();

        if(Session::has('regattaSelectRaceTime')) {
            $to = \Carbon\Carbon::createFromFormat('H:i', Session::get('regattaSelectRaceTime'));
            $from = \Carbon\Carbon::createFromFormat('H:i', $request->uhrzeit);
            $diff_in_minutes = $to->diffInMinutes($from);
            Session::put('regattaSelectRaceTimeDiff' , $diff_in_minutes);
            $Teile=explode(":",$request->uhrzeit);
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
            $timeNew=$request->uhrzeit;
        }

        Session::put('regattaSelectRaceDate'    , $request->datumvon);
        Session::put('regattaSelectRaceTime'    , $request->uhrzeit);
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

        return view('regattaManagement.race.edit' , compact('race'));
    }

    public function editProgram($race_id)
    {
        $race = Race::find($race_id);

        return view('regattaManagement.race.editProgram' , compact('race'));
    }

    public function editResult($race_id)
    {
        $race = Race::find($race_id);

        return view('regattaManagement.race.editResult' , compact('race'));
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
                'rennBezeichnung'  => 'required|max:50',
                'datumvon'         => 'required|date',
                'uhrzeit'          => 'required|date_format:H:i',    //'date_format:H:i|after:time_start',
            ]
        );

        Race::find($race_id)->update([
                'nummer'           => $request->nummer,
                'rennBezeichnung'  => $request->rennBezeichnung,
                'datumvon'         => $request->datumvon,
                'uhrzeit'          => $request->uhrzeit,
                'bearbeiter_id'    => Auth::user()->id,
                'updated_at'       => Carbon::now()
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
            'updated_at'      => Carbon::now()
        ]);

        if($request->programmDatei){
            $extension = $request->programmDatei->extension();
            $newDocumentName = 'programm' . $race_id . '_' . str::random(4) . '.' . $extension;
            Storage::disk('public')->putFileAs(
                'raceDokumente/',
                $request->programmDatei,
                $newDocumentName
            );

            $oldDocumentFile = Race::find($race_id);
            if(isset($oldDocumentFile->programmDatei)){
                Storage::disk('public')->delete('raceDokument/'.$oldDocumentFile->programmDatei);
            }

            Race::find($race_id)->update([
                'programmDatei'   => $newDocumentName,
            ]);
        }

        return redirect('/Rennen/Programm')->with(
            [
                'success'  => 'Die Daten von den Rennen wurden gespeichert.'
            ]
        );
    }

    public function updateResult(Request $request, $race_id)
    {
        Race::find($race_id)->update([
            'ergebnisBeschreibung' => $request->ergebnisBeschreibung,
            'updated_at'           => Carbon::now()
        ]);

        if($request->ergebnisDatei){
            $extension = $request->ergebnisDatei->extension();
            $newDocumentName = 'ergebnis' . $race_id . '_' . str::random(4) . '.' . $extension;
            Storage::disk('public')->putFileAs(
                'raceDokumente/',
                $request->ergebnisDatei,
                $newDocumentName
            );

            $oldDocumentFile = Race::find($race_id);
            if(isset($oldDocumentFile->ergebnisDatei)){
                Storage::disk('public')->delete('raceDokument/'.$oldDocumentFile->ergebnisDatei);
            }

            Race::find($race_id)->update([
                'ergebnisDatei'   => $newDocumentName,
            ]);
        }

        return redirect('/Rennen/Ergebnisse')->with(
            [
                'success'  => 'Die Daten von den Rennen wurden gespeichert.'
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
        if(isset($deleteDocumentFile->programmDatei)){
            Storage::disk('public')->delete('raceDokumente/'.$deleteDocumentFile->programmDatei);
        }
        Race::find($race_Id)->update(
            [
                'programmDatei' => Null,
                'updated_at'    => Carbon::now()
            ]);

        $document = Race::find($race_Id);
        return redirect('/Rennen/Programm')->with(
            [
                'success'  => 'Das Programmdokument <b>' . $document->programmDatei . '</b> wurden gelöscht.'
            ]
        );
    }

    public function deleteResult($race_Id)
    {
        $deleteDocumentFile = Race::find($race_Id);
        if(isset($deleteDocumentFile->ergebnisDatei)){
            Storage::disk('public')->delete('raceDokumente/'.$deleteDocumentFile->ergebnisDatei);
        }
        Race::find($race_Id)->update(
            [
                'ergebnisDatei' => Null,
                'updated_at'    => Carbon::now()
            ]);

        $document = Race::find($race_Id);
        return redirect('/Rennen/Ergebnisse')->with(
            [
                'success'  => 'Das Ergebnisdokument  <b>' . $document->ergebnisDatei . '</b> wurden gelöscht.'
            ]
        );
    }

}
