<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Backlinks - Dashboard') }}
        </h2>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                    Team:
                  </div>

                  <div class="mt-6 text-gray-500">
                      In diesem Bereich werden die Umleitung der Backlinks von vorherrigen Weblayouts bearbeitet.
                      @php
                          //ToDo: Texte Verbessern
                      @endphp
                  </div>
              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Backlink bearbeiten</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                              <form autocomplete="off" action="{{ url('Backlink/update/'.$backlink->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                  // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                @endphp
                                <div class="my-4" >
                                    <label for="backlink">Backlink:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('backlink') ? 'bg-red-300' : '' }}"
                                    id="backlink" placeholder="Backlink" name="backlink" value="{{ old('backlink') ?? $backlink->backlink }}">
                                    <small class="form-text text-danger">{!! $errors->first('backlink') !!}</small>
                                </div>

                                <div class="my-4" >
                                  <label for="name">Parameterberücksichtigung:</label><br>
                                  <select name="teilUrl">
                                      <!-- ToDo: Verbesserung Old Wert behalten bei Valiedierungsfehler -->
                                      <option value="0"
                                          @if ($backlink->teilUrl == 0)
                                            selected
                                          @endif
                                      >keine Bearbeitung
                                      </option>
                                      <option value="1"
                                          @if ($backlink->teilUrl == 1)
                                            selected
                                          @endif
                                      >Parameter werden nach dem ? abgeschnitten
                                      </option>
                                      <option value="2"
                                          @if ($backlink->teilUrl == 2)
                                             selected
                                          @endif
                                      >erster Parameter an der URl angehangen
                                      </option>
                                      <option value="3"
                                          @if ($backlink->teilUrl == 3)
                                             selected
                                          @endif
                                      >ein Parameter wird ersetzt
                                      </option>
                                      <option value="4"
                                              @if ($backlink->teilUrl == 4)
                                              selected
                                          @endif
                                      >den angebende Parameter an der URl angehangen
                                      </option>
                                      <option value="5"
                                         @if ($backlink->teilUrl == 5)
                                             selected
                                          @endif
                                      >ein Parameter verhinder das neu aufnehmen in der Datenbank
                                      </option>
                                  </select>
                                </div>

                                <div class="my-4" >
                                  <label for="prefixNumber">Prefix Anzahl:</label>
                                  <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('prefixNumber') ? 'bg-red-300' : '' }}"
                                         id="prefixNumber" placeholder="1" name="prefixNumber" value="{{ old('prefixNumber') ?? $backlink->prefixNummer }}">
                                  <small class="form-text text-danger">{!! $errors->first('prefixNumber') !!}</small>
                                </div>

                                <div class="my-4" >
                                  <label for="prefixName">Prefix Name:</label>
                                  <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('prefixName') ? 'bg-red-300' : '' }}"
                                         id="prefixName" placeholder="Prefix Name" name="prefixName" value="{{ old('prefixName') ?? $backlink->prefixName }}">
                                  <small class="form-text text-danger">{!! $errors->first('prefixName') !!}</small>
                                </div>

                                <div class="my-4" >
                                    <label for="neueUrl">neue Url:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('neueUrl') ? 'bg-red-300' : '' }}"
                                    id="neueUrl" placeholder="neue Url" name="neueUrl" value="{{ old('neueUrl') ?? $backlink->neueUrl }}">
                                    <small class="form-text text-danger">{!! $errors->first('neueUrl') !!}</small>
                                </div>

                                <div class="py-2">
                                 <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderungen speichern</button>
                                </div>
                             </form>
                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Backlink/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            </div>
                          </div>

                      </div>
                  </div>

              </div>

        </div>
    </div>

</x-app-layout>
