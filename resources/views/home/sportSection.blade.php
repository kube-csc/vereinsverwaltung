@include('_partials.function')
@php
    $textlaengeabteilung = 350;
    $textlaenge = ($abteilungsCount / 2) * ($textlaengeabteilung + 100);
    if ($textlaenge < 1000) {
        $textlaenge = 1000;
    }

    $i = 0;
    $lastAbteilungHome = null;
    foreach ($abteilungHomes as $abteilungHome) {
        $i++;
        if ($i == $abteilungHomesCount) {
            $lastAbteilungHome = $abteilungHome;
        }
    }
@endphp

@if($lastAbteilungHome)
    @php
        $abgeschnitten = 0;
        $ausgabetext = '';
        if (isset($lastAbteilungHome->event_id)) {
            if ($lastAbteilungHome->status == 1 && $lastAbteilungHome->event->nachtermin != '') {
                $ausgabetext = $lastAbteilungHome->event->nachtermin;
            } else {
                $ausgabetext = $lastAbteilungHome->event->beschreibung;
                textmax($ausgabetext, $textlaenge, $abgeschnitten);
            }
        }
    @endphp

    <!-- ======= About Section ======= -->
    <section id="about" class="about">
        <div class="container">
            <div class="row no-gutters">
                <div class="content col-xl-5 d-flex align-items-stretch" data-aos="fade-up">
                    <div class="content">
                        @if(env('APP_SOZIALMEDINANZEIGE') == 'ja')
                            <!-- ======= Facebook======= -->
                            <!-- ToDo: Facebook funktioniert nicht -->
                            <center>
                                <div class="fb-like"
                                     data-href="http://www.{{ str_replace('_' , ' ' , env('VEREIN_DOMAIN')) }}"
                                     data-send="true" data-layout="box_count" data-width="183"
                                     data-show-faces="true" data-font="arial"></div>
                            </center>
                        @endif
                        <h3>{{ $lastAbteilungHome->abteilung }}</h3>
                        {!! $ausgabetext !!}
                        @if ($abgeschnitten == 1)
                            <div class="read-more">
                                <a href="/{{ env('MENUE_ABTEILUNG') }}/{{ str_replace(' ', '_', $lastAbteilungHome->abteilung) }}"
                                   class="icofont-arrow-right">
                                    mehr
                                </a>
                            </div>
                        @endif
                    </div>
                </div>

                @if($lastAbteilungHome->domain != $_SERVER["HTTP_HOST"] || $lastAbteilungHome->status == 1)
                    <div class="col-xl-7 d-flex align-items-stretch">
                        <div class="icon-boxes d-flex flex-column justify-content-center">
                            @php
                                $k = 0;
                                $time = -100;
                            @endphp
                            @foreach ($abteilungs as $abteilung)
                                @php
                                    $k++;
                                    $time = $time + 100;
                                    $abgeschnitten = 0;
                                    if ($abteilung->event_id > 0) {
                                        if ($abteilung->event->nachtermin == '') {
                                            $ausgabetext = $abteilung->event->beschreibung;
                                            textmax($ausgabetext, $textlaengeabteilung, $abgeschnitten);
                                        } else {
                                            $ausgabetext = $abteilung->event->nachtermin;
                                        }
                                    }
                                @endphp

                                @if($k == 1)
                                    <div class="row">
                                @endif

                                <div class="col-md-6 icon-box" data-aos="fade-up" @if($k > 1) data-aos-delay="{{ $time }}" @endif>
                                    <a href="/{{ env('MENUE_ABTEILUNG') }}/{{ str_replace(' ' , '_' , $abteilung->abteilung) }}">
                                        <h4>{{ $abteilung->abteilung }}</h4>
                                    </a>
                                    @if ($abteilung->event_id > 0)
                                        <p>
                                            {!! $ausgabetext !!}
                                            @php
                                                $sportTeams = DB::table('sport_sections')
                                                    ->where('status', '>', '0')
                                                    ->where('sportSection_id', $abteilung->id)
                                                    ->get();
                                                $first = 0;
                                            @endphp

                                            @foreach($sportTeams as $sportTeam)
                                                @if ($first == 0)
                                                    <br>
                                                    <h5><b>{{ env('MENUE_MANNSCHAFTEN') }}:</b></h5>
                                                    <ul>
                                                    @php $first = 1; @endphp
                                                @endif
                                                <li>{{ $sportTeam->abteilung }}</li>
                                            @endforeach

                                            @if ($first == 1)
                                                </ul>
                                            @endif

                                            @if ($abgeschnitten == 1 || $first == 1)
                                                <div class="read-more">
                                                    <a href="/{{ env('MENUE_ABTEILUNG') }}/{{ str_replace(' ', '_', $abteilung->abteilung) }}"
                                                       class="icofont-arrow-right">mehr</a>
                                                </div>
                                            @endif
                                        </p>
                                    @endif
                                </div>

                                @if ($k == 2)
                                    @php $k = 0; @endphp
                                    </div>
                                @endif
                            @endforeach
                            @if($k == 1)
                                </div>
                            @endif
                        </div><!-- End .icon-boxes -->
                    </div>
                @endif
            </div>
        </div>
    </section><!-- End About Section -->
@endif
