<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Team - Dashboard') }}
        </h2>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                    Team:
                  </div>

                  <div class="mt-6 text-gray-500">
                    @php
                      // TODO: Beschreibungstext überarbeiten
                    @endphp
                    Bitte gebe die Daten des Teammitgliedes ein.
                  </div>
              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Team bearbeiten</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">
                              <form autocomplete="off" action="{{ url('Team/update/'.$board->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                  // TODO:  @method('PUT') in Hobby Projekt noch mal erlernen
                                @endphp
                                <div class="my-4" >
                                    <label for="name">Posten (mänlich):</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('postenMaenlich') ? 'bg-red-300' : '' }}"
                                    id="postenMaenlich" placeholder="Posten" name="postenMaenlich" value="{{ old('abteilung') ?? $board->postenMaenlich }}">
                                    <small class="form-text text-danger">{!! $errors->first('postenMaenlich') !!}</small>
                                </div>
                                <div class="my-4" >
                                    <label for="name">Posten (weiblich):</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('postenWeiblich') ? 'bg-red-300' : '' }}"
                                    id="postenWeiblich" placeholder="Posten" name="postenWeiblich" value="{{ old('postenWeiblich') ?? $board->postenWeiblich }}">
                                    <small class="form-text text-danger">{!! $errors->first('postenWeiblich') !!}</small>
                                </div>
                                <div class="my-4" >
                                      <label for="name">Abteilung / Mannschaft:</label><br>
                                      <select name="sportSection_id">
                                          <!-- ToDo: Verbesserung Old Wert behalten bei Valiedierungsfehler -->
                                          <optgroup label="Abeilung:">
                                              @php
                                                  $firsttime = 0;
                                              @endphp
                                              @foreach ($sportSections as $sportSection)
                                                  @if ($sportSection->sportSection_id > 0 && $firsttime == 0)
                                                      @php
                                                          $firsttime = 1;
                                                      @endphp
                                          </optgroup>
                                          <optgroup label="Mannschaft:">
                                              @endif
                                              <option value="{{ $sportSection->id }}"
                                                      @if ($board->sportSection_id == $sportSection->id)
                                                      selected
                                                  @endif
                                              >{{ $sportSection->abteilung }}</option>
                                              @endforeach
                                          </optgroup>
                                      </select>
                                </div>
                                <div class="py-2">
                                 <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderungen speichern</button>
                                </div>
                             </form>
                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Team/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            </div>
                          </div>
                      </div>
                  </div>
              </div>
        </div>
    </div>
</x-app-layout>
