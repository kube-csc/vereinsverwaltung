<?php

namespace App\Http\Controllers;

use App\Models\board;
use App\Models\Document;
use App\Models\Event;
use App\Models\SportSection;
use App\Models\instruction;
use Illuminate\Support\Carbon;

class HomeController extends Controller
{
    public function index(){

      $serverdomain        = $_SERVER["HTTP_HOST"];

      $abteilungHomes      = SportSection::where('status' , '1')
          ->orwhere('domain' , $serverdomain)
          ->orderby('status')
          ->get();
      $abteilungHomesCount = $abteilungHomes->count();

      foreach ($abteilungHomes as $abteilungHome) {
            $sportSection_id = $abteilungHome->id;
        }

      $abteilungs          = SportSection::where('status' , '>' , '1')
          ->where('sportSection_id' , NULL)
          ->orderby('abteilung')
          ->get();
      $abteilungsCount     = $abteilungs->count();

      $eventsFuture       = Event::where('datumbis' , '>=' , Carbon::now()->toDateString())
          ->where('verwendung' , 0)
          ->orderby('datumvon')
          ->limit(5)
          ->get();

      $eventsPast         = Event::where('datumvon' , '<=' , Carbon::now()->toDateString())
          ->where('nachtermin' , '!=' , '')
          ->where('verwendung' , 0)
          ->orderby('datumvon' , 'DESC')
          ->limit(5)
          ->get();

      $boards=board::where('sportSection_id' , $sportSection_id)
          ->join('board_users as bu' , 'bu.board_id' , '=' , 'boards.id')
          ->join('users as us' , 'bu.boardUser_id' , '=' , 'us.id')
          ->orderby('boards.position')
          ->orderby('bu.position')
          ->get();

      $documents = Document::where('footerStatus' , 1)
                            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
                            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
                            ->where('dokumentenFile' ,'!=' , NULL)
                            ->get();

      return view('home.home')->with(
        [
         'abteilungHomes'      => $abteilungHomes,
         'abteilungHomesCount' => $abteilungHomesCount,
         'abteilungs'          => $abteilungs,
         'abteilungsCount'     => $abteilungsCount,
         'boards'              => $boards,
         'documents'           => $documents,
         'eventsFuture'        => $eventsFuture,
         'eventsPast'          => $eventsPast,
         'serverdomain'        => $serverdomain
        ]
     );
    }


    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    //public function sportSectionShow($sportSectionSeorch)
    public function homeSportSelect($sportSectionSeorch)
    {
        $sportSectionSearch = str_replace('_' , ' ' , $sportSectionSeorch);
        $sportSectionNames  = SportSection::where('abteilung' , $sportSectionSearch)->get();

        foreach($sportSectionNames as $sportSectionName) {
            $sportSectionsId = $sportSectionName->id;
        }
        $sportTeamNames = SportSection::where('sportSection_id' , $sportSectionsId)->get();

        $eventsFuture   = Event::where('datumbis' , '>=' , Carbon::now()->toDateString())
            ->where('verwendung' , 0)
            ->where(function ($query) use ($sportSectionsId) {
                $query->where('sportSection_id' , $sportSectionsId)
                      ->orwhere('sportSection_id' , NULL); // Events für allen Abteilungen/Mannschaften
                })
            ->orderby('datumvon')
            ->limit(5)
            ->get();

        $eventsPast    = Event::where('datumvon' , '<=' , Carbon::now()->toDateString())
            ->where('nachtermin' , '!=' , '')
            ->where('verwendung' , 0)
            ->where(function ($query) use ($sportSectionsId) {
                $query->where('sportSection_id' , $sportSectionsId)
                      ->orwhere('sportSection_id' , NULL);  // Events für allen Abteilungen/Mannschaften
                })
            ->orderby('datumvon' , 'DESC')
            ->limit(5)
            ->get();

        $boards=board::where('sportSection_id' , $sportSectionsId)
            ->join('board_users as bu' , 'bu.board_id' , '=' , 'boards.id')
            ->join('users as us' , 'bu.boardUser_id' , '=' , 'us.id')
            ->orderby('boards.position')
            ->orderby('bu.position')
            ->get();

        $documents = Document::where('footerStatus' , 1)
                             ->where('startDatum' , '<=' , Carbon::now()->toDateString())
                             ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
                             ->where('dokumentenFile' ,'!=' , NULL)
                             ->get();

        return view('home.homeSportSelect')->with([
                'sportSectionNames'      => $sportSectionNames,
                'sportTeamNames'         => $sportTeamNames,
                'sportSectionSearch'     => $sportSectionSearch,
                'documents'              => $documents,
                'eventsFuture'           => $eventsFuture,
                'eventsPast'             => $eventsPast,
                'boards'                 => $boards
            ]);
    }

    public function eventShow($eventSeorch)
    {
        $documents = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();
        $eventSeorchName = substr ($eventSeorch , 0, -10);
        $dateFor = substr ($eventSeorch, -10);
        $seoch = str_replace('_' , ' ' , $eventSeorchName);
        $events = event::where('ueberschrift' , $seoch)->where('datumvon' , $dateFor)->get();

        return view('home.eventShow')->with([
            'events'      => $events,
            'documents'   => $documents
        ]);
    }

    public function instructionShow($instructionSeorch)
    {
        $documents = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();
        $seoch = str_replace('_' , ' ' , $instructionSeorch);
        $instructions = instruction::where('ueberschrift' , $seoch)->get();

        return view('instruction.show')->with([
            'instructions'      => $instructions,
            'documents'         => $documents
        ]);
    }

    public function eventFutureAll()
    {
        $documents = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();

        return view('home.eventFutureAll')->with([
            'documents'         => $documents
        ]);
    }

    public function eventPastAll()
    {
        $documents = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();

        return view('home.eventPastAll')->with([
            'documents'         => $documents
        ]);
    }

}
