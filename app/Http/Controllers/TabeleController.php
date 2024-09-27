<?php

namespace App\Http\Controllers;

use App\Models\Race;
use App\Models\RaceType;
use App\Models\Tabele;
use App\Models\Tabledata;
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

    public function aktiv($tabelleid)
    {
        Tabele::find($tabelleid)->update([
            'tabelleVisible'   => '1',
            'bearbeiter_id'    => Auth::id(),
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Die Tabelle wurde sichtbar geschaltet.');
    }

    public function inaktiv($tabelleid)
    {
        Tabele::find($tabelleid)->update([
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
            ->limit(1)
            ->first();

        if(isset($tabeleLevel->tabelleLevelBis)){
            $levelMaxVon = $tabeleLevel->tabelleLevelBis;
            $levelMaxBis = $tabeleLevel->tabelleLevelBis;
        }
        else{
            $levelMaxVon = 1;
            $levelMaxBis = 1;
        }

        $raceLevel = Race::where('event_id' , Session::get('regattaSelectId'))
            ->orderby('level' , 'desc')
            ->limit(1)
            ->first();

        if(isset($raceLevel->level)){
            if($raceLevel->level>$levelMaxVon){
                $levelMaxVon = $raceLevel->level;
                $levelMaxBis = $raceLevel->level;
            }
        }

        $raceTypes  = RaceType::where('regatta_id' , Session::get('regattaSelectId'))
            ->orderby('typ')
            ->get();

        return view('regattaManagement.tabele.create')->with([
            'levelMaxVon'  => $levelMaxVon,
            'levelMaxBis'  => $levelMaxBis,
            'raceTypes'    => $raceTypes
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

        if($request->finaleTable==Null){
            $request->finaleTable=0;
        }

        $tabele= new Tabele([
                'event_id'                 => Session::get('regattaSelectId'),
                'gruppe_id'                => $request->tabelleGruppe,
                'ueberschrift'             => $request->tabelleBezeichnung,
                'tabelleLevelVon'          => $request->tabelleLevelVon,
                'tabelleLevelBis'          => $request->tabelleLevelBis,
                'tabelleDatumVon'          => $request->tabelleDatum,
                'finaleAnzeigen'           => $request->veroeffentlichungUhrzeit,
                'wertungsart'              => $request->wertungsart,
                'tabelleVisible'           => "1",
                'finale'                   => $request->finaleTable,
                'buchholzwertungaktiv'     => $request->buchholzwertungaktiv,
                'bearbeiter_id'            => Auth::id(),
                'autor_id'                 => Auth::id(),
                'updated_at'               => Carbon::now(),
                'created_at'               => Carbon::now()
            ]);

        $tabele->save();

        Session::put('tablePublished'    , $request->veroeffentlichungUhrzeit);
        Session::put('tableLevelVon'     , $request->tabelleLevelVon);
        Session::put('tableLevelBis'     , $request->tabelleLevelBis);
        Session::put('tableLevelSaveVon' , $request->tabelleLevelVon);
        Session::put('tableLevelSaveBis' , $request->tabelleLevelBis);

        return redirect('/Tabelle/neu')->with([
                'success' => 'Die Tabelle <b>' . $request->tabelleBezeichnung . '</b> wurde angelegt.'
            ]
        );
}

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Tabele  $tabele
     * @return \Illuminate\Http\Response
     */
    public function show($tabeleid)
    {
        $tabele = Tabele::find($tabeleid);

        $tabeledatas = Tabledata::where('tabele_id', $tabeleid)
            ->orderby('punkte', 'desc')
            ->orderBy('buchholzzahl')
            ->orderBy('zeitpunktegleich')
            ->orderBy('hundertpunktegleich')
            ->get();

        return view('regattaManagement.tabele.show')->with([
            'tabele'      => $tabele,
            'tabeledatas' => $tabeledatas
        ]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Tabele  $tabele
     * @return \Illuminate\Http\Response
     */
    public function edit($tabelleid)
    {
        $tabele = Tabele::find($tabelleid);

        $tabeleLevel = Tabele::where('event_id' , Session::get('regattaSelectId'))
            ->orderby('tabelleLevelBis' , 'desc')
            ->orderby('tabelleLevelVon' , 'desc')
            ->limit(1)
            ->first();

        $raceTypes  = RaceType::where('regatta_id' , Session::get('regattaSelectId'))
            ->orderby('typ')
            ->get();

        return view('regattaManagement.tabele.edit')->with([
            'tabele'            => $tabele,
            'raceTypes'         => $raceTypes,
            'levelMaxVon'       => $tabeleLevel->tabelleLevelVon,
            'levelMaxBis'       => $tabeleLevel->tabelleLevelBis
        ]);
    }

    public function editResult($tabelleid)
    {
        $tabele = Tabele::find($tabelleid);

        return view('regattaManagement.tabele.editResult', compact('tabele'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Tabele  $tabele
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $tabelleid)
    {
         $request->validate([
                'tabelleBezeichnung'       => 'required|max:50',
                'tabelleDatum'             => 'required|date',
                //'tabelleLevelVon'        => 'tabelleLevelVon<=tabelleLevelBis', //ToDo:: Valedierung vebessern
                'veroeffentlichungUhrzeit' => 'required|date_format:H:i',
                'tabelleGruppe'            => 'required|integer|not_in:0'
            ]
        );

        if($request->tabelleLevelVon > $request->tabelleLevelBis){
            $request->tabelleLevelVon=$request->tabelleLevelBis;
        }

        if($request->finaleTable == Null){
            $request->finaleTable=0;
        }

        if($request->buchholzwertungaktiv == Null || $request->wertungsart == 3){
            $request->buchholzwertungaktiv=0;
        }

        Tabele::find($tabelleid)->update([
                'ueberschrift'             => $request->tabelleBezeichnung,
                'gruppe_id'                => $request->tabelleGruppe,
                'tabelleDatumVon'          => $request->tabelleDatum,
                'tabelleLevelVon'          => $request->tabelleLevelVon,
                'tabelleLevelBis'          => $request->tabelleLevelBis,
                'wertungsart'              => $request->wertungsart,
                'finale'                   => $request->finaleTable,
                'buchholzwertungaktiv'     => $request->buchholzwertungaktiv,
                'finaleAnzeigen'           => $request->veroeffentlichungUhrzeit,
                'bearbeiter_id'            => Auth::id(),
                'updated_at'               => Carbon::now()
            ]
        );

        return redirect('/Tabelle/alle')->with([
                'success' => 'Die Daten vom der Tabelle <b>' . $request->tabelleBezeichnung . '</b> wurden geändert.'
            ]
        );
    }

    public function updateResult(Request $request, $tabelleid)
    {
        if($request->tabelleBeschreibung==""){
            $request->tabelleBeschreibung=Null;
        }

        Tabele::find($tabelleid)->update([
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

            $oldDocumentFile = Tabele::find($tabelleid);
            if(isset($oldDocumentFile->tabelleDatei)){
                Storage::disk('public')->delete('tabeleDokumente/'.$oldDocumentFile->tabelleDatei);
            }

            Tabele::find($tabelleid)->update([
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

    public function deleteResult($tabelleid)
    {
        $deleteDocumentFile = Tabele::find($tabelleid);

        Tabele::find($tabelleid)->update(
            [
                'tabelleDatei'     => Null,
                'fileTabelleDatei' => Null,
                'bearbeiter_id'    => Auth::id(),
                'updated_at'       => Carbon::now()
            ]);

        if(isset($deleteDocumentFile->tabelleDatei)){
            Storage::disk('public')->delete('tabeleDokumente/'.$deleteDocumentFile->tabelleDatei);
        }

        $document = Tabele::find($tabelleid);
        return redirect('Tabelle/Ergebnis/'.$tabelleid)->with(
            [
                'success'  => 'Das Tabellendokument <b>' . $document->tabelleDatei . '</b> wurde gelöscht.'
            ]
        );
    }
}
