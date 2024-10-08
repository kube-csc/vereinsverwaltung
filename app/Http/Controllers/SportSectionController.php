<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\SportSection;
use App\Models\Event;

class SportSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct() {
       $this->middleware('auth');
    }

    public function sportSectionSelected($sportSection_id)
    {
        $this->active = $sportSection_id;
    }

    public function aktiv($sportSection_id)
    {
      SportSection::find($sportSection_id)->update([
            'status'      => '2',
            'updated_at'  => Carbon::now()
          ]);
     return Redirect()->back()->with('success' , env('MENUE_ABTEILUNG').' wurde sichtbar geschaltet.');
    }

    public function inaktiv($sportSection_id)
    {
      SportSection::find($sportSection_id)->update([
            'status'      => '0',
            'updated_at'  => Carbon::now()
          ]);
     return Redirect()->back()->with('success' , env('MENUE_ABTEILUNG').' wurde unsichtbar geschaltet.');
    }

    public function start($sportSection_id)
    {
      SportSection::where('status' , '1')->update([
            'status'      => '2',
            'updated_at'  => Carbon::now()
          ]);
      SportSection::find($sportSection_id)->update([
            'status'      => '1',
            'updated_at'  => Carbon::now()
          ]);
     return Redirect()->back()->with('success' , env('MENUE_ABTEILUNG').' wurde Startseite festgelegt.');
    }

    public function sportSectionSportTeam($sportSection_id)
    {
      $sportSections = SportSection::where('sportSection_id' , NULL)->orderby('abteilung')->paginate(5);
      $sportSectionVonSportTeam = SportSection::find($sportSection_id);
      $sportTeams = SportSection::where('sportSection_id'  , $sportSection_id)->orderby('abteilung')->get();
      return view('admin.sportSection.sportSectionSportTeam')->with(
        [
          'success'               => env('MENUE_MANNSCHAFTEN').' der '.env('MENUE_ABTEILUNG').' '. $sportSectionVonSportTeam->abteilung . ' wurde selectiert.',
          'sportSectionName'      => $sportSectionVonSportTeam->abteilung,
          'sportTeams'            => $sportTeams,
          'sportSections'         => $sportSections,
          'sportSectionIdSelecte' => $sportSection_id,
         ]);
    }

    public function index()
    {
      $sportSections = SportSection::where('sportSection_id' , NULL)->orderby('abteilung')->paginate(5);
      return view('admin.sportSection.index')->with(
        [
          'sportSections' => $sportSections,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response;
     */
    public function create()
    {
      return view('admin.sportSection.create');
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
          'abteilung'                => 'required|max:45',
          'abteilungTeamBezeichnung' => 'required|max:45'
        ]
      );

      $sportSection= new sportSection(
        [
          'abteilung'                 => $request->abteilung,
          'abteilungTeamBezeichnung'  => $request->abteilungTeamBezeichnung,
          'event_id'                  => NULL,
          'sportSection_id'           => NULL,
          'bild'                      => Null,
          'filename'                  => Null,
          'status'                    => 2,
          'bearbeiter_id'             => Auth::user()->id,
          'user_id'                   => Auth::user()->id,
          'updated_at'                => Carbon::now(),
          'created_at'                => Carbon::now()
        ]
       );
      $sportSection->save();

      return redirect('/Abteilung/alle')->with(
        [
          'success' => $request->abteilung . '</b> wurde angelegt.'
        ]
      );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($sportSection_id)
    {
      $sportSection = SportSection::find($sportSection_id);

      if ($sportSection->event_id>0) {
       $event = Event::find($sportSection->event_id);
       $ausgabetext=$event->beschreibung;
       $nachtermin=$event->nachtermin;
      }
      else {
        $ausgabetext='';
        $nachtermin='';
      }

      return view('admin.sportSection.edit')->with(
           [
               'sportSection' =>  $sportSection,
               'ausgabetext'  =>  $ausgabetext,
               'nachtermin'   =>  $nachtermin,
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
    public function update(Request $request, $sportSection_id)
    {
      $request->validate(
        [
          'abteilung'                => 'required|max:45',
          'abteilungTeamBezeichnung' => 'required|max:45',
          'farbe'                    => 'max:255',
          'domain'                   => 'max:255',
        //'domain'                   => 'sometimes|url'  //ToDo: Leere Felder wird nicht akzeptiert
          'bild'                     => 'mimes:jpeg,jpg,bmp,png,gif'
        ]
      );

      SportSection::find($sportSection_id)->update([
        'abteilung'                 => $request->abteilung,
        'abteilungTeamBezeichnung'  => $request->abteilungTeamBezeichnung,
        'farbe'                     => $request->farbe,
        'domain'                    => $request->domain,
        'bearbeiter_id'             => Auth::user()->id,
        'updated_at'                => Carbon::now()
      ]);

      $messagePicture='';
      if($request->bild){
        $fileName = $request->file('bild')->getClientOriginalName();
        $extension = $request->bild->extension();
        $newPictureName = $this->saveInmage($request->bild , $sportSection_id, $extension);
        if($newPictureName <> ''){
          $sportSectionImageName=SportSection::find($sportSection_id);
          $deletePictureName=$sportSectionImageName->bild;
          if (file_exists(public_path().'/storage/header/'.$deletePictureName) && $deletePictureName!=Null){
              unlink(public_path().'/storage/header/'.$deletePictureName);
          }

          SportSection::find($sportSection_id)->update([
            'bild'          => $newPictureName,
            'filename'      => $fileName,
            'bearbeiter_id' => Auth::user()->id,
            'updated_at'    => Carbon::now()
          ]);
          $messagePicture='<br>Das Headerbild wurde hochgeladen.';
        }
        else {
          $messagePicture='<br>Das Headerbild hat ein Hochformat und könnte deswegen nicht hochgeladen werden.';
        }
      }

      if ($request->beschreibung<>'' | $request->nachtermin<>''){
        $sportSection = SportSection::find($sportSection_id);
        if ($sportSection->event_id>0) {
          Event::find($sportSection->event_id)->update([
            'beschreibung'      => $request->beschreibung,
            'nachtermin'        => $request->nachtermin,
            'bearbeiter_id'     => Auth::user()->id,
            'updated_at'        => Carbon::now()
          ]);
        }
        else{
          $createdEvent= new Event([
              'beschreibung'     => $request->beschreibung,
              'nachtermin'       => $request->nachtermin,
              'sportSection_id'  => $request->sportSection_id,
              'verwendung'       => 4,    //4 = Abteilungsbeschreibung
              'autor_id'         => Auth::user()->id,
              'bearbeiter_id'    => Auth::user()->id,
              'datumvon'         => Carbon::now(),
              'datumbis'         => Carbon::now(),
              'updated_at'       => Carbon::now(),
              'created_at'       => Carbon::now()
            ]
           );
           $createdEvent->save();

           $newEventId  = $createdEvent->id;
           SportSection::find($sportSection_id)->update([
            'event_id' => $newEventId
          ]);
        }
      }

      return redirect('/Abteilung/alle')->with(
        [
          'success' => 'Die Daten von <b>' . $request->abteilung . '</b> wurden geändert.'.$messagePicture
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

    public function softDelete($SportTeam_id)
    {
      $delete = SportSection::find($SportTeam_id)->delete();
      return redirect('/Abteilung/alle')->with(
        [
          'success' => env('MENUE_ABTEILUNG').' wurde gelöscht.'
        ]
      );
    }

    public function saveInmage($imageInput , $sportTeam_id , $extension){
     $newPictureName="header" . $sportTeam_id . "_" . str::random(4) . "." . $extension;
     Storage::disk('public')->putFileAs(
         'header/',
          $imageInput,
          $newPictureName
     );
       return $newPictureName;
    }

    public function pictureDelete($sportTeam_id){
         $reportImageName=SportSection::find($sportTeam_id);
         $deleteImageName=$reportImageName->bild;
         if (file_exists(public_path().'/storage/header/'.$deleteImageName)){
            unlink(public_path().'/storage/header/'.$deleteImageName);
         }
         SportSection::find($sportTeam_id)->update([
          'bild'          => Null,
          'filename'      => Null,
          'bearbeiter_id' => Auth::user()->id,
          'updated_at'    => Carbon::now()
         ]);
         return back()->with([
             'success' => 'Das Headerbild vom der '.env('MENUE_ABTEILUNG').' wurde gelöscht'
         ]);
    }
}
