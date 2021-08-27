<?php

namespace App\Http\Controllers;

use App\Models\SportSection;

class HomeController extends Controller
{
    public function index(){

      $serverdomain        = $_SERVER["HTTP_HOST"];

      $abteilungHomes      = SportSection::where('status' , '1')->orwhere('domain',$serverdomain)->orderby('status')->get();
      $abteilungHomesCount = SportSection::where('status' , '1')->orwhere('domain',$serverdomain)->count();

      $abteilungs          = SportSection::where('status' , '>' ,'1')->where('sportSection_id' , NULL)->orderby('abteilung')->get();
      $abteilungsCount     = SportSection::where('status' , '>' ,'1')->where('sportSection_id' , NULL)->count();

      return view('home.home')->with(
        [
         'abteilungHomes'      => $abteilungHomes,
         'abteilungHomesCount' => $abteilungHomesCount,
         'abteilungs'          => $abteilungs,
         'abteilungsCount'    => $abteilungsCount,
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
        $seoch = str_replace('_', ' ', $sportSectionSeorch);
        $sportSectionNames = SportSection::where('abteilung' , $seoch)->get();
        foreach($sportSectionNames as $sportSectionName) {
            $sportSectionsId = $sportSectionName->id;
        }
        $sportTeamNames = SportSection::where('sportSection_id' , $sportSectionsId)->get();
        return view('home.sportSectionShow' , compact('sportSectionNames' , 'sportTeamNames'));
    }

}
