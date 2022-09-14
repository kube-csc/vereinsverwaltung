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
                'tabelleBezeichnung'       => 'required|max:50',
                'tabelleDatum'             => 'required|date',
                'veroeffentlichungUhrzeit' => 'required|date_format:H:i',
            ]
        );

        $tabele= new Tabele([
                'event_id'                 => Session::get('regattaSelectId'),
                'ueberschrift'             => $request->tabelleBezeichnung,
                'tabelleLevelVon'          => $request->tabelleLevelVon,
                'tabelleLevelBis'          => $request->tabelleLevelBis,
                'tabelleDatumVon'          => $request->tabelleDatum,
                'veroeffentlichungUhrzeit' => $request->veroeffentlichungUhrzeit,
                'tabelleVisible'           => "1",
                'finaleAnzeigen'           => Carbon::now(),
                'bearbeiter_id'            => Auth::id(),
                'autor_id'                 => Auth::id(),
                'updated_at'               => Carbon::now(),
                'created_at'               => Carbon::now()
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

    public function editResult($tabele_id)
    {
        $tabele = Tabele::find($tabele_id);

        return view('regattaManagement.tabele.editResult', compact('tabele'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tabele  $tabele
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tabele_id)
    {
        $request->validate([
                'tabelleBezeichnung'       => 'required|max:50',
                'tabelleDatum'             => 'required|date',
                //'tabelleLevelVon'        => 'tabelleLevelVon<=tabelleLevelBis', ToDo:: Valedierung vebessern
                'veroeffentlichungUhrzeit' => 'required|date_format:H:i'
            ]
        );

        if($request->tabelleLevelVon > $request->tabelleLevelBis){
            $request->tabelleLevelVon=$request->tabelleLevelBis;
        }

         Tabele::find($tabele_id)->update([
                'ueberschrift'             => $request->tabelleBezeichnung,
                'tabelleDatumVon'          => $request->tabelleDatum,
                'tabelleLevelVon'          => $request->tabelleLevelVon,
                'tabelleLevelBis'          => $request->tabelleLevelBis,
                'veroeffentlichungUhrzeit' => $request->veroeffentlichungUhrzeit,
                'bearbeiter_id'            => Auth::id(),
                'updated_at'               => Carbon::now()
            ]
        );

        return redirect('/Tabelle/alle')->with([
                'success' => 'Die Daten vom der Tabelle <b>' . $request->tabelleBezeichnung . '</b> wurden geändert.'
            ]
        );
    }

    public function updateResult(Request $request, $tabele_id)
    {
        if($request->tabelleBeschreibung==""){
            $request->tabelleBeschreibung=Null;
        }

        Tabele::find($tabele_id)->update([
            'beschreibung'    => $request->tabelleBeschreibung,
            'bearbeiter_id'   => Auth::id(),
            'updated_at'      => Carbon::now()
        ]);

        if($request->tabeleDatei){
            $extension = $request->tabeleDatei->extension();
            $newDocumentName = 'tabelle' . $tabele_id . '_' . str::random(4) . '.' . $extension;
            $fileTabeleDatei=$request->file('tabeleDatei')->getClientOriginalName();
            Storage::disk('public')->putFileAs(
                'tabeleDokumente/',
                $request->tabeleDatei,
                $newDocumentName
            );

            $oldDocumentFile = Tabele::find($tabele_id);
            if(isset($oldDocumentFile->tabelleDatei)){
                Storage::disk('public')->delete('tabeleDokumente/'.$oldDocumentFile->tabelleDatei);
            }

            Tabele::find($tabele_id)->update([
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

    public function deleteResult($tabele_id)
    {
        $deleteDocumentFile = Tabele::find($tabele_id);

        Tabele::find($tabele_id)->update(
            [
                'tabelleDatei'     => Null,
                'fileTabelleDatei' => Null,
                'bearbeiter_id'    => Auth::id(),
                'updated_at'       => Carbon::now()
            ]);

        if(isset($deleteDocumentFile->tabelleDatei)){
            Storage::disk('public')->delete('tabeleDokumente/'.$deleteDocumentFile->tabelleDatei);
        }

        $document = Tabele::find($tabele_id);
        return redirect('Tabelle/Ergebnis/'.$tabele_id)->with(
            [
                'success'  => 'Das Tabellendokument <b>' . $document->tabelleDatei . '</b> wurde gelöscht.'
            ]
        );
    }
}
