<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\report;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Auth;

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
        report::find($reportId)->update([
            'visible'          => '1',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Bild wurde sichtbar geschaltet.');
    }

    public function inaktiv($reportId)
    {
        report::find($reportId)->update([
            'visible'          => '0',
            'bearbeiter_id'    => Auth::user()->id,
            'updated_at'       => Carbon::now()
        ]);
        return Redirect()->back()->with('success' , 'Bild wurde unsichtbar geschaltet.');
    }

    public function index($event_id)
    {
        $reports = report::where('event_id' , $event_id)->orderby('position')->paginate(5);;

        return view('admin.report.index')->with(
            [
                'reports' =>  $reports,
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
            $newPictureName=$this->saveInmage($request->image , $request->event_id);
            $fileName=$request->file('image')->getClientOriginalName();

            //ToDo: Die widht und height Bearbeitung in die Funktion auslagern
            $newImage = Image::make($request->image);
            $width  = $newImage->width();
            $height = $newImage->height();
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
                        'bearbeiter_id'    => Auth::user()->id,
                        'user_id'          => Auth::user()->id,
                        'updated_at'       => Carbon::now(),
                        'created_at'       => Carbon::now()
                    ]
                );
                $report->save();

                $messagePicture='<br>Das Headerbild wurde hochgeladen.';
            }
            else {
                $messagePicture='<br>Das Headerbild hat ein Hochformat und könnte deswegen nicht hochgeladen werden.';
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
        $report = report::find($reportId);

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

        $reportImageName=report::find($reportId);
        $deletePictureName=$reportImageName->bild;
         if (file_exists(public_path().'/storage/eventImage/'.$deletePictureName) && $deletePictureName!=Null){
            unlink(public_path().'/storage/eventImage/'.$deletePictureName);
        }

        $messagePicture='';
        if($request->image){
            $newPictureName=$this->saveInmage($request->image , $reportId);
            $fileName=$request->file('image')->getClientOriginalName();

            //ToDo: Die widht und height Bearbeitung in die Funktion auslagern
            $newImage = Image::make($request->image);
            $width  = $newImage->width();
            $height = $newImage->height();

              if($newPictureName<>''){
                 report::find($reportId)->update([
                    'bild'         => $newPictureName,
                    'filename'     => $fileName,
                    'pixx'         => $width,
                    'pixy'         => $height,
                ]);
                $messagePicture='<br>Das Bild '. $fileName .' wurde hochgeladen.';
            }
        }

        report::find($reportId)->update([
         'titel'            => $request->reportTitleImage,
         'kommentar'        => $request->reportImageComment,
         'bearbeiter_id'    => Auth::user()->id,
         'updated_at'       => Carbon::now()
        ]);

        $report=report::find($reportId);

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

    //Bilder speichern
    public function saveInmage($imageInput , $event_id){
        $newPictureName="eventBild".$event_id. '_' . str::random(4) . '.jpg';

        /*
        $newImage = Image::make($imageInput);
        $width  = $newImage->width();
        $height = $newImage->height();

         //ToDo: Die widht und height Bearbeitung in die Funktion auslagern
        $newPicture=
            [
                'newPictureName' => $newPictureName,
                'width'          => $width,
                'height'         => $height,
            ];
        */

            Image::make($imageInput)
                //->widen(2050)
                ->save(public_path().'/storage/eventImage/'.$newPictureName);
            // ToDo: Bilderbreite?

            return  $newPictureName;
       }

    // Bilder von reportImage löschen
    public function pictureDelete($reportID){
        $reportImageName=report::find($reportID);
        $deletePictureName=$reportImageName->bild;
        $deletePictureFilename=$reportImageName->filename;
        if (file_exists(public_path().'/storage/eventImage/'.$deletePictureName)){
            unlink(public_path().'/storage/eventImage/'.$deletePictureName);
        }
        report::find($reportID)->update([
            'bild'      => Null,
            'pixx'      => 0,
            'pixy'      => 0,
            'filename'  => Null,
        ]);

        return back()->with([
            'success' => 'Das Bild'. $deletePictureFilename .' vom Event wurde gelöscht'
        ]);
    }
}
