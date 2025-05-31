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
                        Bitte setze die Mannschaften aus den Tabellen.
                    </div>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">

                        <div class="flex items-center">
                        {{--    <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Bahn Zuweisungen.</div>   --}}

                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">
                               <label for="name">Nummer:</label>
                               {{ $race->nummer }} - {{ $race->rennBezeichnung }}
                               <br>
                               @if($tabele)
                               <label for="name">Tabelle:</label>
                               {{ $tabele->ueberschrift }}
                                    @if($race->mix==1)
                                        <br>Mix Rennen
                                    @endif
                               @endif
                               @if($race->bahnen>0)
                                    <br>Bahnen: {{ $race->bahnen }}
                               @endif
                                @php
                                    $rennUhrzeitAlt= substr($race->rennUhrzeit, 0, -3);
                                @endphp
                                <br>
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

                                <form autocomplete="off" action="{{ url('/Teamverlosung/planen/update/'.$race->id) }}" method="post" enctype="multipart/form-data">
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
                                            <label for="name">Bahn {{ $lane->bahn }} setzen aus Tabelle:</label>

                                            <input type="hidden" name="laneId[{{$bahn}}]" value="{{ $lane->id }}">
                                            <br>
                                            <select name="tabelevorId[{{$bahn}}]]" id="tabelevorId[{{$bahn}}]]" >
                                                <option value=""
                                                    @if( $lane->tabelevor_id == NULL )
                                                            selected
                                                    @endif
                                                >keine Tabelle</option>

                                                @foreach($tabeleVorRennens as $tabeleVorRennen)
                                                    <option value="{{ $tabeleVorRennen->id }}"
                                                        @if( $tabeleVorRennen->id == $lane->tabelevor_id )
                                                                selected
                                                        @endif
                                                        @selected( $lane->tabelevor_id == 0 && $tabele->gruppe_id == $tabeleVorRennen->gruppe_id)
                                                    >{{ $tabeleVorRennen->ueberschrift }}</option>
                                                @endforeach
                                            </select>

                                            <select name="platzvor[{{$bahn}}]" id="platzvor[{{$bahn}}]">
                                                <option value="">kein Platz</option>
                                                @for($i = 1; $i <= $teamsCount; $i++)
                                                    <option value="{{ $i }}" @if(isset($lane->platzvor) && $lane->platzvor == $i) selected @endif>{{ $i }}</option>
                                                @endfor
                                            </select>

                                            @if($race->mix == 1)
                                               <select name="tabeleId[{{$bahn}}]]" id="tabeleId[{{$bahn}}]]" >
                                                    <option value=""
                                                            @if( $lane->tabele_id == NULL )
                                                                selected
                                                        @endif
                                                    >keine Tabelle</option>

                                                    @foreach($tabeleAlls as $tabeleAll)
                                                        <option value="{{ $tabeleAll->id }}"
                                                                @if( $tabeleAll->id == $lane->tabele_id )
                                                                    selected
                                                            @endif
                                                        >{{ $tabeleAll->ueberschrift }}</option>
                                                    @endforeach
                                               </select>
                                            @else
                                                <input type="hidden" name="tabeleId[{{$bahn}}]" value="{{ $lane->tabele_id }}">
                                            @endif

                                        </div>
                                    @endforeach
                                    <input type="hidden" name="bahnMax" value="{{ $bahn }}">
                                    <div class="py-2">
                                        <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderung speichern</button>
                                    </div>

                                </form>
                                <br>

                                @if($previousRace)
                                <a class="p-2 bg-blue-500 w-40 rounded shadow text-white mr-2" href="{{ url('/Teamverlosung/setzen/'.$previousRace->id) }}">
                                    <i class="fas fa-arrow-circle-up"></i>Rennen Zurück</a>
                                </a>
                                @endif

                                <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Rennen/alle">
                                    <i class="fas fa-arrow-circle-up"></i>Zurück
                                </a>

                                @if($nextRace)
                                <a class="p-2 bg-blue-500 w-40 rounded shadow text-white ml-2" href="{{ url('/Teamverlosung/setzen/'.$nextRace->id) }}">
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

