@extends('layouts.headFrontend')

@section('title' ,'Starteite')

@section('content')

<main id="main">

@include('home.sportSection')

@include('home.eventFuture')

@include('home.eventPast')

@if('r' == 'h')
@include('home.counts')
@endif

@include('home.team')

@include('home.contakt')

</main><!-- End #main -->

@endsection

@php
    // TODO: Funktion anderes Integrieren
    function textmax(&$beschreibung,$sollang,&$abgeschnitten)
    {
     $abgeschnitten=0;
     $laenge=strlen($beschreibung);
     if ($laenge>$sollang)
      {
        $beschreibung=substr($beschreibung,0,$sollang);
        $beschreibung=$beschreibung."...";  // TODO:  Punkte werden nicht angefügt
        $abgeschnitten=1;
      }
    }
@endphp
