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
                    Event: {{ old('clubname') ?? $club->clubname }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    @php
                      // ToDo: Beschreibungstext überarbeiten
                    @endphp
                    Bitte gebe die Daten von {{ env('MENUE_VERBAND') }} ein.
                  </div>
              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Neue {{ env('MENUE_VERBAND') }}</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                             <form autocomplete="off" action="{{ url('Club/update/'.$club->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                  // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                @endphp
                                <div class="my-4" >
                                    <label for="name">{{ env('MENUE_VERBAND') }} Name:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('clubname') ? 'bg-red-300' : '' }}"
                                    id="clubname" placeholder="{{ env('MENUE_VERBAND') }}" name="clubname" value="{{ old('clubname') ?? $club->clubname }}">
                                    <small class="form-text text-danger">{!! $errors->first('clubname') !!}</small>
                                </div>

                                 @foreach ( $club->sporttypes as $sporttype )
                                     <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Club/{{ $club->id }}/Sportart/{{ $sporttype->id }}/detach')">
                                         <div class="justify-between my-2">
                                             <div class="flex">
                                                 <p class="font-bold text-lg">{{ $sporttype->sportart }} </p>
                                             </div>
                                         </div>
                                     </div>
                                 @endforeach

                                <div class="py-2">
                                 <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderung speichern</button>
                                </div>
                             </form>
                             <br>
                             <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Club/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>

                          </div>

                      </div>
                  </div>

                  <div class="p-6 border-t border-gray-200 md:border-t-0 md:border-l">
                      <div class="flex items-center">
                          <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Sportart Pool</div>
                       </div>

                      @foreach ( $verfuegbareSporttypes as $sporttype )
                          <div class="rounded border shadow p-3 my-2 bg-blue-200" onclick="window.location.replace('/Club/{{ $club->id }}/Sportart/{{ $sporttype->id }}/attach')">
                              <div class="justify-between my-2">
                                  <div class="flex">
                                      <p class="font-bold text-lg">{{ $sporttype->sportart }} </p>
                                  </div>
                              </div>
                          </div>
                      @endforeach

                  </div>

              </div>

            </div>
        </div>
    </div>

</x-app-layout>

