@if($eventsFuture->count() > 0)
<!-- ======= Services Section ======= -->
 <section id="services" class="services">
    <div class="container">
        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>Termine</h2>
            @if($eventsFuture->count()>4)
               <div class="read-more">
                 <a href="{{ url('EventFuture') }}">alle Termine<i class="icofont-arrow-right"></i></a>
               </div>
            @endif
            @php
                //ToDo: Text bearbeiten
//             //<p>Text ?</p>
            @endphp
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
                        <div class="icon"><i class="bx bxl-dribbble"></i></div>
                        <h4 class="title"><a href="/Event/detail/{{ str_replace(' ', '_', $eventFuture->ueberschrift) }}_{{$eventFuture->datumvon}}">{{$eventFuture->ueberschrift}}</a></h4>
                        @if(isset($eventFuture->eventGroupName->termingruppe))
                          <p class="description">{{$eventFuture->eventGroupName->termingruppe}}</p>
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
                             <a href="/Event/detail/{{ str_replace(' ', '_', $eventFuture->ueberschrift) }}_{{$eventFuture->datumvon}}" class="about-btn">
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
