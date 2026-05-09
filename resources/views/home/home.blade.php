@extends('layouts.headFrontend')

@section('title' ,'Startseite')

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
