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
                      Rennen: {{ $race->nummer }}<br>
                      {{ $race->rennBezeichnung }}<br>
                      um {{ date("H:i", strtotime($race->rennUhrzeit)) }} Uhr am {{ date("d.m.Y", strtotime($race->rennDatum)) }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    @php
                      // ToDo: Beschreibungstext überarbeiten
                    @endphp
                    Bitte bearbeiten die Ergebnisse des Rennens.
                  </div>
              </div>
              <form autocomplete="off" action="{{ url('Rennen/Ergebnis/update/'.$race->id) }}" method="post" enctype="multipart/form-data">
                  @csrf
                  @php
                      // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                  @endphp
              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Ergebnisse des Rennens bearbeiten</div>
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
                                    <label for="ergebnisBeschreibung">Beschreibung des Ergebnis:</label>
                                    <textarea rows="10" cols="200" name="ergebnisBeschreibung" class="w-full rounded border shadow p-2 mr-2 my-2">{{ $race->ergebnisBeschreibung }}</textarea>
                                    <small class="form-text text-danger">{!! $errors->first('ergebnisBeschreibung') !!}</small>
                                </div>
                                <div class="my-4" >
                                  @php
                                      $ractetime= substr($ractetime, 0, -3);
                                  @endphp
                                  <label for="rennUhrzeit">Wann wurde das Rennen gestartet:</label>
                                  <input type="time" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennUhrzeit') ? 'bg-red-300' : '' }}"
                                         id="rennUhrzeit" name="rennUhrzeit" value="{{ old('rennUhrzeit') ?? $ractetime }}">
                                  <small class="form-text text-danger">{!! $errors->first('rennUhrzeit') !!}</small>
                                </div>
                                <div class="my-4" >
                                   <label for="rennzeit">Richtige Rennzeit:</label>
                                   <input type="checkbox" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('rennzeit') ? 'bg-red-300' : '' }}"
                                          id="rennzeit" name="rennzeit" value="1"
                                          @if(old('rennzeit')==1 or $race->rennzeit==1)
                                              checked
                                          @endif
                                   >
                                   <small class="form-text text-danger">{!! $errors->first('rennzeit') !!}</small>
                                </div>
                                <div>
                                  <label for="zeit">Zeit in Minuten die pro Rennen aufgeholt werden kann:</label>
                                  <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zeit') ? 'bg-red-300' : '' }}"
                                         id="zeit" name="zeit" value="{{ Session::get('regattaZeit') }}" min="0" max="59">
                                  <small class="form-text text-danger">{!! $errors->first('zeit') !!}</small>
                                </div>
                                <div>
                                    <label for="zeitMinAbstand">Minimaler Zeitabstand in Minuten:</label>
                                    <input type="number" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('zeitMinAbstand') ? 'bg-red-300' : '' }}"
                                           id="zeitMinAbstand" name="zeitMinAbstand" value="{{ Session::get('regattaZeitMinAbstand') }}" min="0" max="59">
                                    <small class="form-text text-danger">{!! $errors->first('zeitMinAbstand') !!}</small>
                                </div>
                                <div class="my-4" >
                                    <label for="name">Dokument:</label>

                                    @if(isset($race->ergebnisDatei))
                                        <div class="flex ml-2">
                                            <div class="flex-initial"><a href="/storage/raceDokumente/{{ $race->ergebnisDatei }}" target="_blank">{{ $race->fileErgebnisDatei }}</a></div>
                                            <div class="flex-initial ml-2 fas fa-times text-red-600 hover:text-red-00 cursor-pointer">
                                                <a href="/Rennen/Ergebnis/loeschen/{{ $race->id }}">x</a>
                                            </div>
                                        </div>
                                    @endif
                                    <input type="file" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('ergebnisDatei') ? 'bg-red-300' : '' }}"
                                           id="ergebnisDatei" name="ergebnisDatei">
                                     <small class="form-text text-danger">{!! $errors->first('ergebnisDatei') !!}</small>
                                </div>
                                <div class="py-2">
                                   <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderung speichern</button>
                                </div>
                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Rennen/Ergebnisse"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
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
                                            <input type="checkbox" id="raceDocId[{{ $i }}]" name="raceDocId[{{ $i }}]" value="{{ $raceDocument->id }}" {{ $race->fileErgebnisDatei==$raceDocument->fileErgebnisDatei && $raceDocument->fileErgebnisDatei!=""? 'checked' : '' }}>
                                            <a class="ml-2 btn btn-sm btn-outline-primary" href="{{ url('Rennen/Ergebnis/'.$raceDocument->id) }}">
                                                <box-icon name='file'></box-icon>
                                            </a>
                                        </div>
                                    </div>
                                    <div class="justify-between my-2">
                                        <div class="flex">
                                            <p class="font-bold text-lg">{{ $raceDocument->nummer }}. {{ $raceDocument->rennBezeichnung }}<br>{{ $raceDocument->fileErgebnisDatei}}</p>
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
