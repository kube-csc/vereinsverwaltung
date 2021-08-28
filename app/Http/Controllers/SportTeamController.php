<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;
use Auth;

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
  return Redirect()->back()->with('success' , 'Die Mannschaft wurde sichtbar geschaltet.');
 }

 public function inaktiv($sportTeam_id)
 {
   SportSection::find($sportTeam_id)->update([
         'status'      => '0',
         'updated_at'  => Carbon::now()
       ]);
  return Redirect()->back()->with('success' , 'Die Mannschaft wurde unsichtbar geschaltet.');
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
  return Redirect()->back()->with('success' , 'Die Mannschaft wurde Startseite festgelegt.');
 }

 public function index()
 {
   $sportTeams = SportSection::where('sportSection_id' , '>' , '0')->orderby('abteilung')->paginate(5);
   return view('admin.sportTeam.index')->with(
     [
       'sportTeams'    => $sportTeams,
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
       'mannschaft'         => 'required|max:40'
     ]
    );

    $SportTeam= new sportSection(
     [
       'abteilung'        => $request->mannschaft,
       'event_id'         => NULL,
       'sportSection_id'  => $request->sportSection_id,
       'status'           => 2,
       'user_id'          => Auth::user()->id,
       'updated_at'       => Carbon::now(),
       'created_at'       => Carbon::now()
     ]
    );
    $SportTeam->save();

   return redirect('/Mannschaft/alle')->with(
     [
       'success' => 'Die Mannschaft <b>' . $request->mannschaft . '</b> wurde angelegt.'
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
   }
   else {
     $ausgabetext='';
   }
   return view('admin.sportTeam.edit',compact('SsportTeam' , 'ausgabetext'));
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
       'abteilung'         => 'required|max:40',
       'farbe'             => 'max:255',
       'domain'            => 'max:255',
       //'domain'            => 'sometimes|url'  //todo: Lehre Felder wird nicht akzepiert
       'bild'              => 'mimes:jpeg,jpg,bmp,png,gif'
     ]
   );

   SportSection::find($sportTeam_id)->update([
     'abteilung'         => $request->abteilung,
     'farbe'             => $request->farbe,
     'domain'            => $request->domain,
     'updated_at'        => Carbon::now()
   ]);

   $messagePicture='';
   if($request->bild){
     $newPictureName=$this->saveInmage($request->bild , $sportTeam_id);
     if($newPictureName<>''){
       SportSection::find($sportTeam_id)->update([
         'bild'         => $newPictureName
       ]);
       $messagePicture='<br>Das Headerbild wurde hochgeladen.';
     }
     else {
       $messagePicture='<br>Das Headerbild hat ein Hochformat und könnte deswegen nicht hochgeladen werden.';
     }
   }

   if ($request->beschreibung<>'') {
     $sportTeam = sportSection::find($sportTeam_id);
     if ($SportTeam->event_id>0){
       Event::find($sportTeam->event_id)->update([
         'beschreibung'      => $request->beschreibung,
         'bearbeiter_id'     => Auth::user()->id,
         'updated_at'        => Carbon::now()
       ]);
     }
     else{
       $createdEvent= new Event(
         [
           'beschreibung'     => $request->beschreibung,
           'sportSection_id'  => $request->sportSection_id,
           'veranstaltung'    => 4,   //4 = Mannschaftsbeschreibung
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
         'event_id'         => $newEventId
       ]);
     }
   }

   return redirect('/Mannschaft/alle')->with(
     [
       'success' => 'Die Daten von der Mannschaft <b>' . $request->abteilung . '</b> wurden geändert.'.$messagePicture
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
       'success' => 'Das Mannschaft wurde gelöscht.'
     ]
   );
 }

 //Bilder Speichern
 public function saveInmage($bildInput , $sportTeam_id){
  $newPictureName="header".$sportTeam_id.'.jpg';
  $bild = Image::make($bildInput);
  $breite= $bild->width();
  $hoehe = $bild->height();
  if($breite > $hoehe){
    //Querformat
    Image::make($bildInput)
      ->widen(2050)
      ->save(public_path().'/storage/header/'.$newPictureName);
   // TODO: Bilderbreite richtige
    return $newPictureName;
  }
 }

 // Bilder von SportTeams löschen
    public function pictureDelete($sportTeam_id){
      $newPictureName="header".$sportTeam_id.'.jpg';
      if (file_exists(public_path().'/storage/header/'.$newPictureName)){
         unlink(public_path().'/storage/header/'.$newPictureName);
       }
      sportSection::find($sportTeam_id)->update([
       'bild'      => ''
      ]);
      return back()->with([
          'success' => 'Das Headerbild vom der Mannschaft wurde gelöscht'
      ]);
    }

}
