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
                      Tabelle: {{ old('ueberschrift') ?? $tabele->ueberschrift }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    Bitte gebe die Daten der Tabelle ein.
                  </div>
              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Tabelle ändern</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                             <form autocomplete="off" action="{{ url('Tabelle/update/'.$tabele->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                  // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                @endphp
                                  <div class="my-4">
                                      <label for="tabelleBezeichnung">Bezeichnung der Tabelle:</label>
                                      <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('tabelleBezeichnung') ? 'bg-red-300' : '' }}"
                                             id="tabelleBezeichnung" placeholder="Bezeichnung des Tabelle" name="tabelleBezeichnung" value="{{ old('tabelleBezeichnung') ?? $tabele->ueberschrift }}">
                                      <small class="form-text text-danger">{!! $errors->first('tabelleBezeichnung') !!}</small>
                                  </div>
                                  <div>
                                      <label for="tabelleDatum">Datum:</label>
                                      <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('tabelleDatum') ? 'bg-red-300' : '' }}"
                                             id="tabelleDatum" name="tabelleDatum" value="{{ Session::get('regattaSelectRaceDate') }}"
                                             min="{{ Session::get('regattaSelectRaceDateForm') }}" max="{{ Session::get('regattaSelectRaceDateUntil') }}">
                                      <small class="form-text text-danger">{!! $errors->first('tabelleDatum') !!}</small>
                                  </div>

                                  <div class="my-4">
                                      <label for="tabelleLevelVon">von Regatta Abschnitt:</label><br>
                                      <select name="tabelleLevelVon" id="tabelleLevelVon">
                                          @for ($i = 1; $i <= $levelMaxBis; $i++)
                                              <option value="{{ $i }}"
                                                      @if($i==$tabele->tabelleLevelVon)
                                                          selected
                                                  @endif
                                              >
                                                  Abschnitt {{ $i }}
                                              </option>
                                          @endfor
                                      </select>
                                  </div>

                                  <div class="my-4">
                                      <label for="tabelleLevelBis">bis Regatta Abschnitt:</label><br>
                                      <select name="tabelleLevelBis" id="tabelleLevelBis">
                                          @for ($i = 1; $i <= $levelMaxBis; $i++)
                                              <option value="{{ $i }}"
                                                      @if($i==$tabele->tabelleLevelBis)
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
                                     @php
                                         $veroeffentlichungUhrzeitAlt= substr($tabele->finaleAnzeigen, 0, -3);
                                     @endphp
                                     <label for="veroeffentlichungUhrzeit">Veröffentlichungszeit der Ergebnisse:</label>
                                     <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('veroeffentlichungUhrzeit') ? 'bg-red-300' : '' }}"
                                            id="veroeffentlichungUhrzeit" name="veroeffentlichungUhrzeit" value="{{ old('veroeffentlichungUhrzeit') ?? $veroeffentlichungUhrzeitAlt}}">
                                     <small class="form-text text-danger">{!! $errors->first('veroeffentlichungUhrzeit') !!}</small>
                                  </div>

                                  <div class="my-4">
                                     <label for="wertungsart">Wertungsart:</label><br>
                                     <select name="wertungsart" id="wertungsart" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('wertungsart') ? 'bg-red-300' : '' }}">
                                         <option value="1" {{ old('wertungsart') == 1 || $tabele->wertungsart == 1 ? 'selected' : '' }}>Punkte</option>
                                         <option value="2" {{ old('wertungsart') == 2 || $tabele->wertungsart == 2 ? 'selected' : '' }}>Zeit</option>
                                         <option value="3" {{ old('wertungsart') == 3 || $tabele->wertungsart == 3 ? 'selected' : '' }}>Einzelner Lauf</option>
                                     </select>
                                     <small class="form-text text-danger">{!! $errors->first('wertungsart') !!}</small>
                                  </div>

                                  <div class="my-4" id="tabelleSystemWrapper">
                                      <label for="tabelleSystem">Tabellen Punkte System:</label>

                                      @php
                                          $defaultSystemId = old('tabelleSystem', $tabele->system_id ?? ($pointsystemIds->first() ?? 1));
                                      @endphp

                                      <select class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('tabelleSystem') ? 'bg-red-300' : '' }}"
                                              id="tabelleSystem" name="tabelleSystem">
                                          @forelse($pointsystemIds as $systemId)
                                              <option value="{{ $systemId }}" {{ (string)$defaultSystemId === (string)$systemId ? 'selected' : '' }}>
                                                  System {{ $systemId }}
                                              </option>
                                          @empty
                                              <option value="" selected>Kein Punktesystem vorhanden</option>
                                          @endforelse
                                      </select>
                                      <small class="form-text text-danger">{!! $errors->first('tabelleSystem') !!}</small>

                                      <div id="tabelleSystemPreview" class="mt-2 text-sm text-gray-600"></div>
                                  </div>

                                 <div class="my-4">
                                        <label for="getrenntewertung">Getrennte Wertung bei Mixrennen:</label><br>
                                        <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('getrenntewertung') ? 'bg-red-300' : '' }}"
                                               id="getrenntewertung" name="getrenntewertung" value="1"
                                               @if(old('getrenntewertung') == 1 or $tabele->getrenntewertung == 1)
                                                   checked
                                               @endif
                                        >
                                        <small class="form-text text-danger">{!! $errors->first('getrenntewertung') !!}</small>
                                 </div>

                                  <div class="my-4">
                                     <label for="buchholzwertungaktiv">Buchholzwertung aktiv:</label><br>
                                     <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('buchholzwertungaktiv') ? 'bg-red-300' : '' }}"
                                            id="buchholzwertungaktiv" name="buchholzwertungaktiv" value="1"
                                            @if(old('buchholzwertungaktiv')==1 or $tabele->buchholzwertungaktiv == 1)
                                                checked
                                         @endif
                                     >
                                     <small class="form-text text-danger">{!! $errors->first('buchholzwertungaktiv') !!}</small>
                                 </div>

                                  <div class="my-4">
                                        <label for="finaleTable">Finale Tabelle:</label>
                                        <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('finaleTable') ? 'bg-red-300' : '' }}"
                                               id="finaleTable" name="finaleTable" value="1"
                                               @if(old('finaleTable') == 1 or $tabele->finale == 1)
                                                   checked
                                           @endif
                                  </div>

                                  <div class="my-4">
                                     <label for="tabelleGruppe">Renn Gruppe:</label><br>
                                     <select name="tabelleGruppe" id="tabelleGruppe" class="{{ $errors->has('tabelleGruppe') ? 'bg-red-300' : '' }}">
                                         <option value="0"
                                             @if($tabele->gruppe_id==0 or old('tabelleGruppe')==0)
                                                     selected
                                             @endif
                                         >
                                             keine Gruppe
                                         </option>
                                         @foreach($raceTypes as $raceType)
                                             <option value="{{ $raceType->id }}"
                                                     @if($raceType->id==$tabele->gruppe_id or $raceType->id==old('tabelleGruppe'))
                                                         selected
                                                 @endif
                                             >
                                                 {{ $raceType->typ }}
                                             </option>
                                         @endforeach
                                     </select>
                                     <br><small class="form-text text-danger">{!! $errors->first('tabelleGruppe') !!}</small>
                                  </div>

                                  <div class="py-2">
                                     <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Speichern</button>
                                  </div>
                             </form>

                              <script>
                                  (function () {
                                      var pointsystemsBySystem = @json($pointsystemsBySystem ?? []);

                                      function renderPreview() {
                                          var previewEl = document.getElementById('tabelleSystemPreview');
                                          var systemEl = document.getElementById('tabelleSystem');
                                          if (!previewEl || !systemEl) return;

                                          var systemId = String(systemEl.value || '');
                                          var rows = pointsystemsBySystem[systemId] || [];

                                          if (!systemId || rows.length === 0) {
                                              previewEl.innerHTML = '<span class="text-gray-500">Keine Vorschau verfügbar.</span>';
                                              return;
                                          }

                                          var html = '<div class="font-semibold mb-1">Punktevergabe (System ' + systemId + ')</div>';
                                          html += '<table class="min-w-full text-sm">';
                                          html += '<thead><tr><th class="text-left pr-4">Platz</th><th class="text-left">Punkte</th></tr></thead>';
                                          html += '<tbody>';
                                          for (var i = 0; i < rows.length; i++) {
                                              html += '<tr><td class="pr-4">' + rows[i].platz + '</td><td>' + rows[i].punkte + '</td></tr>';
                                          }
                                          html += '</tbody></table>';

                                          previewEl.innerHTML = html;
                                      }

                                      function syncTabelleSystemVisibility() {
                                          var wertungsartEl = document.getElementById('wertungsart');
                                          var wrapper = document.getElementById('tabelleSystemWrapper');
                                          var systemEl = document.getElementById('tabelleSystem');
                                          var previewEl = document.getElementById('tabelleSystemPreview');

                                          if (!wertungsartEl || !wrapper || !systemEl) return;

                                          var isPunkte = String(wertungsartEl.value) === '1';

                                          wrapper.style.display = isPunkte ? '' : 'none';
                                          systemEl.disabled = !isPunkte;
                                          systemEl.required = isPunkte;

                                          if (previewEl) {
                                              previewEl.style.display = isPunkte ? '' : 'none';
                                          }

                                          if (isPunkte) {
                                              renderPreview();
                                          }
                                      }

                                      document.addEventListener('DOMContentLoaded', function () {
                                          var wertungsartEl = document.getElementById('wertungsart');
                                          if (wertungsartEl) {
                                              wertungsartEl.addEventListener('change', syncTabelleSystemVisibility);
                                          }

                                          var systemEl = document.getElementById('tabelleSystem');
                                          if (systemEl) {
                                              systemEl.addEventListener('change', renderPreview);
                                          }

                                          syncTabelleSystemVisibility();
                                      });
                                  })();
                              </script>

                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Tabelle/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                           </div>
                          </div>

                      </div>
                  </div>

              </div>

            </div>
        </div>
</x-app-layout>
