<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event - Dashboard') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

              <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                        Event
                  </div>

                  <div class="mt-6 text-gray-500">
                    Bitte gebe ein neues Event ein.
                  </div>

              </div>

              <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Neues Event</div>
                      </div>

                      <div class="ml-12">
                          <div class="mt-2 text-sm text-gray-500">

                            @error('errormessage')
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

                              <form class="my-4" autocomplete="off" action="{{ route('event.store') }}" method="post">
                                @csrf

                                <div>
                                    <label for="name">Eventname:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('ueberschrift') ? 'bg-red-300' : '' }}"
                                           id="ueberschrift" placeholder="Event Name" name="ueberschrift" value="{{ old('ueberschrift') }}">
                                    <small class="form-text text-danger">{!! $errors->first('ueberschrift') !!}</small>
                                </div>
                                <div>
                                    <label for="name">Start Termin:</label>
                                    <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumvon') ? 'bg-red-300' : '' }}"
                                           id="datumvon" placeholder="Event Startdatum" name="datumvon" value="{{ old('datumvon') }}">
                                    <small class="form-text text-danger">{!! $errors->first('datumvon') !!}</small>
                                </div>
                                <div>
                                    <label for="name">End Termin:</label>
                                    <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumbis') ? 'bg-red-300' : '' }}"
                                           id="datumbis" placeholder="Event Enddatum" name="datumbis" value="{{ old('datumbis') }}">
                                    <small class="form-text text-danger">{!! $errors->first('datumbis') !!}</small>
                                </div>
                                <div>
                                    <label for="name">Start Anmeldestarttermin:</label>
                                    <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumvona') ? 'bg-red-300' : '' }}"
                                           id="datumvona" placeholder="Event Anmeldestarttermin" name="datumvona" value="{{ old('datumvona') }}">
                                    <small class="form-text text-danger">{!! $errors->first('datumvona') !!}</small>
                                </div>
                                <div>
                                    <label for="name">End Anmeldeendtermin:</label>
                                    <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumbisa') ? 'bg-red-300' : '' }}"
                                           id="datumbisa" placeholder="Event Anmeldeschluss" name="datumbisa" value="{{ old('datumbisa') }}">
                                    <small class="form-text text-danger">{!! $errors->first('datumbisa') !!}</small>
                                </div>
                                <div>
                                    <label for="name">Ansprechpartner:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('ansprechpartner') ? 'bg-red-300' : '' }}"
                                           id="ansprechpartner" placeholder="Ansprechparter" name="ansprechpartner" value="{{ old('ansprechpartner') }}">
                                    <small class="form-text text-danger">{!! $errors->first('ansprechpartner') !!}</small>
                                </div>
                                <div>
                                    <label for="name">Telefon:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('telefon') ? 'bg-red-300' : '' }}"
                                           id="telefon" placeholder="Telefonnummer" name="telefon" value="{{ old('telefon') }}">
                                    <small class="form-text text-danger">{!! $errors->first('telefon') !!}</small>
                                </div>
                                <div>
                                    <label for="name">E-Mail:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('email') ? 'bg-red-300' : '' }}"
                                           id="email" placeholder="E-Mail" name="email" value="{{ old('email') }}">
                                    <small class="form-text text-danger">{!! $errors->first('email') !!}</small>
                                </div>
                                <div>
                                    <label for="name">Homepage:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('homepage') ? 'bg-red-300' : '' }}"
                                           id="homepage" placeholder="Homepage" name="homepage" value="{{ old('email') }}">
                                    <small class="form-text text-danger">{!! $errors->first('homepage') !!}</small>
                                </div>
                                <div class="my-4" >
                                    <label for="name">Beschreibung:</label>
                                    <textarea rows="25" cols="200" name="beschreibung" class="w-full rounded border shadow p-2 mr-2 my-2">{{ old('beschreibung') }}</textarea>
                                    <small class="form-text text-danger">{!! $errors->first('beschreibung') !!}</small>
                                </div>
                                <div class="my-4" >
                                  <label for="name">Nachbericht:</label>
                                  <textarea rows="25" cols="200" name="nachbericht" class="w-full rounded border shadow p-2 mr-2 my-2">{{ old('nachbericht') }}</textarea>
                                  <small class="form-text text-danger">{!! $errors->first('nachbericht') !!}</small>
                                </div>
                                <div class="my-4" >
                                  <label for="name">Anmeldetext:</label>
                                  <textarea rows="25" cols="200" name="anmeldetext" class="w-full rounded border shadow p-2 mr-2 my-2">{{ old('anmeldetext') }}</textarea>
                                  <small class="form-text text-danger">{!! $errors->first('anmeldetext') !!}</small>
                                </div>
                                <div class="my-4" >
                                    @if (!isset($sportSection_id))
                                    <label for="name">Abteilung / Mannschaft:</label><br>
                                    <select name="sportSection_id">
                                            <option value="">Alle Abteilungen / Mannschaften</option>
                                     <optgroup label="Abeilung:">
                                      @php
                                       $firsttime = 0;
                                      @endphp
                                        @foreach ($sportSections as $sportSection)
                                            @if ($sportSection->sportSection_id > 0 && $firsttime == 0)
                                                @php ($firsttime = 1)
                                                </optgroup>
                                                <optgroup label="Mannschaft:">
                                            @endif
                                            <option value="{{ $sportSection->id }}"
                                            @if ($sportSection->status == 1)
                                             selected
                                            @endif
                                            >{{ $sportSection->abteilung }}</option>
                                        @endforeach
                                     </optgroup>
                                    </select>

                                    @else
                                    <input type="hidden" id="sportSection_id" name="sportSection_id" value="{{ old('sportSection_id') ?? $sportSection_id }}">
                                    @endif
                                </div>
                                <div class="my-4" >
                                      <label for="name">Event Gruppe:</label><br>
                                      <select name="eventGroup_id">
                                          <option value="">keine Event Gruppe</option>

                                             @foreach ($eventGroups as $eventGroup)
                                              <option value="{{ $eventGroup->id }}">
                                                {{ $eventGroup->termingruppe }}
                                              </option>
                                             @endforeach

                                      </select>
                               </div>
                               <div class="py-2">
                                 <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">neues Event anlegen</button>
                               </div>

                            </form>
                            <br>
                            <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Event/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>

                            </div>
                          </div>

                      </div>
                  </div>

              </div>

            </div>
        </div>
    </div>
</x-app-layout>
