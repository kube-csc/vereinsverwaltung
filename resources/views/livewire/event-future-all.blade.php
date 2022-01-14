<section id="services" class="services"> <!-- ======= Services Section ======= -->
    <div class="container">
        <div class="section-title">
            <h2>zukünftige Termine</h2>
            @php
                //ToDo: Text bearbeiten
               //<p>Text ?</p>
            @endphp
            <div class="row g-2">
                <div class="col-md">
                    <div class="form-floating">
                        <label>Event Filter</label>
                        <input class="form-control" wire:model.debounce.1000ms="search" maxlength="50" size="25">
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-floating">
                        <i class="bx bx-chevron-down" wire:click="monthDecrease"></i>
                        <label>Monat</label>
                        <i class="bx bx-chevron-up" wire:click="monthIncrease"></i></box-icon>
                        <input class="form-control" wire:model.debounce.1000ms="month" type="number">
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-floating">
                        <i class="bx bx-chevron-down" wire:click="yearDecrease"></i>
                        </box-icon><label>Jahr</label>
                        <i class="bx bx-chevron-up" wire:click="yearIncrease"></i>
                        <input class="form-control" wire:model.debounce.1000ms="year" type="number">
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-floating">
                        <label for="name">{{env('Menue_Abteilung')}} / Mannschaften:</label><br>
                        <select wire:model="sportSection_id">
                            <!-- name="sportSection_id"  -->
                            <option value="" selected>Alle {{env('Menue_Abteilung')}} / Mannschaften</option>
                            <optgroup label="Abeilung:">
                                @php
                                    $firsttime = 0;
                                @endphp
                                @foreach ($sportSections as $sportSection)
                                    @if ($sportSection->sportSection_id > 0 && $firsttime == 0)
                                        @php
                                            $firsttime = 1;
                                        @endphp
                            </optgroup>
                            <optgroup label="Mannschaft:">
                                @endif
                                <option value="{{ $sportSection->id }}">{{ $sportSection->abteilung }}</option>
                                @endforeach
                            </optgroup>
                        </select>
                    </div>
                </div>
            </div>
            @php /*
            <div class="row g-2">
                <div class="col-md">
                    <div class="form-floating">
                      @if($search != "")
                        Filter: {{ $search }}
                      @endif
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-floating">
                      @if($month != "")
                        Monat: {{ $month }}
                      @endif
                    </div>
                </div>
                <div class="col-md">
                    <div class="form-floating">
                      @if($year != "")
                        Jahr: {{ $year }}
                      @endif
                    </div>
                </div>
            </div>
            */
            @endphp
            <br>  <!-- ToDo: Bessere Formatierung -->
        <div class="row">
            @php
                $countMax=$eventsFuture->count();
                $count=5;
            @endphp
            @if($eventsFutureCount==0)
                <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                    <div class="icon-box" data-aos="fade-up">
                       Für den eingestellten Filter sind keine Termine vorhanden.
                    <div>
                <div>
            @endif
            @foreach($eventsFuture as $eventFuture)
                @php
                    --$count;
                @endphp
                @if( $count != 0 && $countMax != 6)
                    <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                        <div class="icon-box" data-aos="fade-up">
                            @php
                                /*
                                 <div class="icon"><i class="bx bxl-dribbble"></i></div>
                                */
                               // ToDo: Query abfrage im Controller verlegen
                               $reports  = DB::table('reports')->where('event_id' , $eventFuture->id)
                                           ->where('visible' , 1)
                                           ->where('typ' , 1)
                                           ->orderby('startseite' ,'desc')
                                           ->orderby('position')
                                           ->limit(1)
                                           ->get();
                            @endphp

                            <h4 class="title"><a href="/Event/detail/{{ str_replace(' ', '_', $eventFuture->ueberschrift) }}_{{$eventFuture->datumvon}}">{{$eventFuture->ueberschrift}}</a></h4>
                            <div>
                                @foreach($reports as $report)
                                    <img src="/storage/eventImage/{{$report->bild}}" width="100%"/>
                                @endforeach
                            </div>

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
