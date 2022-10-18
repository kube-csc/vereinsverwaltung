<?php

namespace App\Http\Controllers;

use App\Models\Club;
use App\Models\Sporttype;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Auth;

class ClubController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function aktiv($clubId)
    {
        club::find($clubId)->update([
            'visible'       => '1',
            'bearbeiter_id' => Auth::id(),
            'updated_at'    => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , env('MENUE_VERBAND'). ' wurde sichtbar geschaltet.');
    }

    public function inaktiv($clubId)
    {
        club::find($clubId)->update([
            'visible'       => '0',
            'bearbeiter_id' => Auth::id(),
            'updated_at'    => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , env('MENUE_VERBAND'). ' wurde unsichtbar geschaltet.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $clubs = Club::paginate(5);

        return view('admin.club.index' , compact('clubs'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.club.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate(
            [
                'clubname' => 'required|max:50'
            ]
        );

        $club= new Club(
            [
                'clubname'      => $request->clubname,
                'bearbeiter_id' => Auth::id(),
                'user_id'       => Auth::id(),
                'updated_at'    => Carbon::now(),
                'created_at'    => Carbon::now()
            ]
        );
        $club->save();


        return redirect('/Club/alle')->with(
            [
                'success' => $request->clubname . '</b> wurde angelegt.'
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function show(Club $club)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function edit($clubId)
    {
        $club          = Club::find($clubId);
        $allSporttypes = Sporttype::all();
        $verwendeteSporttypes  = $club->sporttypes;
        $verfuegbareSporttypes = $allSporttypes->diff($verwendeteSporttypes);

        return view('admin.club.edit')->with(
            [
                'club'                  => $club,
                'verfuegbareSporttypes' => $verfuegbareSporttypes
            ]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $clubId)
    {
        $request->validate(
            [
                'clubname'         => 'required|max:50'
            ]
        );

        club::find($clubId)->update([
            'clubname'        => $request->clubname,
            'bearbeiter_id'   => Auth::id(),
            'updated_at'      => Carbon::now()
        ]);

        return redirect('/Club/alle')->with(
            [
                'success' => 'Die Daten von <b>' . $request->clubname . '</b> wurden geändert.'
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Club  $club
     * @return \Illuminate\Http\Response
     */
    public function destroy(Club $club)
    {
        //
    }

    public function softDelete($clubId)
    {
        $delete = club::find($clubId)->delete();
        return redirect('/Club/alle')->with(
            [
                'success' => env('MENUE_VERBAND'). ' wurde gelöscht.'
            ]
        );
    }

    public function clubAttachSporttype($clubId , $sporttypeid)
    {
        $club = Club::find($clubId);
        $sporttype  = Sporttype::find($sporttypeid);
        $club->sporttypes()->attach($sporttypeid);

      return back()->with('success' , 'Die Sportart <b>'. $sporttype->clubname . '</b> wurde angehangen.');
    }

    public function clubDetachSporttype($clubId , $sporttypeid)
    {
        $club = Club::find($clubId);
        $sporttype  = Sporttype::find($sporttypeid);
        $club->sporttypes()->detach($sporttypeid);

        return back()->with('success' , 'Die Sportart <b>'. $sporttype->clubname . '</b> wurde entfernd.');
    }
}
