<?php

namespace App\Http\Controllers;

use App\Models\eventGroup;
use Illuminate\Support\Carbon;
use Auth;

use Illuminate\Http\Request;

class EventGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $eventGroups = eventGroup::paginate(5);
         return view('admin.eventGroup.index' , compact('eventGroups'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('admin.eventGroup.create');
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
                'termingruppe' => 'required|max:50'
            ]
        );

        $eventGroup= new eventGroup(
            [
                'termingruppe'     => $request->termingruppe,
                'user_id'          => Auth::user()->id,
                'updated_at'       => Carbon::now(),
                'created_at'       => Carbon::now()
            ]
        );
        $eventGroup->save();

        return redirect('/Eventgruppe/alle')->with(
            [
                'success' => 'Die Event Gruppe <b>' . $request->termingruppe . '</b> wurde angelegt.'
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\eventGroup  $eventGroup
     * @return \Illuminate\Http\Response
     */
    public function show(eventGroup $eventGroup)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\eventGroup  $eventGroup
     * @return \Illuminate\Http\Response
     */
    public function edit($eventGroup_id)
    {
        $eventGroup =eventGroup::find($eventGroup_id);
        return view('admin.eventGroup.edit',compact('eventGroup'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\eventGroup  $eventGroup
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $eventGroup_id)
    {
        $request->validate(
            [
              'termingruppe'         => 'required|max:50'
            ]
        );

        eventGroup::find($eventGroup_id)->update([
            'termingruppe'    => $request->termingruppe,
            'updated_at'      => Carbon::now()
          ]);

        return redirect('/Eventgruppe/alle')->with(
            [
                'success' => 'Die Daten von der Event Gruppe <b>' . $request->termingruppe . '</b> wurden geändert.'
            ]
        );

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\eventGroup  $eventGroup
     * @return \Illuminate\Http\Response
     */
    public function destroy(eventGroup $eventGroup)
    {
        //
    }

    public function softDelete($eventGroup_id)
    {
        $delete = eventGroup::find($eventGroup_id)->delete();
        return redirect('/Eventgruppe/alle')->with(
            [
                'success' => 'Die Event Gruppe wurde gelöscht.'
            ]
        );
    }
}
