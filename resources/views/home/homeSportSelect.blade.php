@extends('layouts.headFrontend')

@section('title' , str_replace('_' , ' ' , env('Menue_Abteilung')))

@section('content')

<main id="main">

    @include('home.sportSectionShow')

    @include('home.eventFuture')

    @include('home.eventPast')

    @include('home.team')

</main><!-- End #main -->

@endsection

@php
    // ToDo: Funktion anderes Integrieren
    function textmax(&$beschreibung,$sollang,&$abgeschnitten)
    {
     $abgeschnitten=0;
     $laenge=strlen($beschreibung);
     if ($laenge>$sollang)
      {
        $beschreibung=substr($beschreibung,0,$sollang);
        $beschreibung=$beschreibung."...";
        $abgeschnitten=1;
      }
    }
@endphp
