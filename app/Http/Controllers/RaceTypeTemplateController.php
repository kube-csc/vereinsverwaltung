<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\RaceTypeTemplate;
use Illuminate\Http\Request;

class RaceTypeTemplateController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $raceTypeTemplates = RaceTypeTemplate::all();

        return view('regattaManagement.raceTypeTemplate.index', compact('raceTypeTemplates'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('regattaManagement.raceTypeTemplate.create');

        //return view('regattaManagement.raceTypeTemplate.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'typ' => 'required|string|max:255',
            'altervon' => 'required|integer',
            'alterbis' => 'required|integer',
            'min' => 'required|integer',
            'max' => 'required|integer',
            'weiblichmin' => 'required|integer',
            'weiblichmax' => 'required|integer',
            'manmin' => 'required|integer',
            'manmax' => 'required|integer',
            'training' => 'required|integer|min:0',
            'bahnen' => 'required|integer|min:0',
            'zusatzmanschaft' => 'boolean',
            'beschreibung' => 'nullable|string',
            'distanz' => 'required|string|max:255',
            'meldeGebuehr' => 'required|numeric',
        ]);

        if(!isset($request->zusatzmanschaft)){
            $validatedData['zusatzmanschaft'] = 0;
        }
        $validatedData['bearbeiter_id'] = auth()->id();
        $validatedData['autor_id'] = auth()->id();

        RaceTypeTemplate::create($validatedData);

        return redirect()->route('raceTypeTemplate.index')->with('success', 'Renntyp Vorlage '.$request->typ.' erfolgreich eingetragen.');
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
        $raceTypeTemplate = RaceTypeTemplate::findOrFail($id);

        return view('regattaManagement.raceTypeTemplate.edit', compact('raceTypeTemplate'));
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
        $validatedData = $request->validate([
            'typ' => 'required|string|max:255',
            'altervon' => 'required|integer',
            'alterbis' => 'required|integer',
            'min' => 'required|integer',
            'max' => 'required|integer',
            'weiblichmin' => 'required|integer',
            'weiblichmax' => 'required|integer',
            'manmin' => 'required|integer',
            'manmax' => 'required|integer',
            'training' => 'required|integer|min:0',
            'bahnen' => 'required|integer|min:0',
            'zusatzmanschaft' => 'boolean',
            'beschreibung' => 'nullable|string',
            'distanz' => 'required|string',
            'meldeGebuehr' => 'required|numeric'
        ]);

        if(!isset($request->zusatzmanschaft)){
            $validatedData['zusatzmanschaft'] = 0;
        }
        $validatedData['bearbeiter_id'] = auth()->id();

        // Retrieve the RaceType instance by its ID
        $raceTypeTemplate = RaceTypeTemplate::findOrFail($id);

        $raceTypeTemplate->update($validatedData);

        return redirect()->route('raceTypeTemplate.index')->with('success', 'Renntyp '.$raceTypeTemplate->typ.' erfolgreich geändert.');
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
