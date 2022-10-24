<?php

namespace App\Http\Controllers;

use App\Models\Sporttype;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Auth;

class SporttypeController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function aktiv($sporttypeId)
    {
        sporttype::find($sporttypeId)->update([
            'visible'       => '1',
            'bearbeiter_id' => Auth::id(),
            'updated_at'    => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Die Sportart wurde sichtbar geschaltet.');
    }

    public function inaktiv($sporttypeId)
    {
        sporttype::find($sporttypeId)->update([
            'visible'       => '0',
            'bearbeiter_id' => Auth::id(),
            'updated_at'    => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Die Sportart wurde unsichtbar geschaltet.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sporttypes = Sporttype::paginate(5);

        return view('admin.sporttype.index' , compact('sporttypes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.sporttype.create');
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
                'sporttypename' => 'required|max:50'
            ]
        );

        $sporttype= new Sporttype(
            [
                'sportart'      => $request->sporttypename,
                'bearbeiter_id' => Auth::id(),
                'user_id'       => Auth::id(),
                'updated_at'    => Carbon::now(),
                'created_at'    => Carbon::now()
            ]
        );
        $sporttype->save();

        return redirect('/Sportart/alle')->with(
            [
                'success' => 'Die Sportart '. $request->sporttypename . '</b> wurde angelegt.'
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Sporttype  $sporttype
     * @return \Illuminate\Http\Response
     */
    public function show(Sporttype $sporttype)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Sporttype  $sporttype
     * @return \Illuminate\Http\Response
     */
    public function edit($sporttypeId)
    {
        $sporttype=Sporttype::find($sporttypeId);

        return view('admin.sporttype.edit',compact('sporttype'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Sporttype  $sporttype
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $sporttypeId)
    {
        $request->validate(
            [
                'sporttypename'         => 'required|max:50'
            ]
        );

        Sporttype::find($sporttypeId)->update([
            'sportart'        => $request->sporttypename,
            'bearbeiter_id'   => Auth::id(),
            'updated_at'      => Carbon::now()
        ]);

        return redirect('/Sportart/alle')->with(
            [
                'success' => 'Die Daten von <b>' . $request->sporttypename . '</b> wurden geändert.'
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Sporttype  $sporttype
     * @return \Illuminate\Http\Response
     */
    public function destroy(Sporttype $sporttype)
    {
        //
    }

    public function softDelete($sporttypeId)
    {
        $delete = Sporttype::find($sporttypeId)->delete();

        return redirect('/Sportart/alle')->with(
            [
                'success' => 'Die Sportart wurde gelöscht.'
            ]
        );
    }
}
