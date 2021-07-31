<?php
// TODO: Config Daten anderes Einbinden
  $verein= "Kanuten Emscher-Lippe e.V.";
  $vereinstrasse = "Zu den Sportstätten 5";
  $vereinsplz = "D-45711";
  $vereinsort = "Datteln";
  $vereintelefon = "(02363) 8420";
  $vereinfax="";
  $vereinemail = "kel@kel-datteln.de";
  $vereineintagngsort = "Recklinghausen";
  $vereinvrnummer = "VR 0627";
  $keywords = "Kanuten Emscher-Lippe, Datteln, KEL, Kanuverein, Sport, Kanuwandersport, Kanurennsport, Kanu, Kajak, Paddeln, Drachenboot, Drachenbootregatta,
               Wassersport, Regatta, SUP, Outrigger";
  $description = "Wir sind ein Kanuverein mit Jugend-, Wander-, Rennsport-, SUP-, Outrigger- und Drachenbootabteilung in Datteln NRW am Dortmund-Ems-Kanal.";

  $slogen="Wir sind ein Kanuverein in Datteln am Dortmund Ems Kanal.";
  $canonical="https://www.kel-datteln.de/neu";
  $domain="kel-datteln.de";

  $sozialmediaanzeigen='n';
?>

@extends('layouts.headFrontend')

@section('title' ,'Starteite')

@section('content')

<main id="main">

@include('home.sportSection')

<?php /* TODO: TeamSeite erstellen
include('_partials.sportSectionTeam')
*/
?>

@include('home.contakt')

</main><!-- End #main -->

@endsection
