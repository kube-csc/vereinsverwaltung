@extends('layouts.headFrontend')

@section('title' ,'Starteite')

@section('content')

<main id="main">

@include('home.sportSection')

<?php /* TODO: Team Seite erstellen
include('_partials.sportSectionTeam')
*/
?>

@include('home.contakt')

</main><!-- End #main -->

@endsection
