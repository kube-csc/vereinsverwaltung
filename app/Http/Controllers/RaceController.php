<?php

namespace App\Http\Controllers;

use App\Models\Race;
use App\Models\Tabele;
use App\Models\RaceType;
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
            'visible'               => '1',
            'bearbeiter_id'    => Auth::id(),
            'updated_at'       => Carbon::now()
        ]);

        return Redirect()->back()->with('success' , 'Das Rennen wurde sichtbar geschaltet.');
    }

    public function inaktiv($raceId)
    {
        Race::find($raceId)->update([
            'visible'               => '0',
            'bearbeiter_id'    => Auth::id(),
            'updated_at'       => Carbon::now()
        ]);

        return Redirect()->back()->with('success' , 'Das Rennen wurde unsichtbar geschaltet.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */

    public function index()
    {
        $races = Race::where([
            'races.event_id' => Session::get('regattaSelectId')
        ])
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->paginate(10);

        return view('regattaManagement.race.index')->with([
            'titel'  => 'Rennen bearbeiten',
            'races'  => $races,
            'funktionStatus' => 0
        ]);
    }

    public function indexProgram()
    {
        $races = Race::where('event_id', Session::get('regattaSelectId'))
            ->where('visible', 1)
            ->where(function($query) {
                $query->where('status', '<', 2)
                    //->orWhere('programmDatei', null);
                    ->where('programmDatei', null);
            })
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->paginate(10);

        return view('regattaManagement.race.index')->with([
            'titel'  => 'Rennen die kein Programm haben bearbeiten',
            'races'  => $races,
            'funktionStatus' => 1 // Programm
        ]);
    }

    public function indexProgramAll()
    {
        $races = Race::where('event_id',Session::get('regattaSelectId'))
            ->where(function($query) {
                $query->where('programmDatei', '!=', null)
                    ->orWhere(function($subQuery) {
                        $subQuery->where('status', '>', 1)
                            ->where('status', '<', 3);
                    });
            })
            ->where('visible' , 1)
            ->orderby('rennDatum' , 'desc')
            ->orderby('rennUhrzeit' , 'desc')
            ->paginate(10);

        return view('regattaManagement.race.index')->with([
            'titel'                  => 'Programm der Rennen bearbeiten',
            'races'                => $races,
            'funktionStatus' => 1 // Programm
        ]);
    }

    public function indexResult()
    {
        $races = Race::where('event_id',Session::get('regattaSelectId'))
            ->where(function($query) {
                $query->whereNull('ergebnisDatei')
                    ->where('status', 2);
            })
            ->where('visible' , 1)
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->paginate(10);

        return view('regattaManagement.race.index')->with([
            'titel'  => 'Rennen die kein Ergebnisse haben bearbeiten',
            'races'  => $races,
            'funktionStatus' => 2 // Ergebnis
        ]);
    }

    public function indexResultAll()
    {
        $races = Race::where('event_id',Session::get('regattaSelectId'))
            ->where(function($query) {
                $query->where('programmDatei', '!=', null)
                    ->orWhere('status', 4);
            })
            ->where('visible' , 1)
            ->orderby('rennDatum' , 'desc')
            ->orderby('rennUhrzeit' , 'desc')
            ->paginate(10);

        return view('regattaManagement.race.index')->with([
            'titel'                  => 'Ergebnissse der Rennen bearbeiten',
            'races'               => $races,
            'funktionStatus' => 2 // Ergebnis
        ]);
    }

    public function indexResultControll()
    {
        $races = Race::where('event_id',Session::get('regattaSelectId'))
            ->where(function($query) {
                $query->whereNull('ergebnisDatei')
                    ->where('status', 3);
            })
            ->where('visible' , 1)
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->paginate(10);

        return view('regattaManagement.race.index')->with([
            'titel'  => 'Rennen die ein Ergebnisse haben kontrollieren',
            'races'  => $races,
            'funktionStatus' => 2 // Ergebnis
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

        $tabeleLevel = Tabele::where('event_id' , Session::get('regattaSelectId'))
            ->orderby('tabelleLevelBis' , 'desc')
            ->limit(1)
            ->first();

        if(isset($tabeleLevel->tabelleLevelBis)){
            if($tabeleLevel->tabelleLevelBis>$levelMax){
                $levelMax = $tabeleLevel->tabelleLevelBis;
            }
        }

        $tabeles = Tabele::where('event_id' , Session::get('regattaSelectId'))
            ->get();

        // Nur RaceTypes des aktiven Events laden
        $raceTypes = RaceType::where('regatta_id', Session::get('regattaSelectId'))->get();

        // Anzahl Bahnen aus Session holen
        $rennBahnenSession = Session::get('rennBahnen');

        return view('regattaManagement.race.create' , compact('levelMax', 'tabeles', 'raceTypes', 'rennBahnenSession'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request) {
        $rules = [
            'rennBezeichnung'             => 'required|max:50',
            'rennDatum'                        => 'required|date',
            'rennUhrzeit'                      => 'required|date_format:H:i',
            'veroeffentlichungUhrzeit'  => 'required|date_format:H:i',
            'liveStreamURL'                   => 'nullable|max:255',
            'einspielerURL'                    => 'nullable|string|max:255',
            'abspielzeit'                         => 'nullable|integer|min:0',
        ];

        // tabeleId ist nur erforderlich, wenn einzelRennen nicht 1 ist
        if($request->einzelRennen != 1) {
            $rules['tabeleId'] = 'required';
        } else {
            // Bei Einzelrennen muss gruppe_id gewählt werden
            $rules['gruppe_id'] = 'required';
        }

        $request->validate($rules);

        if($request->rennMix == Null){
            $request->rennMix=0;
        }

        if($request->einzelRennen == Null) {
            $request->einzelRennen=0;
        }

        if($request->getrenntewertung==Null){
            $request->getrenntewertung=0;
        }

        if($request->einzelRennen == 1) {
            $tabele= new Tabele([
                'event_id'                  => Session::get('regattaSelectId'),
                'gruppe_id'                => $request->gruppe_id,
                'ueberschrift'            => $request->rennBezeichnung,
                'tabelleLevelVon'      => $request->regattaLevel,
                'tabelleLevelBis'        => $request->regattaLevel,
                'tabelleDatumVon'    => $request->rennDatum,
                'finaleAnzeigen'        => $request->veroeffentlichungUhrzeit,
                'getrenntewertung'  => $request->getrenntewertung,
                'wertungsart'            => 3,
                'tabelleVisible'          => 0,
                'finale'                      => 0,
                'bearbeiter_id'          => Auth::id(),
                'autor_id'                  => Auth::id(),
                'updated_at'             => Carbon::now(),
                'created_at'              => Carbon::now()
            ]);

            $tabele->save();
            $request->tabeleId=$tabele->id;
        }

        $race= new Race([
                'event_id'                          => Session::get('regattaSelectId'),
                'tabele_id'                         => $request->tabeleId,
                'nummer'                          => $request->nummer,
                'bahnen'                           => $request->rennBahnen,
                'rennBezeichnung'           => $request->rennBezeichnung,
                'rennDatum'                     => $request->rennDatum,
                'rennUhrzeit'                    => $request->rennUhrzeit,
                'verspaetungUhrzeit'        => $request->rennUhrzeit,
                'veroeffentlichungUhrzeit'=> $request->veroeffentlichungUhrzeit,
                'level'                                => $request->regattaLevel,
                'mix'                                  => $request->rennMix,
                'visible'                              => 1,
                'liveStreamURL'                 => $request->liveStreamURL,
                'einspielerURL'                  => $request->einspielerURL,
                'abspielzeit'                       => $request->abspielzeit,
                'bearbeiter_id'                   => Auth::id(),
                'autor_id'                           => Auth::id(),
                'updated_at'                      => Carbon::now(),
                'created_at'                       => Carbon::now()
            ]);
        $race->save();

        // ToDo: Wenn es ein Rennen mit Mix ist, dann muss in der Tabele  das Feld maxrennen auf 0 gesetzt werden
        $tabele = Tabele::where('id', $request->tabeleId)
            ->where('maxrennen', '>', 0)
            ->update(['maxrennen' => 0]);

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

        $rennNummer=intval($request->nummer);
        if($rennNummer==$request->nummer){
            $rennNummer=$rennNummer+1;
        }
        else {
            $prefixPattern = '/[-.]/'; // Unterstützt sowohl "-" als auch "."
            $teile = preg_split($prefixPattern, $request->nummer, 2);
            if (count($teile) === 2) {
                $vordererTeil = $teile[0];
                $hintererTeil = $teile[1];
                $originalPrefix = strpos($request->nummer, '-') !== false ? '-' : '.'; // Ermittelt das ursprüngliche Präfix

                // Prüfen, ob der hintere Teil eine Zahl ist
                if (is_numeric($hintererTeil)) {
                    $hintererTeil += 1; // Erhöhe die Zahl um 1
                    $rennNummer = $vordererTeil . $originalPrefix . $hintererTeil; // Verwendet das ursprüngliche Präfix
                } else {
                    $rennNummer = $request->nummer;
                }
            } else {
                $rennNummer = $request->nummer;
            }
        }

        // Anzahl Bahnen in Session speichern, wenn gesetzt
        if ($request->filled('rennBahnen')) {
            Session::put('rennBahnen', $request->rennBahnen);
        }

        Session::put('regattaSelectRaceDate'         , $request->rennDatum);
        Session::put('regattaSelectRaceTime'         , $request->rennUhrzeit);
        Session::put('regattaSelectRaceTimeNew'  , $timeNew);
        Session::put('regattaSelectRacePublished' , $request->veroeffentlichungUhrzeit);
        Session::put('regattaSelectRaceName'       , $request->rennBezeichnung);
        Session::put('rennNummer'                        , $rennNummer);
        Session::put('rennLevelSave'                     , $request->regattaLevel);

        return redirect('/Rennen/neu')->with([
            'success'         => 'Das Rennen <b>' . $request->rennBezeichnung . '</b> wurde angelegt.'
        ]);
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function edit($raceid)
    {
        $race = Race::find($raceid);

        $raceLevel = Race::where('event_id' , Session::get('regattaSelectId'))
            ->orderby('level' , 'desc')
            ->limit(1)
            ->first();

        $tabeles = Tabele::where('event_id' , Session::get('regattaSelectId'))
            ->get();

        return view('regattaManagement.race.edit')->with([
            'race'          => $race,
            'levelMax'   => $raceLevel->level,
            'tabeles'     => $tabeles
        ]);
    }

    public function editProgram($race_id)
    {
        $race = Race::find($race_id);

        $seorch=$race->programmDatei;
        $raceDocuments = Race::where('event_id' , Session::get('regattaSelectId'))
            ->where('id' , '!=' , $race_id)
            ->where('level' , $race->level)
            ->where('visible' , 1)
            ->where(function ($query) use ($seorch){
                $query->where('programmDatei' , NULL)
                      ->orwhere('programmDatei' , $seorch);
            })
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->get();

        return view('regattaManagement.race.editProgram')->with([
            'race'                   => $race,
            'raceDocuments' => $raceDocuments
        ]);
    }

    public function editResult($race_id)
    {
        $race = Race::find($race_id);

        $seorch=$race->ergebnisDatei;
        $raceDocuments = Race::where('event_id' , Session::get('regattaSelectId'))
            ->where('id' , '!=' , $race_id)
            ->where('level'     , $race->level)
            ->where('visible'   , 1)
            ->where(function ($query) use ($seorch){
                $query->where('ergebnisDatei' , NULL)
                    ->orwhere('ergebnisDatei' , $seorch);
            })
            ->orderby('rennDatum')
            ->orderby('rennUhrzeit')
            ->get();

        // ToDo: if($race->ergebnisDatei  muss noch berücksichtigt werden
        // if($race->ergebnisDatei == NULL && $race->ergebnisBeschreibung == '') {
        if($race->status == 2 && $race->rennDatum == Carbon::now()->toDateString()) {
            $ractetime1 = Carbon::now();
            $ractetime2 = $ractetime1->subMinute(3);
            $ractetime  = $ractetime2->toTimeString();
        }
        else{
            $ractetime=$race->verspaetungUhrzeit;
        }

        return view('regattaManagement.race.editResult')->with([
            'race'          => $race,
            'raceDocuments' => $raceDocuments,
            'ractetime'     => $ractetime
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Race  $race
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $race_id)
    {
        $request->validate([
                'rennBezeichnung'             => 'required|max:50',
                'rennDatum'                       => 'required|date',
                'rennUhrzeit'                      => 'required|date_format:H:i',
                'veroeffentlichungUhrzeit' => 'required|date_format:H:i',
                'liveStreamURL'                  => 'nullable|max:255',
                'einspielerURL'                   => 'nullable|string|max:255',
                'abspielzeit'                        => 'nullable|integer|min:0',
            ]
        );

        if($request->rennMix==Null){
            $request->rennMix=0;
        }

        if($request->einzelRennen == Null) {
            $request->einzelRennen=0;
        }

        if($request->einzelRennen == 1 && $request->tabeleId == Null) {
            $tabele= new Tabele([
                'event_id'                 => Session::get('regattaSelectId'),
                'ueberschrift'           => $request->rennBezeichnung,
                'tabelleLevelVon'     => $request->regattaLevel,
                'tabelleLevelBis'       => $request->regattaLevel,
                'tabelleDatumVon'   => $request->rennDatum,
                'finaleAnzeigen'      => $request->veroeffentlichungUhrzeit,
                'wertungsart'          => 3,
                'tabelleVisible'        => 1,
                'finale'                     => 0,
                'bearbeiter_id'         => Auth::id(),
                'autor_id'                 => Auth::id(),
                'updated_at'            => Carbon::now(),
                'created_at'             => Carbon::now()
            ]);

            $tabele->save();
            $request->tabeleId=$tabele->id;
        }

        Race::find($race_id)->update([
                'nummer'                            => $request->nummer,
                'rennBezeichnung'             => $request->rennBezeichnung,
                'bahnen'                             => $request->rennBahnen,
                'rennDatum'                        => $request->rennDatum,
                'rennUhrzeit'                      => $request->rennUhrzeit,
                'verspaetungUhrzeit'          => $request->rennUhrzeit,
                'veroeffentlichungUhrzeit' => $request->veroeffentlichungUhrzeit,
                'level'                                  => $request->regattaLevel,
                'tabele_id'                           => $request->tabeleId,
                'mix'                                    => $request->rennMix,
                'liveStreamURL'                  => $request->liveStreamURL,
                'einspielerURL'                   => $request->einspielerURL,
                'abspielzeit'                        => $request->abspielzeit,
                'bearbeiter_id'                     => Auth::id(),
                'updated_at'                        => Carbon::now()
            ]
        );

        // Unterscheiden, welche Aktion ausgeführt werden soll
        if ($request->input('action') == 'save_and_edit_next') {
            // Logik für "Speichern & nächstes Rennen bearbeiten"
            $nextRace = Race::where([
                'races.event_id' => Session::get('regattaSelectId')
            ])
                ->where('rennUhrzeit','>=', $request->rennUhrzeit)
                ->whereNot('id', $race_id)
                ->orderBy('rennDatum')
                ->orderBy('rennUhrzeit')
                ->orderBy('id')
                ->first();

            if ($nextRace) {
                return redirect('Rennen/edit/'.$nextRace->id)->with([
                    'success' => 'Die Daten vom Rennen <b>' . $request->nummer . ' ' . $request->rennBezeichnung . '</b> wurden geändert.'
                ]);
            } else {
                return redirect('/Rennen/alle')->with([
                    'error' => 'Kein weiteres Rennen gefunden.',
                    'success' => 'Die Daten vom Rennen <b>' . $request->nummer . ' ' . $request->rennBezeichnung . '</b> wurden geändert.'
                ]);
            }
        }
        else {
            // Logik für "Speichern"
            return redirect('/Rennen/alle')->with([
                  'success' => 'Die Daten vom Rennen <b>' . $request->nummer . ' ' . $request->rennBezeichnung . '</b> wurden geändert.'
                ]
            );
        }
    }

    public function updateProgram(Request $request, $race_id)
    {
         Race::find($race_id)->update([
            'beschreibung'    => $request->beschreibung,
            'bearbeiter_id'   => Auth::id(),
            'updated_at'      => Carbon::now()
        ]);

        $oldDocumentFile = Race::find($race_id);

        if($request->programmDatei){
            $extension = $request->programmDatei->extension();
            $newDocumentName = 'programm' . $race_id . '_' . str::random(4) . '.' . $extension;
            $fileProgrammDatei=$request->file('programmDatei')->getClientOriginalName();
            Storage::disk('public')->putFileAs(
                'raceDokumente/',
                $request->programmDatei,
                $newDocumentName
            );

            Race::find($race_id)->update([
                'programmDatei'     => $newDocumentName,
                'fileProgrammDatei' => $fileProgrammDatei,
            ]);

            if(isset($request->raceDocId)) {
                $raceDocIds = $request->raceDocId;
                foreach ($raceDocIds as $raceDocId) {
                    Race::find($raceDocId)->update([
                        'programmDatei'     => $newDocumentName,
                        'fileProgrammDatei' => $fileProgrammDatei,
                        'bearbeiter_id'     => Auth::id(),
                        'updated_at'        => Carbon::now()
                    ]);
                }
            }

            $oldDocumentFileCount=Race::where('programmDatei' , $oldDocumentFile->programmDatei)->count();
            if($oldDocumentFileCount==0) {
                if (isset($oldDocumentFile->programmDatei)) {
                    Storage::disk('public')->delete('raceDokumente/' . $oldDocumentFile->programmDatei);
                }
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
        $request->validate([
                'zeit'   => 'min:0|max:10'
            ]
        );

        Session::put('regattaZeit' , $request->zeit);
        Session::put('regattaZeitMinAbstand' , $request->zeitMinAbstand);

        if($request->rennzeit==Null){
            $request->rennzeit=0;
        }

        Race::find($race_id)->update([
            'ergebnisBeschreibung' => $request->ergebnisBeschreibung,
            'verspaetungUhrzeit'   => $request->rennUhrzeit,
            'rennzeit'             => $request->rennzeit,
            'bearbeiter_id'        => Auth::id(),
            'updated_at'           => Carbon::now()
        ]);

        $oldDocumentFile = Race::find($race_id);

        if($request->ergebnisDatei){
            $extension = $request->ergebnisDatei->extension();
            $newDocumentName = 'ergebnis' . $race_id . '_' . str::random(4) . '.' . $extension;
            $fileErgebnisDatei=$request->file('ergebnisDatei')->getClientOriginalName();
            Storage::disk('public')->putFileAs(
                'raceDokumente/',
                $request->ergebnisDatei,
                $newDocumentName
            );

            Race::find($race_id)->update([
                'ergebnisDatei'     => $newDocumentName,
                'fileErgebnisDatei' => $fileErgebnisDatei,
            ]);

            if(isset($request->raceDocId)) {
                $raceDocIds = $request->raceDocId;
                foreach ($raceDocIds as $raceDocId) {
                    Race::find($raceDocId)->update([
                        'ergebnisDatei'     => $newDocumentName,
                        'fileErgebnisDatei' => $fileErgebnisDatei,
                        'bearbeiter_id'     => Auth::id(),
                        'updated_at'        => Carbon::now()
                    ]);
                }
            }

            $oldDocumentFileCount=Race::where('ergebnisDatei' , $oldDocumentFile->ergebnisDatei)->count();
            if($oldDocumentFileCount==0) {
                if (isset($oldDocumentFile->ergebnisDatei)) {
                    Storage::disk('public')->delete('raceDokumente/' . $oldDocumentFile->ergebnisDatei);
                }
            }
        }

        $berechnung=$this->timeVerschiebung($race_id, $request->rennUhrzeit, $request->zeit, $request->zeitMinAbstand);

        return redirect('/Rennen/Ergebnisse')->with(
            [
                'success'  => 'Die Ergebnisse des Rennens wurden gespeichert.'
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
                'bearbeiter_id'         => Auth::id(),
                'updated_at'            => Carbon::now()
            ]);

        DB::table('races')
            ->where('programmDatei' , $deleteDocumentFile->programmDatei)
            ->update([
                'programmDatei'      => Null,
                'fileProgrammDatei' => Null,
                'bearbeiter_id'         => Auth::id(),
                'updated_at'            => Carbon::now()
            ]);

        if (isset($deleteDocumentFile->programmDatei)) {
                Storage::disk('public')->delete('raceDokumente/' . $deleteDocumentFile->programmDatei);
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
                'ergebnisDatei'      => Null,
                'fileErgebnisDatei' => Null,
                'bearbeiter_id'       => Auth::id(),
                'updated_at'          => Carbon::now()
            ]);

        DB::table('races')
            ->where('ergebnisDatei' , $deleteDocumentFile->ergebnisDatei)
            ->update([
                'ergebnisDatei'      => Null,
                'fileErgebnisDatei' => Null,
                'bearbeiter_id'       => Auth::id(),
                'updated_at'          => Carbon::now()
            ]);

        if (isset($deleteDocumentFile->ergebnisDatei)) {
                Storage::disk('public')->delete('raceDokumente/' . $deleteDocumentFile->ergebnisDatei);
        }

        $document = Race::find($race_Id);
        return redirect('Rennen/Ergebnis/'.$race_Id)->with(
            [
              'success'  => 'Das Ergebnisdokument  <b>' . $document->ergebnisDatei . '</b> wurde gelöscht.'
            ]
        );
    }

    public function raceTime($race_id) {
        $race = Race::find($race_id);
        return view('regattaManagement.race.raceTime')->with([
            'race'          => $race
         ]);
    }

    public function updateRaceTime(Request $request, $race_id)
    {
        $request->validate([
                'zeit'   => 'min:0|max:10'
            ]
        );

        Session::put('regattaZeit' , $request->zeit);
        Session::put('regattaZeitMinAbstand' , $request->zeitMinAbstand);

        if($request->rennzeit==Null){
            $request->rennzeit=0;
        }

        Race::find($race_id)->update([
            'verspaetungUhrzeit'   => $request->rennUhrzeit,
            'rennzeit'             => $request->rennzeit,
            'bearbeiter_id'        => Auth::id(),
            'updated_at'           => Carbon::now()
        ]);

        $berechnung=$this->timeVerschiebung($race_id, $request->rennUhrzeit, $request->zeit, $request->zeitMinAbstand);

        return redirect('/Rennen/Ergebnisse')->with(
            [
                'success'  => 'Die Rennzeit des Rennens wurden gespeichert.'
            ]
        );
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

    public function sliteShowResultActivate($id)
    {
        $race = \App\Models\Race::findOrFail($id);
        $race->sliteShowResult = true;
        $race->save();
        return back()->with('success', 'Slideshow-Ergebnis aktiviert.');
    }

    public function sliteShowResultDeactivate($id)
    {
        $race = \App\Models\Race::findOrFail($id);
        $race->sliteShowResult = false;
        $race->save();
        return back()->with('success', 'Slideshow-Ergebnis deaktiviert.');
    }

    public function liveStreamActivate($id)
    {
        $race = \App\Models\Race::findOrFail($id);
        $race->liveStream = true;
        $race->save();
        return back()->with('success', 'Livestream aktiviert.');
    }

    public function liveStreamDeactivate($id)
    {
        $race = \App\Models\Race::findOrFail($id);
        $race->liveStream = false;
        $race->save();
        return back()->with('success', 'Livestream deaktiviert.');
    }

    public function aktivLive($raceId)
    {
        Race::where('aktuellLiveVideo', '1')->update([
            'aktuellLiveVideo' => '0',
            'bearbeiter_id'    => Auth::id(),
            'updated_at'       => Carbon::now()
        ]);

        Race::find($raceId)->update([
            'aktuellLiveVideo' => '1',
            'bearbeiter_id'    => Auth::id(),
            'updated_at'       => Carbon::now()
        ]);

        return Redirect()->back()->with('success' , 'Das Rennen wurde für Webliveübertragung aktiviert.');
    }

    public function inaktivLive($raceId)
    {
        Race::find($raceId)->update([
            'aktuellLiveVideo' => '0',
            'bearbeiter_id'    => Auth::id(),
            'updated_at'       => Carbon::now()
        ]);

        return Redirect()->back()->with('success' , 'Das Rennen wurde für Webliveübertragung deaktiviert.');
    }
}
