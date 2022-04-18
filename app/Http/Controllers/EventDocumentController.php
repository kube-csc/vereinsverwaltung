<?php

namespace App\Http\Controllers;

use App\Models\report;
use App\Models\Event;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class EventDocumentController extends Controller
{

    public function __construct(){
        $this->middleware('auth');
    }

    public function aktiv($reportId)
    {
        report::find($reportId)->update([
            'visible'          => '1',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Dokument wurde sichtbar geschaltet.');
    }

    public function inaktiv($reportId)
    {
        report::find($reportId)->update([
            'visible'          => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Dokument wurde unsichtbar geschaltet.');
    }

    public function webaktiv($reportId)
    {
        report::find($reportId)->update([
            'webseite'         => '1',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Dokument wurde für den öffendlichen Bereich sichtbar geschaltet.');
    }

    public function webinaktiv($reportId)
    {
        report::find($reportId)->update([
            'webseite'         => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Dokument wurde für den öffendlichen Bereich unsichtbar geschaltet.');
    }

    public function start($reportId)
    {
        $report = report::find($reportId);
        $eventID=$report->event_id;

        report::where('startseite' , '1')
            ->where('event_id' , $eventID)
            ->update([
                'startseite'       => 0,
                'bearbeiter_id'    => Auth::user()->id,
                'updated_at'       => Carbon::now()
            ]);

        report::find($reportId)->update([
            'startseite'       => 1,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Dokument wurde an der Startseite festgelegt.');
    }

    public function maxtop($reportId)
    {
        report::find($reportId)->update([
            'position'         => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $report=report::find($reportId);
        $eventID=$report->event_id;

        // ToDo verbessern der Updatefunktion
        //board::all()->update(['position' => 'position']);

        $reports = report::where('event_id' , $eventID)
            ->orderby('position')
            ->get();
        $positionNew=10;
        foreach ($reports as $report){
            report::find($report->id)->update([
                'position'         => $positionNew
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Das Dokument wurde zur obersten Position verschoben.');
    }

    public function top($reportId)
    {
        $report = report::find($reportId);
        $positionNew=$report->position-11;
        $eventID=$report->event_id;

        report::find($reportId)->update([
            'position'         => $positionNew,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $positionNew=10;
        $reports = report::where('event_id' , $eventID)
            ->orderby('position')
            ->get();
        foreach ($reports as $report){
            report::find($report->id)->update([
                'position'      => $positionNew
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Das Dokument wurde eine Position nach oben verschoben.');
    }

    public function down($reportId)
    {
        // ToDo verebessern der Updatefunktion
        $report = report::find($reportId);
        $positionNew=$report->position+11;
        $eventID=$report->event_id;
        report::find($reportId)->update([
            'position'         => $positionNew,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $reports = report::where('event_id' , $eventID)
            ->orderby('position')
            ->get();
        $positionNew=10;
        foreach ($reports as $report){
            report::find($report->id)->update([
                'position'      => $positionNew
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Das Dokument wurde eine Position nach unten verschoben.');
    }

    public function maxdown($reportId)
    {
        $report  = report::find($reportId);
        $eventId = $report->event_id;

        $reports = report::where('event_id' , $eventId)
            ->where('verwendung' , '>' , '1')
            ->where('verwendung' , '<' , '6')
            ->orderby('position' , 'desc')
            ->limit(1)
            ->get();

        foreach ($reports as $report){
            $positionNew=$report->position+10;
        }

        report::find($reportId)->update([
            'position'         => $positionNew,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $reports = report::where('event_id' , $eventId)
            ->where('verwendung' , '>' , '1')
            ->where('verwendung' , '<' , '6')
            ->orderby('position')
            ->get();
        $positionNew=10;
        foreach ($reports as $report){
            report::find($report->id)->update([
                'position'  => $positionNew,
            ]);
            $positionNew=$positionNew+10;
        }

        return Redirect()->back()->with('success' , 'Das Dokument wurde zur letzten Position verschoben.');
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function index($event_id)
    {
        $reports = report::where('event_id' , $event_id)
            ->where('verwendung' , '>' , '1')
            ->where('verwendung' , '<' , '6')
            ->orderby('position')
            ->paginate(5);

        $reportMaxs = report::where('event_id' , $event_id)
            ->where('verwendung' , '>' , '1')
            ->where('verwendung' , '<' , '6')
            ->orderby('position')->get();
        if($reportMaxs->count()>0){
            $reportMaxID  = $reportMaxs->last()->id;
        }
        else{
            $reportMaxID=0;
        }

        $event = event::find($event_id);

        return view('admin.eventDocument.index')->with(
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
        return view('admin.eventDocument.create' , compact('event_id'));
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
                'documentName' => 'required|max:45'
            ]
        );

        $reports = report::where('event_id' , $request->event_id)
            ->where('verwendung' , '>' , '1')
            ->where('verwendung' , '<' , '6')
            ->orderby('position' , 'desc')
            ->limit(1)
            ->get();

        $positionNew=10;
        foreach ($reports as $report){
            $positionNew=$report->position+10;
        }

        $report= new report(
            [
                'titel'            => $request->documentName,
                'verwendung'       => $request->verwendung,
                'visible'          => 1,
                'webseite'         => 1,
                'position'         => $positionNew,
                'event_id'         => $request->event_id,
                'bearbeiter_id'    => Auth::user()->id,
                'user_id'          => Auth::user()->id,
                'updated_at'       => Carbon::now(),
                'created_at'       => Carbon::now()
            ]
        );
        $report->save();

        return redirect('EventDokumente/'.$request->event_id)->with(
            [
                'success' => 'Das Dokument <b>' . $request->documentName . ' wurden angelegt.'
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
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($reportId)
    {
        $report = report::find($reportId);
        $event  = event::find($report->event_id);

        return view('admin.eventDocument.edit')->with(
            [
                'report' =>  $report,
                'event'  =>  $event,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $reportId)
    {
        $request->validate(
            [
                'reportTitleDocument'   => 'required|max:45',
                'documentFile'          => 'mimes:jpeg,jpg,bmp,png,gif,pdf,doc,docx,xls,xlsx,odt'
            ]
        );

        $messageDocument='';
        if($request->documentFile){

            $reportDocumentName=report::find($reportId);
            $deleteDocumentName=$reportDocumentName->bild;
            if (file_exists(public_path().'/storage/eventDokumente/'.$deleteDocumentName) && $deleteDocumentName!=Null){
                unlink(public_path().'/storage/eventDokumente/'.$deleteDocumentName);
            }

            // Note: Ist überfüssig wenn keine alten Daten übernommen wurden
            $deleteDocumentName=$reportDocumentName->image;
            if (file_exists(public_path().'/daten/text/'.$deleteDocumentName) && $deleteDocumentName!=Null && $reportDocumentName->image!=''){
                unlink(public_path().'/daten/text/'.$deleteDocumentName);
            }

            $extension = $request->documentFile->extension();

            $typOption=[
                'jpg'  => '1',
                'gif'  => '2',
                'png'  => '3',
                'bmp'  => '4',
                'jpeg' => '5',
                'pdf'  => '10',
                'doc'  => '11',
                'xls'  => '12',
                'xlsx' => '13',
                'docx' => '14',
                'odt'  => '15'
            ];

            $typ= $typOption[$extension];

            if(!isset($typ)){
                dd('Abbfruch flashcer Extension');
            }

            $fileName=$request->file('documentFile')->getClientOriginalName();
            $newDocumentName = 'dokument' . $reportId . '_' . str::random(4) . '.' . $extension;
            Storage::disk('public')->putFileAs(
                'eventDokumente/',
                $request->documentFile,
                $newDocumentName
            );

            if($newDocumentName<>''){
                report::find($reportId)->update([
                    'bild'           => $newDocumentName,
                    'filename'       => $fileName,
                    'typ'            => $typ
                 ]);
                $messageDocument='<br>Das Datei '. $fileName .' wurde hochgeladen.';
            }
        }

        report::find($reportId)->update([
            'titel'            => $request->reportTitleDocument,
            'kommentar'        => $request->reportDocumentComment,
            'verwendung'       => $request->verwendung,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $report=report::find($reportId);

        return redirect('/EventDokumente/'.$report->event_id)->with(
            [
                'success' => 'Die Daten von der Datei <b>' . $request->titel . '</b> wurden geändert.'.$messageDocument
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($document_id)
    {
        $report = report::find($document_id);
        $report->delete();

        $reports = report::where('event_id' , $report->event_id)
            ->where('verwendung' , '>' , '1')
            ->where('verwendung' , '<' , '6')
            ->orderby('position')
            ->paginate(5);

        $reportMaxs = report::where('event_id' , $report->event_id)
            ->where('verwendung' , '>' , '1')
            ->where('verwendung' , '<' , '6')
            ->orderby('position')->get();
        if($reportMaxs->count()>0){
            $reportMaxID=$reportMaxs->last()->id;
        }
        else{
            $reportMaxID=0;
        }

        $event = event::find($report->event_id);

        return view('admin.eventDocument.index')->with(
            [
                'reports'     =>  $reports,
                'reportMaxID' => $reportMaxID,
                'event'       =>  $event
            ]
        );
    }

    public function dokumentedelete($documentId)
    {
        $deleteDocumentFile = report::find($documentId);
        if(isset($deleteDocumentFile->bild)){
            Storage::disk('public')->delete('eventDokumente/'.$deleteDocumentFile->bild);
        }
        report::find($documentId)->update(
            [
                'bild'             => Null,
                'filename'         => Null,
                'typ'              => Null,
                'bearbeiter_id'    => Auth::user()->id,
                'updated_at'       => Carbon::now()
            ]);

        return redirect('EventDokumente/'.$deleteDocumentFile->event_id)->with(
            [
                'success'  => 'Die Datei <b>' . $deleteDocumentFile->filename . '</b> wurden gelöscht.'
            ]
        );
    }

    public function dokumenteAltdelete($documentId)
    {
        $deleteDocumentFile = report::find($documentId);
        $deleteDocumentName=$deleteDocumentFile->image;
        $deleteDocumentFilename=$deleteDocumentFile->filename;
        if (file_exists('../public/daten/text/'.$deleteDocumentName)) {
            unlink('../public/daten/text/' . $deleteDocumentName);
        }

        report::find($documentId)->update(
            [
                'image'         => Null,
                'filename'      => Null,
                'typ'           => Null,
                'bearbeiter_id' => Auth::user()->id,
                'updated_at'    => Carbon::now()
            ]);

        return redirect('EventDokumente/'.$deleteDocumentFile->event_id)->with(
            [
                'success'  => 'Die Datei <b>' . $deleteDocumentFilename . '</b> wurden gelöscht.'
            ]
        );
    }
}
