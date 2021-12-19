<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mannschaft - Dashboard') }}
        </h2>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                    Abteilung: {{ old('abteilung') ?? $sportTeam->abteilung }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    @php
                      // ToDo: Beschreibungstext überarbeiten
                    @endphp
                    Bitte gebe die Daten der Mannschaft ein.
                  </div>
              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                        <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Mannschaft bearbeitern</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                              <form autocomplete="off" action="{{ url('Mannschaft/update/'.$sportTeam->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                  // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                @endphp
                                <div class="my-4" >
                                    <label for="name">Mannschaftsname:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('abteilung') ? 'bg-red-300' : '' }}"
                                    id="abteilung" placeholder="Abteilung" name="abteilung" value="{{ old('abteilung') ?? $sportTeam->abteilung }}">
                                    <small class="form-text text-danger">{!! $errors->first('abteilung') !!}</small>
                                </div>
                                <div class="my-4" >
                                    <label for="abteilungTeamBezeichnung">Team Bezeichnung:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('abteilungTeamBezeichnung') ? 'bg-red-300' : '' }}"
                                    id="abteilungTeamBezeichnung" placeholder="Team Bezeichnung" name="abteilungTeamBezeichnung" value="{{ old('abteilungTeamBezeichnung') ?? $sportTeam->abteilungTeamBezeichnung }}">
                                    <small class="form-text text-danger">{!! $errors->first('abteilungTeamBezeichnung') !!}</small>
                                </div>
                                <div class="my-4" >
                                    <label for="name">Domain:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('domain') ? 'bg-red-300' : '' }}"
                                    id="domain" placeholder="http://..." name="domain" value="{{ old('domain') ?? $sportTeam->domain }}">
                                    <small class="form-text text-danger">{!! $errors->first('domain') !!}</small>
                                </div>
                                <div class="my-4" >
                                    <label for="name">Akzentfarbe:</label>
                                    rgba(.....)
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('farbe') ? 'bg-red-300' : '' }}"
                                    id="frabe" placeholder="205,16,118" name="farbe" value="{{ old('farbe') ?? $sportTeam->farbe }}">
                                    <small class="form-text text-danger">{!! $errors->first('farbe') !!}</small>
                                </div>
                                <div class="my-4" >
                                    <label for="name">Headerbild:</label>
                                    @if($sportTeam->bild)
                                     <img src="/storage/header/{{$sportTeam->bild}}" />
                                    @endif
                                    <input type="file" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('bild') ? 'bg-red-300' : '' }}"
                                    id="bild" name="bild" value="">
                                    <small class="form-text text-danger">{!! $errors->first('bild') !!}</small>
                                </div>
                                <div class="my-4" >
                                    <textarea rows="25" cols="200" name="beschreibung" class="w-full rounded border shadow p-2 mr-2 my-2">{{ $ausgabetext }}</textarea>
                                    <small class="form-text text-danger">{!! $errors->first('beschreibung') !!}</small>
                                </div>
                                  <div class="my-4" >
                                      <label for="name">Kurzbeschreibung:</label>
                                      <textarea rows="25" cols="100" name="nachtermin" class="w-full rounded border shadow p-2 mr-2 my-2">{{ $nachtermin }}</textarea>
                                      <small class="form-text text-danger">{!! $errors->first('nachtermin') !!}</small>
                                  </div>
                                <div class="py-2">
                                   <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderungen speichern</button>
                                </div>
                             </form>
                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Mannschaft/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            </div>
                          </div>

                      </div>
                  </div>

              </div>

            </div>
        </div>
    </div>    @php   // TODO:  Wird der div benötigt?
              @endphp
</x-app-layout>
