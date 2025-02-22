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
                        <b>Anmeldezeitraum: </b>
                        @if(isset($event->datumvona))
                            von {{ date("d.m.Y", strtotime($event->datumvona)) }}
                        @endif
                            bis {{ date("d.m.Y", strtotime($event->datumbisa)) }}
                    </p>
                @endif
                @if(isset($event->homepage) && $event->homepage <> '')
                    <p>
                        <b>Homepage: </b>
                        <a href="http://{{ $event->homepage }}">Link</a>
                    </p>
                @endif
                @if($event->datumbis > Illuminate\Support\Carbon::now())
                    @if(isset($event->ansprechpartner) or isset($event->telefon) or isset($event->email))
                        <p>
                            <b>Ansprechpartner: </b>
                            @endif
                            @if(isset($event->ansprechpartner))
                                {{ $event->ansprechpartner }}<br>
                            @endif
                            @if(isset($event->telefon))
                                <b>Telefon: </b>
                                {{ $event->telefon }}<br>
                            @endif
                            @if(isset($event->email))
                                <b>E-Mail: </b>
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
                    <p>{!! $event->nachtermin !!}</p>
                @endif
            </div>

        @foreach($socialMedias as $socialMedia)
            @if($socialMedia->webseite == 1)
                <div class="section-title" data-aos="fade-in" data-aos-delay="100">
                    <div>
                        {!! str_replace('[URL]', $socialMedia->filename, $socialMedia->playerlink) !!}
                        <div>
                            {{ $socialMedia->titel }}
                            {{ $socialMedia->kommentar }}
                        </div>
                    </div>
                </div>
            @endif
        @endforeach

            @if($eventDokumentes->count()>0)
                @php
                   $groupflak=0;
                   $verwendung = [
                        "2" => "Ausschreibung",
                        "3" => "Programm",
                        "4" => "Ergebnisse",
                        "5" => "Plakat / Flyer",
                        "6" => "Pressebericht",
                    ];
                @endphp
            <div class="section-title" data-aos="fade-in" data-aos-delay="100">
                    <b>Dokumente zum herunterladen:</b>
                        @foreach($eventDokumentes as $eventDokumente)
                        @if($loop->first)
                            @php($groupflak=$eventDokumente->verwendung)
                    <ul>
                      <li>{{ $verwendung[$groupflak] }}</li>
                      <ul>
                        @else
                            @if($eventDokumente->verwendung != $groupflak)
                                @php($groupflak=$eventDokumente->verwendung)
                      </ul>
                    </ul>
                    <ul>
                      <li>{{ $verwendung[$groupflak] }}</li>
                      <ul>
                            @endif
                        @endif
                          @if( $eventDokumente->bild != NULL)
                        <li><a href="/storage/eventDokumente/{{ $eventDokumente->bild }}">{{ $eventDokumente->titel }}</a></li>
                          @else
                        <li><a href="/daten/text/{{ $eventDokumente->image }}">{{ $eventDokumente->titel }}</a></li>
                          @endif
                        @endforeach
                      </ul>
                    </ul>
                </div>
            @endif

            <livewire:event-gallery :reportId="$event->id" />

        </div>
    </section><!-- End Portfolio Section -->
  @endforeach

  </main><!-- End #main -->
@endsection
