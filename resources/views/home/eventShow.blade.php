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
                        @if($event->datumbisa <= Illuminate\Support\Carbon::now() && isset($event->datumbisa))
                            <p>
                                <b>Anmeldezeitraum:</b><br>
                                @if(isset($event->datumvona))
                                 von {{ date("d.m.Y", strtotime($event->datumvona)) }}<br>
                                @endif
                                 bis {{ date("d.m.Y", strtotime($event->datumbisa)) }}
                            </p>
                        @endif
                        @if(isset($event->homepage))
                            <p>
                                <b>Homepage:</b><br>
                                {{ $event->homepage }}
                            </p>
                        @endif
                        @if($event->datumbis > Illuminate\Support\Carbon::now())
                            @if(isset($event->ansprechparter) or isset($event->telefon) or isset($event->email))
                                <p>
                                    <b>Ansprechpartner:</b><br>
                                    @endif
                                    @if(isset($event->ansprechparter))
                                        {{ $event->ansprechparter }}<br>
                                    @endif
                                    @if(isset($event->telefon))
                                        <b>Telefon:</b><br>
                                        {{ $event->telefon }}<br>
                                    @endif
                                    @if(isset($event->email))
                                        <b>E-Mail:</b><br>
                                        {{ $event->email }}
                                    @endif
                                    @if(isset($event->ansprechparter) or isset($event->telefon) or isset($event->email))
                                </p>
                            @endif
                                <p>
                                  {!! $event->beschreibung !!}
                                </p>
                        @endif
                        @if($event->datumvon < Illuminate\Support\Carbon::now())
                            @if(isset($event->homepage) && $event->homepage <> '')
                               <p>Homepage: {!! $event->homepage !!}</p>
                            @endif
                                    <p>{!! $event->nachtermin !!}</p>
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
