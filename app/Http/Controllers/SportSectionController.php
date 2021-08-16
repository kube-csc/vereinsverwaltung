<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Carbon;
use Intervention\Image\Facades\Image;
//use Illuminate\Support\Facades\DB;
use Auth;

use App\Models\SportSection;
use App\Models\Event;

class SportSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */

    public function __construct(){
       $this->middleware('auth');
    }

    public function sportSectionSelected($sportSection_id)
    {
        $this->active = $sportSection_id;
    }

    public function aktiv($sportSection_id)
    {
      sportSection::find($sportSection_id)->update([
            'status'      => '2',
            'updated_at'  => Carbon::now()
          ]);
     return Redirect()->back()->with('success' , 'Abteilung wurde sichtbar geschaltet.');
    }

    public function inaktiv($sportSection_id)
    {
      sportSection::find($sportSection_id)->update([
            'status'      => '0',
            'updated_at'  => Carbon::now()
          ]);
     return Redirect()->back()->with('success' , 'Abteilung wurde unsichtbar geschaltet.');
    }

    public function start($sportSection_id)
    {
     sportSection::where('status' , '1')->update([
            'status'      => '2',
            'updated_at'  => Carbon::now()
          ]);
      sportSection::find($sportSection_id)->update([
            'status'      => '1',
            'updated_at'  => Carbon::now()
          ]);
     return Redirect()->back()->with('success' , 'Abteilung wurde Startseite festgelegt.');
    }

    public function sportSectionSportTeam($sportSection_id)
    {
      $sportSections = sportSection::where('sportSections_id' , '')->orderby('abteilung')->paginate(5);
      $sportSectionVonSportTeam = sportSection::find($sportSection_id);
      $sportTeams = sportSection::where('sportSections_id'  , $sportSection_id)->orderby('abteilung')->get();
      return view('admin.sportSection.sportSectionSportTeam')->with(
        [
          'success'               => 'Mannschaften der Abteilung ' . $sportSectionVonSportTeam->abteilung . ' wurde selectiert.',
          'sportSectionName'      => $sportSectionVonSportTeam->abteilung,
          'sportTeams'            => $sportTeams,
          'sportSections'         => $sportSections,
          'sportSectionIdSelecte' => $sportSection_id,
         ]);
    }

    public function index()
    {
      $sportSections = sportSection::where('sportSections_id' , '')->orderby('abteilung')->paginate(5);
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
          'abteilung'         => 'required|max:40'
        ]
      );

      $sportSection= new sportSection(
        [
          'abteilung'        => $request->abteilung,
          'event_id'         => NULL,
          'sportSections_id' => 0,
          'status'           => 2,
          'user_id'          => Auth::user()->id,
          'updated_at'       => Carbon::now(),
          'created_at'       => Carbon::now()
        ]
       );
      $sportSection->save();

      return redirect('/Abteilung/alle')->with(
        [
          'success' => 'Die Abteilung <b>' . $request->abteilung . '</b> wurde angelegt.'
        ]
      );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($SportTeam_id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($SportTeam_id)
    {
      $sportSection = sportSection::find($SportTeam_id);

      if ($sportSection->event_id>0) {
       //$event = DB::table('events')->find($sportSection->event_id);
       $event = Event::find($sportSection->event_id);
       $ausgabetext=$event->beschreibung ;
      }
      else {
        $ausgabetext='';
      }
      return view('admin.sportSection.edit',compact('sportSection' , 'ausgabetext'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $SportTeam_id)
    {
      $request->validate(
        [
          'abteilung'         => 'required|max:40',
          'farbe'             => 'max:255',
          'domain'            => 'max:255',
          //'domain'           => 'sometimes|url'  //todo: Lehre Felder wird nicht akzepiert
          'bild'              => 'mimes:jpeg,jpg,bmp,png,gif'
        ]
      );

      sportSection::find($SportTeam_id)->update([
        'abteilung'         => $request->abteilung,
        'farbe'             => $request->farbe,
        'domain'            => $request->domain,
        'updated_at'        => Carbon::now()
      ]);

      $messagePicture='';
      if($request->bild){
        $newPictureName=$this->saveInmage($request->bild , $SportTeam_id);
        if($newPictureName<>''){
          sportSection::find($SportTeam_id)->update([
            'bild'         => $newPictureName
          ]);
          $messagePicture='<br>Das Headerbild wurde hochgeladen.';
        }
        else {
          $messagePicture='<br>Das Headerbild hat ein Hochformat und könnte deswegen nicht hochgeladen werden.';
        }
      }

      if ($request->beschreibung<>'') {
        $sportSection = sportSection::find($SportTeam_id);
        if ($sportSection->event_id>0){
          Event::find($sportSection->event_id)->update([
            'beschreibung'      => $request->beschreibung,
            'bearbeiter_id'     => Auth::user()->id,
            'updated_at'        => Carbon::now()
          ]);
        }
        else{
          $createdEvent= new Event(
            [
              'beschreibung'     => $request->beschreibung,
              'SportTeam_id'     => $request->SportTeam_id,
              'verwendung'       => 4,   //4 = Abteilungsbeschreibung
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

           sportSection::find($SportTeam_id)->update([
            'event_id'         => $newEventId
          ]);
        }
      }

      return redirect('/Abteilung/alle')->with(
        [
          'success' => 'Die Daten von der Abteilung <b>' . $request->abteilung . '</b> wurde geändert.'.$messagePicture
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
      $delete = sportSection::find($SportTeam_id)->delete();
      return redirect('/Abteilung/alle')->with(
        [
          'success' => 'Das Abteilung wurde gelöscht.'
        ]
      );
    }

    //Bilder Speichern
    public function saveInmage($bildInput , $SportTeam_id){
     $newPictureName="header".$SportTeam_id.'.jpg';
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

    // Bilder von SportSection löschen
       public function pictureDelete($SportTeam_id){
         $newPictureName="header".$SportTeam_id.'.jpg';
         if (file_exists(public_path().'/storage/header/'.$newPictureName)){
            unlink(public_path().'/storage/header/'.$newPictureName);
          }
         sportSection::find($SportTeam_id)->update([
          'bild'         => ''
         ]);
         return back()->with([
             'success' => 'Das Headerbild vom der Abteilung wurde gelöscht'
         ]);
       }

}
