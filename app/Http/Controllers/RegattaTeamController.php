<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RaceType;
use Illuminate\Http\Request;
use App\Models\RegattaTeam;
use App\Models\TeamWertungsGruppe;

class RegattaTeamController extends Controller
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
        $regattaId = session()->get('regattaSelectId');
        $regattaTeams = RegattaTeam::where('regatta_id', $regattaId)
            ->orderBy('datum')
            ->paginate(10);

        return view('regattaManagement.regattaTeam.index', compact('regattaTeams'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $gruppen = RaceType::where('regatta_id', session()->get('regattaSelectId'))->get();
        $regattaTeam = new RegattaTeam();
        return view('regattaManagement.regattaTeam.create', compact('regattaTeam', 'gruppen'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $regattaId = session()->get('regattaSelectId');
        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                function ($attribute, $value, $fail) use ($regattaId) {
                    $exists = RegattaTeam::where('regatta_id', $regattaId)
                        ->where('teamname', $value)
                        ->exists();
                    if ($exists) {
                        $fail('Der Teamname ist innerhalb dieser Regatta bereits vergeben.');
                    }
                }
            ],
            'verein' => 'nullable|string|max:255',
            'teamcaptain' => 'nullable|string|max:255',
            'strasse' => 'nullable|string|max:255',
            'plz' => 'nullable|string|max:20',
            'ort' => 'nullable|string|max:255',
            'telefon' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'homepage' => 'nullable|string|max:255',
            'beschreibung' => 'nullable|string',
            'kommentar' => 'nullable|string',
            'gruppe_id' => 'required',
            'werbung' => 'nullable|in:0,1,2,3,4,5,6,7,8,9,10,11,12,13',
            'status' => 'required|in:Gelöscht,Neumeldung,Warteliste,Nicht angetreten,Disqualifiziert,Ausgeschieden',
        ]);

        $regattaTeam = new RegattaTeam();
        $regattaTeam->regatta_id = $regattaId;
        $regattaTeam->teamname = $request->input('name');
        $regattaTeam->verein = $request->input('verein');
        $regattaTeam->teamcaptain = $request->input('teamcaptain');
        $regattaTeam->strasse = $request->input('strasse');
        $regattaTeam->plz = $request->input('plz');
        $regattaTeam->ort = $request->input('ort');
        $regattaTeam->telefon = $request->input('telefon');
        $regattaTeam->email = $request->input('email');
        $regattaTeam->homepage = $request->input('homepage');
        $regattaTeam->beschreibung = $request->input('beschreibung');
        $regattaTeam->kommentar = $request->input('kommentar');
        $regattaTeam->gruppe_id = $request->input('gruppe_id');
        $regattaTeam->werbung = $request->input('werbung');
        $regattaTeam->status = $request->input('status', 'Neumeldung');
        // Setze Standardwerte für Felder, die in der Migration als required stehen
        $regattaTeam->datum = now();
        $regattaTeam->training = 0;
        $regattaTeam->passwort = '';
        $regattaTeam->teamlink = 0;
        $regattaTeam->mailen = '';
        $regattaTeam->save();

        return redirect()->route('regattaTeam.index')->with('success', 'Team erfolgreich gemeldet.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($regattaTeam_id)
    {
        $regattaTeam = RegattaTeam::findOrFail($regattaTeam_id);
        return view('regattaManagement.regattaTeam.show', compact('regattaTeam'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($regattaTeam_id)
    {
        $regattaTeam = RegattaTeam::findOrFail($regattaTeam_id);
        $gruppen     = RaceType::where('regatta_id', session()->get('regattaSelectId'))->get();

        return view('regattaManagement.regattaTeam.edit', compact('regattaTeam', 'gruppen'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $regattaTeam = RegattaTeam::findOrFail($id);

        $request->validate([
            'name' => [
                'required',
                'string',
                'max:255',
                // Name muss innerhalb der gleichen Regatta eindeutig sein, außer für das aktuelle Team
                function ($attribute, $value, $fail) use ($request, $regattaTeam) {
                    $exists = RegattaTeam::where('regatta_id', $regattaTeam->regatta_id)
                        ->where('teamname', $value)
                        ->where('id', '!=', $regattaTeam->id)
                        ->exists();
                    if ($exists) {
                        $fail('Der Teamname ist innerhalb dieser Regatta bereits vergeben.');
                    }
                }
            ],
            'verein' => 'nullable|string|max:255',
            'teamcaptain' => 'nullable|string|max:255',
            'strasse' => 'nullable|string|max:255',
            'plz' => 'nullable|string|max:20',
            'ort' => 'nullable|string|max:255',
            'telefon' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'homepage' => 'nullable|string|max:255',
            'beschreibung' => 'nullable|string',
            'kommentar' => 'nullable|string',
            'gruppe_id' => 'required',
            'werbung' => 'nullable|in:0,1,2,3,4,5,6,7,8,9,10,11,12,13',
            'status' => 'required|in:Gelöscht,Neumeldung,Warteliste,Nicht angetreten,Disqualifiziert,Ausgeschieden',
        ]);

        $regattaTeam->teamname = $request->input('name');
        $regattaTeam->verein = $request->input('verein');
        $regattaTeam->teamcaptain = $request->input('teamcaptain');
        $regattaTeam->strasse = $request->input('strasse');
        $regattaTeam->plz = $request->input('plz');
        $regattaTeam->ort = $request->input('ort');
        $regattaTeam->telefon = $request->input('telefon');
        $regattaTeam->email = $request->input('email');
        $regattaTeam->homepage = $request->input('homepage');
        $regattaTeam->beschreibung = $request->input('beschreibung');
        $regattaTeam->kommentar = $request->input('kommentar');
        $regattaTeam->gruppe_id = $request->input('gruppe_id');
        $regattaTeam->werbung = $request->input('werbung');
        $regattaTeam->status = $request->input('status', 'Neumeldung');
        $regattaTeam->save();

        return redirect()->route('regattaTeam.index')->with('success', 'Team erfolgreich aktualisiert.');
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

    public function werbungsquelle()
    {
        // Wenn nicht eingeloggt, muss die Regatta-ID über die URL kommen
        $regattaId = session()->get('regattaSelectId');
        $regattaTeams = RegattaTeam::where('regatta_id', $regattaId)
            ->where('status', '!=', 'Gelöscht')
            ->get();

        // Werbungsoptionen auslagern
        $werbungConfig = include base_path('textimport/werbung_options.php');
        $werbungOptions = $werbungConfig['options'];

        // Statistik initialisieren mit IDs als Key
        $statistik = [];
        foreach ($werbungOptions as $key => $label) {
            $statistik[$key] = 0;
        }
        foreach ($regattaTeams as $team) {
            $key = (string)($team->werbung ?? '0');
            if (array_key_exists($key, $statistik)) {
                $statistik[$key]++;
            } else {
                $statistik['0']++;
            }
        }

        // Sortiere Statistik absteigend nach Anzahl (nach Wert, Key bleibt ID)
        arsort($statistik);

        // Für Tortendiagramm: Labels und Werte als Arrays (Labels nach sortierter Statistik)
        $labels = [];
        $values = [];
        foreach ($statistik as $key => $anzahl) {
            $labels[] = $werbungOptions[$key] ?? $key;
            $values[] = $anzahl;
        }

        return view('regattaManagement.regattaTeam.werbungsquelle', [
            'statistik' => $statistik,
            'labels' => $labels,
            'values' => $values,
            'regattaTeams' => $regattaTeams,
            'werbungOptions' => $werbungOptions,
        ]);
    }
}
