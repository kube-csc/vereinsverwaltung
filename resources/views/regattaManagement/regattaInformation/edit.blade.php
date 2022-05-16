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
                      Tittel: {{ $regattaInformation->informationTittel }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    @php
                      // ToDo: Beschreibungstext überarbeiten
                    @endphp
                    Bitte gebe die Daten der Regatta Information ein.
                  </div>
              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Regatta Information</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                              <form autocomplete="off" action="{{ url('/Renneninformation/update/'.$regattaInformation->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                  // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                @endphp
                                  <div class="my-4" >
                                      <label for="name">Tiiel:</label>
                                      <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('informationTittel') ? 'bg-red-300' : '' }}"
                                             id="informationTittel" placeholder="Tittel" name="informationTittel" value="{{ old('informationTittel') ?? $regattaInformation->informationTittel }}">
                                      <small class="form-text text-danger">{!! $errors->first('informationTittel') !!}</small>
                                  </div>
                                  <div class="my-4" >
                                      <label for="name">Information:</label>
                                      <textarea rows="10" cols="200" name="informationBeschreibung" class="w-full rounded border shadow p-2 mr-2 my-2">{{ $regattaInformation->informationBeschreibung }}</textarea>
                                      <small class="form-text text-danger">{!! $errors->first('informationBeschreibung') !!}</small>
                                  </div>
                                  <div class="my-4" >
                                      <label for="name">Start Datum:</label>
                                      <input type="datetime-local" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('startDatum') ? 'bg-red-300' : '' }}"
                                             id="startDatum" name="startDatum" value="{{ old('startDatum') ?? $startDatum }}"
                                             min="{{ Session::get('regattaSelectRaceDateForm') }}T00:00:00" max="{{ Session::get('regattaSelectRaceDateUntil') }}T23:59:59">
                                      Aktiv: <input type="checkbox" id="startDatumAktiv" name="startDatumAktiv" value="1" {{ $regattaInformation->startDatumAktiv==1 ? 'checked' : '' }}>
                                      <small class="form-text text-danger">{!! $errors->first('startDatum') !!}</small>
                                  </div>
                                  <div class="my-4" >
                                      <label for="name">End Datum:</label>
                                      <input type="datetime-local" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('endDatum') ? 'bg-red-300' : '' }}"
                                             id="endDatum" name="endDatum" value="{{ old('endDatum') ?? $endDatum }}"
                                             min="{{ Session::get('regattaSelectRaceDateForm') }}T00:00:00" max="{{ Session::get('regattaSelectRaceDateUntil') }}T23:59:59">
                                      Aktiv: <input type="checkbox" id="endDatumAktiv" name="endDatumAktiv" value="1" {{ $regattaInformation->endDatumAktiv==1 ? 'checked' : '' }}>
                                      <small class="form-text text-danger">{!! $errors->first('endDatum') !!}</small>
                                  </div>
                                  <div class="py-2">
                                     <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderung speichern</button>
                                  </div>
                             </form>
                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Renneninformation/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                           </div>
                          </div>

                      </div>
                  </div>

              </div>

            </div>
        </div>
</x-app-layout>
