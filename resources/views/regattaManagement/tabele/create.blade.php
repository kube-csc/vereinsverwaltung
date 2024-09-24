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
                      Tabelle
                  </div>

                  <div class="mt-6 text-gray-500">
                    Bitte gebe ein neues Tabelle ein.
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Neue Tabelle</div>
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

                            <form class="my-4" autocomplete="off" action="{{ route('tabele.store') }}" method="post">
                                @csrf
                                <div>
                                    <label for="name">Bezeichnung des Tabelle:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('tabelleBezeichnung') ? 'bg-red-300' : '' }}"
                                           id="tabelleBezeichnung" placeholder="Bezeichnung des Tabelle" name="tabelleBezeichnung" value="{{ old('tabelleBezeichnung') }}">
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
                                    <label for="name">Veröffentlichungszeit der Ergebnisse:</label>
                                    <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('veroeffentlichungUhrzeit') ? 'bg-red-300' : '' }}"
                                           id="veroeffentlichungUhrzeit" name="veroeffentlichungUhrzeit" value="{{ Session::get('tablePublished') }}">
                                    <small class="form-text text-danger">{!! $errors->first('veroeffentlichungUhrzeit') !!}</small>
                                </div>
                                <div>
                                  <label for="name">von Regatta Abschnitt:</label><br>
                                  <select name="tabelleLevelVon">
                                      @for ($i = 1; $i <= $levelMaxVon; $i++)
                                          <option value="{{ $i }}"
                                                  @if($i==Session::get('tableLevelSaveVon'))
                                                      selected
                                              @endif
                                          >
                                              Abschnitt {{ $i }}
                                          </option>
                                      @endfor
                                      <option value="{{ $i }}">Abschnitt +</option>
                                  </select>
                                </div>

                                <div>
                                    <label for="name">bis Regatta Abschnitt:</label><br>
                                    <select name="tabelleLevelBis">
                                        @for ($i = 1; $i <= $levelMaxBis; $i++)
                                            <option value="{{ $i }}"
                                                @if($i==Session::get('tableLevelSaveBis'))
                                                  selected
                                                @endif
                                            >
                                                Abschnitt {{ $i }}
                                            </option>
                                        @endfor
                                        <option value="{{ $i }}">Abschnitt +</option>
                                    </select>
                                </div>

                                <div class="my-4" >
                                    <label for="buchholzzahlaktiv">Bucholzzahl aktiv:</label>
                                    <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('buchholzzahlaktiv') ? 'bg-red-300' : '' }}"
                                           id="buchholzzahlaktiv" name="buchholzzahlaktiv" value="1"
                                           @if(old('buchholzzahlaktiv')==1)
                                               checked
                                        @endif
                                    >
                                    <small class="form-text text-danger">{!! $errors->first('buchholzzahlaktiv') !!}</small>
                                </div>

                                <div class="my-4" >
                                    <label for="finaleTable">Finaletabelle:</label>
                                    <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('finaleTable') ? 'bg-red-300' : '' }}"
                                           id="finaleTable" name="finaleTable" value="1"
                                           @if(old('finaleTable')==1)
                                               checked
                                        @endif
                                    >
                                    <small class="form-text text-danger">{!! $errors->first('finaleTable') !!}</small>
                                </div>

                                <div class="py-2">
                                 <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">neue Tabelle anlegen</button>
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
