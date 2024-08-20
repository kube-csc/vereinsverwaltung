<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regatta Verwaltung') }} {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        Rennen: {{ old('ueberschrift') ?? $race->rennBezeichnung }}
                    </div>

                    <div class="mt-6 text-gray-500">
                        @php
                            // ToDo: Beschreibungstext überarbeiten
                        @endphp
                        Bitte verwalten Sie die Bahn Zuweisungen.
                    </div>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">

                        <div class="flex items-center">
                        {{--    <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Bahn Zuweisungen.</div>   --}}
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                               <label for="name">Nummer:</label>
                               {{ $race->nummer }} {{ $race->rennBezeichnung }}
                               <br>
                               @if($tabele)
                               <label for="name">Tabelle:</label>
                               {{ $tabele->ueberschrift }}
                                    @if($race->mix==1)
                                        <br>Mix Rennen
                                    @endif
                               <br>
                               @endif
                                @php
                                    $rennUhrzeitAlt= substr($race->rennUhrzeit, 0, -3);
                                @endphp
                                <label for="name">Startzeit:</label>
                                {{ $rennUhrzeitAlt }} Uhr {{ \Carbon\Carbon::parse($race->rennDatum)->format('d.m.Y') }}
                                <br>
                                <label for="name">Regatta Abschnitt:</label>
                                {{ $race->level }}
                                <br>
                                <label for="name">Rennstatus:</label>
                                @if($race->status==0)
                                    Bahnen noch nicht besetzt
                                @endif
                                @if($race->status==1)
                                    Bahnen besetzt noch nicht geprüft
                                @endif
                                @if($race->status==2)
                                    Bahnen besetzt und geprüft
                                @endif
                                @if($race->status==3)
                                    Rennergebniss eingetragen
                                @endif
                                @if($race->status==4)
                                  Rennergebniss gewertet
                                @endif
                                <br>
                                @php
                                    $veroeffentlichungUhrzeitAlt= substr($race->veroeffentlichungUhrzeit, 0, -3);
                                @endphp
                                <label for="name">Veröffentlichungszeit der Ergebnisse:</label>
                                {{ $veroeffentlichungUhrzeitAlt }} Uhr
                            </div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">

                                  <form autocomplete="off" action="{{ url('/Rennergebnisse/update/'.$race->id) }}" method="post" enctype="multipart/form-data">
                                        @csrf
                                        @php
                                            // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                          $bahn=0;
                                        @endphp
                                        @foreach($lanes as $lane)
                                            @php
                                                $bahn++
                                            @endphp

                                            <div class="my-4" >
                                                <select name="platz[{{$bahn}}]]" id="platz[{{$bahn}}]]" >
                                                        <option value="0"
                                                                @if( $lane->platz == NULL )
                                                                    selected
                                                            @endif
                                                        >kein Platz</option>

                                                    @for($i=1; $i<= $race->bahnen; $i++)
                                                        <option value="{{ $i }}"
                                                                @if( $lane->platz == $i )
                                                                    selected
                                                            @endif
                                                        >{{ $i }}
                                                        </option>
                                                    @endfor
                                                </select>
                                                <label for="name">Bahn:</label>
                                                {{ $lane->bahn }}
                                                @if($lane->mannschaft_id>0)
                                                    {{ $lane->regattaTeam->teamname }}
                                                @endif
                                                <input type="hidden" name="laneId[{{$bahn}}]" value="{{ $lane->id }}">
                                            </div>
                                        @endforeach

                                      <div class="my-4" >
                                          @php
                                              $ractetime= substr($ractetime, 0, -3);
                                          @endphp
                                          <label for="rennUhrzeit">Wann wurde das Rennen gestartet:</label>
                                          <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennUhrzeit') ? 'bg-red-300' : '' }}"
                                                 id="rennUhrzeit" name="rennUhrzeit" value="{{ old('rennUhrzeit') ?? $ractetime }}">
                                          <small class="form-text text-danger">{!! $errors->first('rennUhrzeit') !!}</small>
                                      </div>
                                      <div class="my-4" >
                                          <label for="rennzeit">Richtige Rennzeit:</label>
                                          <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennzeit') ? 'bg-red-300' : '' }}"
                                                 id="rennzeit" name="rennzeit" value="1"
                                                 @if(old('rennzeit')==1 or $race->rennzeit==1)
                                                     checked
                                              @endif
                                          >
                                          <small class="form-text text-danger">{!! $errors->first('rennzeit') !!}</small>
                                      </div>
                                      <div>
                                          <label for="zeit">Zeit in Minuten die pro Rennen aufgeholt werden kann:</label>
                                          <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zeit') ? 'bg-red-300' : '' }}"
                                                 id="zeit" name="zeit" value="{{ Session::get('regattaZeit') }}" min="0" max="59">
                                          <small class="form-text text-danger">{!! $errors->first('zeit') !!}</small>
                                      </div>
                                      <div>
                                          <label for="zeitMinAbstand">Minimaler Zeitabstand in Minuten:</label>
                                          <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zeitMinAbstand') ? 'bg-red-300' : '' }}"
                                                 id="zeitMinAbstand" name="zeitMinAbstand" value="{{ Session::get('regattaZeitMinAbstand') }}" min="0" max="59">
                                          <small class="form-text text-danger">{!! $errors->first('zeitMinAbstand') !!}</small>
                                      </div>

                                        <input type="hidden" name="bahnMax" value="{{ $bahn }}">
                                        <div class="py-2">
                                            <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderung speichern</button>
                                        </div>

                                  </form>
                                <br>
                                @if($previousRace)
                                    <a class="p-2 bg-blue-500 w-40 rounded shadow text-white mr-2" href="{{ url('/Teamverlosung/Ergebnisse/'.$previousRace->id) }}">
                                        <i class="fas fa-arrow-circle-up"></i>Rennen Zurück</a>
                                    </a>
                                @endif

                                <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Rennen/alle">
                                    <i class="fas fa-arrow-circle-up"></i>Zurück
                                </a>

                                @if($nextRace)
                                    <a class="p-2 bg-blue-500 w-40 rounded shadow text-white ml-2" href="{{ url('/Teamverlosung/Ergebnisse/'.$nextRace->id) }}">
                                        <i class="fas fa-arrow-circle-up"></i>Rennen weiter</a>
                                    </a>
                                @endif
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>

