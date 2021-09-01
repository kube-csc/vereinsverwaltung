<?php

namespace App\Http\Controllers;

use App\Models\board;
use App\Models\SportSection;
use Illuminate\Support\Facades\DB;

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

      /*
      $sportSectionMenus   = SportSection::where('status' , '>' , '1')
          ->where('sportSection_id' , NULL)
          ->orderby('abteilung')
          ->get();
      */

      $boards=board::where('sportSection_id' , $sportSection_id)
          ->join('board_user as bu' , 'bu.board_id' , '=' , 'boards.id')
          ->join('users as us' , 'bu.user_id' , '=' , 'us.id')
          ->get();

      /*
        $boards=board::select("bu.email as vorstandsemail",
            "vorname",
            "nachname",
            "geschlecht",
            "postenmaenlich",
            "postenweiblich"
        )
            ->where('sportSection_id' , $sportSection_id)
            ->join('board_user as bu' , 'bu.board_id' , '=' , 'boards.id')
            ->join('users as us' , 'bu.user_id' , '=' , 'us.id')
            ->get();
*/

      return view('home.home')->with(
        [
         'abteilungHomes'      => $abteilungHomes,
         'abteilungHomesCount' => $abteilungHomesCount,
         'abteilungs'          => $abteilungs,
         'abteilungsCount'     => $abteilungsCount,
         //'sportSectionMenus'   => $sportSectionMenu,
         'boards'              => $boards,
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
    public function sportSectionShow($sportSectionSeorch)
    {
        $seoch = str_replace('_' , ' ' , $sportSectionSeorch);
        $sportSectionNames = SportSection::where('abteilung' , $seoch)->get();

        $abteilungs          = SportSection::where('status' , '>' , '1')
            ->where('sportSection_id' , NULL)
            ->orderby('abteilung')
            ->get();

        foreach($sportSectionNames as $sportSectionName) {
            $sportSectionsId = $sportSectionName->id;
        }
        $sportTeamNames = SportSection::where('sportSection_id' , $sportSectionsId)->get();
        return view('home.sportSectionShow' , compact('sportSectionNames' , 'sportTeamNames' , 'abteilungs'));
    }

}
