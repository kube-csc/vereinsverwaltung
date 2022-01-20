<section id="services" class="services"> <!-- ======= Services Section ======= -->
    <div class="container">

        <div class="section-title">
            <h2>Die neusten Berichte, Fotos und Videos</h2>
            @php
                //ToDo: Text eingeben Vergangende Termine
               //<p>Text</p>
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
                $countMax=$eventsPast->count();
                $count=5;
            @endphp
            @if($eventsPastCount==0)
                <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                    <div class="icon-box">
                        Für den eingestellten Filter sind keine Termine vorhanden.
                    <div>
                <div>
            @endif
            @foreach($eventsPast as $eventPast)
                @php
                    --$count;
                @endphp
                @if( $count != 0 && $countMax != 6)
                    <div class="col-md-6 col-lg-3 d-flex align-items-stretch mb-5 mb-lg-0">
                        <div class="icon-box">
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

                            <h4 class="title"><a href="/Event/detail/{{ str_replace(' ', '_', $eventPast->ueberschrift) }}_{{$eventPast->datumvon}}">{{$eventPast->ueberschrift}}</a></h4>
                            <div>
                                @foreach($reports as $report)
                                    @if($report->image != Null)
                                        @if (is_file('daten/bilder/'.$report->image)==true)
                                            <img src="daten/bilder/{{$report->image}}" width="100%"/>
                                        @else
                                            @auth
                                                <p class="text-danger">Image {{$report->image}} ist nicht auf dem Server</p>
                                            @endauth
                                        @endif
                                    @endif
                                    @if($report->bild != Null)
                                        @if (is_file('storage/eventImage/'.$report->bild))
                                            <img src="storage/eventImage/{{$report->bild}}" width="100%"/>
                                        @else
                                          @auth
                                             <p class="text-danger">Bild {{$report->bild}} ist nicht auf dem Server</p>
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
                                <a href="/Event/detail/{{ str_replace(' ', '_', $eventPast->ueberschrift) }}_{{$eventPast->datumvon}}" class="about-btn">mehr
                                    <i class="bx bx-chevron-right"></i>
                                </a>
                            @endif

                        </div>
                    </div>
                @endif
            @endforeach

        </div>
        <br>
        {{ $eventsPast->links('livewire.pagination') }}

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
