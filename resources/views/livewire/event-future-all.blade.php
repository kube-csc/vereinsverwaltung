<section id="services" class="services"> <!-- ======= Services Section ======= -->
    <div class="container">
        <div class="section-title" data-aos="fade-in" data-aos-delay="50">
            <h2>Termine</h2>
            @php
                //ToDo: Text bearbeiten
               //<p>Text ?</p>
            @endphp
                @php //ToDo: div class bearbeiten für filterabfrage und Filterausgabe @endphp
                <div class="col-md-6 d-flex align-items-md-center">
                    <div>
                        <label>Filter</label>
                        <input wire:model.debounce.1000ms="search" maxlength="50" size="25">
                    </div>
                    <div>
                        <i class="bx bx-chevron-up" wire:click="monthIncrease"></i></box-icon>
                        <label>Monat</label>
                        <i class="bx bx-chevron-down" wire:click="monthDecrease"></i>
                        <input wire:model.debounce.2000ms="month" type="number">
                    </div>
                    <div>
                        <i class="bx bx-chevron-up" wire:click="yearIncrease"></i>
                        </box-icon><label>Jahr</label>
                        <i class="bx bx-chevron-down" wire:click="yearDecrease"></i>
                        <input wire:model.debounce.1000ms="year" type="number">
                    </div>
                </div>
                <div class="col-md-6 d-flex align-items-stretch ">
                    <div>
                    @if($search != "")
                        Filter: {{ $search }}
                    @endif
                    </div>
                    <div>
                    @if($month != "")
                        Monat: {{ $month }}
                    @endif
                    </div>
                    <div>
                    @if($year != "")
                        Jahr: {{ $year }}
                    @endif
                    </DIV>
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
                            @if(isset($eventFuture->sportSectionName->abteilung))
                                <p class="description">{{ $eventFuture->sportSectionName->abteilung }}</p>
                            @endif
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
        <br>
        {{ $eventsFuture->links('livewire.pagination') }}

    </div>
</section><!-- End Services Section -->


@php
    // ToDo: Funktion anderes Integrieren
    function textmax(&$beschreibung,$sollang,&$abgeschnitten)
    {
     $abgeschnitten=0;
     $laenge=strlen($beschreibung);
     if ($laenge>$sollang)
      {
        $beschreibung=substr($beschreibung,0,$sollang);
        $beschreibung=$beschreibung."...";
        $abgeschnitten=1;
      }
    }
@endphp
