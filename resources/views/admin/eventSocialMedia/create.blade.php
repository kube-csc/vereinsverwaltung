<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Social Media') }} - Dashboard
        </h2>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                      Social Media Templet Verwaltung
                  </div>

                  <div class="mt-6 text-gray-500">
                      Beschreibung
                      <!-- ToDo: Beschreibung -->
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Neues Social Media Templet</div>
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

                              <form class="my-4" autocomplete="off" action="{{ url('/Event/SocialMedia/speichern/'.$event_id) }}" method="post">
                                @csrf
                               <input type="hidden" id="event_id" name="event_id" value="{{ $event_id }}">
                                <div>
                                    <label for="socialMediaTitel">Titel des Sozial Media:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('socialMediaTitel') ? 'bg-red-300' : '' }}"
                                           id="socialMediaTitel" placeholder="Titel" name="socialMediaTitel" value="{{ old('socialMediaTitel') }}">
                                    <small class="form-text text-danger">{!! $errors->first('socialMediaTitel') !!}</small>
                                </div>
                                <div class="my-4" >
                                    <label for="socialMediaComment">Kommentar des Sozial Media:</label>
                                    <textarea rows="15" cols="100" name="socialMediaComment" class="w-full rounded border shadow p-2 mr-2 my-2">{{ old('socialMediaComment') }}</textarea>
                                    <small class="form-text text-danger">{!! $errors->first('socialMediaComment') !!}</small>
                                </div>
                                <div>
                                    <label for="socialMediaId">Sozial Media ID:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('socialMediaId') ? 'bg-red-300' : '' }}"
                                    id="socialMediaId" placeholder="Sozial Media ID" name="socialMediaId" value="{{ old('socialMediaId') }}">
                                    <small class="form-text text-danger">{!! $errors->first('socialMediaId') !!}</small>
                                </div>
                                <div class="my-4" >
                                  <label for="player">Abspieler:<br>
                                      id durch [URL] ersetzen
                                  </label>
                                  <br>
                                  <select name="player" class="w-full border rounded shadow p-2 mr-2 my-2">
                                      @foreach($players as $player)
                                        <option value="{{ $player->id }}" {{ old('player') == $player->id ? 'selected' : '' }}>{{ $player->playername }}</option>
                                      @endforeach
                                  </select>
                                    <small class="form-text text-danger">{!! $errors->first('player') !!}</small>
                                </div>
                                <div class="py-2">
                                  <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Anlegen</button>
                                </div>
                            </form>
                            <br>
                            <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Event/SocialMedia/{{ $event_id }}"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Adminmenu"><i class="fas fa-arrow-circle-up"></i>Zurück zum Adminmenu</a>
                            </div>
                          </div>
                      </div>
                  </div>
              </div>
            </div>
        </div>
    </div>
</x-app-layout>
