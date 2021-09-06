@if($eventsPast->count() > 0)
  <!-- ======= Services Section ======= -->
  <section id="services" class="services">
    <div class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            @php
              //ToDo: Text bearbeiten
            @endphp
            <h2>Vergange Termine</h2>
            <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p>
        </div>

        <div class="row">

            @foreach($eventsPast as $eventPast)

                <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                    <div class="icon-box" data-aos="fade-up">
                        <div class="icon"><i class="bx bxl-dribbble"></i></div>
                        @php
                        @endphp
                        <h4 class="title"><a href="/Event/detail/{{ str_replace(' ', '_', $eventPast->ueberschrift) }}_{{$eventPast->datumvon}}">{{$eventPast->ueberschrift}}</a></h4>
                        @if($eventPast->datumvon == $eventPast->datumbis)
                            <p>am {{ date("d.m.Y", strtotime($eventPast->datumvon)) }}</p>
                        @else
                            <p>von {{ date("d.m.Y", strtotime($eventPast->datumvon)) }} bis {{ date("d.m.Y", strtotime($eventPast->datumbis)) }}</p>
                        @endif
                        @if(isset($eventPast->eventGroupName->termingruppe))
                            <p class="description">{{$eventPast->eventGroupName->termingruppe}}</p>
                        @endif
                        @php
                            $abgeschnitten=0;
                            $ausgabetext=$eventPast->nachtermin;
                            $textlaenge=400;
                            textmax($ausgabetext,$textlaenge,$abgeschnitten);
                        @endphp
                        <p class="description">{{$ausgabetext}}</p>
                        @if ($abgeschnitten==1)
                            <a href="/Event/detail/{{ str_replace(' ', '_', $eventPast->ueberschrift) }}_{{$eventPast->datumvon}}" class="about-btn">mehr<i class="bx bx-chevron-right"></i></a>
                        @endif

                    </div>
                </div>

            @endforeach

        </div>

    </div>
  </section><!-- End Services Section -->
@endif
