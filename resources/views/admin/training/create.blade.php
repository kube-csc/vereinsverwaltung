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
                       Bitte gebe ein neues Training ein.
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
                                    <label for="name">Start Termin:</label>
                                    <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumvon') ? 'bg-red-300' : '' }}"
                                           id="datumvon" placeholder="Event Startdatum" name="datumvon" value="{{ old('datumvon') }}">
                                    <small class="form-text text-danger">{!! $errors->first('datumvon') !!}</small>
                                </div>
                                <div>
                                    <label for="name">End Termin:</label>
                                    <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumbis') ? 'bg-red-300' : '' }}"
                                           id="datumbis" placeholder="Event Enddatum" name="datumbis" value="{{ old('datumbis') }}">
                                    <small class="form-text text-danger">{!! $errors->first('datumbis') !!}</small>
                                </div>
                                <div>
                                    <label for="zeitvon">Start Uhrzeit:</label>
                                    <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zeitvon') ? 'bg-red-300' : '' }}"
                                           id="zeitvon" placeholder="Start Uhrzeit" name="zeitvon" value="{{ old('zeitvon') }}">
                                    <small class="form-text text-danger">{!! $errors->first('zeitvon') !!}</small>
                                </div>

                                <div>
                                     <label for="zeitbis">End Uhrzeit:</label>
                                     <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zeitbis') ? 'bg-red-300' : '' }}"
                                            id="zeitbis" placeholder="End Uhrzeit" name="zeitbis" value="{{ old('zeitbis') }}">
                                     <small class="form-text text-danger">{!! $errors->first('zeitbis') !!}</small>
                                </div>

                                <div>
                                    <label for="sportgeraeteanzahl">Maximale Sportgeräte:</label>
                                    <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('sportgeraeteanzahl') ? 'bg-red-300' : '' }}"
                                           id="sportgeraeteanzahl" placeholder="Sportgeräte" name="sportgeraeteanzahl" value="{{ old('sportgeraeteanzahl') }}">
                                    <small class="form-text text-danger">{!! $errors->first('sportgeraeteanzahl') !!}</small>
                                </div>

                                <div>
                                    <label for="sportgeraeteGebucht">Gebuchte Sportgeräte:</label>
                                    <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('sportgeraeteGebucht') ? 'bg-red-300' : '' }}"
                                           id="sportgeraeteGebucht" placeholder="Maximale Sportgeräte" name="sportgeraeteGebucht" value="{{ old('sportgeraeteGebucht') }}">
                                    <small class="form-text text-danger">{!! $errors->first('sportgeraeteGebucht') !!}</small>
                                </div>

                                <div>
                                    <label for="wiederholung">Wiederholung nach vielen Tagen:</label>
                                    <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('wiederholung') ? 'bg-red-300' : '' }}"
                                           id="wiederholung" placeholder="Wiederholung" name="wiederholung" value="{{ old('wiederholung') }}">
                                    <small class="form-text text-danger">{!! $errors->first('wiederholung') !!}</small>
                                </div>

                                <div>
                                    <label for="vorschauTage">Vorschau Tage:</label>
                                    <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('vorschauTage') ? 'bg-red-300' : '' }}"
                                           id="vorschauTage" placeholder="Vorschau Tage" name="vorschauTage" value="{{ old('vorschauTage') }}">
                                    <small class="form-text text-danger">{!! $errors->first('vorschauTage') !!}</small>
                                </div>

                                <div class="form-field">
                                    <label for="courseId" class="form-label">Training:</label><br>
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
