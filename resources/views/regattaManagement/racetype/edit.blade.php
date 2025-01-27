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
                        Renntype: {{ old('typ') ?? $raceType->typ }}
                    </div>

                    <div class="mt-6 text-gray-500">
                        @php
                            // ToDo: Beschreibungstext überarbeiten
                        @endphp
                        Bitte gebe die Daten von Renntype ein.
                    </div>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Renntype ändern</div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">

                                <form autocomplete="off" action="{{ url('/Rennklassen/update/'.$raceType->id) }}" name="action" id="action" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @php
                                        // ToDo: @method('PUT') in Hobby Projekt noch mal erlernen
                                    @endphp
                                    <div class="my-4" >
                                        <label for="nummer">Typ:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('typ') ? 'bg-red-300' : '' }}"
                                               id="typ" placeholder="Nummer" name="nummer" value="{{ old('typ') ?? $raceType->typ }}">
                                        <small class="form-text text-danger">{!! $errors->first('typ') !!}</small>
                                    </div>

                                    <div class="my-4" >
                                        <label for="beschreibung">Beschreibung:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('beschreibung') ? 'bg-red-300' : '' }}"
                                               id="beschreibung" placeholder="Beschreibung" name="beschreibung" value="{{ old('beschreibung') ?? $raceType->beschreibung }}">
                                        <small class="form-text text-danger">{!! $errors->first('beschreibung') !!}</small>
                                    </div>

                                    <div class="my-4" >
                                        <label for="distanz">Distanz:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('distanz') ? 'bg-red-300' : '' }}"
                                               id="distanz" placeholder="Distanz" name="distanz" value="{{ old('distanz') ?? $raceType->distanz }}">
                                        <small class="form-text text-danger">{!! $errors->first('distanz') !!}</small>
                                    </div>

                                    <div class="my-4" >
                                        <label for="altervon">alter Von:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('altervon') ? 'bg-red-300' : '' }}"
                                               id="altervon" placeholder="altervon" name="altervon" value="{{ old('altervon') ?? $raceType->altervon }}">
                                        <small class="form-text text-danger">{!! $errors->first('altervon') !!}</small>
                                    </div>

                                    <div class="my-4" >
                                        <label for="alterbis">alter Bis:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('alterbis') ? 'bg-red-300' : '' }}"
                                               id="alterbis" placeholder="alterbis" name="alterbis" value="{{ old('alterbis') ?? $raceType->alterbis }}">
                                        <small class="form-text text-danger">{!! $errors->first('alterbis') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="min">Min:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('min') ? 'bg-red-300' : '' }}"
                                               id="min" placeholder="Min" name="min" value="{{ old('min') ?? $raceType->min }}">
                                        <small class="form-text text-danger">{!! $errors->first('min') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="max">Max:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('max') ? 'bg-red-300' : '' }}"
                                               id="max" placeholder="Max" name="max" value="{{ old('max') ?? $raceType->max }}">
                                        <small class="form-text text-danger">{!! $errors->first('max') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="weiblichmin">Weiblich Min:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('weiblichmin') ? 'bg-red-300' : '' }}"
                                               id="weiblichmin" placeholder="Weiblich Min" name="weiblichmin" value="{{ old('weiblichmin') ?? $raceType->weiblichmin }}">
                                        <small class="form-text text-danger">{!! $errors->first('weiblichmin') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="weiblichmax">Weiblich Max:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('weiblichmax') ? 'bg-red-300' : '' }}"
                                               id="weiblichmax" placeholder="Weiblich Max" name="weiblichmax" value="{{ old('weiblichmax') ?? $raceType->weiblichmax }}">
                                        <small class="form-text text-danger">{!! $errors->first('weiblichmax') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="manmin">Man Min:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('manmin') ? 'bg-red-300' : '' }}"
                                               id="manmin" placeholder="Man Min" name="manmin" value="{{ old('manmin') ?? $raceType->manmin }}">
                                        <small class="form-text text-danger">{!! $errors->first('manmin') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="manmax">Man Max:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('manmax') ? 'bg-red-300' : '' }}"
                                               id="manmax" placeholder="Man Max" name="manmax" value="{{ old('manmax') ?? $raceType->manmax }}">
                                        <small class="form-text text-danger">{!! $errors->first('manmax') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="bahnen">Bahnen:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('bahnen') ? 'bg-red-300' : '' }}"
                                               id="bahnen" placeholder="Bahnen" name="bahnen" value="{{ old('bahnen') ?? $raceType->bahnen }}">
                                        <small class="form-text text-danger">{!! $errors->first('bahnen') !!}</small>
                                    </div>

                                    <div class="my-4" >
                                        <label for="zusatzmanschaft">Zusatzmannschaft</label>
                                        <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zusatzmanschaft') ? 'bg-red-300' : '' }}"
                                               id="zusatzmanschaft" name="zusatzmanschaft" value="1"
                                               @if(old('zusatzmanschaft') == 1 or $raceType->zusatzmanschaft == 1)
                                                   checked
                                            @endif
                                        >
                                    </div>

                                    <div class="my-4">
                                        <label for="meldeGebuehr">Meldegebühr:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('meldeGebuehr') ? 'bg-red-300' : '' }}"
                                               id="meldeGebuehr" placeholder="Meldegebühr" name="meldeGebuehr" value="{{ old('meldeGebuehr') ?? $raceType->meldeGebuehr }}">
                                        <small class="form-text text-danger">{!! $errors->first('meldeGebuehr') !!}</small>
                                    </div>

                                    <div class="py-2">
                                        <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" name="action" id="action" value="save">Speichern</button>
                                    </div>

                                </form>
                                <br>
                                <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Rennklassen/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>

