<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ env('MENUE_VERBAND') }} {{ __('Dashboard') }}
        </h2>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                    Sportart: {{ old('sporttypename') ?? $sporttype->sportart }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    @php
                      // ToDo: Beschreibungstext überarbeiten
                    @endphp
                    Bitte gebe die Daten von der {{ env('MENUE_VERBAND') }} ein.
                  </div>
              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Neue Sportart</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                             <form autocomplete="off" action="{{ url('Sportart/update/'.$sporttype->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                  // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                @endphp
                                <div class="my-4" >
                                    <label for="name">Sportart:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('sporttypename') ? 'bg-red-300' : '' }}"
                                    id="sporttypename" placeholder="Sportart" name="sporttypename" value="{{ old('sporttypename') ?? $sporttype->sportart }}">
                                    <small class="form-text text-danger">{!! $errors->first('sporttypename') !!}</small>
                                </div>
                                <div class="py-2">
                                 <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderung speichern</button>
                                </div>
                             </form>
                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Sportart/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>

                          </div>

                      </div>
                  </div>

              </div>

            </div>
        </div>
    </div>

</x-app-layout>
