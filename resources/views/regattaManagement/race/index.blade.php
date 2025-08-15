<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regatta Verwaltung') }} {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                    {{ $titel }}
                  </div>

                  <div class="mt-6 text-gray-500">
                   In diesem Bereich werden die Rennen der Regatta bearbeitet.
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                                @if($funktionStatus==1)
                                    Programm der
                                @endif
                                @if($funktionStatus==2)
                                    Ergebnisse der
                                @endif
                                    Rennen</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                            <div class="my-4 flex">
                               <a href="{{ route('race.create') }}">
                                <box-icon name='plus'></box-icon>
                              </a>
                            </div>

                            <div style="text-align: left">
                              <div>
                                  @if (session()->has('success'))
                                      <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                          {!! session('success') !!}
                                      </div>
                                  @endif
                                  @if (session()->has('error'))
                                      <div class="p-3 bg-red-500 text-red-800 rounded shadow-sm">
                                            {!! session('error') !!}
                                      </div>
                                  @endif
                              </div>

                              @foreach ($races as $race)
                              <div class="rounded border shadow p-3 my-2 bg-blue-200">
                                  <div class="justify-between my-2">
                                     <div>
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Rennen/edit/'.$race->id) }}">
                                            <box-icon name='edit' type='solid'></box-icon>
                                        </a>
                                        @if($race['visible']==1)
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Rennen/inaktiv/'.$race->id) }}">
                                            <box-icon name='show' ></box-icon>
                                        </a>
                                        @endif
                                        @if($race['visible']==0)
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Rennen/aktiv/'.$race->id) }}">
                                            <box-icon name='hide' ></box-icon>
                                        </a>
                                        @endif
                                        {{-- DoTo: Ziehlfotot hochladen
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Rennen/EinlaufFoto/'.$race->id) }}">
                                            <box-icon name='image'></box-icon>
                                        </a>
                                        --}}
                                        {{--
                                         Wenn $funktionStatus == 1: Es wird das Rennprogramm (also die geplanten Rennen) angezeigt und entsprechende Aktionen (z.B. „Programm der Rennen“).
                                         Wenn $funktionStatus == 2: Es werden die Rennergebnisse angezeigt und entsprechende Aktionen (z.B. „Ergebnisse der Rennen“).
                                         Bei anderen Werten werden spezielle Aktionen wie Teamverlosung angeboten.
                                        --}}
                                        @if($funktionStatus==1)
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Rennen/Programm/'.$race->id) }}">
                                            <box-icon name='file'></box-icon>
                                        </a>
                                        @endif
                                        @if($funktionStatus==2)
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Rennen/Ergebnis/'.$race->id) }}">
                                            <box-icon name='file'></box-icon>
                                        </a>
                                        <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Rennen/Zeit/'.$race->id) }}">
                                            <box-icon name='time'></box-icon>
                                        </a>
                                        @endif
                                        {{--
                                        Ausgabe der Rennaufstellung
                                        --}}
                                        @if($funktionStatus != 1 and $funktionStatus != 2)
                                          <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Teamverlosung/'.$race->id) }}">
                                             <box-icon name='clipboard'></box-icon>
                                          </a>
                                        @endif
                                        @if($funktionStatus == 1 && $race->tabele_id && $race->status <4)
                                          <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Teamverlosung/setzen/'.$race->id) }}">
                                              <box-icon name='user'></box-icon>
                                          </a>
                                        @endif
                                        @if($funktionStatus == 1 && $race->tabele_id && $race->status <2)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Teamverlosung/planen/'.$race->id) }}">
                                                <box-icon name='shuffle'></box-icon>
                                            </a>
                                        @endif
                                        @if($funktionStatus == 2 && $race->tabele_id)
                                          <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Teamverlosung/Ergebnisse/'.$race->id) }}">
                                              <box-icon name='user'></box-icon>
                                          </a>
                                        @endif
                                        @if($race['aktuellLiveVideo']==1)
                                             <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Rennen/liveAktuell/inaktiv/'.$race->id) }}">
                                                 <box-icon name='pin' type='solid'></box-icon>
                                             </a>
                                         @endif
                                        @if($race['aktuellLiveVideo']==0)
                                             <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Rennen/liveAktuell/aktiv/'.$race->id) }}">
                                                 <box-icon name='pin' ></box-icon>
                                             </a>
                                         @endif
                                         @if($race['sliteShowResult']==1)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Rennen/sliteShowResult/deactivate/'.$race->id) }}" title="Slideshow-Ergebnis AUS">
                                                <box-icon name='slideshow' type='solid' color="orange"></box-icon>
                                            </a>
                                        @else
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Rennen/sliteShowResult/activate/'.$race->id) }}" title="Slideshow-Ergebnis EIN">
                                                <box-icon name='slideshow'></box-icon>
                                            </a>
                                        @endif
                                        @if($race['liveStream']==1)
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Rennen/liveStream/deactivate/'.$race->id) }}" title="Livestream AUS">
                                                <box-icon name='video' type='solid' color="red"></box-icon>
                                            </a>
                                        @else
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('/Rennen/liveStream/activate/'.$race->id) }}" title="Livestream EIN">
                                                <box-icon name='video'></box-icon>
                                            </a>
                                        @endif
                                     </div>
                                  </div>
                                  <div class="justify-between my-2">
                                    <div class="flex">
                                      <p class="font-bold text-lg">
                                          @if($race->nummer!=Null or $race->nummer!="")
                                          {{ $race->nummer }}. {{ $race->rennBezeichnung }}
                                          @endif
                                          @if($race->tabele_id>0)
                                              <br>Wertung: {{ $race->raceTabele->ueberschrift }}
                                              @if($race->mix==1)
                                                  <br>Mix Rennen
                                              @endif
                                          @endif
                                          @if($race->bahnen>0)
                                              <br>Bahnen: {{ $race->bahnen }}
                                          @endif
                                          <br>Regatta Abschnitt: {{ $race->level }}
                                          @if($race->programmDatei!=Null && $funktionStatus==1)
                                              <br><a href="/storage/raceDokumente/{{ $race->programmDatei }}" target="_blank">{{ $race->fileProgrammDatei }}</a>
                                          @endif
                                          @if($race->ergebnisDatei!=Null && $funktionStatus==2)
                                              <br><a href="/storage/raceDokumente/{{ $race->ergebnisDatei }}" target="_blank">{{ $race->fileErgebnisDatei }}</a>
                                          @endif
                                        </p>
                                      <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $race->updated_at->diffForHumans() }}</p>
                                    </div>
                                    <div class="flex">
                                        am {{ date("d.m.Y", strtotime($race->rennDatum)) }} um {{ date("H:i", strtotime($race->rennUhrzeit)) }} Uhr
                                        @if($race->ergebnisDatei==Null)
                                          voraussichtlich
                                        @else
                                          gestartet um
                                        @endif
                                        {{ date("H:i", strtotime($race->verspaetungUhrzeit)) }} Uhr
                                        <br>Status: {{ $race->status }}
                                    </div>
                                  </div>

                              </div>
                              @endforeach

                              {{ $races->links() }}

                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Regattamenu"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            </div>
                          </div>

                      </div>
                  </div>

                  <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
                     <div class="flex items-center">

                       <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">

                       </div>

                     </div>

                  </div>
              </div>
            </div>
        </div>
    </div>
</x-app-layout>
