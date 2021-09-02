@extends('layouts.headFrontend')

@section('title' ,'Event')

@section('content')

    <main id="main">

    <!-- ======= About Section ======= -->
        <section id="about" class="about">
            <div class="container">
                <div class="row no-gutters">
            <?php
            foreach ($events as $event)
             {
             ?>
                    <div class="content col-xl-5 d-flex align-items-stretch" data-aos="fade-up">
                        <div class="content">
                            <!-- ======= Facebook======= -->
                            <center>
                                <div class="fb-like" data-href="http://www.{{ str_replace('_' , ' ' , env('Verein_Domain')) }} data-send="true" data-layout="box_count" data-width="183" data-show-faces="true" data-font="arial"></div>
                            </center>

                            <h3>{{ $event->ueberschrift }}</h3>
                        @if($event->datumvon == $event->datumbis)
                            <p>am {{ date("d.m.Y", strtotime($event->datumvon)) }}</p>
                        @else
                            <p>von {{ date("d.m.Y", strtotime($event->datumvon)) }} bis {{ date("d.m.Y", strtotime($event->datumbis)) }}</p>
                        @endif
                        @if($event->datumbis < Illuminate\Support\Carbon::now())
                            {!! $event->beschreibung !!}
                        @endif
                        @if($event->datumvon > Illuminate\Support\Carbon::now())
                            {!! $event->nachtermin !!}
                        @endif
                        </div>
                    </div>
             @php
             }
             @endphp
                    <div class="col-xl-7 d-flex align-items-stretch">
                        <div class="icon-boxes d-flex flex-column justify-content-center">


                        </div><!-- End .content-->
                    </div>
                </div>
            </div>
        </section><!-- End About Section -->


    </main><!-- End #main -->

@endsection
