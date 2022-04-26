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
                    Bitte gebe die Daten des Rennen ein.
                  </div>
              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Rennen ändern</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                              <form autocomplete="off" action="{{ url('Rennen/update/'.$race->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                  // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                @endphp
                                  <div class="my-4" >
                                      <label for="name">Nummer:</label>
                                      <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('nummer') ? 'bg-red-300' : '' }}"
                                             id="nummer" placeholder="Nummer" name="nummer" value="{{ old('nummer') ?? $race->nummer }}">
                                      <small class="form-text text-danger">{!! $errors->first('nummer') !!}</small>
                                  </div>
                                  <div class="my-4" >
                                      <label for="name">Bezeichnung des Rennen:</label>
                                      <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennBezeichnung') ? 'bg-red-300' : '' }}"
                                             id="rennBezeichnung" placeholder="Bezeichnung des Rennen" name="rennBezeichnung" value="{{ old('rennBezeichnung') ?? $race->rennBezeichnung }}">
                                      <small class="form-text text-danger">{!! $errors->first('rennBezeichnung') !!}</small>
                                  </div>
                                  <div class="my-4" >
                                      <label for="name">Datum:</label>
                                      <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumvon') ? 'bg-red-300' : '' }}"
                                             id="datumvon" placeholder="Startzeit" name="datumvon" value="{{ old('datumvon') ?? $race->datumvon }}"
                                             min="{{ Session::get('regattaSelectRaceDateForm') }}" max="{{ Session::get('regattaSelectRaceDateUntil') }}">
                                      <small class="form-text text-danger">{!! $errors->first('rennBezeichnung') !!}</small>
                                  </div>
                                  <div class="my-4" >
                                      @php
                                         $uhrzeitAlt= substr($race->uhrzeit, 0, -3);
                                      @endphp
                                      <label for="name">Zeit:</label>
                                      <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('uhrzeit') ? 'bg-red-300' : '' }}"
                                             id="uhrzeit" placeholder="Startzeit" name="uhrzeit" value="{{ old('uhrzeit') ?? $uhrzeitAlt }}">
                                      <small class="form-text text-danger">{!! $errors->first('uhrzeit') !!}</small>
                                  </div>
                                  <div class="py-2">
                                   <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderung speichern</button>
                                  </div>
                             </form>
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
