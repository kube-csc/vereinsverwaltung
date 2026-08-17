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
                      Rennen
                  </div>

                  <div class="mt-6 text-gray-500">
                    Bitte gebe ein neues Rennen ein.
                  </div>
              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Neues Rennen</div>
                      </div>

                      <div class="ml-12">
                         <div class="mt-2 text-sm text-gray-500">

                            @error('errormessage')
                              <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror

                            <div style="text-align: left">
                               <div>
                                   @if (session()->has('success'))
                                   <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                       {!! session('success') !!}
                                   </div>
                                   @endif
                               </div>

                               <form class="my-4" autocomplete="off" action="{{ route('race.store') }}" method="post">
                                  @csrf
                                   <div>
                                      <label for="nummer">Nummer:</label>
                                      <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('nummer') ? 'bg-red-300' : '' }}"
                                              id="nummer" placeholder="Nummer" name="nummer" value="{{ Session::get('rennNummer') ?? old('nummer')}}">
                                      <small class="form-text text-danger">{!! $errors->first('nummer') !!}</small>
                                  </div>
                                  <div>
                                      <label for="rennBezeichnung">Bezeichnung des Rennen:</label>
                                      <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennBezeichnung') ? 'bg-red-300' : '' }}"
                                             id="rennBezeichnung" placeholder="Bezeichnung des Rennen" name="rennBezeichnung" value="{{ Session::get('regattaSelectRaceName') ?? old('rennBezeichnung') }}">
                                      <small class="form-text text-danger">{!! $errors->first('rennBezeichnung') !!}</small>
                                  </div>

                                  <div>
                                      <label for="rennDatum">Datum:</label>
                                      <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennDatum') ? 'bg-red-300' : '' }}"
                                             id="rennDatum" name="rennDatum" value="{{ Session::get('regattaSelectRaceDate') }}"
                                             min="{{ Session::get('regattaSelectRaceDateForm') }}" max="{{ Session::get('regattaSelectRaceDateUntil') }}">
                                      <small class="form-text text-danger">{!! $errors->first('rennDatum') !!}</small>
                                  </div>
                                  <div>
                                      <label for="rennUhrzeit">Startzeit:</label>
                                      <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennUhrzeit') ? 'bg-red-300' : '' }}"
                                             id="rennUhrzeit" name="rennUhrzeit" value="{{ Session::get('regattaSelectRaceTimeNew') }}">
                                      <small class="form-text text-danger">{!! $errors->first('rennUhrzeit') !!}</small>
                                  </div>
                                  <div class="my-4">
                                      <label for="veroeffentlichungUhrzeit">Veröffentlichungszeit der Ergebnisse:</label>
                                      <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('veroeffentlichungUhrzeit') ? 'bg-red-300' : '' }}"
                                           id="veroeffentlichungUhrzeit" name="veroeffentlichungUhrzeit" value="{{ Session::get('regattaSelectRacePublished') }}">
                                      <small class="form-text text-danger">{!! $errors->first('veroeffentlichungUhrzeit') !!}</small>
                                  </div>

                                  <div>
                                      <label for="rennBahnen">Anzahl der Bahnen:</label>
                                      <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennBahnen') ? 'bg-red-300' : '' }}"
                                             id="rennBahnen" name="rennBahnen"
                                             value="{{ old('rennBahnen') ?? ($rennBahnenSession ?? '') }}">
                                      <small class="form-text text-danger">{!! $errors->first('rennBahnen') !!}</small>
                                  </div>
                                   @php
                                       if(old('tabeleId') != Null){
                                           $tableId=old('tabeleId');
                                       }
                                       else{
                                           $tableId=Null;
                                       }
                                   @endphp

                                   @if($tableId === null || $tableId == 0)
                                       <div class="my-4">
                                           <label for="einzelRennen">Einzelrennen:</label>
                                           <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('einzelRennen') ? 'bg-red-300' : '' }}"
                                                  id="einzelRennen" name="einzelRennen" value="1"
                                                  @if(old('einzelRennen') == 1)
                                                      checked
                                               @endif
                                           >
                                           <div class="text-xs text-gray-600">
                                               Bei Einzelrennen wird automatisch eine Tabelle angelegt.
                                           </div>
                                       </div>
                                       {{-- Gruppen-Auswahl für Einzelrennen --}}
                                       <div class="my-4" id="gruppeSelectWrapper" @if(old('einzelRennen') != 1) style="display:none;" @endif>
                                           <label for="gruppe_id">Gruppe für Einzelrennen:</label>
                                           <select name="gruppe_id" id="gruppe_id" class="w-full border rounded shadow p-2 mr-2 my-2">
                                               <option value="">Bitte wählen</option>
                                               @foreach ($raceTypes as $raceType)
                                                   <option value="{{ $raceType->id }}"
                                                       @if(old('gruppe_id') == $raceType->id)
                                                           selected
                                                       @endif
                                                   >{{ $raceType->typ }}</option>
                                               @endforeach
                                           </select>
                                           <small class="form-text text-danger">{!! $errors->first('gruppe_id') !!}</small>
                                       </div>
                                       <div class="my-4" id="einzelRennenFinaleWrapper" @if(old('einzelRennen') != 1) style="display:none;" @endif>
                                           <label for="einzelRennenFinale">Als Finallauf markieren:</label>
                                           <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2"
                                                  id="einzelRennenFinale" name="einzelRennenFinale" value="1"
                                                  @if(old('einzelRennenFinale') == 1)
                                                      checked
                                                  @endif
                                                  @if(old('einzelRennen') != 1)
                                                      disabled
                                                  @endif
                                           >
                                       </div>
                                       <script>
                                           document.addEventListener('DOMContentLoaded', function() {
                                               const einzelRennen = document.getElementById('einzelRennen');
                                               const gruppeSelectWrapper = document.getElementById('gruppeSelectWrapper');
                                               const einzelRennenFinaleWrapper = document.getElementById('einzelRennenFinaleWrapper');
                                               const einzelRennenFinale = document.getElementById('einzelRennenFinale');
                                               const tabeleIdWrapper = document.getElementById('tabeleIdWrapper');
                                               const tabeleId = document.getElementById('tabeleId');

                                               function toggleEinzelRennenFields(isEinzelRennen) {
                                                   if(isEinzelRennen) {
                                                       gruppeSelectWrapper.style.display = '';
                                                       einzelRennenFinaleWrapper.style.display = '';
                                                       einzelRennenFinale.disabled = false;
                                                       tabeleIdWrapper.style.display = 'none';
                                                       tabeleId.disabled = true;
                                                   } else {
                                                       gruppeSelectWrapper.style.display = 'none';
                                                       einzelRennenFinaleWrapper.style.display = 'none';
                                                       einzelRennenFinale.checked = false;
                                                       einzelRennenFinale.disabled = true;
                                                       tabeleIdWrapper.style.display = '';
                                                       tabeleId.disabled = false;
                                                   }
                                               }

                                               if(einzelRennen) {
                                                   toggleEinzelRennenFields(einzelRennen.checked);
                                                   einzelRennen.addEventListener('change', function() {
                                                       toggleEinzelRennenFields(this.checked);
                                                   });
                                               }
                                           });
                                       </script>
                                   @endif


                                   <div class="my-4">
                                       <label for="rennMix">Mix Rennen:</label>
                                       <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennMix') ? 'bg-red-300' : '' }}"
                                              id="rennMix" name="rennMix" value="1"
                                              @if(old('rennMix') == 1)
                                                  checked
                                           @endif
                                       >
                                   </div>

                                   <div class="my-4" id="tabeleIdWrapper">
                                       <label for="tabeleId">Tabelle:</label><br>
                                       <select name="tabeleId" id="tabeleId" >
                                           <option value=""
                                                   @if( $tableId == 0 )
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
                                       <br>
                                       <small class="text-red-500 ">{!! $errors->first('tabeleId') !!}</small>
                                   </div>

                                  <div>
                                    <label for="regattaLevel">Regatta Abschnitt:</label><br>
                                    <select name="regattaLevel" id="regattaLevel">
                                        @for ($i = 1; $i <= $levelMax; $i++)
                                            <option value="{{ $i }}"
                                                @if($i==Session::get('rennLevelSave'))
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
                                             id="liveStreamURL" name="liveStreamURL" placeholder="Video ID" value="{{ old('liveStreamURL') }}">
                                      <small class="form-text text-danger">{!! $errors->first('liveStreamURL') !!}</small>
                                  </div>

                                   <div class="my-4">
                                       <label for="einspielerURL">Einspieler Video ID Youtube:</label>
                                       <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('einspielerURL') ? 'bg-red-300' : '' }}"
                                              id="einspielerURL" name="einspielerURL" placeholder="Video ID" value="{{ old('einspielerURL') }}">
                                       <small class="form-text text-danger">{!! $errors->first('einspielerURL') !!}</small>
                                   </div>
                                   <div class="my-4">
                                       <label for="abspielzeit">Abspielzeit (Sekunden):</label>
                                       <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('abspielzeit') ? 'bg-red-300' : '' }}"
                                              id="abspielzeit" name="abspielzeit" placeholder="Zeit in Sekunden" value="{{ old('abspielzeit') }}">
                                       <small class="form-text text-danger">{!! $errors->first('abspielzeit') !!}</small>
                                   </div>

                                  <div class="py-2">
                                     <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">neues Rennen anlegen</button>
                                  </div>
                               </form>

                                <br>
                               <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Regattamenu"><i class="fas fa-arrow-circle-up"></i>Zurück</a>

                            </div>
                         </div>

                      </div>
                  </div>

              </div>

           </div>
       </div>
    </div>
</x-app-layout>
