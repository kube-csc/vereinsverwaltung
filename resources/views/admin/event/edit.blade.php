<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Event - Dashboard') }}
        </h2>
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

               <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                  <div class="mt-8 text-2xl">
                    Event: {{ old('ueberschrift') ?? $event->ueberschrift }}
                  </div>

                  <div class="mt-6 text-gray-500">
                    @php
                      // ToDo: Beschreibungstext überarbeiten
                    @endphp
                    Bitte gebe die Daten der Abteilung ein.
                  </div>
               </div>

               <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                  <div class="p-6">
                      <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Event ändern</div>
                      </div>

                      <div class="ml-12">
                         <div class="mt-2 text-sm text-gray-500">

                             <form autocomplete="off" action="{{ url('/Event/update/'.$event->id) }}" method="post" enctype="multipart/form-data">
                                @csrf
                                @php
                                  // ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                @endphp
                                <div class="my-4" >
                                    <label for="name">Eventname:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('ueberschrift') ? 'bg-red-300' : '' }}"
                                           id="ueberschrift" placeholder="Event Name" name="ueberschrift" value="{{ old('ueberschrift') ?? $event->ueberschrift }}">
                                    <small class="form-text text-danger">{!! $errors->first('ueberschrift') !!}</small>
                                </div>
                                <div>
                                    <label for="name">Start Termin:</label>
                                    <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumvon') ? 'bg-red-300' : '' }}"
                                           id="datumvon" placeholder="Event Startdatum" name="datumvon" value="{{ old('datumvon') ?? $event->datumvon }}">
                                    <small class="form-text text-danger">{!! $errors->first('datumvon') !!}</small>
                                </div>
                                <div>
                                     <label for="name">End Termin:</label>
                                     <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumbis') ? 'bg-red-300' : '' }}"
                                            id="datumbis" placeholder="Event Enddatum" name="datumbis" value="{{ old('datumbis') ?? $event->datumbis }}">
                                     <small class="form-text text-danger">{!! $errors->first('datumbis') !!}</small>
                                </div>
                                <div>
                                    <label for="name">Start Anmeldestarttermin:</label>
                                    <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumvona') ? 'bg-red-300' : '' }}"
                                           id="datumvona" placeholder="Event Anmeldestarttermin" name="datumvona" value="{{ old('datumvona') ?? $event->datumvona }}">
                                    <small class="form-text text-danger">{!! $errors->first('datumvona') !!}</small>
                                </div>
                                <div>
                                    <label for="name">Event Anmeldeschluss:</label>
                                    <input type="date" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('datumbisa') ? 'bg-red-300' : '' }}"
                                           id="datumbisa" placeholder="Event Anmeldeschluss" name="datumbisa" value="{{ old('datumbisa') ?? $event->datumbisa }}">
                                    <small class="form-text text-danger">{!! $errors->first('datumbisa') !!}</small>
                                </div>
                                <div>
                                     <label for="name">Ansprechpartner:</label>
                                     <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('ansprechpartner') ? 'bg-red-300' : '' }}"
                                            id="ansprechpartner" placeholder="Ansprechparter" name="ansprechpartner" value="{{ old('ansprechpartner') ?? $event->ansprechpartner }}">
                                     <small class="form-text text-danger">{!! $errors->first('ansprechpartner') !!}</small>
                                </div>
                                <div>
                                     <label for="name">Telefon:</label>
                                     <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('telefon') ? 'bg-red-300' : '' }}"
                                            id="telefon" placeholder="Telefonnummer" name="telefon" value="{{ old('telefon') ?? $event->telefon }}">
                                     <small class="form-text text-danger">{!! $errors->first('telefon') !!}</small>
                                </div>
                                <div>
                                     <label for="name">E-Mail:</label>
                                     <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('email') ? 'bg-red-300' : '' }}"
                                           id="email" placeholder="E-Mail" name="email" value="{{ old('email') ?? $event->email }}">
                                     <small class="form-text text-danger">{!! $errors->first('email') !!}</small>
                                </div>
                                <div>
                                    <label for="name">Homepage:</label>
                                    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('homepage') ? 'bg-red-300' : '' }}"
                                           id="homepage" placeholder="Homepage" name="homepage" value="{{ old('homepage') ?? $event->homepage }}">
                                    <small class="form-text text-danger">{!! $errors->first('homepage') !!}</small>
                                </div>
                                <div class="my-4" >
                                     <label for="name">Ankündigung:</label>
                                     <textarea rows="25" cols="200" name="beschreibung" class="w-full rounded border shadow p-2 mr-2 my-2">{{ old('beschreibung') ?? $event->beschreibung }}</textarea>
                                     <small class="form-text text-danger">{!! $errors->first('beschreibung') !!}</small>
                                </div>
                                <div class="my-4" >
                                     <label for="name">Nachbericht:</label>
                                     <textarea rows="25" cols="200" name="nachbericht" class="w-full rounded border shadow p-2 mr-2 my-2">{{ old('nachbericht') ?? $event->nachtermin }}</textarea>
                                     <small class="form-text text-danger">{!! $errors->first('nachbericht') !!}</small>
                                </div>
                                <div class="my-4" >
                                     <label for="name">Anmeldetext:</label>
                                     <textarea rows="25" cols="200" name="anmeldetext" class="w-full rounded border shadow p-2 mr-2 my-2">{{ old('anmeldetext') ?? $event->anmeldetext }}</textarea>
                                     <small class="form-text text-danger">{!! $errors->first('anmeldetext') !!}</small>
                                </div>
                                <div class="my-4" id="emailAntwort" style="display: {{ $event->regatta == 1  || $event->emailAntwort <>'' ? 'block' : 'none' }};">
                                     <label for="emailAntwort">E-Mail Antwort bei Anmeldung:</label>
                                     <textarea rows="25" cols="200" name="emailAntwort" class="w-full rounded border shadow p-2 mr-2 my-2">{{ old('emailAntwort') ?? $event->emailAntwort }}</textarea>
                                     <small class="form-text text-danger">{!! $errors->first('emailAntwort') !!}</small>
                                </div>

