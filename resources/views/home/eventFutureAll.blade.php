@extends('layouts.FrontendLivewire')

@section('title' ,'Alle zukünftige Events')

@section('content')

    <main id="main">

        <livewire:event-future-all />

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
