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
                        <label for="name">Nummer:</label>
                        {{ $race->nummer }} {{ $race->rennBezeichnung }}
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
                               @endif
                               @if($race->bahnen>0)
                                   <br>Bahnen: {{ $race->bahnen }}
                               @endif

                               @php
                                   $rennUhrzeitAlt= substr($race->rennUhrzeit, 0, -3);
                               @endphp
                               <label for="name">Startzeit:</label>
                               {{ $rennUhrzeitAlt }} Uhr {{ \Carbon\Carbon::parse($race->rennDatum)->format('d.m.Y') }}
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
                                 Rennergebniss eingetragen, geprüft und gewertet
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
                                    @php
                                      $platz=0;
                                    @endphp
                                    @foreach($lanes as $lane)
                                        @php
                                            $platz++
                                        @endphp
                                        <div class="my-4" >
                                            @if($lane->mannschaft_id>0)
                                                @if($platzRennen==1)
                                                <label for="name">Platz:</label>
                                                {{ $lane->platz }} <br>
                                                @endif
                                                @if($platzRennen==2)
                                                    <label for="name">Platz:</label>
                                                    {{ $platz }}
                                                        <label for="name">Zeit:</label>
                                                        @if($highhour>0)
                                                            @if($lane->hundert<10)
                                                                @php
                                                                    $text="0".$lane->hundert;
                                                                @endphp
                                                                {{ $zeit }},{{ $text }}
                                                            @else
                                                                {{ $zeit }},{{ $lane->hundert }}
                                                            @endif
                                                        @else
                                                            @php
                                                                $time = \Carbon\Carbon::createFromFormat('H:i:s', $lane->zeit);
                                                                $minutes = $time->format('i');
                                                                $seconds = $time->format('s');
                                                            @endphp
                                                                @if($lane->hundert<10)
                                                                    @php
                                                                        $text="0".$lane->hundert;
                                                                    @endphp
                                                                    {{ $minutes }}:{{ $seconds }},{{ $text }}
                                                                @else
                                                                    {{ $minutes }}:{{ $seconds }},{{ $lane->hundert }}
                                                                @endif
                                                        @endif
                                                    <br>
                                                @endif
                                            @endif
                                            <label for="name">Bahn:</label>
                                            {{ $lane->bahn }}
                                            @if($lane->mannschaft_id!=Null)
                                                {{ $lane->regattaTeam->teamname }}
                                            @endif
                                            @if($race->mix == 1)
                                              - {{ $lane->getTableLane->ueberschrift }}
                                            @endif
                                        </div>
                                    @endforeach
                                <br>
                                <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Rennen/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

