<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\eventGroup;
use App\Models\SportSection;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class EventController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\Http\Response
     */
    public function __construct(){
        $this->middleware('auth');
    }

    public function index()
    {
        $events = event::where([
            ['verwendung' , '0'],
            ['datumbis' ,'>=', Carbon::now()->toDateString()]
          ])
            ->orderby('datumvon')
            ->paginate(5);

        return view('admin.event.index')->with([
            'events' => $events
          ]);
    }

    public function indexPast()
    {
        $events = event::where([
             ['verwendung' , '0'],
             ['datumbis' ,'<=', Carbon::now()->toDateString()]
           ])
             ->orderby('datumvon' , 'desc')
             ->paginate(5);

        return view('admin.event.index')->with([
            'events' => $events
          ]);
    }

    public function IndexRegatta()
    {
        $events = event::where([
            ['id' , '!=' , Session::get('regattaSelectId')],
            ['verwendung', '0'],
            ['regatta', '1']
        ])
            ->orderby('datumbis', 'desc')
            ->paginate(5);

        return view('admin.event.indexRegatta')->with([
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
        $sportSections = SportSection::where('status' , '>' ,'0')->orderby('status')
            ->orderby('sportSection_id')
            ->orderby('abteilung')
            ->get();

        $eventGroups = eventGroup::orderby('termingruppe')
            ->get();

        return view('admin.event.create')->with(
            [
                'sportSection_id'  => $sportSection_id,
                'sportSections'    => $sportSections,
                'eventGroups'      => $eventGroups,
            ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $request->validate([
              'ueberschrift'     => 'required|max:50',
              'datumvon'         => 'required|date',
              'datumbis'         => 'required|date|after_or_equal:datumvon',
              'datumvona'        => 'nullable|date',
              'datumbisa'        => 'nullable|date|after_or_equal:datumvona',
              'ansprechpartner'  => 'max:50',
              'telefon'          => 'max:25',
              'email'            => 'max:50',
              'homepage'         => 'max:255',
            ]
        );

        $event= new event([
            'ueberschrift'     => $request->ueberschrift,
            'datumvon'         => $request->datumvon,
            'datumbis'         => $request->datumbis,
            'datumvona'        => $request->datumvona,
            'datumbisa'        => $request->datumbisa,
            'beschreibung'     => $request->beschreibung,
            'anmeldetext'      => $request->anmeldetext,
            'emailAntwort'     => $request->emailAntwort,
            'ansprechpartner'  => $request->ansprechpartner,
            'telefon'          => $request->telefon,
            'email'            => $request->email,
            'homepage'         => $request->homepage,
            'nachtermin'       => $request->nachbericht,
            'sportSection_id'  => $request->sportSection_id,
            'eventGroup_id'    => $request->eventGroup_id,
            'verwendung'       => '0',
            'bearbeiter_id'    => Auth::id(),
            'autor_id'         => Auth::id(),
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $event_id)
    {
        $request->validate([
                'ueberschrift'         => 'required|max:50',
                'datumvon'             => 'required|date',
                'datumbis'             => 'required|date|after_or_equal:datumvon',
                'datumvona'            => 'nullable|date',
                'datumbisa'            => 'nullable|date|after_or_equal:datumvona',
                'ansprechpartner'      => 'max:50',
                'telefon'              => 'max:25',
                'email'                => 'max:50',
                'homepage'             => 'max:255',
            ]
        );

        if($request->regatta == Null){
            $request->regatta = 0;
        }

        Event::find($event_id)->update([
            'ueberschrift'     => $request->ueberschrift,
            'datumvon'         => $request->datumvon,
            'datumbis'         => $request->datumbis,
            'datumvona'        => $request->datumvona,
            'datumbisa'        => $request->datumbisa,
            'beschreibung'     => $request->beschreibung,
            'anmeldetext'      => $request->anmeldetext,
            'emailAntwort'     => $request->emailAntwort,
            'ansprechpartner'  => $request->ansprechpartner,
            'einverstaendnis'  => $request->einverstaendnis,
            'telefon'          => $request->telefon,
            'email'            => $request->email,
            'homepage'         => $request->homepage,
            'nachtermin'       => $request->nachbericht,
            'sportSection_id'  => $request->sportSection_id,
            'eventGroup_id'    => $request->eventGroup_id,
            'bearbeiter_id'    => Auth::id(),
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

    public function regattaAktivSelect()
    {
        $events = event::where([
            'verwendung'  => '0',
            'regatta'  => Null,
         ])
            ->orderby('datumvon' , 'desc')
            ->paginate(5);

            return view('admin.event.indexRegattaAktiv')->with([
            'events' => $events
        ]);
    }

    public function regattaAktiv(Request $request, $event_id)
    {
        Event::find($event_id)->update([
                'regatta' => 1,
                'bearbeiter_id' => Auth::id(),
                'updated_at' => Carbon::now()
            ]
        );

        return redirect('/Regatta/alle')->with([
                'success' => 'Für das Event wurde die Regattaverwaltung aktiviert.'
            ]
        );
    }

    public function regattaInaktiv(Request $request, $event_id)
    {
        Event::find($event_id)->update([
                'regatta' => 0,
                'bearbeiter_id' => Auth::id(),
                'updated_at' => Carbon::now()
            ]
        );

        return redirect('/Event/alle')->with([
                'success' => 'Für das Event wurde die Regattaverwaltung deaktiviert.'
            ]
        );
    }

    public function selectRegatta($event_id)
    {
        $eventSelect=event::find($event_id);

        Session::put('regattaSelectId' , $event_id);
        Session::put('regattaSelectUeberschrift' , $eventSelect->ueberschrift);
        Session::put('regattaSelectRaceDate' , $eventSelect->datumvon);
        Session::put('regattaSelectRaceDateForm' , $eventSelect->datumvon);
        Session::put('regattaSelectRaceDateUntil' , $eventSelect->datumbis);

        return view('regattaManagement.regattaMenu');
    }
}
