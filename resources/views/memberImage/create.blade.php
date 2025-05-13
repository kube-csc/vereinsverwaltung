@extends('layouts.frontend')

@section('title' , 'Bilder upload - '.$event->ueberschrift)

@section('content')

    <main id="main">
        <section id="portfolio" class="portfolio">
            <div class="container">
                <div class="section-title" data-aos="fade-in" data-aos-delay="100">

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
              <br>
              <form action="{{ route('memberImage.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="event_id" id="event_id" value="{{ $event->id }}">
                    <div>
                        <label for="image">Bild hochladen:</label>
                        <input type="file" name="image" id="image" accept="image/*">
                        <small class="form-text text-danger">{!! $errors->first('image') !!}</small>
                    </div>
                    <div>
                        <label for="hashtag">Hashtag:</label>
                        <input type="text" name="hashtag" id="hashtag" placeholder="#Hashtag" value="{{ old('hashtag') }}">
                        <small class="form-text text-danger">{!! $errors->first('hashtag') !!}</small>
                    </div>
                    <div>
                        <label for="controlNumber">Kontrollzahl:</label>
                        <input type="text" name="controlNumber" id="controlNumber" value="{{ old('controlNumber') }}">
                        <small class="form-text text-danger">{!! $errors->first('controlNumber') !!}</small>
                    </div>
                    <div>
                        <button type="submit">Absenden</button>
                    </div>
              </form>
              <br><br><br>
            </div>
        </section><!-- End Portfolio Section -->

    </main><!-- End #main -->
@endsection
