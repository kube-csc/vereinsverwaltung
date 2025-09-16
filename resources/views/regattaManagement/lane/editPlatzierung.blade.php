<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Platzierungen eingeben für Rennen: {{ $race->rennBezeichnung }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg p-6">
                <div class="mb-6">
                    <div class="text-lg text-gray-600 leading-7 font-semibold">
                        <label>Nummer:</label>
                        {{ $race->nummer }} {{ $race->rennBezeichnung }}<br>
                        @if($tabele)
                            <label>Tabelle:</label>
                            {{ $tabele->ueberschrift }}
                            @if($race->mix==1)
                                <br>Mix Rennen
                            @endif
                            <br>
                        @endif
                        @php
                            $rennUhrzeitAlt = substr($race->rennUhrzeit, 0, -3);
                        @endphp
                        <label>Startzeit:</label>
                        {{ $rennUhrzeitAlt }} Uhr {{ \Carbon\Carbon::parse($race->rennDatum)->format('d.m.Y') }}<br>
                        <label>Regatta Abschnitt:</label>
                        {{ $race->level }}<br>
                        <label>Rennstatus:</label>
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
                            $veroeffentlichungUhrzeitAlt = substr($race->veroeffentlichungUhrzeit, 0, -3);
                        @endphp
                        <label>Veröffentlichungszeit der Ergebnisse:</label>
                        {{ $veroeffentlichungUhrzeitAlt }} Uhr
                    </div>
                </div>
                <form action="{{ url('/Teamverlosung/platzierung/update/'.$race->id) }}" method="post">
                    @csrf
                    <table class="w-full mb-6">
                        <thead>
                            <tr>
                                <th class="text-left">Platz</th>
                                <th class="text-left">Bahn - Mannschaft</th>
                            </tr>
                        </thead>
                        <tbody>
                        @for($platz = 1; $platz <= $race->bahnen; $platz++)
                            <tr>
                                <td>{{ $platz }}</td>
                                <td>
                                    @php
                                        $selectedBahn = old('bahn_'.$platz) ?? ($lanes->where('platz', $platz)->first()->bahn ?? null);
                                    @endphp
                                    <select name="bahn_{{ $platz }}" class="border rounded p-1">
                                        <option value="">-- Bahn wählen --</option>
                                        @foreach($lanes as $bahnNr => $lane)
                                            <option value="{{ $bahnNr }}"
                                                @if($selectedBahn == $bahnNr) selected @endif>
                                                {{ $bahnNr }}{{ $bahnTeams[$bahnNr] ? ' - '.$bahnTeams[$bahnNr]->teamname : '' }}
                                            </option>
                                        @endforeach
                                    </select>
                                </td>
                            </tr>
                        @endfor
                        </tbody>
                    </table>
                    <div class="my-4">
                        <label for="newCalculate">Neue Tabellenberechnung:</label>
                        <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2"
                               id="newCalculate" name="newCalculate" value="1"
                               @if(old('newCalculate')==1) checked @endif>
                    </div>
                    <div class="my-4">
                        @php
                            $ractetime = isset($ractetime) ? substr($ractetime, 0, -3) : '';
                        @endphp
                        <label for="rennUhrzeit">Wann wurde das Rennen gestartet:</label>
                        <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2"
                               id="rennUhrzeit" name="rennUhrzeit" value="{{ old('rennUhrzeit') ?? $ractetime }}">
                    </div>
                    <div class="my-4">
                        <label for="rennzeit">Richtige Rennzeit:</label>
                        <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2"
                               id="rennzeit" name="rennzeit" value="1"
                               @if(old('rennzeit')==1 or (isset($race) && $race->rennzeit==1)) checked @endif>
                    </div>
                    <div class="my-4">
                        <label for="rennzeit_vorsprung">Vorsprung der Rennzeit mitnehmen:</label>
                        <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2"
                               id="rennzeit_vorsprung" name="rennzeit_vorsprung" value="1"
                               @if(old('rennzeit_vorsprung', Session::get('regattaRennzeitVorsprung'))==1) checked @endif>
                    </div>
                    <div>
                        <label for="zeit">Zeit in Minuten die pro Rennen aufgeholt werden kann:</label>
                        <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2"
                               id="zeit" name="zeit" value="{{ Session::get('regattaZeit') }}" min="0" max="59">
                    </div>
                    <div>
                        <label for="zeitMinAbstand">Minimaler Zeitabstand in Minuten:</label>
                        <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2"
                               id="zeitMinAbstand" name="zeitMinAbstand" value="{{ Session::get('regattaZeitMinAbstand') }}" min="0" max="59">
                    </div>
                    <div class="py-2">
                        <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Speichern</button>
                    </div>
                </form>
                <br>
                <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="{{ route('race.index') }}">
                    <i class="fas fa-arrow-circle-up"></i>Zurück
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
