<?php

namespace App\Http\Controllers;

use App\Models\RegattaInformation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class RegattaInformationController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function aktiv($regattaInfoId)
    {
        RegattaInformation::find($regattaInfoId)->update([
            'visible'          => '1',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Die Information wurde sichtbar geschaltet.');
    }

    public function inaktiv($regattaInfoId)
    {
        RegattaInformation::find($regattaInfoId)->update([
            'visible'          => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Die Information wurde unsichtbar geschaltet.');
    }

    public function maxtop($regattaInfoId)
    {
        RegattaInformation::find($regattaInfoId)->update([
            'position'         => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        // ToDo verbessern der Updatefunktion
        $regattaInformations = RegattaInformation::orderby('position')->get();
        $positionNew=10;
        foreach ($regattaInformations as $regattaInformation){
            RegattaInformation::find($regattaInformation->id)->update([
                'position' => $positionNew
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Die Information wurde zur Top Position verschoben.');
    }

    public function top($regattaInfoId)
    {
        // ToDo verbessern der Updatefunktion
        $regattaInformation = RegattaInformation::find($regattaInfoId);
        $positionNew=$regattaInformation->position-11;

        $regattaInformation::find($regattaInfoId)->update([
            'position'       => $positionNew,
            'bearbeiter_id'  => Auth::user()->id,
            'updated_at'     => Carbon::now()
        ]);

        $positionNew=10;
        $regattaInformations = RegattaInformation::orderby('position')->get();
        foreach ($regattaInformations as $regattaInformation){
            RegattaInformation::find($regattaInformation->id)->update([
                'position' => $positionNew
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Die Information wurde eine Position nach oben verschoben.');
    }

    public function down($regattaInfoId)
    {
        // ToDo verbessern der Updatefunktion
        $regattaInformation = RegattaInformation::find($regattaInfoId);
        $positionNew=$regattaInformation->position+11;
        RegattaInformation::find($regattaInfoId)->update([
            'position'       => $positionNew,
            'bearbeiter_id'  => Auth::user()->id,
            'updated_at'     => Carbon::now()
        ]);

        $regattaInformations = RegattaInformation::orderby('position')->get();
        $positionNew=10;
        foreach ($regattaInformations as $regattaInformation){
            RegattaInformation::find($regattaInformation->id)->update([
                'position' => $positionNew
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Die Information wurde eine Position nach unten verschoben.');
    }

    public function maxdown($regattaInfoId)
    {
        $regattaInformations = RegattaInformation::orderby('position' , 'desc')->limit(1)->get();
        foreach ($regattaInformations as $regattaInformation){
            $positionNew=$regattaInformation->position+10;
        }

        RegattaInformation::find($regattaInfoId)->update([
            'position'      => $positionNew,
            'bearbeiter_id' => Auth::user()->id,
            'updated_at'    => Carbon::now()
        ]);

        $regattaInformations = RegattaInformation::orderby('position')->get();
        $positionNew=10;
        foreach ($regattaInformations as $regattaInformation){
            RegattaInformation::find($regattaInformation->id)->update([
                'position' => $positionNew,
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Die Information wurde zur letzten Position verschoben.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $regattaInformations = RegattaInformation::where([
            'event_id' => Session::get('regattaSelectId'),
        ])
            ->orderby('position')
            ->paginate(5);

        $regattaInformationMaxs = RegattaInformation::orderby('position')->get();
        if($regattaInformationMaxs->count()>0){
            $regattaInformationMaxId = $regattaInformationMaxs->last()->id;
        }
        else{
            $regattaInformationMaxId=0;
        }

        return view('regattaManagement.regattaInformation.index')->with([
            'regattaInformationens'   => $regattaInformations,
            'regattaInformationMaxId' => $regattaInformationMaxId
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('regattaManagement.regattaInformation.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
                'informationTittel'  => 'required|max:50',
                'endDatum'           => 'after_or_equal:startDatum',
            ]
        );

        $regattaInformation= RegattaInformation::where('event_id' , Session::get('regattaSelectId'))
            ->orderBy('position' , 'desc')
            ->first();

        $neuPosition = $regattaInformation ? $regattaInformation->position + 10 : 10;

        if($request->startDatumAktiv == Null){
            $request->startDatumAktiv=0;
        }

        if($request->endDatumAktiv == Null){
            $request->endDatumAktiv=0;
        }

        $regattaInformation = new RegattaInformation([
                'event_id'                 => Session::get('regattaSelectId'),
                'position'                 => $neuPosition,
                'informationTittel'        => $request->informationTittel,
                'informationBeschreibung'  => $request->informationBeschreibung,
                'startDatum'               => $request->startDatum,
                'startDatumVerschoben'     => $request->startDatum,
                'startDatumAktiv'          => $request->startDatumAktiv,
                'endDatum'                 => $request->endDatum,
                'endDatumVerschoben'       => $request->endDatum,
                'endDatumAktiv'            => $request->endDatumAktiv,
                'bearbeiter_id'            => Auth::user()->id,
                'user_id'                  => Auth::user()->id,
                'updated_at'               => Carbon::now(),
                'created_at'               => Carbon::now()
            ]
        );
        $regattaInformation->save();

        return redirect('/Renneninformation/alle')->with([
                'success'         => 'Das Regatta Information wurde angelegt.'
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\RegattaInformation  $regattaInformation
     * @return \Illuminate\Http\Response
     */
    public function show(RegattaInformation $regattaInformation)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\RegattaInformation  $regattaInformation
     * @return \Illuminate\Http\Response
     */
    public function edit($regattaInfoId)
    {
        $regattaInformation = RegattaInformation::find($regattaInfoId);
        $startDatum= str_replace (" " , "T" , $regattaInformation->startDatum);
        $endDatum=   str_replace (" " , "T" , $regattaInformation->endDatum);

        return view('regattaManagement.regattaInformation.edit')->with([
            'regattaInformation' => $regattaInformation,
            'startDatum'         => $startDatum,
            'endDatum'           => $endDatum
        ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\RegattaInformation  $regattaInformation
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $regattaInfoId)
    {
        $request->validate([
                'informationTittel'  => 'required|max:50',
                'endDatum'           => 'after_or_equal:startDatum',
            ]
        );

        if($request->startDatumAktiv == Null){
            $request->startDatumAktiv=0;
        }

        if($request->endDatumAktiv == Null){
            $request->endDatumAktiv=0;
        }

        RegattaInformation::find($regattaInfoId)->update([
                'informationTittel'        => $request->informationTittel,
                'informationBeschreibung'  => $request->informationBeschreibung,
                'startDatum'               => $request->startDatum,
                'startDatumVerschoben'     => $request->startDatum,
                'startDatumAktiv'          => $request->startDatumAktiv,
                'endDatum'                 => $request->endDatum,
                'endDatumVerschoben'       => $request->endDatum,
                'endDatumAktiv'            => $request->endDatumAktiv,
                'bearbeiter_id'            => Auth::user()->id,
                'user_id'                  => Auth::user()->id,
                'updated_at'               => Carbon::now(),
                'created_at'               => Carbon::now()
            ]
        );

        return redirect('/Renneninformation/alle')->with([
                'success' => 'Die Regatta Infomation <b>' . $request->informationTittel . '</b> wurden geändert.'
            ]
        );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\RegattaInformation  $regattaInformation
     * @return \Illuminate\Http\Response
     */
    public function destroy(RegattaInformation $regattaInformation)
    {
        //
    }
}
