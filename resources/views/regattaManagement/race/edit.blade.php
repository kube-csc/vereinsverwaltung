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

                             <form autocomplete="off" action="{{ url('Rennen/update/'.$race->id) }}" name="action" id="action" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                  // ToDo: @method('PUT') in Hobby Projekt noch mal erlernen
                                @endphp
                                  <div class="my-4" >
                                      <label for="nummer">Nummer:</label>
                                      <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('nummer') ? 'bg-red-300' : '' }}"
                                             id="nummer" placeholder="Nummer" name="nummer" value="{{ old('nummer') ?? $race->nummer }}">
                                      <small class="form-text text-danger">{!! $errors->first('nummer') !!}</small>
                                  </div>
                                  <div class="my-4" >
                                      <label for="rennBezeichnung">Bezeichnung des Rennen:</label>
                                      <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennBezeichnung') ? 'bg-red-300' : '' }}"
                                             id="rennBezeichnung" placeholder="Bezeichnung des Rennen" name="rennBezeichnung" value="{{ old('rennBezeichnung') ?? $race->rennBezeichnung }}">
                                      <small class="form-text text-danger">{!! $errors->first('rennBezeichnung') !!}</small>
                                  </div>
                                  <div class="my-4" >
                                      <label for="rennDatum">Datum:</label>
                                      <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennDatum') ? 'bg-red-300' : '' }}"
                                             id="rennDatum" name="rennDatum" value="{{ old('rennDatum') ?? $race->rennDatum }}"
                                             min="{{ Session::get('regattaSelectRaceDateForm') }}" max="{{ Session::get('regattaSelectRaceDateUntil') }}">
                                      <small class="form-text text-danger">{!! $errors->first('rennDatum') !!}</small>
                                  </div>
                                  <div class="my-4" >
                                      @php
                                         $rennUhrzeitAlt= substr($race->rennUhrzeit, 0, -3);
                                      @endphp
                                      <label for="rennUhrzeit">Startzeit:</label>
                                      <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennUhrzeit') ? 'bg-red-300' : '' }}"
                                             id="rennUhrzeit" name="rennUhrzeit" value="{{ old('rennUhrzeit') ?? $rennUhrzeitAlt }}">
                                      <small class="form-text text-danger">{!! $errors->first('rennUhrzeit') !!}</small>
                                  </div>
                                  <div class="my-4" >
                                      @php
                                          $veroeffentlichungUhrzeitAlt= substr($race->veroeffentlichungUhrzeit, 0, -3);
                                      @endphp
                                      <label for="veroeffentlichungUhrzeit">Veröffentlichungszeit der Ergebnisse:</label>
                                      <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('veroeffentlichungUhrzeit') ? 'bg-red-300' : '' }}"
                                             id="veroeffentlichungUhrzeit" name="veroeffentlichungUhrzeit" value="{{ old('veroeffentlichungUhrzeit') ?? $veroeffentlichungUhrzeitAlt }}">
                                      <small class="form-text text-danger">{!! $errors->first('veroeffentlichungUhrzeit') !!}</small>
                                  </div>

                                  <div>
                                      <label for="rennBahnen">Anzahl der Bahnen:</label>
                                      <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennBahnen') ? 'bg-red-300' : '' }}"
                                             id="rennBahnen" name="rennBahnen" value="{{ old('rennBahnen') ?? $race->bahnen }}">
                                      <small class="form-text text-danger">{!! $errors->first('rennBahnen') !!}</small>
                                  </div>

                                  @php
                                      if(old('tabeleId') != Null){
                                          $tableId=old('tabeleId');
                                      }
                                      else{
                                          $tableId=$race->tabele_id;
                                      }
                                  @endphp

                                  @if($tableId == 0)
                                      <div class="my-4">
                                          <label for="einzelRennen">Einzelrennen:</label>
                                          <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('einzelRennen') ? 'bg-red-300' : '' }}"
                                                 id="einzelRennen" name="einzelRennen" value="1"
                                                 @if(old('einzelRennen') == 1 or $race->einzelRennen == 1)
                                                     checked
                                              @endif
                                          >
                                      </div>
                                  @endif

                                  <div class="my-4" >
                                      <label for="tabeleId">Tabelle:</label><br>
                                      <select name="tabeleId" id="tabeleId" >
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
                                      <label for="rennMix">Mix Rennen:</label>
                                      <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennMix') ? 'bg-red-300' : '' }}"
                                                 id="rennMix" name="rennMix" value="1"
                                                 @if(old('rennMix') == 1 or $race->mix == 1)
                                                     checked
                                                 @endif
                                      >
                                  </div>

                                  <div class="my-4" >
                                      <label for="regattaLevel">Regatta Abschnitt:</label><br>
                                      <select name="regattaLevel" id="regattaLevel">
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
                                  <div class="my-4">
                                      <label for="liveStreamURL">Livestream Video ID Youtube:</label>
                                      <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('liveStreamURL') ? 'bg-red-300' : '' }}"
                                             id="liveStreamURL" name="liveStreamURL" placeholder="Video ID" value="{{ old('liveStreamURL') ?? $race->liveStreamURL }}">
                                      <small class="form-text text-danger">{!! $errors->first('liveStreamURL') !!}</small>
                                  </div>

                                  <div class="my-4">
                                     <label for="einspielerURL">Einspieler Video ID Youtube:</label>
                                     <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('einspielerURL') ? 'bg-red-300' : '' }}"
                                            id="einspielerURL" name="einspielerURL" placeholder="Video ID" value="{{ old('einspielerURL', $race->einspielerURL ?? '') }}">
                                     <small class="form-text text-danger">{!! $errors->first('einspielerURL') !!}</small>
                                  </div>
                                  <div class="my-4">
                                     <label for="abspielzeit">Abspielzeit (Sekunden):</label>
                                     <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('abspielzeit') ? 'bg-red-300' : '' }}"
                                            id="abspielzeit" name="abspielzeit" placeholder="z\.B\. 30" value="{{ old('abspielzeit', $race->abspielzeit ?? '') }}">
                                     <small class="form-text text-danger">{!! $errors->first('abspielzeit') !!}</small>
                                  </div>

                                  <div class="py-2">
                                      <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" name="action" id="action" value="save">Speichern</button>
                                      <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white" name="action" id="action" value="save_and_edit_next">Speichern & nächstes Rennen bearbeiten</button>
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
