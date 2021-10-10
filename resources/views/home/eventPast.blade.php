@if($eventsPast->count() > 0)
  <!-- ======= Services Section ======= -->
  <section id="services" class="services">
    <div class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            <h2>Die neusten Berichte, Fotos und Videos</h2>
            @if($eventsPast->count()>4)
                <div class="read-more">
                    <a href="{{ url('EventPast') }}">alle Termine<i class="icofont-arrow-right"></i></a>
                </div>
            @endif
            @php
                //ToDo: Text eingeben Vergangende Termine
               //<p>Text</p>
            @endphp
           </div>

        <div class="row">
            @php
                $countMax=$eventsFuture->count();
                $count=5;
            @endphp
            @foreach($eventsPast as $eventPast)
               @php
                 --$count;
               @endphp
               @if( $count != 0 && $countMax != 6)
                <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                    <div class="icon-box" data-aos="fade-up">
                        <div class="icon"><i class="bx bxl-dribbble"></i></div>

                        <h4 class="title"><a href="/Event/detail/{{ str_replace(' ', '_', $eventPast->ueberschrift) }}_{{$eventPast->datumvon}}">{{$eventPast->ueberschrift}}</a></h4>
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
                            <a href="/Event/detail/{{ str_replace(' ', '_', $eventPast->ueberschrift) }}_{{$eventPast->datumvon}}" class="about-btn">mehr
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
