<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Berichte - Dashboard') }}
        </h2>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                        Bericht
                  </div>

                  <div class="mt-6 text-gray-500">
                    Bitte gebe eine neue Bericht an.
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Neuen Bericht</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                            @error('messageSportSection')
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

                              <form class="my-4" autocomplete="off" action="{{ route('report.store') }}" method="post" enctype="multipart/form-data">
                                @csrf
                                <input type="hidden" id="event_id" name="event_id" value="{{ old('event_id') ?? $event_id }}">
                                <div>
                                    <label for="reportTitleImage">Titel des Bildes:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('reportTitleImage') ? 'bg-red-300' : '' }}"
                                    id="reportTitleImage" placeholder="Titel" name="reportTitleImage" value="{{ old('reportTitleImage') }}">
                                    <small class="form-text text-danger">{!! $errors->first('reportTitleImage') !!}</small>
                                </div>
                                <div class="my-4" >
                                  <label for="reportImageComment">Kommentar des Bildes:</label>
                                  <textarea rows="15" cols="100" name="reportImageComment" class="w-full rounded border shadow p-2 mr-2 my-2"></textarea>
                                  <small class="form-text text-danger">{!! $errors->first('reportImageComment') !!}</small>
                                </div>
                                <div class="my-4" >
                                  <label for="image">Bild:</label>
                                  <input type="file" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('image') ? 'bg-red-300' : '' }}"
                                         id="image" name="image" value="">
                                  <small class="form-text text-danger">{!! $errors->first('image') !!}</small>
                                </div>
                                <div class="py-2">
                                  <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">neues Bild anlegen</button>
                                </div>
                              </form>
                              <br>
                              <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Eventvergangenheit/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            </div>
                          </div>

                      </div>
                  </div>
              </div>
            </div>
        </div>
    </div>
</x-app-layout>
