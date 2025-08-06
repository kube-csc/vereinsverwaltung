<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Training bearbeiten') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        Training bearbeiten
                    </div>
                    <div class="mt-6 text-gray-500">
                        Bitte bearbeite das Training.
                    </div>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Training bearbeiten</div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">

                                @error('errormessage')
                                <span class="text-red-500 text-xs">{{ $message }}</span>
                                @enderror

                                <div style="text-align: left">
                                    <div>
                                        @if (session()->has('message'))
                                            <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                                {{ session('message') }}
                                            </div>
                                        @endif
                                    </div>

                                    <form class="my-4" autocomplete="off" action="{{ route('training.update', $training->id) }}" method="post">
                                        @csrf
                                        <input type="hidden" name="sportSection_id" value="{{ $training->sportSection_id }}">
                                        <div>
                                            <label for="datumvon">Start Termin:</label>
                                            <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumvon') ? 'bg-red-300' : '' }}"
                                                   id="datumvon" placeholder="Event Startdatum" name="datumvon" value="{{ old('datumvon', $training->datumvon) }}">
                                            <small class="form-text text-danger">{!! $errors->first('datumvon') !!}</small>
                                        </div>
                                        <div>
                                            <label for="datumbis">End Termin:</label>
                                            <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumbis') ? 'bg-red-300' : '' }}"
                                                   id="datumbis" placeholder="Event Enddatum" name="datumbis" value="{{ old('datumbis', $training->datumbis) }}">
                                            <small class="form-text text-danger">{!! $errors->first('datumbis') !!}</small>
                                        </div>
                                        <div>
                                            <label for="zeitvon">Start Uhrzeit:</label>
                                            <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zeitvon') ? 'bg-red-300' : '' }}"
                                                   id="zeitvon" placeholder="Start Uhrzeit" name="zeitvon" value="{{ old('zeitvon', $training->zeitvon) }}">
                                            <small class="form-text text-danger">{!! $errors->first('zeitvon') !!}</small>
                                        </div>

                                        <div>
                                            <label for="zeitbis">End Uhrzeit:</label>
                                            <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zeitbis') ? 'bg-red-300' : '' }}"
                                                   id="zeitbis" placeholder="End Uhrzeit" name="zeitbis" value="{{ old('zeitbis', $training->zeitbis) }}">
                                            <small class="form-text text-danger">{!! $errors->first('zeitbis') !!}</small>
                                        </div>

                                        <div>
                                            <label for="sportgeraeteanzahl">Maximale Anzahl Sportgeräte:</label>
                                            <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('sportgeraeteanzahl') ? 'bg-red-300' : '' }}"
                                                   id="sportgeraeteanzahl" placeholder="Maximale Anzahl Sportgeräte" name="sportgeraeteanzahl" value="{{ old('sportgeraeteanzahl', $training->sportgeraeteanzahl) }}">
                                            <small class="form-text text-danger">{!! $errors->first('sportgeraeteanzahl') !!}</small>
                                        </div>

                                        <div>
                                            <label for="sportgeraeteReserviert">Reservierte Sportgeräte:</label>
                                            <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('sportgeraeteReserviert') ? 'bg-red-300' : '' }}"
                                                   id="sportgeraeteReserviert" placeholder="Anzahl Sportgeräte" name="sportgeraeteReserviert" value="{{ old('sportgeraeteReserviert', $training->sportgeraeteReserviert) }}">
                                            <small class="form-text text-danger">{!! $errors->first('sportgeraeteReserviert') !!}</small>
                                        </div>

                                        <div>
                                            <label for="wiederholung">Wiederholung nach vielen Tagen:</label>
                                            <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('wiederholung') ? 'bg-red-300' : '' }}"
                                                   id="wiederholung" placeholder="Wiederholung" name="wiederholung" value="{{ old('wiederholung', $training->wiederholung) }}">
                                            <small class="form-text text-danger">{!! $errors->first('wiederholung') !!}</small>
                                        </div>

                                        <div>
                                            <label for="vorschauTage">Vorschau Tage:</label>
                                            <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('vorschauTage') ? 'bg-red-300' : '' }}"
                                                   id="vorschauTage" placeholder="Vorschau Tage" name="vorschauTage" value="{{ old('vorschauTage', $training->vorschauTage) }}">
                                            <small class="form-text text-danger">{!! $errors->first('vorschauTage') !!}</small>
                                        </div>

                                        <div class="form-field">
                                            <label for="courseId" class="form-label">Training:</label><br>
                                            <select name="courseId">
                                                @foreach ($courses as $course)
                                                    <option value="{{ $course->course_id }}" @selected(old('courseId', $training->course_id) == $course->course_id)>
                                                        {{ $course->kursName }}
                                                    </option>
                                                @endforeach
                                            </select>
                                        </div>

                                        <div class="py-2">
                                            <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Training aktualisieren</button>
                                        </div>

                                    </form>
                                    <br>
                                    <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Training/alle/{{ $training->sportSection_id }}"><i class="fas fa-arrow-circle-up"></i>Zurück</a>

                                </div>
                            </div>

                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>
