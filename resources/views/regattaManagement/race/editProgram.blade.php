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
                      Rennen: {{ $race->nummer }}<br>
                      {{ $race->rennBezeichnung }}<br>
                      um {{ date("H:i", strtotime($race->rennUhrzeit)) }} Uhr am {{ date("d.m.Y", strtotime($race->rennDatum)) }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    @php
                      // ToDo: Beschreibungstext überarbeiten
                    @endphp
                    Bitte bearbeite das Programm des Rennens.
                  </div>
              </div>
              <form autocomplete="off" action="{{ url('/Rennen/Programm/update/'.$race->id) }}" method="post" enctype="multipart/form-data">
                  @csrf
                  @php
                      // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                  @endphp

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">

                      <div class="p-6">
                          <div class="flex items-center">
                                <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Programm des Rennens bearbeiten</div>
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
                                        <label for="name">Beschreibung zum Rennen:</label>
                                        <textarea rows="10" cols="200" name="beschreibung" class="w-full rounded border shadow p-2 mr-2 my-2">{{ $race->beschreibung }}</textarea>
                                        <small class="form-text text-danger">{!! $errors->first('beschreibung') !!}</small>
                                    </div>

                                    <div class="my-4" >
                                        <label for="name">Dokument:</label>

                                        @if(isset($race->programmDatei))
                                            <div class="flex ml-2">
                                                <div class="flex-initial"><a href="/storage/raceDokumente/{{ $race->programmDatei }}" target="_blank">{{ $race->fileProgrammDatei }}</a></div>
                                                <div class="flex-initial ml-2 fas fa-times text-red-600 hover:text-red-00 cursor-pointer">
                                                    <a href="/Rennen/Programm/loeschen/{{ $race->id }}">x</a>
                                                </div>
                                            </div>
                                        @endif
                                        <input type="file" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('programmDatei') ? 'bg-red-300' : '' }}"
                                               id="programmDatei" name="programmDatei">
                                         <small class="form-text text-danger">{!! $errors->first('programmDatei') !!}</small>
                                    </div>
                                    <div class="py-2">
                                       <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderung speichern</button>
                                    </div>

                                 <br>
                                 <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Rennen/Programm"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                              </div>
                          </div>
                      </div>

                      <div class="p-6">
                          <div class="flex items-center">
                              <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Rennen an den das gleiche Dokument angehangen werden soll</div>
                          </div>

                          <div class="ml-12">
                              <div class="mt-2 text-sm text-gray-500">
                                  @php
                                  $i=0;
                                  @endphp
                                  @foreach ( $raceDocuments as $raceDocument)
                                      @php
                                         ++$i;
                                      @endphp
                                      <div class="rounded border shadow p-3 my-2 bg-blue-200" >
                                          <div class="justify-between my-2">
                                              <div>
                                                  <label for="raceDocId[{{ $i }}]">Mit Bearbeiten: </label>
                                                  <input type="checkbox" id="raceDocId[{{ $i }}]" name="raceDocId[{{ $i }}]" value="{{ $raceDocument->id }}" {{ $race->programmDatei==$raceDocument->programmDatei && $raceDocument->programmDatei!="" ? 'checked' : '' }}>
                                                  <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Rennen/Programm/'.$raceDocument->id) }}">
                                                      <box-icon name='file'></box-icon>
                                                  </a>
                                              </div>
                                          </div>
                                          <div class="justify-between my-2">
                                              <div class="flex">
                                                  <p class="font-bold text-lg">{{ $raceDocument->nummer }}. {{ $raceDocument->rennBezeichnung }}<br>{{ $raceDocument->fileProgrammDatei}}</p>
                                                  <p class="mx-3 py-1 text-xs text-gray-500 font-semibold">{{ $raceDocument->updated_at->diffForHumans() }}</p>
                                              </div>
                                              <div class="flex">
                                                  von {{ date("d.m.Y", strtotime($raceDocument->rennDatum)) }} {{ date("h:m", strtotime($raceDocument->rennUhrzeit)) }}
                                              </div>
                                          </div>

                                      </div>
                                  @endforeach

                              </div>
                          </div>
                      </div>

                    </div>
              </form>
            </div>

        </div>
    </div>
</x-app-layout>
