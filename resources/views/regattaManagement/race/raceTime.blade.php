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
                        Rennen: {{ $race->nummer }}<br>
                        {{ $race->rennBezeichnung }}<br>
                        um {{ date("H:i", strtotime($race->rennUhrzeit)) }} Uhr am {{ date("d.m.Y", strtotime($race->rennDatum)) }}
                    </div>

                    <div class="mt-6 text-gray-500">
                        @php
                            // ToDo: Beschreibungstext überarbeiten
                        @endphp
                        Bitte die aktuelle Startzeit des Rennens eingeben.
                    </div>
                </div>
                <form autocomplete="off" action="{{ url('Rennen/Zeit/update/'.$race->id) }}" method="post" enctype="multipart/form-data">
                    @csrf
                    @php
                        // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                    @endphp
                    <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Startzeit des Rennens eingeben</div>
                            </div>

                            <div class="ml-12">
                                <div class="mt-2 text-sm text-gray-500">
                                    <div>
                                        @if (session()->has('success'))
                                            <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                                {!! session('success') !!}
                                            </div>
                                        @endif
                                    </div>
                                    <div class="my-4" >
                                        @php
                                            $ractetime= substr($race->verspaetungUhrzeit, 0, -3);
                                        @endphp
                                        <label for="name">Wann wurde das Rennen gestartet:</label>
                                        <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennUhrzeit') ? 'bg-red-300' : '' }}"
                                               id="rennUhrzeit" name="rennUhrzeit" value="{{ old('rennUhrzeit') ?? $ractetime }}">
                                        <small class="form-text text-danger">{!! $errors->first('rennUhrzeit') !!}</small>
                                    </div>
                                    <div class="my-4" >
                                        <label for="name">Richtige Rennzeit:</label>
                                        <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennzeit') ? 'bg-red-300' : '' }}"
                                               id="rennzeit" name="rennzeit" value="1"
                                            @if(old('rennzeit')==1 or $race->rennzeit==1)
                                                   checked
                                            @endif
                                        >
                                        <small class="form-text text-danger">{!! $errors->first('rennzeit') !!}</small>
                                    </div>
                                    <div class="my-4">
                                        <label for="rennzeit_vorsprung">Vorsprung der Rennzeit mitnehmen:</label>
                                        <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2"
                                               id="rennzeit_vorsprung" name="rennzeit_vorsprung" value="1"
                                               @if(old('rennzeit_vorsprung', Session::get('regattaRennzeitVorsprung'))==1) checked @endif>
                                    </div>
                                    <div>
                                        <label for="name">Zeit in Minuten die pro Rennen aufgeholt werden kann:</label>
                                        <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zeit') ? 'bg-red-300' : '' }}"
                                               id="zeit" name="zeit" value="{{ Session::get('regattaZeit') }}" min="0" max="59">
                                        <small class="form-text text-danger">{!! $errors->first('zeit') !!}</small>
                                    </div>
                                    <div>
                                        <label for="name">Minimaler Zeitabstand in Minuten:</label>
                                        <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zeitMinAbstand') ? 'bg-red-300' : '' }}"
                                               id="zeit" name="zeitMinAbstand" value="{{ Session::get('regattaZeitMinAbstand') }}" min="0" max="59">
                                        <small class="form-text text-danger">{!! $errors->first('zeitMinAbstand') !!}</small>
                                    </div>
                                    <div class="py-2">
                                        <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Zeit speichern</button>
                                    </div>
                                    <br>
                                    <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Rennen/Ergebnisse"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                                </div>
                            </div>

                        </div>

                        <div class="p-6">
                            <div class="flex items-center">
                                <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold"></div>
                            </div>

                            <div class="ml-12">
                                <div class="mt-2 text-sm text-gray-500">

                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
