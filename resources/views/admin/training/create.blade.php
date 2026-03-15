<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Training - Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
           <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                       Training
                  </div>
                  <div class="mt-6 text-gray-500">
                       Bitte lege ein neues Training an.
                  </div>
              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Neues Training</div>
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

                              <form class="my-4" autocomplete="off" action="{{ route('training.store') }}" method="post">
                                @csrf
                                <input type="hidden" name="sportSection_id" value="{{ $sportSectionId }}">
                                <div>
                                    <label for="datumvon">Startdatum:</label>
                                    <p class="text-xs text-gray-500">Datum des ersten Termins.</p>
                                    <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumvon') ? 'bg-red-300' : '' }}"
                                           id="datumvon" name="datumvon" value="{{ old('datumvon') }}">
                                    <small class="form-text text-danger">{!! $errors->first('datumvon') !!}</small>
                                </div>
                                <div>
                                    <label for="datumbis">Enddatum:</label>
                                    <p class="text-xs text-gray-500">Datum des letzten Termins.</p>
                                    <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumbis') ? 'bg-red-300' : '' }}"
                                           id="datumbis" name="datumbis" value="{{ old('datumbis') }}">
                                    <small class="form-text text-danger">{!! $errors->first('datumbis') !!}</small>
                                </div>
                                <div>
                                    <label for="zeitvon">Startzeit:</label>
                                    <p class="text-xs text-gray-500">Uhrzeit, zu der das Training beginnt.</p>
                                    <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zeitvon') ? 'bg-red-300' : '' }}"
                                           id="zeitvon" name="zeitvon" value="{{ old('zeitvon') }}">
                                    <small class="form-text text-danger">{!! $errors->first('zeitvon') !!}</small>
                                </div>

                                <div>
                                     <label for="zeitbis">Endzeit:</label>
                                     <p class="text-xs text-gray-500">Uhrzeit, zu der das Training endet.</p>
                                     <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zeitbis') ? 'bg-red-300' : '' }}"
                                            id="zeitbis" name="zeitbis" value="{{ old('zeitbis') }}">
                                     <small class="form-text text-danger">{!! $errors->first('zeitbis') !!}</small>
                                </div>

                                <div>
                                    <label for="sportgeraeteanzahl">Maximale Teilnehmerplätze:</label>
                                    <p class="text-xs text-gray-500">Obergrenze für Anmeldungen pro Termin.</p>
                                    <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('sportgeraeteanzahl') ? 'bg-red-300' : '' }}"
                                           id="sportgeraeteanzahl" min="1" placeholder="z. B. 12" name="sportgeraeteanzahl" value="{{ old('sportgeraeteanzahl') }}">
                                    <small class="form-text text-danger">{!! $errors->first('sportgeraeteanzahl') !!}</small>
                                </div>

                                <div>
                                    <label for="sportgeraeteReserviert">Reservierte Teilnehmerplätze:</label>
                                    <p class="text-xs text-gray-500">Plätze, die nicht frei gebucht werden können (z. B. für Trainer / Gäste).</p>
                                    <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('sportgeraeteReserviert') ? 'bg-red-300' : '' }}"
                                           id="sportgeraeteReserviert" min="0" placeholder="z. B. 2" name="sportgeraeteReserviert" value="{{ old('sportgeraeteReserviert') }}">
                                    <small class="form-text text-danger">{!! $errors->first('sportgeraeteReserviert') !!}</small>
                                </div>

                                <div>
                                    <label for="wiederholung">Wiederholung (alle X Tage):</label>
                                    <p class="text-xs text-gray-500">Intervall, in dem neue Termine automatisch erstellt werden (0/leer = keine Wiederholung).</p>
                                    <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('wiederholung') ? 'bg-red-300' : '' }}"
                                           id="wiederholung" min="0" placeholder="z. B. 7" name="wiederholung" value="{{ old('wiederholung') }}">
                                    <small class="form-text text-danger">{!! $errors->first('wiederholung') !!}</small>
                                </div>

                                <div>
                                    <label for="vorschauTage">Planungshorizont (Tage):</label>
                                    <p class="text-xs text-gray-500">Wie viele Tage im Voraus Termine (aus dem Kurs/Trainingstyp) automatisch erzeugt werden.</p>
                                    <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('vorschauTage') ? 'bg-red-300' : '' }}"
                                           id="vorschauTage" min="0" placeholder="z. B. 30" name="vorschauTage" value="{{ old('vorschauTage') }}">
                                    <small class="form-text text-danger">{!! $errors->first('vorschauTage') !!}</small>
                                </div>

                                <div class="form-field">
                                    <label for="courseId" class="form-label">Trainingstyp (Kurs):</label>
                                    <p class="text-xs text-gray-500">Vorlage/Kurs, aus dem die Termine erzeugt werden.</p>
                                    <br>
                                    <select name="courseId">
                                        @foreach ($courses as $course)
                                            <option value="{{ $course->course_id }}" @selected(old('courseId') == $course->course_id)>
                                                {{ $course->kursName }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="py-2">
                                   <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Training anlegen</button>
                                </div>

                            </form>
                            <br>
                            <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Training/alle/{{ $sportSectionId }}"><i class="fas fa-arrow-circle-up"></i>Zurück</a>

                            </div>
                         </div>

                      </div>
                  </div>

              </div>

           </div>
        </div>
    </div>
</x-app-layout>
