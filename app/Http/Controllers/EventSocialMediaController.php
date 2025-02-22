<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Player;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;

class EventSocialMediaController extends Controller
{
    public function __construct(){
        $this->middleware('auth');
    }

    public function aktiv($socialMediaId)
    {
        Report::find($socialMediaId)->update([
            'visible'          => '1',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Video wurde sichtbar geschaltet.');
    }

    public function inaktiv($socialMediaId)
    {
        Report::find($socialMediaId)->update([
            'visible'          => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Video wurde unsichtbar geschaltet.');
    }

    public function webaktiv($socialMediaId)
    {
        Report::find($socialMediaId)->update([
            'webseite'         => '1',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        return Redirect()->back()->with('success' , 'Das Video wurde für den öffentlichen Bereich freigeschaltet.');
    }

    public function maxtop($socialMediaId)
    {        $report = Report::find($socialMediaId);

        // Setze ausgewählten Datensatz auf Position 10
        $report->update([
            'position' => 10,
            'bearbeiter_id' => Auth::user()->id,
            'updated_at' => Carbon::now()
        ]);

        // Alle anderen Datensätze neu durchnummerieren
        $position = 20;
        Report::where('event_id', $report->event_id)
            ->where('verwendung', 7)
            ->where('id', '!=', $socialMediaId)
            ->orderBy('position')
            ->each(function($item) use (&$position) {
                $item->update(['position' => $position]);
                $position += 10;
            });

        return Redirect()->back()->with('success' , 'Das Video wurde zur obersten Position verschoben.');
    }

    public function up($socialMediaId)
    {
        $report = Report::find($socialMediaId);

        $previousReport = Report::where('event_id', $report->event_id)
            ->where('verwendung', 7)
            ->where('position', '<', $report->position)
            ->orderBy('position', 'desc')
            ->first();

        if ($previousReport) {
            $tempPosition = $report->position;
            $report->update([
                'position' => $previousReport->position,
                'bearbeiter_id' => Auth::user()->id,
                'updated_at' => Carbon::now()
            ]);
            $previousReport->update([
                'position' => $tempPosition,
                'bearbeiter_id' => Auth::user()->id,
                'updated_at' => Carbon::now()
            ]);
        }

        return Redirect()->back()->with('success' , 'Das Video wurde eine Position nach oben verschoben.'.$previousReport->position.'-'.$report->position);
    }

    public function down($socialMediaId)
    {
        $report = Report::find($socialMediaId);

        $nextReport = Report::where('event_id', $report->event_id)
            ->where('verwendung', 7)
            ->where('position', '>', $report->position)
            ->orderBy('position')
            ->first();

        if ($nextReport) {
            $tempPosition = $report->position;
            $report->update([
                'position' => $nextReport->position,
                'bearbeiter_id' => Auth::user()->id,
                'updated_at' => Carbon::now()
            ]);
            $nextReport->update([
                'position' => $tempPosition,
                'bearbeiter_id' => Auth::user()->id,
                'updated_at' => Carbon::now()
            ]);
        }

        return Redirect()->back()->with('success' , 'Das Video wurde eine Position nach unten verschoben.');
    }

    public function maxdown($socialMediaId)
    {
        $report = Report::find($socialMediaId);

        $maxPosition = Report::where('event_id', $report->event_id)
            ->where('verwendung', 7)
            ->max('position');

        $report->update([
            'position' => $maxPosition + 10,
            'bearbeiter_id' => Auth::user()->id,
            'updated_at' => Carbon::now()
        ]);

        return Redirect()->back()->with('success' , 'Das Video wurde zur obersten Position verschoben.');
    }

    public function webinaktiv($socialMediaId)
    {
        Report::find($socialMediaId)->update([
            'webseite'         => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Video wurde für den öffentlichen Bereich unsichtbar gemacht.');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function index($event_id)
    {
        $reports = Report::where('event_id' , $event_id)
            ->where('verwendung' , 7)
            ->orderby('position')
            ->paginate(10);

        $reportMaxs = Report::where('event_id' , $event_id)
            ->where('verwendung' ,  7)
            ->orderby('position')->get();
        if($reportMaxs->count()>0){
            $reportMaxID  = $reportMaxs->last()->id;
        }
        else{
            $reportMaxID=0;
        }

        $event = event::find($event_id);

        return view('admin.eventSocialMedia.index')->with(
            [
                'reports'     => $reports,
                'reportMaxID' => $reportMaxID,
                'event'       => $event,
            ]
        );
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create($event_id)
    {
        $players = Player::all();

        return view('admin.eventSocialMedia.create' , compact('event_id', 'players'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'socialMediaTitel' => 'required|max:255',
            'socialMediaComment' => 'nullable',
            'socialMediaId' => 'required',
            'event_id' => 'required|exists:events,id'
        ]);

        $maxPosition = Report::where('event_id', $request->event_id)
            ->where('verwendung', 7)
            ->max('position') ?? 0;

        $report = new report(
            [
                'event_id'         => $request->event_id,
                'titel'            => $request->socialMediaTitel,
                'kommentar'        => $request->socialMediaComment,
                'filename'         => $request->socialMediaId,
                'player'           => $request->player,
                'visible'          => 1, // 1 = sichtbar
                'webseite'         => 1, // 0 = Interner Bereich sichtbar / 1 = Externer Bereich sichtbar
                'verwendung'       => 7, // 7 = Video id für ein Social Media Templet
                'position'         => $maxPosition + 10,
                'bearbeiter_id'    => Auth::user()->id,
                'user_id'          => Auth::user()->id,
                'updated_at'       => Carbon::now(),
                'created_at'       => Carbon::now()
            ]
        );
        $report->save();

        return redirect('/Event/SocialMedia/'.$request->event_id)->with(
            [
                'success' => 'Das Social Media Templet <b>' . $request->socialMediaTitel . '</b> wurde angelegt.'
            ]
        );
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
     * @param  int  $socialMediaId
     * @return \Illuminate\Http\Response
     */
    public function edit($socialMediaId)
    {
        $report = Report::find($socialMediaId);
        $event = Event::find($report->event_id);
        $players = Player::all();

        return view('admin.eventSocialMedia.edit' , compact('event','report', 'players'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Http\RedirectResponse|\Illuminate\Routing\Redirector
     */
    public function update(Request $request, $id)
    {
        $validatedData = $request->validate([
            'socialMediaTitel' => 'required|max:255',
            'socialMediaComment' => 'nullable',
            'socialMediaId' => 'required',
            'event_id' => 'required|exists:events,id'
        ]);

        Report::find($id)->update([
            'titel'            => $request->socialMediaTitel,
            'kommentar'        => $request->socialMediaComment,
            'filename'         => $request->socialMediaId,
            'player'           => $request->player,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        return redirect('/Event/SocialMedia/'.$request->event_id)->with(
            [
                'success' => 'Das Social Media Templet <b>' . $request->socialMediaTitel . '</b> wurde aktualisiert.'
            ]
        );
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
