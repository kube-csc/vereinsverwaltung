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
                        Renntypevorlage: {{ old('typ')}}
                    </div>

                    <div class="mt-6 text-gray-500">
                        @php
                            // ToDo: Beschreibungstext überarbeiten
                        @endphp
                        Bitte gebe die Daten von Renntypevorlage ein.
                    </div>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Renntypenvorlagen ändern</div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">

                                <form autocomplete="off" action="{{ url('/Rennklassenvorlage/speichern/') }}" name="action" id="action" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @php
                                        // ToDo: @method('PUT') in Hobby Projekt noch mal erlernen
                                    @endphp
                                    <div class="my-4" >
                                        <label for="typ">Typ:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('typ') ? 'bg-red-300' : '' }}"
                                               id="typ" placeholder="Klasse" name="typ" value="{{ old('typ') }}">
                                        <small class="form-text text-danger">{!! $errors->first('typ') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="beschreibung">Beschreibung:</label>
                                        <textarea class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('beschreibung') ? 'bg-red-300' : '' }}"
                                                  id="beschreibung" placeholder="Beschreibung" name="beschreibung">{{ old('beschreibung') }}</textarea>
                                        <small class="form-text text-danger">{!! $errors->first('beschreibung') !!}</small>
                                    </div>

                                    <div class="my-4" >
                                        <label for="distanz">Distanz:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('distanz') ? 'bg-red-300' : '' }}"
                                               id="distanz" placeholder="Distanz" name="distanz" value="{{ old('distanz') }}">
                                        <small class="form-text text-danger">{!! $errors->first('distanz') !!}</small>
                                    </div>

                                    <div class="my-4" >
                                        <label for="altervon">alter Von:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('altervon') ? 'bg-red-300' : '' }}"
                                               id="altervon" placeholder="alter Von" name="altervon" value="{{ old('altervon') }}">
                                        <small class="form-text text-danger">{!! $errors->first('altervon') !!}</small>
                                    </div>

                                    <div class="my-4" >
                                        <label for="alterbis">alter Bis:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('alterbis') ? 'bg-red-300' : '' }}"
                                               id="alterbis" placeholder="alter Bis" name="alterbis" value="{{ old('alterbis') }}">
                                        <small class="form-text text-danger">{!! $errors->first('alterbis') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="min">Mindest Teilnehmerzahl:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('min') ? 'bg-red-300' : '' }}"
                                               id="min" placeholder="Mindest Teilnehmerzahl" name="min" value="{{ old('min') }}">
                                        <small class="form-text text-danger">{!! $errors->first('min') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="max">Maximale Teilnehmerzahl:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('max') ? 'bg-red-300' : '' }}"
                                               id="max" placeholder="Maximale Teilnehmerzahl" name="max" value="{{ old('max') }}">
                                        <small class="form-text text-danger">{!! $errors->first('max') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="weiblichmin">Mindest weibliche Teilnehmer:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('weiblichmin') ? 'bg-red-300' : '' }}"
                                               id="weiblichmin" placeholder="Mindest weibliche Teilnehmer" name="weiblichmin" value="{{ old('weiblichmin') }}">
                                        <small class="form-text text-danger">{!! $errors->first('weiblichmin') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="weiblichmax">Maximale weibliche Teilnehmer:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('weiblichmax') ? 'bg-red-300' : '' }}"
                                               id="weiblichmax" placeholder="Maximale weibliche Teilnehmer" name="weiblichmax" value="{{ old('weiblichmax') }}">
                                        <small class="form-text text-danger">{!! $errors->first('weiblichmax') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="manmin">Maximale männliche Teilnehmer:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('manmin') ? 'bg-red-300' : '' }}"
                                               id="manmin" placeholder="Maximale männliche Teilnehmer" name="manmin" value="{{ old('manmin') }}">
                                        <small class="form-text text-danger">{!! $errors->first('manmin') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="manmax">Maximale männliche Teilnehmer:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('manmax') ? 'bg-red-300' : '' }}"
                                               id="manmax" placeholder="Maximale männliche Teilnehmer" name="manmax" value="{{ old('manmax') }}">
                                        <small class="form-text text-danger">{!! $errors->first('manmax') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="bahnen">Bahnen:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('bahnen') ? 'bg-red-300' : '' }}"
                                               id="bahnen" placeholder="Bahnen" name="bahnen" value="{{ old('bahnen') }}">
                                        <small class="form-text text-danger">{!! $errors->first('bahnen') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="training">kostenfreie Trainings pro Meldung:</label>
                                        <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('training') ? 'bg-red-300' : '' }}"
                                               id="training" placeholder="kostenfreie Trainings pro Meldung" name="training" value="{{ old('training') }}">
                                        <small class="form-text text-danger">{!! $errors->first('training') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="max_trainingstermine">Max. buchbare Trainingstermine:</label>
                                        <input type="number" min="0" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('max_trainingstermine') ? 'bg-red-300' : '' }}"
                                               id="max_trainingstermine" placeholder="Max. buchbare Trainingstermine" name="max_trainingstermine" value="{{ old('max_trainingstermine', 0) }}">
                                        <small class="form-text text-danger">{!! $errors->first('max_trainingstermine') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="training_preis">Preis pro Trainingseinheit:</label>
                                        <input type="number" min="0" step="0.01" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('training_preis') ? 'bg-red-300' : '' }}"
                                               id="training_preis" placeholder="Preis pro Trainingseinheit" name="training_preis" value="{{ old('training_preis', 0) }}">
                                        <small class="form-text text-danger">{!! $errors->first('training_preis') !!}</small>
                                    </div>

                                    <div class="my-4">
                                        <label for="meldeGebuehr">Meldegebühr:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('meldeGebuehr') ? 'bg-red-300' : '' }}"
                                               id="meldeGebuehr" placeholder="Meldegebühr" name="meldeGebuehr" value="{{ old('meldeGebuehr') }}">
                                        <small class="form-text text-danger">{!! $errors->first('meldeGebuehr') !!}</small>
                                    </div>

                                    <div class="my-4" >
                                        <label for="zusatzmanschaft">Rennen mit Teams auffüllen:</label>
                                        <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2"
                                               id="zusatzmanschaft" name="zusatzmanschaft" value="1"
                                               @if(old('zusatzmanschaft') == 1)
                                                   checked
                                            @endif
                                        >
                                    </div>

                                    <div class="py-2">
                                        <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" name="action" id="action" value="save">Speichern</button>
                                        <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Rennklassenvorlage/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                                    </div>

                                </form>
                                <br>

                            </div>
                        </div>

                    </div>
                </div>

            </div>

        </div>
    </div>
</x-app-layout>

