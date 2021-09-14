@extends('layouts.frontend')

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
                            @if(env('Verein_Sozialmediaanzeigen')=='ja')
                             <center>
                               <div class="fb-like" data-href="http://www.{{ str_replace('_' , ' ' , env('Verein_Domain')) }} data-send="true" data-layout="box_count" data-width="183" data-show-faces="true" data-font="arial"></div>
                             </center>
                            @endif
                            <h3>{{ $event->ueberschrift }}</h3>
                        @if($event->datumvon == $event->datumbis)
                            <p>am {{ date("d.m.Y", strtotime($event->datumvon)) }}</p>
                        @else
                            <p>von {{ date("d.m.Y", strtotime($event->datumvon)) }} bis {{ date("d.m.Y", strtotime($event->datumbis)) }}</p>
                        @endif
                        @if($event->ansprechparten > Illuminate\Support\Carbon::now())
                            @if(isset($event->ansprechparten) && $event->ansprechparten <> '')
                                Ansprechpartner: {!! $event->ansprechparten !!}<br>
                            @endif
                            @if(isset($event->telefon) && $event->telefon <> '')
                                Telefon: {!! $event->telefon !!}<br>
                            @endif
                            @if(isset($event->email) && $event->email <> '')
                                Email: {!! $event->email !!}</br>
                            @endif
                            @if(isset($event->homepage) && $event->homepage <> '')
                                Homepage: {!! $event->homepage !!}</br>
                            @endif
                            {!! $event->beschreibung !!}
                        @endif
                        @if($event->datumvon < Illuminate\Support\Carbon::now())
                            @if(isset($event->homepage) && $event->homepage <> '')
                                Homepage: {!! $event->homepage !!}<br>
                            @endif
                            {!! $event->nachtermin !!}
                        @endif
                        </p>
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
