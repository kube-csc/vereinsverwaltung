<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\SportSection;

class HomeController extends Controller
{
    public function index(){

      $serverdomain        = $_SERVER["HTTP_HOST"];
      //$abteilungHomes      = DB::table('sport_sections')->where('status' , '1')->orwhere('domain',$serverdomain)->orderby('status')->get();
      //$abteilungHomesCount = DB::table('sport_sections')->where('status' , '1')->orwhere('domain',$serverdomain)->count();
      $abteilungHomes      = SportSection::where('status' , '1')->orwhere('domain',$serverdomain)->orderby('status')->get();
      $abteilungHomesCount = SportSection::where('status' , '1')->orwhere('domain',$serverdomain)->count();

      //$abteilungs          = DB::table('sport_sections')->where('status' ,'>' ,'1')->where('sportSections_id' , '')->orderby('abteilung')->get();
      //$abteilungsCount     = DB::table('sport_sections')->where('status' ,'>' ,'1')->where('sportSections_id' , '')->count();
      $abteilungs      = SportSection::where('status' ,'>' ,'1')->where('sportSections_id' , '')->orderby('abteilung')->get();
      $abteilungsCount = SportSection::where('status' ,'>' ,'1')->where('sportSections_id' , '')->count();

      return view('home.home')->with(
        [
          'abteilungHomes'      => $abteilungHomes,
          'abteilungHomesCount' => $abteilungHomesCount,
          'abteilungs'          => $abteilungs,
          'abteilungsCount'     => $abteilungsCount,
          'serverdomain'        => $serverdomain
        ]
     );
    }
}
