<?php

namespace App\Http\Controllers;

use App\Models\Tabele;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class TabeleController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function aktiv($raceId)
    {
        Tabele::find($raceId)->update([
            'tabelleVisible'   => '1',
            'bearbeiter_id'    => Auth::id(),
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Die Tabelle wurde sichtbar geschaltet.');
    }

    public function inaktiv($raceId)
    {
        Tabele::find($raceId)->update([
            'tabelleVisible'   => '0',
            'bearbeiter_id'    => Auth::id(),
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Die Tabelle wurde unsichtbar geschaltet.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $tabeles = Tabele::where([
            'event_id' => Session::get('regattaSelectId')
        ])
            ->orderby('tabelleLevelVon')
            ->orderby('tabelleLevelBis')
            ->orderby('ueberschrift')
            ->paginate(5);

        return view('regattaManagement.tabele.index')->with([
            'tabeles'  => $tabeles,
            'status'   => 0
        ]);
    }

    public function indexTabeleResoult()
    {
        $tabeles = Tabele::where([
            ['event_id' , Session::get('regattaSelectId')],
            ['tabelleDatei' , Null]
        ])
            ->orderby('tabelleLevelVon')
            ->orderby('tabelleLevelBis')
            ->orderby('ueberschrift')
            ->paginate(5);

        return view('regattaManagement.tabele.index')->with([
            'tabeles'  => $tabeles,
            'status' => 2
        ]);
    }

    public function indexTabeleResoultAll()
    {
        $tabeles = Tabele::where([
            ['event_id' , Session::get('regattaSelectId')],
            ['tabelleDatei' , '!=' ,  Null]
        ])
            ->orderby('tabelleLevelVon')
            ->orderby('tabelleLevelBis')
            ->orderby('ueberschrift')
            ->paginate(5);

        return view('regattaManagement.tabele.index')->with([
            'tabeles'  => $tabeles,
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
        $tabeleLevel = Tabele::where('event_id' , Session::get('regattaSelectId'))
            ->orderby('tabelleLevelBis' , 'desc')
            ->orderby('tabelleLevelVon' , 'desc')
            ->limit(1)
            ->first();

        if(isset($tabeleLevel->tabelleLevelBis)){
            $levelMaxVon = $tabeleLevel->tabelleLevelVon;
            $levelMaxBis = $tabeleLevel->tabelleLevelBis;
        }
        else{
            $levelMaxVon = 1;
            $levelMaxBis = 1;
        }

        return view('regattaManagement.tabele.create')->with([
            'levelMaxVon'  => $levelMaxVon,
            'levelMaxBis'  => $levelMaxBis
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request){
        $request->validate([
                  'tabelleBezeichnung'  => 'required|max:50',
                  'tabelleDatum'        => 'required|date',
            ]
        );

        $tabele= new Tabele([
                'event_id'            => Session::get('regattaSelectId'),
                'ueberschrift'        => $request->tabelleBezeichnung,
                'tabelleLevelVon'     => $request->tabelleLevelVon,
                'tabelleLevelBis'     => $request->tabelleLevelBis,
                'tabelleDatumVon'     => $request->tabelleDatum,
                'tabelleVisible'      => "1",
                'finaleAnzeigen'      => Carbon::now(),
                'bearbeiter_id'       => Auth::id(),
                'autor_id'            => Auth::id(),
                'updated_at'          => Carbon::now(),
                'created_at'          => Carbon::now()
            ]);

        $tabele->save();

        return redirect('/Tabelle/neu')->with([
                'success' => 'Die Tabelle <b>' . $request->tabelenBezeichnung . '</b> wurde angelegt.'
            ]
        );
}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tabele  $tabele
     * @return \Illuminate\Http\Response
     */
    public function show(Tabele $tabele)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tabele  $tabele
     * @return \Illuminate\Http\Response
     */
    public function edit($raceid)
    {
        $tabele = Tabele::find($raceid);

        $tabeleLevel = Tabele::where('event_id' , Session::get('regattaSelectId'))
            ->orderby('tabelleLevelBis' , 'desc')
            ->orderby('tabelleLevelVon' , 'desc')
            ->limit(1)
            ->first();

        return view('regattaManagement.tabele.edit')->with([
            'tabele'      => $tabele,
            'levelMaxVon'  => $tabeleLevel->tabelleLevelVon,
            'levelMaxBis'  => $tabeleLevel->tabelleLevelBis
        ]);
    }

    public function editResult($race_id)
    {
        $tabele = Tabele::find($race_id);

        return view('regattaManagement.tabele.editResult', compact('tabele'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tabele  $tabele
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $race_id)
    {
        $request->validate([
                'tabelleBezeichnung'   => 'required|max:50',
                'tabelleDatum'         => 'required|date',
                //'tabelleLevelVon'      => 'tabelleLevelVon<=tabelleLevelBis', ToDo:: Valedierung vebessern
            ]
        );

        if($request->tabelleLevelVon > $request->tabelleLevelBis){
            $request->tabelleLevelVon=$request->tabelleLevelBis;
        }

         Tabele::find($race_id)->update([
                'ueberschrift'       => $request->tabelleBezeichnung,
                'tabelleDatumVon'    => $request->tabelleDatum,
                'tabelleLevelVon'    => $request->tabelleLevelVon,
                'tabelleLevelBis'    => $request->tabelleLevelBis,
                'bearbeiter_id'      => Auth::id(),
                'updated_at'         => Carbon::now()
            ]
        );

        return redirect('/Tabelle/alle')->with([
                'success' => 'Die Daten vom der Tabelle <b>' . $request->tabelleBezeichnung . '</b> wurden geändert.'
            ]
        );
    }

    public function updateResult(Request $request, $race_id)
    {
        if($request->tabelleBeschreibung==""){
            $request->tabelleBeschreibung=Null;
        }

        Tabele::find($race_id)->update([
            'beschreibung'    => $request->tabelleBeschreibung,
            'bearbeiter_id'   => Auth::id(),
            'updated_at'      => Carbon::now()
        ]);

        if($request->tabeleDatei){
            $extension = $request->tabeleDatei->extension();
            $newDocumentName = 'programm' . $race_id . '_' . str::random(4) . '.' . $extension;
            $fileTabeleDatei=$request->file('tabeleDatei')->getClientOriginalName();
            Storage::disk('public')->putFileAs(
                'tabeleDokumente/',
                $request->tabeleDatei,
                $newDocumentName
            );

            $oldDocumentFile = Tabele::find($race_id);
            if(isset($oldDocumentFile->tabeleDatei)){
                Storage::disk('public')->delete('tabeleDokumente/'.$oldDocumentFile->tabeleDatei);
            }

            Tabele::find($race_id)->update([
                'tabelleDatei'     => $newDocumentName,
                'fileTabelleDatei' => $fileTabeleDatei,
            ]);
        }

        return redirect('/Tabelle/Ergebnisse')->with(
            [
                'success'  => 'Die Daten der Tabelle wurden gespeichert.'
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Tabele  $tabele
     * @return \Illuminate\Http\Response
     */
    public function destroy(Tabele $tabele)
    {
        //
    }

    public function deleteResult($race_Id)
    {
        $deleteDocumentFile = Tabele::find($race_Id);

        Tabele::find($race_Id)->update(
            [
                'tabelleDatei'     => Null,
                'fileTabelleDatei' => Null,
                'bearbeiter_id'    => Auth::id(),
                'updated_at'       => Carbon::now()
            ]);

        if(isset($deleteDocumentFile->tabelleDatei)){
            Storage::disk('public')->delete('tabeleDokumente/'.$deleteDocumentFile->tabelleDatei);
        }

        $document = Tabele::find($race_Id);
        return redirect('Tabelle/Ergebnis/'.$race_Id)->with(
            [
                'success'  => 'Das Tabellendokument <b>' . $document->tabelleDatei . '</b> wurde gelöscht.'
            ]
        );
    }
}
