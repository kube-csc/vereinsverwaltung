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
                        {{ $tabele->rennBezeichnung }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    @php
                      // ToDo: Beschreibungstext überarbeiten
                    @endphp
                    Bitte bearbeiten die Ergebnisse der Tabelle.
                  </div>
              </div>
              <form autocomplete="off" action="{{ url('Tabelle/Ergebnis/update/'.$tabele->id) }}" method="post" enctype="multipart/form-data">
                  @csrf
                  @php
                      // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                  @endphp
              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Ergebnisse der Tabelle bearbeiten</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">
                              <div>
                                  @if (session()->has('success'))
                                      <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                          {!! session('success') !!}
                                      </div>
                                  @endif
                              </div>

                                <div class="my-4" >
                                    <label for="name">Beschreibung der Tabelle:</label>
                                    <textarea rows="10" cols="200" name="tabelleBeschreibung" class="w-full rounded border shadow p-2 mr-2 my-2">{{ $tabele->beschreibung }}</textarea>
                                    <small class="form-text text-danger">{!! $errors->first('tabelleBeschreibung') !!}</small>
                                </div>

                                <div class="my-4" >
                                    <label for="name">Dokument:</label>

                                    @if(isset($tabele->tabelleDatei))
                                        <div class="flex ml-2">
                                            <div class="flex-initial"><a href="/storage/tabeleDokumente/{{ $tabele->tabelleDatei }}" target="_blank">{{ $tabele->tabelleDatei }}</a></div>
                                            <div class="flex-initial ml-2 fas fa-times text-red-600 hover:text-red-00 cursor-pointer">
                                                <a href="/Tabelle/Ergebnis/loeschen/{{ $tabele->id }}">x</a>
                                            </div>
                                        </div>
                                    @endif
                                    <input type="file" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('tabeleDatei') ? 'bg-red-300' : '' }}"
                                           id="tabeleDatei" name="tabeleDatei">
                                     <small class="form-text text-danger">{!! $errors->first('tabeleDatei') !!}</small>
                                </div>
                                <div class="py-2">
                                   <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderung speichern</button>
                                </div>
                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Tabelle/Ergebnisse"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                           </div>
                          </div>

                  </div>

                  <div class="p-6">
                    <div class="flex items-center">
                        <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold"> </div>
                    </div>

                    <div class="ml-12">
                        <div class="mt-2 text-sm text-gray-500">


                        </div>
                    </div>
                  </div>
              </div>
              </form>
            </div>
        </div>
    </div>
</x-app-layout>
