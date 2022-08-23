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
                      Rennen: {{ old('ueberschrift') ?? $tabele->ueberschrift }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    @php
                      // ToDo: Beschreibungstext überarbeiten
                    @endphp
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
                                  <div class="my-4" >
                                      <label for="name">Bezeichnung der Tabelle:</label>
                                      <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('tabelleBezeichnung') ? 'bg-red-300' : '' }}"
                                             id="tabelleBezeichnung" placeholder="Bezeichnung des Tabelle" name="tabelleBezeichnung" value="{{ old('tabelleBezeichnung') ?? $tabele->ueberschrift }}">
                                      <small class="form-text text-danger">{!! $errors->first('tabelleBezeichnung') !!}</small>
                                  </div>
                                  <div>
                                      <label for="name">Datum:</label>
                                      <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('tabelleDatum') ? 'bg-red-300' : '' }}"
                                             id="tabelleDatum" name="tabelleDatum" value="{{ Session::get('regattaSelectRaceDate') }}"
                                             min="{{ Session::get('regattaSelectRaceDateForm') }}" max="{{ Session::get('regattaSelectRaceDateUntil') }}">
                                      <small class="form-text text-danger">{!! $errors->first('tabelleDatum') !!}</small>
                                  </div>

                                  <div class="my-4" >
                                      <label for="name">Regatta Abschnitt:</label><br>
                                      <select name="tabelleLevelVon">
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

                                  <div class="my-4" >
                                      <label for="name">Regatta Abschnitt:</label><br>
                                      <select name="tabelleLevelBis">
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

                                  <div class="py-2">
                                     <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderung speichern</button>
                                  </div>
                             </form>
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