{{--                                <div class="my-4">--}}
{{--                                     <label for="regatta">Regatta:</label>--}}
{{--                                     <input type="checkbox" name="regatta" id="regatta" value="1" {{ old('regatta', $event->regatta) == 1 ? 'checked' : '' }}>--}}
{{--                                     <small class="form-text text-danger">{!! $errors->first('regatta') !!}</small>--}}
{{--                                </div>--}}

                                <div class="my-4" id="einverstaendnis" style="display: {{ $event->regatta == 1  || $event->einverstaendnis <>'' ? 'block' : 'none' }};">
                                      <label for="einverstaendnis">Einverständniserklärung:</label>
                                      <textarea rows="25" cols="200" name="einverstaendnis" class="w-full rounded border shadow p-2 mr-2 my-2">{{ old('einverstaendnis') ?? $event->einverstaendnis }}</textarea>
                                      <small class="form-text text-danger">{!! $errors->first('einverstaendnis') !!}</small>
                                </div>
                                <div class="my-4" >
                                     <label for="name">{{ env('MENUE_ABTEILUNG') }}
                                         @if(env('MENUE_MANNSCHAFTEN')<>"nein")
                                             / {{ env('MENUE_MANNSCHAFTEN') }}
                                         @endif
                                         :
                                     </label>
                                     <br>
                                     <select name="sportSection_id">
                                         <!-- ToDo: Verbesserung Old Wert behalten bei Valiedierungsfehler -->
                                         <option value=""
                                             @if ($event->sportSection_id == NULL)
                                                 selected
                                             @endif
                                             >Alle {{ env('MENUE_ABTEILUNG') }}
                                             @if(env('MENUE_MANNSCHAFTEN')<>"nein")
                                                 / {{ env('MENUE_MANNSCHAFTEN') }}
                                             @endif
                                         </option>
                                     <optgroup label="{{ env('MENUE_ABTEILUNG') }}:">
                                     @php
                                      $firsttime = 0;
                                     @endphp

                                        @foreach ($sportSections as $sportSection)
                                            @if ($sportSection->sportSection_id > 0 && $firsttime == 0)
                                                @php
                                                 $firsttime = 1;
                                                @endphp
                                                </optgroup>
                                                <optgroup label="{{ env('MENUE_MANNSCHAFTEN') }}:">
                                            @endif
                                            <option value="{{ $sportSection->id }}"
                                            @if ($event->sportSection_id == $sportSection->id)
                                             selected
                                            @endif
                                            >{{ $sportSection->abteilung }}</option>
                                        @endforeach

                                    </optgroup>
                                    </select>
                                </div>
                                <div class="my-4" >
                                    <label for="name">Event Gruppe:</label><br>
                                      <!-- ToDo: Verbesserung Old Wert behalten bei Valiedierungsfehler -->
                                      <select name="eventGroup_id">
                                        <option value=""
                                            @if( $event->eventGroup_id == NULL )
                                              selected
                                            @endif
                                        >keine Event Gruppe</option>

                                        @foreach ($eventGroups as $eventGroup)
                                            <option value="{{ $eventGroup->id }}"
                                                  @if( $event->eventGroup_id == $eventGroup->id )
                                                     selected
                                                  @endif
                                            >{{ $eventGroup->termingruppe }}</option>
                                        @endforeach

                                      </select>
                                </div>

                                <div class="py-2">
                                 <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderung speichern</button>
                                </div>
                             </form>
                             <br>
                              @if($event->datumbis >= date("d.m.Y", strtotime(now())) )
                                 <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Event/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                              @else
                                 <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Eventvergangenheit/alle"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                              @endif
                            </div>
                         </div>

                      </div>
                  </div>

               </div>

            </div>
        </div>
</x-app-layout>

