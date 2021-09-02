<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\eventGroup;
use App\Models\SportSection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function index()
    {
        $events = event::where([
            ['verwendung' , '0'],
            ['datumbis' ,'>=', Carbon::now()]
          ])
            ->orderby('datumbis')
            ->paginate(5);

        return view('admin.event.index')->with([
            'events' => $events
          ]);
    }

    public function indexPast()
    {
        $events = event::where([
             ['verwendung' , '0'],
             ['datumbis' ,'<=', Carbon::now()]
           ])
             ->orderby('datumbis')
             ->paginate(5);
        return view('admin.event.index')->with([
            'events' => $events
          ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        $sportSections = SportSection::where('status' , '>' ,'0')->orderby('status')
                                                                 ->orderby('sportSection_id')
                                                                 ->orderby('abteilung')
                                                                 ->get();
        $eventGroups = eventGroup::orderby('termingruppe')
            ->get();
        return view('admin.event.create' , compact('sportSections' , 'eventGroups'));
    }

    public function createSportSection($sportSection_id)
    {
        return view('admin.event.create' , compact('sportSection_id'));
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
              'ueberschrift'   => 'required|max:50',
              'datumvon'       => 'required|date',
              'datumbis'       => 'required|date|after_or_equal:datumvon'
            ]
        );

        $event= new event([
            'ueberschrift'     => $request->ueberschrift,
            'datumvon'         => $request->datumvon,
            'datumbis'         => $request->datumbis,
            'beschreibung'     => $request->beschreibung,
            'nachtermin'       => $request->nachbericht,
            'sportSection_id'  => $request->sportSection_id,
            'eventGroup_id'    => $request->eventGroup_id,
            'bearbeiter_id'    => Auth::user()->id,
            'autor_id'         => Auth::user()->id,
            'updated_at'       => Carbon::now(),
            'created_at'       => Carbon::now()
           ]
        );
        $event->save();

        return redirect('/Event/alle')->with([
              'success' => 'Das Event <b>' . $request->ueberschrift . '</b> wurde angelegt.'
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function show(Event $event)
    {
      //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function edit($event_id)
    {
        $event = Event::find($event_id);
        $sportSections = SportSection::where('status' , '>' ,'0')->orderby('status')
                                                                 ->orderby('sportSection_id')
                                                                 ->orderby('abteilung')
                                                                 ->get();

        $eventGroups = eventGroup::orderby('termingruppe')
            ->get();
        return view('admin.event.edit' , compact('event' , 'sportSections' , 'eventGroups'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $event_id)
    {
        $request->validate([
              'ueberschrift'         => 'required|max:50',
              'datumvon'             => 'required|date',
              'datumbis'             => 'required|date|after_or_equal:datumvon'
            ]
        );
     // todo: sportSection_id bearbeiten
        Event::find($event_id)->update([
            'ueberschrift'     => $request->ueberschrift,
            'datumvon'         => $request->datumvon,
            'datumbis'         => $request->datumbis,
            'beschreibung'     => $request->beschreibung,
            'nachtermin'      => $request->nachbericht,
            'sportSection_id'  => $request->sportSection_id,
            'eventGroup_id'    => $request->eventGroup_id,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
           ]
        );

        return redirect('/Event/alle')->with([
              'success' => 'Die Daten von dem Event <b>' . $request->ueberschrift . '</b> wurden geändert.'
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Event  $event
     * @return \Illuminate\Http\Response
     */
    public function destroy(Event $event)
    {
        //
    }
}
