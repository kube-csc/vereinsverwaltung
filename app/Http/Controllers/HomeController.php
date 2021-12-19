<?php

namespace App\Http\Controllers;

use App\Models\board;
use App\Models\Event;
use App\Models\SportSection;
use App\Models\instruction;
use App\Models\Document;
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
            $sportSectionTeamNameMenu= $abteilungHome->abteilungTeamBezeichnung;
            $sportSectionTeamName= $abteilungHome->abteilungTeamBezeichnung;
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
            ->where('boards.visible' , 1)
            ->where('bu.visible' , 1)
            ->orderby('boards.position')
            ->orderby('bu.position')
            ->get();

        $footerDocuments = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('visible' , 1)
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();



        return view('home.home')->with(
            [
                'abteilungHomes'              => $abteilungHomes,
                'abteilungHomesCount'         => $abteilungHomesCount,
                'abteilungs'                  => $abteilungs,
                'abteilungsCount'             => $abteilungsCount,
                'sportSectionTeamNameMenu'    => $sportSectionTeamNameMenu,
                'sportSectionTeamName'        => $sportSectionTeamName,
                'boards'                      => $boards,
                'footerDocuments'             => $footerDocuments,
                'eventsFuture'                => $eventsFuture,
                'eventsPast'                  => $eventsPast,
                'serverdomain'                => $serverdomain
            ]
        );
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function homeSportSelect($sportSectionSeorch)
    {
        $sportSectionSearch = str_replace('_' , ' ' , $sportSectionSeorch);
        $sportSectionNames  = SportSection::where('abteilung' , $sportSectionSearch)->get();

        foreach($sportSectionNames as $sportSectionName) {
            $sportSectionsId = $sportSectionName->id;
            $sportSectionTeamName = $sportSectionName->abteilungTeamBezeichnung;
        }
        $sportTeamNames = SportSection::where('sportSection_id' , $sportSectionsId)->get();

        $serverdomain        = $_SERVER["HTTP_HOST"];
        $abteilungHomes      = SportSection::where('status' , '1')
            ->orwhere('domain' , $serverdomain)
            ->orderby('status')
            ->get();

        foreach ($abteilungHomes as $abteilungHome) {
            $sportSectionTeamNameMenu= $abteilungHome->abteilungTeamBezeichnung;
        }

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
            ->where('boards.visible' , 1)
            ->where('bu.visible' , 1)
            ->orderby('boards.position')
            ->orderby('bu.position')
            ->get();

        $documents = Document::where('sportSection_id' , $sportSectionsId)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();

        $footerDocuments = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('visible' , 1)
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();

        return view('home.homeSportSelect')->with([
            'sportSectionNames'           => $sportSectionNames,
            'sportTeamNames'              => $sportTeamNames,
            'sportSectionSearch'          => $sportSectionSearch,
            'sportSectionTeamNameMenu'    => $sportSectionTeamNameMenu,
            'sportSectionTeamName'        => $sportSectionTeamName,
            'documents'                   => $documents,
            'footerDocuments'             => $footerDocuments,
            'eventsFuture'                => $eventsFuture,
            'eventsPast'                  => $eventsPast,
            'boards'                      => $boards
        ]);
    }

    public function eventShow($eventSeorch)
    {
        $serverdomain        = $_SERVER["HTTP_HOST"];
        $abteilungHomes      = SportSection::where('status' , '1')
            ->orwhere('domain' , $serverdomain)
            ->orderby('status')
            ->get();

        foreach ($abteilungHomes as $abteilungHome) {
            $sportSectionTeamNameMenu= $abteilungHome->abteilungTeamBezeichnung;
        }

        $footerDocuments = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('visible' , 1)
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();

        $eventSeorchName = substr ($eventSeorch , 0, -10);
        $dateFor = substr ($eventSeorch, -10);
        $seoch = str_replace('_' , ' ' , $eventSeorchName);
        $events = event::where('ueberschrift' , $seoch)->where('datumvon' , $dateFor)->get();

        return view('home.eventShow')->with([
            'events'                      => $events,
            'footerDocuments'             => $footerDocuments,
            'sportSectionTeamNameMenu'    => $sportSectionTeamNameMenu
        ]);
    }

    public function instructionShow($instructionSeorch)
    {
        $serverdomain        = $_SERVER["HTTP_HOST"];
        $abteilungHomes      = SportSection::where('status' , '1')
            ->orwhere('domain' , $serverdomain)
            ->orderby('status')
            ->get();

        foreach ($abteilungHomes as $abteilungHome) {
            $sportSectionTeamNameMenu= $abteilungHome->abteilungTeamBezeichnung;
        }

        $footerDocuments = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('visible' , 1)
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();

        $seoch = str_replace('_' , ' ' , $instructionSeorch);
        $instructions = instruction::where('ueberschrift' , $seoch)->get();

        foreach($instructions as $instruction){
            $instructionId = $instruction->id;
        }

        $documents = Document::where('instruction_id' , $instructionId)
                             ->where('startDatum' , '<=' , Carbon::now()->toDateString())
                             ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
                             ->where('dokumentenFile' ,'!=' , NULL)
                             ->get();

        return view('instruction.show')->with([
            'documents'                   => $documents,
            'instructions'                => $instructions,
            'footerDocuments'             => $footerDocuments,
            'sportSectionTeamNameMenu'    => $sportSectionTeamNameMenu
        ]);
    }

    public function eventFutureAll()
    {
        $serverdomain        = $_SERVER["HTTP_HOST"];
        $abteilungHomes      = SportSection::where('status' , '1')
            ->orwhere('domain' , $serverdomain)
            ->orderby('status')
            ->get();

        foreach ($abteilungHomes as $abteilungHome) {
            $sportSectionTeamNameMenu= $abteilungHome->abteilungTeamBezeichnung;
        }

        $footerDocuments = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('visible' , 1)
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();

        $documents = $this->journey();

        return view('home.eventFutureAll')->with([
            'footerDocuments'             => $footerDocuments,
            'sportSectionTeamNameMenu'    => $sportSectionTeamNameMenu
        ]);
    }

    public function eventPastAll()
    {
        $serverdomain        = $_SERVER["HTTP_HOST"];
        $abteilungHomes      = SportSection::where('status' , '1')
            ->orwhere('domain' , $serverdomain)
            ->orderby('status')
            ->get();

        foreach ($abteilungHomes as $abteilungHome) {
            $sportSectionTeamNameMenu= $abteilungHome->abteilungTeamBezeichnung;
        }

        $footerDocuments = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('visible' , 1)
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();

        return view('home.eventPastAll')->with([
            'footerDocuments'             => $footerDocuments,
            'sportSectionTeamNameMenu'    => $sportSectionTeamNameMenu
        ]);
    }


    public function journey()
    {
        $serverdomain        = $_SERVER["HTTP_HOST"];
        $abteilungHomes      = SportSection::where('status' , '1')
            ->orwhere('domain' , $serverdomain)
            ->orderby('status')
            ->get();

        foreach ($abteilungHomes as $abteilungHome) {
            $sportSectionTeamNameMenu= $abteilungHome->abteilungTeamBezeichnung;
        }

        $footerDocuments = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('visible' , 1)
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();

        return view('home.journey')->with([
            'footerDocuments'             => $footerDocuments,
            'sportSectionTeamNameMenu'    => $sportSectionTeamNameMenu
        ]);
    }

    public function imprint()
    {
        $serverdomain        = $_SERVER["HTTP_HOST"];
        $abteilungHomes      = SportSection::where('status' , '1')
            ->orwhere('domain' , $serverdomain)
            ->orderby('status')
            ->get();

        foreach ($abteilungHomes as $abteilungHome) {
            $sportSectionTeamNameMenu= $abteilungHome->abteilungTeamBezeichnung;
        }

        $footerDocuments = Document::where('footerStatus' , 1)
            ->where('startDatum' , '<=' , Carbon::now()->toDateString())
            ->where('endDatum'   , '>=' , Carbon::now()->toDateString())
            ->where('visible' , 1)
            ->where('dokumentenFile' ,'!=' , NULL)
            ->get();

        return view('home.imprint')->with([
            'footerDocuments'             => $footerDocuments,
            'sportSectionTeamNameMenu'    => $sportSectionTeamNameMenu
        ]);
    }
}
