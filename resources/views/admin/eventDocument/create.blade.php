<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dokumenten - Dashboard') }}
        </h2>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                      Dokumentenverwaltung
                  </div>

                  <div class="mt-6 text-gray-500">
                      Beschreibung
                      <!-- ToDo: Beschreibung -->
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Neuen Dokument</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                            @error('dokumentenName')
                              <span class="text-red-500 text-xs">{{ $message }}</span>
                            @enderror

                            <div style="text-align: left">
                              <div>
                                  @if (session()->has('message'))
                                  <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                      {{ session('message') }}
                                  </div>
                                  @endif
                              </div>

                              <form class="my-4" autocomplete="off" action="{{ url('EventDokumente/speichern/'.$event_id) }}" method="post">
                                @csrf
                                  <input type="hidden" id="event_id" name="event_id" value="{{ $event_id }}">
                                <div>
                                    <label for="dokumentName">Dokumenten Name:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('documentName') ? 'bg-red-300' : '' }}"
                                    id="dokumentName" placeholder="Dokumenten Name" name="documentName" value="{{ old('documentName') }}">
                                    <small class="form-text text-danger">{!! $errors->first('documentName') !!}</small>
                                </div>

                                <div class="my-4" >
                                  <label for="verwendung">Dokumententyp:</label>
                                  <br>
                                  <select name="verwendung" class="w-full border rounded shadow p-2 mr-2 my-2">
                                      <option value="2">Ausschreibung</option>
                                      <option value="3">Programm</option>
                                      <option value="4">Ergebnisse</option>
                                      <option value="5">Plakat / Flyer</option>
                                  </select>
                                </div>

                                <div class="py-2">
                                  <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">neues Dokument anlegen</button>
                                </div>

                            </form>
                            <br>
                            <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Adminmenu"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            </div>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
    </div>
</x-app-layout>
