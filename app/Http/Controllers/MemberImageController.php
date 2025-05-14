<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Document;
use App\Models\Event;
use App\Models\Report;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MemberImageController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Contracts\View\View|\Illuminate\View\View
     */
    public function create($event_id)
    {
        $footerDocuments = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('visible' , 1)
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();

        $event= Event::find($event_id);

        return view('memberImage.create')->with([
            'event' => $event,
            'footerDocuments' => $footerDocuments,
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
        $event= Event::find($request->event_id);
        $controlNumber = $event->password;

        // Validate the request
        $request->validate([
            'image'    => 'required|image|mimes:jpeg,png,jpg,gif|max:2048',
            'hashtag'  => 'string|max:255',
            'controlNumber' => ['required', function ($attribute, $value, $fail) use ($controlNumber) {
                if (strcasecmp($value, $controlNumber) !== 0) {
                    $fail('Die Kontrollzahl ist ungültig.');
                }
            }],
        ]);

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

            $newPictureName = $this->saveInmage($request->image, $event->id, $extension);
        }

        if($newPictureName<>'') {
            $report = new report(
                [
                    'event_id'      => $event->id,
                    'titel'         => $event->ueberschrift,
                    'hashtag'       => $request->hashtag,
                    'bild'          => $newPictureName,
                    //'pixx'             => $width,
                    //'pixy'             => $height,
                    'filename'      => $fileName,
                    'visible'       => 1,
                    'verwendung'    => 1, // 1 = Es ist ein Bild
                    'webseite'      => 1,
                    'typ'           => $typ,
                    'bearbeiter_id' => 1,
                    'user_id'       => 1,
                    'updated_at'    => Carbon::now(),
                    'created_at'    => Carbon::now()
                ]
            );
            $report->save();
        }

        return redirect('/Bericht/'.$request->event_id)->with([
                'success' => 'Das Bild wurde erfolgreich hochgeladen.'
            ]);
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
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //
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

    public function saveInmage($imageInput , $regattaTeam_id , $extension){

        $newPictureName="eventImage" . $regattaTeam_id . "_" . \Illuminate\Support\Str::random(4) . "." . $extension;
        Storage::disk('public')->putFileAs(
            'eventImage/',
            $imageInput,
            $newPictureName
        );

        return  $newPictureName;
    }
}
