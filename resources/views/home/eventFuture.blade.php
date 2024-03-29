@if($eventsFuture->count() > 0)
<!-- ======= Services Section ======= -->
 <section id="services" class="services">
    <div class="container">
        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>Termine</h2>
            @if($eventsFuture->count()>4)
               <div class="read-more">
                 <a href="{{ url('/Termine') }}">alle Termine<i class="icofont-arrow-right"></i></a>
               </div>
            @endif
        </div>
        <div class="row">
             @php
             $countMax=$eventsFuture->count();
             $count=5;
             @endphp
             @foreach($eventsFuture as $eventFuture)
                 @php
                  --$count;
                 @endphp
               @if( $count != 0 && $countMax != 6)
                 <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                    <div class="icon-box" data-aos="fade-up">
                         @auth
                            @php
                                /*
                                 <div class="icon"><i class="bx bxl-dribbble"></i></div>
                                */
                                  // ToDo: Query abfrage im Controller verlegen
                                  $reports  = DB::table('reports')->where('event_id' , $eventFuture->id)
                                              ->where('visible' , 1)
                                              ->where('typ' , 1)
                                              ->orderby('startseite' , 'desc')
                                              ->orderby('position')
                                              ->limit(1)
                                              ->get();
                            @endphp
                        @elseguest
                            @php
                                /*
                                 <div class="icon"><i class="bx bxl-dribbble"></i></div>
                                */
                                  // ToDo: Query abfrage im Controller verlegen
                                  $reports  = DB::table('reports')->where('event_id' , $eventFuture->id)
                                              ->where('visible' , 1)
                                              ->where('webseite' , 1)
                                              ->where('typ' , 1)
                                              ->orderby('startseite' , 'desc')
                                              ->orderby('position')
                                              ->limit(1)
                                              ->get();
                            @endphp
                        @endauth
                        <h4 class="title">
                        <a href="/Bericht/{{ $eventFuture->id }}">{{ $eventFuture->ueberschrift }}</a></h4>
                        <div>
                            @foreach($reports as $report)
                                @if($report->image != Null)
                                    @if (!is_file('/daten/bilder/'.$report->image)==true)
                                        <img src="/daten/bilder/{{ $report->image }}" width="100%" alt="{{ $report->titel }}"/>
                                    @else
                                        @auth
                                            <p class="text-danger">Image {{ $report->image }} ist nicht auf dem Server vorhanden.</p>
                                        @endauth
                                    @endif
                                @endif
                                @if($report->bild != Null)
                                    @if (!is_file('/storage/eventImage/'.$report->bild))
                                        <img src="/storage/eventImage/{{ $report->bild }}" width="100%" alt="{{ $report->titel }}"/>
                                    @else
                                        @auth
                                            <p class="text-danger">Bild {{ $report->bild }} ist nicht auf dem Server vorhanden.</p>
                                        @endauth
                                    @endif
                                @endif
                            @endforeach
                        </div>
                        @if(isset($eventFuture->sportSectionName->abteilung))
                          <p class="description">{{ $eventFuture->sportSectionName->abteilung }}</p>
                        @endif
                        @if(isset($eventFuture->eventGroupName->termingruppe))
                          <p class="description">{{ $eventFuture->eventGroupName->termingruppe }}</p>
                        @endif
                        @if($eventFuture->datumvon == $eventFuture->datumbis)
                          <p>am {{ date("d.m.Y", strtotime($eventFuture->datumvon)) }}</p>
                        @else
                          <p>von {{ date("d.m.Y", strtotime($eventFuture->datumvon)) }}<br>
                             bis {{ date("d.m.Y", strtotime($eventFuture->datumbis)) }}
                          </p>
                        @endif
                        @if($eventFuture->datumbisa <= Illuminate\Support\Carbon::now() && isset($eventFuture->datumbisa))
                            <p>
                                <b>Anmeldezeitraum:</b><br>
                                @if(isset($eventFuture->datumvona))
                                    von {{ date("d.m.Y", strtotime($eventFuture->datumvona)) }}<br>
                                @endif
                                    bis {{ date("d.m.Y", strtotime($eventFuture->datumbisa)) }}
                            </p>
                        @endif
                        @if(isset($eventFuture->homepage))
                          <p>
                            <b>Homepage:</b><br>
                            {{ $eventFuture->homepage }}
                          </p>
                        @endif
                        @if(isset($eventFuture->ansprechparter) or isset($eventFuture->telefon) or isset($eventFuture->email))
                         <p>
                          <b>Ansprechpartner:</b><br>
                        @endif
                        @if(isset($eventFuture->ansprechparter))
                          {{ $eventFuture->ansprechparter }}<br>
                        @endif
                        @if(isset($eventFuture->telefon))
                          <b>Telefon:</b><br>
                          {{ $eventFuture->telefon }}<br>
                        @endif
                        @if(isset($eventFuture->email))
                          <b>E-Mail:</b><br>
                          {{ $eventFuture->email }}
                        @endif
                        @if(isset($eventFuture->ansprechparter) or isset($eventFuture->telefon) or isset($eventFuture->email))
                          </p>
                        @endif
                        @php
                          $abgeschnitten=0;
                          $ausgabetext=$eventFuture->beschreibung;
                          $textlaenge=300;
                          textmax($ausgabetext,$textlaenge,$abgeschnitten);
                        @endphp
                        <p class="description">
                            {!! $ausgabetext !!}
                        </p>
                        @if ($abgeschnitten==1)
                           <div class="read-more">
                             <a href="/Bericht/{{$eventFuture->id}}" class="about-btn">
                                 mehr<i class="bx bx-chevron-right"></i>
                             </a>
                           </div>
                        @endif
                    </div>
                </div>
                @endif
            @endforeach
        </div>
    </div>
 </section><!-- End Services Section -->
@endif
