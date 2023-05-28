<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Report;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

// Wird nicht mehr benötigt
use Intervention\Image\Facades\Image;


class ReportController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
        $this->middleware('auth');
    }

    public function aktiv($reportId)
    {
        Report::find($reportId)->update([
            'visible'          => '1',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Bild wurde sichtbar geschaltet.');
    }

    public function inaktiv($reportId)
    {
        Report::find($reportId)->update([
            'visible'          => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Bild wurde unsichtbar geschaltet.');
    }

    public function webaktiv($reportId)
    {
        Report::find($reportId)->update([
            'webseite'         => '1',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Bild wurde für den öffendlichen Bereich sichtbar geschaltet.');
    }

    public function webinaktiv($reportId)
    {
        Report::find($reportId)->update([
            'webseite'         => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Bild wurde für den öffendlichen Bereich unsichtbar geschaltet.');
    }

    public function start($reportId)
    {
        $report = Report::find($reportId);
        $eventID=$report->event_id;

        Report::where('startseite' , '1')
            ->where('event_id' , $eventID)
            ->update([
                'startseite'       => 0,
                'bearbeiter_id'    => Auth::user()->id,
                'updated_at'       => Carbon::now()
            ]);

        Report::find($reportId)->update([
            'startseite'       => 1,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Das Bild wurde an der Startseite festgelegt.');
    }

    public function maxtop($reportId)
    {
        Report::find($reportId)->update([
            'position'         => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $report=Report::find($reportId);
        $eventId=$report->event_id;

        // ToDo verbessern der Updatefunktion
        //board::all()->update(['position' => 'position']);

        $reports = Report::where('event_id' , $eventId)
            ->where(function ($query) use ($eventId) {
                $query->where('verwendung' , '1')
                    ->orwhere('verwendung' , NULL);
            })
            ->orderby('position')
            ->get();
        $positionNew=10;
        foreach ($reports as $report){
            Report::find($report->id)->update([
                'position'         => $positionNew
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Das Bild wurde zur obersten Position verschoben.');
    }

    public function top($reportId)
    {
        $report = Report::find($reportId);
        $positionNew=$report->position-11;
        $eventId=$report->event_id;

        Report::find($reportId)->update([
            'position'         => $positionNew,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $positionNew=10;
        $reports = Report::where('event_id' , $eventId)
            ->where(function ($query) use ($eventId) {
                $query->where('verwendung' , '1')
                    ->orwhere('verwendung' , NULL);
            })
            ->orderby('position')
            ->get();
        foreach ($reports as $report){
            Report::find($report->id)->update([
                'position'      => $positionNew
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Das Bild wurde eine Position nach oben verschoben.');
    }

    public function down($reportId)
    {
        // ToDo verebessern der Updatefunktion
        $report = Report::find($reportId);
        $positionNew=$report->position+11;
        $eventId=$report->event_id;
        Report::find($reportId)->update([
            'position'         => $positionNew,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $reports = Report::where('event_id' , $eventId)
            ->where(function ($query) use ($eventId) {
                $query->where('verwendung' , '1')
                    ->orwhere('verwendung' , NULL);
            })
            ->orderby('position')
            ->get();
        $positionNew=10;
        foreach ($reports as $report){
            Report::find($report->id)->update([
                'position'      => $positionNew
            ]);
            $positionNew=$positionNew+10;
        }
        return Redirect()->back()->with('success' , 'Das Bild wurde eine Position nach unten verschoben.');
    }

    public function maxdown($reportId)
    {
        $report  = Report::find($reportId);
        $eventId = $report->event_id;

        $reports = Report::where('event_id' , $eventId)
            ->where(function ($query) use ($eventId) {
                $query->where('verwendung' , '1')
                    ->orwhere('verwendung' , NULL);
            })
            ->orderby('position' , 'desc')
            ->limit(1)
            ->get();
        foreach ($reports as $report){
            $positionNew=$report->position+10;
        }

        Report::find($reportId)->update([
            'position'         => $positionNew,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $reports = Report::where('event_id' , $eventId)
            ->where(function ($query) use ($eventId) {
                $query->where('verwendung' , '1')
                    ->orwhere('verwendung' , NULL);
            })
            ->orderby('position')
            ->get();
        $positionNew=10;
        foreach ($reports as $report){
            Report::find($report->id)->update([
                'position'      => $positionNew,
            ]);
            $positionNew=$positionNew+10;
        }

        return Redirect()->back()->with('success' , 'Das Bild wurde zur letzten Position verschoben.');
    }

    public function index($event_id)
    {
        $reports = Report::where('event_id' , $event_id)
            ->where('verwendung' , '1')
            ->orderby('position')
            ->paginate(5);

        return view('admin.report.index')->with(
            [
                'reports' => $reports,
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
        return view('admin.report.create',compact('event_id'));
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
                'reportTitleImage'   => 'required|max:45',
            //  'image'              => 'mimes:jpeg,jpg,bmp,png,gif' //ToDo: Abfrage Image einfügen
            ]
        );

        $messagePicture='';
        if($request->image){
            $fileName=$request->file('image')->getClientOriginalName();
            $extension = $request->image->extension();
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
                dd('Abbruch falscher Extension');
            }

            //ToDo: Die width und height Bearbeitung in die Funktion auslagern
            //$newImage = Image::make($request->image);
            //$width  = $newImage->width();
            //$height = $newImage->height();

            $width  = 0;
            $height = 0;

            $newPictureName=$this->saveInmage($request->image , $request->event_id , $extension);

            if($newPictureName<>''){
                $report = new report(
                    [
                        'event_id'         => $request->event_id,
                        'titel'            => $request->reportTitleImage,
                        'kommentar'        => $request->reportImageComment,
                        'bild'             => $newPictureName,
                        'pixx'             => $width,
                        'pixy'             => $height,
                        'filename'         => $fileName,
                        'visible'          => 1,
                        'verwendung'       => 1, // 1 = Es ist ein Bild
                        'typ'              => $typ,
                        'bearbeiter_id'    => Auth::user()->id,
                        'user_id'          => Auth::user()->id,
                        'updated_at'       => Carbon::now(),
                        'created_at'       => Carbon::now()
                    ]
                );
                $report->save();

                $messagePicture='<br>Das Bild wurde hochgeladen.';
            }
            else {
                $messagePicture='<br>Kein Bild vorhanden.';
            }
        }

        return redirect('/Event/alle')->with(
            [
                'success' => 'Das Bild <b>' . $request->reportTitleImage . '</b> wurde angelegt.'.$messagePicture
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\report  $report
     * @return \Illuminate\Http\Response
     */
    public function show(report $report)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\report  $report
     * @return \Illuminate\Http\Response
     */
    public function edit($reportId)
    {
        $report = Report::find($reportId);

        return view('admin.report.edit')->with(
            [
                'report' =>  $report,
            ]
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\report  $report
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $reportId)
    {
        $request->validate(
            [
                'reportTitleImage'   => 'required|max:45',
                //  'image'              => 'mimes:jpeg,jpg,bmp,png,gif' //ToDo: Abfrage Image einfügen
            ]
        );

        $messagePicture='';
        if($request->image){

            $reportImageName=Report::find($reportId);
            $deletePictureName=$reportImageName->bild;
            if (file_exists(public_path().'/storage/eventImage/'.$deletePictureName) && $deletePictureName!=Null){
                unlink(public_path().'/storage/eventImage/'.$deletePictureName);
            }

            $fileName=$request->file('image')->getClientOriginalName();
            $extension = $request->image->extension();
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
                dd('Abbruch falscher Extension');
            }

            //ToDo: Die width und height Bearbeitung in die Funktion auslagern
            /*
            $newImage = Image::make($request->image);
            $width  = $newImage->width();
            $height = $newImage->height();
            */

            $width  = 0;
            $height = 0;

            $newPictureName=$this->saveInmage($request->image , $request->event_id , $extension);

            if($newPictureName<>''){
                Report::find($reportId)->update([
                    'bild'         => $newPictureName,
                    'filename'     => $fileName,
                    'verwendung'   => 1,   // DoTo:: Wird verwendet weil bei der Speicherung von Bilder der Wert 1 vergessen wurde
                    'pixx'         => $width,
                    'pixy'         => $height,
                ]);
                $messagePicture='<br>Das Bild '. $fileName .' wurde hochgeladen.';
            }
        }

        Report::find($reportId)->update([
            'titel'            => $request->reportTitleImage,
            'kommentar'        => $request->reportImageComment,
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);

        $report=Report::find($reportId);

        return redirect('/Bericht/alle/'.$report->event_id)->with(
            [
                'success' => 'Die Daten von dem Bild <b>' . $request->titel . '</b> wurden geändert.'.$messagePicture
            ]
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\report  $report
     * @return \Illuminate\Http\Response
     */
    public function destroy(report $report)
    {
        //
    }

    public function saveInmage($imageInput , $event_id , $extension){
        /*
        Image::make($imageInput)
            ->save(public_path().'/storage/eventImage/'.$newPictureName);
        */

        $newPictureName="eventBild" . $event_id . "_" . str::random(4) . "." . $extension;
        Storage::disk('public')->putFileAs(
            'eventImage/',
            $imageInput,
            $newPictureName
        );

        return  $newPictureName;
    }

    // Bilder von reportBilder löschen
    public function pictureDelete($reportID){
        $reportImageName=Report::find($reportID);
        $deletePictureName=$reportImageName->bild;
        $deletePictureFilename=$reportImageName->filename;
        if (file_exists(public_path().'/storage/eventImage/'.$deletePictureName)){
            unlink(public_path().'/storage/eventImage/'.$deletePictureName);
        }
        Report::find($reportID)->update([
            'bild'      => Null,
            'pixx'      => 0,
            'pixy'      => 0,
            'filename'  => Null,
        ]);

        return back()->with([
            'success' => 'Das Bild'. $deletePictureFilename .' vom Event wurde gelöscht.'
        ]);
    }

    // Bilder von reportImage löschen es sind die Bilder vom alten Ablageort
    // Note: Ist überfüssig wenn keine alten daten übernommen wurden
    public function imageDelete($reportID){
        $reportImageName=Report::find($reportID);
        $deletePictureName=$reportImageName->image;
        $deletePictureFilename=$reportImageName->filename;
        if (file_exists('../public/daten/bilder/'.$deletePictureName)){
            unlink('../public/daten/bilder/'.$deletePictureName);
        }
        Report::find($reportID)->update([
            'image'     => Null,
            'pixx'      => 0,
            'pixy'      => 0,
            'filename'  => Null,
        ]);

        return back()->with([
            'success' => 'Das Bild'. $deletePictureFilename .' vom Event wurde gelöscht.'
        ]);
    }

    public function takeover()
    {
        $reports = Report::where('image', Null)
            ->where('bild', '!=', '')
            ->limit(2)
            ->get();

        foreach ($reports as $report) {

            $newordner = date('Y', strtotime($report->created_at));

            /*
                        Report::find($reportID)->update([
                            'image'     => $newImage,
                            'ordner'   => $newOrdner
                        ]);
            */
        }

        return view('admin.report.takeover')->with(
            [
                'reports' =>  $reports,
            ]
        );
    }
}
