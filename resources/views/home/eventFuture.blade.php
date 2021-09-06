@if($eventsFuture->count() > 0)
<!-- ======= Services Section ======= -->
 <section id="services" class="services">
    <div class="container">

        <div class="section-title" data-aos="fade-in" data-aos-delay="100">
            @php
                //ToDo: Text bearbeiten
            @endphp
            <h2>Anstehende Termine</h2>
            <p>Magnam dolores commodi suscipit. Necessitatibus eius consequatur ex aliquid fuga eum quidem. Sit sint consectetur velit. Quisquam quos quisquam cupiditate. Et nemo qui impedit suscipit alias ea. Quia fugiat sit in iste officiis commodi quidem hic quas.</p>
        </div>

        <div class="row">

            @foreach($eventsFuture as $eventFuture)

                <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                    <div class="icon-box" data-aos="fade-up">
                        <div class="icon"><i class="bx bxl-dribbble"></i></div>
                        @php
                        @endphp
                        <h4 class="title"><a href="/Event/detail/{{ str_replace(' ', '_', $eventFuture->ueberschrift) }}_{{$eventFuture->datumvon}}">{{$eventFuture->ueberschrift}}</a></h4>
                        @if($eventFuture->datumvon == $eventFuture->datumbis)
                            <p>am {{ date("d.m.Y", strtotime($eventFuture->datumvon)) }}</p>
                        @else
                            <p>von {{ date("d.m.Y", strtotime($eventFuture->datumvon)) }} bis {{ date("d.m.Y", strtotime($eventFuture->datumbis)) }}</p>
                        @endif
                        @if(isset($eventFuture->eventGroupName->termingruppe))
                            <p class="description">{{$eventFuture->eventGroupName->termingruppe}}</p>
                        @endif
                        @php
                            $abgeschnitten=0;
                            $ausgabetext=$eventFuture->beschreibung;
                            $textlaenge=400;
                            textmax($ausgabetext,$textlaenge,$abgeschnitten);
                        @endphp
                        <p class="description">{{$ausgabetext}}</p>
                        @if ($abgeschnitten==1)
                            <a href="/Event/detail/{{ str_replace(' ', '_', $eventFuture->ueberschrift) }}_{{$eventFuture->datumvon}}" class="about-btn">mehr<i class="bx bx-chevron-right"></i></a>
                        @endif
                    </div>
                </div>

            @endforeach

        </div>

    </div>
 </section><!-- End Services Section -->
@endif
