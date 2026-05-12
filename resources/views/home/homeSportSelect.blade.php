@extends('layouts.headFrontend')

@section('title' , str_replace('_' , ' ' , env('MENUE_ABTEILUNG')))

@section('content')

<main id="main">

    @include('home.sportSectionShow')

    @include('home.eventFuture')

    @include('home.eventPast')

    @include('home.team')

</main><!-- End #main -->

@endsection
