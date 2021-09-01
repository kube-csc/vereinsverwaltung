@extends('layouts.headFrontend')

@section('title' ,'Starteite')

@section('content')

<main id="main">

@include('home.sportSection')

@include('home.eventFuture')

@include('home.eventPast')

@include('home.counts')

@include('home.team')

@include('home.contakt')

</main><!-- End #main -->

@endsection
