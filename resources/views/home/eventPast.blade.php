@if($eventsPast->count() > 0)
  <!-- ======= Services Section ======= -->
  <section id="services" class="services">
    <div class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>Die neusten Berichte, Fotos und Videos</h2>
            @if($eventsPast->count()>4)
                <div class="read-more">
                    <a href="{{ url('Berichte') }}">alle Termine<i class="icofont-arrow-right"></i></a>
                </div>
            @endif
            @php
                //ToDo: Text eingeben Vergangende Termine
               //<p>Text</p>
            @endphp
           </div>

        <div class="row">
            @php
                $countMax=$eventsPast->count();
                $count=5;
            @endphp
            @foreach($eventsPast as $eventPast)
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
                              $reports  = DB::table('reports')->where('event_id' , $eventPast->id)
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
                              $reports  = DB::table('reports')->where('event_id' , $eventPast->id)
                                          ->where('visible' , 1)
                                          ->where('webseite' , 1)
                                          ->where('typ' , 1)
                                          ->orderby('startseite' , 'desc')
                                          ->orderby('position')
                                          ->limit(1)
                                          ->get();
                          @endphp
                        @endauth
                        <h4 class="title"><a href="/Bericht/{{$eventPast->id}}">{{$eventPast->ueberschrift}}</a></h4>
                        <div>
                            @foreach($reports as $report)
                                @if($report->image != Null)
                                    @if (!is_file('/daten/bilder/'.$report->image)==true)
                                      <img src="/daten/bilder/{{$report->image}}" width="100%" alt="{{$report->titel}}"/>
                                    @else
                                        @auth
                                           <p class="text-danger">Image {{$report->image}} ist nicht auf dem Server vorhanden.</p>
                                        @endauth
                                    @endif
                                @endif
                                @if($report->bild != Null)
                                    @if (!is_file('/storage/eventImage/'.$report->bild))
                                        <img src="/storage/eventImage/{{$report->bild}}" width="100%" alt="{{$report->titel}}"/>
                                    @else
                                        @auth
                                           <p class="text-danger">Bild {{$report->bild}} ist nicht auf dem Server vorhanden.</p>
                                        @endauth
                                    @endif
                                @endif
                            @endforeach
                        </div>

                       @if(isset($eventPast->sportSectionName->abteilung))
                            <p class="description">{{ $eventPast->sportSectionName->abteilung }}</p>
                        @endif
                        @if(isset($eventPast->eventGroupName->termingruppe))
                            <p class="description">
                                {{$eventPast->eventGroupName->termingruppe}}
                            </p>
                        @endif
                        @if($eventPast->datumvon == $eventPast->datumbis)
                            <p>am {{ date("d.m.Y", strtotime($eventPast->datumvon)) }}</p>
                        @else
                            <p>von {{ date("d.m.Y", strtotime($eventPast->datumvon)) }}<br>
                               bis {{ date("d.m.Y", strtotime($eventPast->datumbis)) }}</p>
                        @endif
                        @php
                            $abgeschnitten=0;
                            $ausgabetext=$eventPast->nachtermin;
                            $textlaenge=400;
                            textmax($ausgabetext,$textlaenge,$abgeschnitten);
                        @endphp
                           <p class="description">
                            {!! $ausgabetext !!}
                           </p>
                        @if($abgeschnitten==1)
                            <a href="/Bericht/{{$eventPast->id}}" class="about-btn">mehr
                                <i class="bx bx-chevron-right"></i>
                            </a>
                        @endif

                    </div>
                </div>
               @endif
            @endforeach

        </div>

    </div>
  </section><!-- End Services Section -->
@endif
