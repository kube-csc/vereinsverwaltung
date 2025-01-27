<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RaceType;
use App\Models\RaceTypeTemplate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

class RaceTypeController extends Controller
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
        $raceTypes = RaceType::where('regatta_id', Session::get('regattaSelectId'))
            ->paginate(10);

        return view('regattaManagement.racetype.index', compact('raceTypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $raceTypes = RaceType::where('regatta_id', Session::get('regattaSelectId'))->get();
        $raceTypeIds = $raceTypes->pluck('race_type_template_id')->toArray();

        $raceTypeTemplates = RaceTypeTemplate::whereNotIn('id', $raceTypeIds)
            ->orderBy('typ')
            ->get();

        return view('regattaManagement.racetype.creat', compact('raceTypeTemplates'));

    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store($raceTypeTemplate_id)
    {
        // Find the RaceTypeTemplate by ID
        $raceTypeTemplate = RaceTypeTemplate::findOrFail($raceTypeTemplate_id);

        // Create a new RaceType instance and copy the attributes
        $raceType = new RaceType();
        $raceType->regatta_id = Session::get('regattaSelectId');
        $raceType->race_type_template_id = $raceTypeTemplate->id;
        $raceType->typ = $raceTypeTemplate->typ;
        $raceType->beschreibung = $raceTypeTemplate->beschreibung;
        $raceType->distanz = $raceTypeTemplate->distanz;
        $raceType->altervon = $raceTypeTemplate->altervon;
        $raceType->alterbis = $raceTypeTemplate->alterbis;
        $raceType->min = $raceTypeTemplate->min;
        $raceType->max = $raceTypeTemplate->max;
        $raceType->weiblichmin = $raceTypeTemplate->weiblichmin;
        $raceType->weiblichmax = $raceTypeTemplate->weiblichmax;
        $raceType->manmin = $raceTypeTemplate->manmin;
        $raceType->manmax = $raceTypeTemplate->manmax;
        $raceType->training = $raceTypeTemplate->training;
        $raceType->bahnen = $raceTypeTemplate->bahnen;
        $raceType->zusatzmanschaft = $raceTypeTemplate->zusatzmanschaft;
        $raceType->meldeGebuehr = $raceTypeTemplate->meldeGebuehr;
        $raceType->bearbeiter_id = auth()->id();
        $raceType->autor_id = auth()->id();

        // Save the new RaceType
        $raceType->save();

        // Redirect back with a success message
        return redirect()->route('raceType.index')->with('success', 'Renntyp '.$raceTypeTemplate->typ.' erfolgreich kopiert.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $raceType = RaceType::findOrFail($id);

        return view('regattaManagement.racetype.edit', compact('raceType'));
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
        // Retrieve the RaceType instance by its ID
        $raceType = RaceType::findOrFail($id);

        // Validate the incoming request data
        $validatedData = $request->validate([
            'distanz' => 'required|string',
            'altervon' => 'required|integer',
            'alterbis' => 'required|integer',
            'min' => 'required|integer',
            'max' => 'required|integer',
            'weiblichmin' => 'required|integer',
            'weiblichmax' => 'required|integer',
            'manmin' => 'required|integer',
            'manmax' => 'required|integer',
            'bahnen' => 'required|integer',
            'meldeGebuehr' => 'required|numeric',
            'zusatzmanschaft' => 'boolean',
        ]);

        if(!isset($request->zusatzmanschaft)){
            $validatedData['zusatzmanschaft'] = 0;
        }

        // Update the RaceType instance with the validated data
        $raceType->update($validatedData);

        // Redirect back with a success message
        return redirect()->route('raceType.index')->with('success', 'Renntyp '.$raceType->typ.' erfolgreich geändert.');
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
}
