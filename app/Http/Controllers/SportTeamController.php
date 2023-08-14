<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;
use App\Models\SportSection;
use App\Models\Event;

class SportTeamController extends Controller
{
  public $active;

 /**
  * Display a listing of the resource.
  *
  * @return \Illuminate\Http\Response
  */

 public function __construct(){
    $this->middleware('auth');
 }

 public function aktiv($sportTeam_id)
 {
   SportSection::find($sportTeam_id)->update([
         'status'      => '2',
         'updated_at'  => Carbon::now()
       ]);
  return Redirect()->back()->with('success' , env('MENUE_MANNSCHAFTEN').' wurde sichtbar geschaltet.');
 }

 public function inaktiv($sportTeam_id)
 {
   SportSection::find($sportTeam_id)->update([
         'status'      => '0',
         'updated_at'  => Carbon::now()
       ]);
  return Redirect()->back()->with('success' , env('MENUE_MANNSCHAFTEN').' wurde unsichtbar geschaltet.');
 }

 public function start($sportTeams_id)
 {
   SportSection::where('status' , '1')->update([
         'status'      => '2',
         'updated_at'  => Carbon::now()
       ]);
   SportSection::find($sportTeams_id)->update([
         'status'      => '1',
         'updated_at'  => Carbon::now()
       ]);
  return Redirect()->back()->with('success' , env('MENUE_MANNSCHAFTEN').' wurde Startseite festgelegt.');
 }

 public function index()
 {
   $sportTeams = SportSection::where('sportSection_id' , '>' , '0')->orderby('abteilung')->paginate(5);
   return view('admin.sportTeam.index')->with(
     [
       'sportTeams' => $sportTeams,
     ]);
 }

 /**
  * Show the form for creating a new resource.
  *
  * @return \Illuminate\Http\Response;
  */
 public function create($sportSection_id)
 {
    return view('admin.sportTeam.create',compact('sportSection_id'));
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
         'mannschaft'                => 'required|max:45',
         'mannschaftTeamBezeichnung' => 'required|max:45'
     ]
    );

    $sportTeam= new sportSection(
     [
       'abteilung'                 => $request->mannschaft,
       'abteilungTeamBezeichnung'  => $request->mannschaftTeamBezeichnung,
       'event_id'                  => NULL,
       'sportSection_id'           => $request->sportSection_id,
       'bild'                      => Null,
       'filename'                  => Null,
       'status'                    => 2,
       'bearbeiter_id'             => Auth::user()->id,
       'user_id'                   => Auth::user()->id,
       'updated_at'                => Carbon::now(),
       'created_at'                => Carbon::now()
     ]
    );
    $sportTeam->save();

   return redirect('/Mannschaft/alle')->with(
     [
       'success' => env('MENUE_MANNSCHAFTEN').' <b>' . $request->mannschaft . '</b> wurde angelegt.'
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
 public function edit($sportTeam_id)
 {
   $sportTeam = SportSection::find($sportTeam_id);

   if ($sportTeam->event_id>0) {
    $event = Event::find($sportTeam->event_id);
    $ausgabetext=$event->beschreibung ;
    $nachtermin=$event->nachtermin;
   }
   else {
     $ausgabetext='';
     $nachtermin='';
   }

   return view('admin.sportTeam.edit')->with(
       [
           'sportTeam'    =>  $sportTeam,
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
 public function update(Request $request, $sportTeam_id)
 {
   $request->validate(
     [
       'abteilung'                => 'required|max:45',
       'abteilungTeamBezeichnung' => 'required|max:45',
       'farbe'                    => 'max:255',
       'domain'                   => 'max:255',
       //'domain'                 => 'sometimes|url'  //ToDo: Lehre Felder wird nicht akzepiert
       'bild'                     => 'mimes:jpeg,jpg,bmp,png,gif'
     ]
   );

   SportSection::find($sportTeam_id)->update([
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
       $newPictureName = $this->saveInmage($request->bild , $sportTeam_id, $extension);
       if($newPictureName <> ''){
           $sportSectionImageName=SportSection::find($sportTeam_id);
           $deletePictureName=$sportSectionImageName->bild;
           if (file_exists(public_path().'/storage/header/'.$deletePictureName) && $deletePictureName!=Null){
               unlink(public_path().'/storage/header/'.$deletePictureName);
           }
       SportSection::find($sportTeam_id)->update([
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
     $sportTeam = sportSection::find($sportTeam_id);
     if ($sportTeam->event_id>0){
       Event::find($sportTeam->event_id)->update([
         'beschreibung'      => $request->beschreibung,
         'nachtermin'        => $request->nachtermin,
         'bearbeiter_id'     => Auth::user()->id,
         'updated_at'        => Carbon::now()
       ]);
     }
     else{
       $createdEvent= new Event(
         [
           'beschreibung'     => $request->beschreibung,
           'nachtermin'       => $request->nachtermin,
           'sportSection_id'  => $request->sportSection_id,
           'verwendung'       => 4,   //4 = Mannschaftsbeschreibung
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
        SportSection::find($sportTeam_id)->update([
         'event_id' => $newEventId
       ]);
     }
   }

   return redirect('/Mannschaft/alle')->with(
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

    public function softDelete($SportTeams_id)
    {
      $delete = sportSection::find($SportTeams_id)->delete();
    return redirect('/Mannschaft/alle')->with(
     [
       'success' => env('MENUE_MANNSCHAFTEN').' wurde gelöscht.'
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
      sportSection::find($sportTeam_id)->update([
       'bild'          => Null,
       'filename'      => Null,
       'bearbeiter_id' => Auth::user()->id,
       'updated_at'    => Carbon::now()
      ]);
      return back()->with([
          'success' => 'Das Headerbild vom der '.env('MENUE_MANNSCHAFTEN').' wurde gelöscht'
      ]);
    }
}
