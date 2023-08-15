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
                                      <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennDatum') ? 'bg-red-300' : '' }}"
                                             id="rennDatum" name="rennDatum" value="{{ old('rennDatum') ?? $race->rennDatum }}"
                                             min="{{ Session::get('regattaSelectRaceDateForm') }}" max="{{ Session::get('regattaSelectRaceDateUntil') }}">
                                      <small class="form-text text-danger">{!! $errors->first('rennDatum') !!}</small>
                                  </div>
                                  <div class="my-4" >
                                      @php
                                         $rennUhrzeitAlt= substr($race->rennUhrzeit, 0, -3);
                                      @endphp
                                      <label for="name">Startzeit:</label>
                                      <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennUhrzeit') ? 'bg-red-300' : '' }}"
                                             id="rennUhrzeit" name="rennUhrzeit" value="{{ old('rennUhrzeit') ?? $rennUhrzeitAlt }}">
                                      <small class="form-text text-danger">{!! $errors->first('rennUhrzeit') !!}</small>
                                  </div>
                                  <div class="my-4" >
                                      @php
                                          $veroeffentlichungUhrzeitAlt= substr($race->veroeffentlichungUhrzeit, 0, -3);
                                      @endphp
                                      <label for="name">Veröffungszeit der Ergebnisse:</label>
                                      <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('veroeffentlichungUhrzeit') ? 'bg-red-300' : '' }}"
                                             id="veroeffentlichungUhrzeit" name="veroeffentlichungUhrzeit" value="{{ old('veroeffentlichungUhrzeit') ?? $veroeffentlichungUhrzeitAlt }}">
                                      <small class="form-text text-danger">{!! $errors->first('veroeffentlichungUhrzeit') !!}</small>
                                  </div>

                                  @php
                                   if(old('tabeleId') != Null){
                                       $tableId=old('tabeleId');
                                   }
                                   else{
                                       $tableId=$race->tabele_id;
                                   }
                                  @endphp
                                  <div class="my-4" >
                                      <label for="name">Tabelle:</label><br>
                                      <select name="tabeleId">
                                          <option value=""
                                              @if( $tableId == NULL )
                                                 selected
                                              @endif
                                          >keine Tabelle</option>

                                          @foreach ($tabeles as $tabele)
                                              <option value="{{ $tabele->id }}"
                                                  @if( $tableId == $tabele->id )
                                                      selected
                                                  @endif
                                              >{{ $tabele->ueberschrift }}</option>
                                          @endforeach

                                      </select>
                                  </div>

                                  <div class="my-4" >
                                      <label for="name">Regatta Abschnitt:</label><br>
                                      <select name="regattaLevel">
                                          @for ($i = 1; $i <= $levelMax; $i++)
                                              <option value="{{ $i }}"
                                                  @if($i==$race->level)
                                                          selected
                                                  @endif
                                              >
                                                  Abschnitt {{ $i }}
                                              </option>
                                          @endfor
                                          <option value="{{ $i }}">Abschnitt +</option>
                                      </select>
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
