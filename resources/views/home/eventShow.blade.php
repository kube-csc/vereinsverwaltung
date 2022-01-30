@extends('layouts.frontend')

@section('title' ,'Termin')

@section('content')

  <main id="main">

  @foreach ($events as $event)

    <section id="portfolio" class="portfolio">
        <div class="container">
            <div class="section-title" data-aos="fade-in" data-aos-delay="100">
                <!-- ======= Facebook======= -->
                @if(env('Verein_Sozialmediaanzeigen')=='ja')
                <center>
                    <div class="fb-like" data-href="http://www.{{ str_replace('_' , ' ' , env('Verein_Domain')) }} data-send="true" data-layout="box_count" data-width="183" data-show-faces="true" data-font="arial"></div>
                </center>
                @endif

                <h2>{{ $event->ueberschrift }}</h2>

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
                    <br>
                    <p>
                        {!! $event->beschreibung !!}
                    </p>
                @endif

                @if($event->datumvon < Illuminate\Support\Carbon::now())
                    @if(isset($event->homepage) && $event->homepage <> '')
                       <br>
                       <p>Homepage: {!! $event->homepage !!}</p>
                    @endif
                    <p>{!! $event->nachtermin !!}</p>
                @endif

            </div>

            <livewire:event-gallery :reportId="$event->id" />


        </div>
    </section><!-- End Portfolio Section -->
  @endforeach

  </main><!-- End #main -->

@endsection
