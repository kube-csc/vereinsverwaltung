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
                        Event: {{ $event->ueberschrift }}<br>
                        Datei: {{ old('socialMediaTitel') ?? $report->titel }}
                    </div>

                    <div class="mt-6 text-gray-500">
                        @php
                            // ToDo: Beschreibungstext überarbeiten
                        @endphp
                        Füge die Daten des Social Media Templates ein.
                    </div>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Social Media Templet bearbeiten</div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">

                                <form autocomplete="off" action="{{ url('/Event/SocialMedia/update/'.$report->id) }}" method="post" enctype="multipart/form-data">
                                    @csrf
                                    @php
                                        //ToDo:  @method('PUT') in Hobby Projekt noch mal erlernen
                                    @endphp
                                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                                    <div class="my-4" >
                                        <label for="name">Titel des Videos:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('reportTitleDocument') ? 'bg-red-300' : '' }}"
                                               id="socialMediaTitel" placeholder="Titel" name="socialMediaTitel" value="{{ old('socialMediaTitel') ?? $report->titel }}">
                                        <small class="form-text text-danger">{!! $errors->first('socialMediaTitel') !!}</small>
                                    </div>
                                    <div class="my-4" >
                                        <label for="name">Kommentar des Videos:</label>
                                        <textarea rows="15" cols="150" name="socialMediaComment" class="w-full rounded border shadow p-2 mr-2 my-2">{{ old('socialMediaComment') ?? $report->kommentar }}</textarea>
                                        <small class="form-text text-danger">{!! $errors->first('socialMediaComment') !!}</small>
                                    </div>
                                    <div class="my-4" >
                                        <label for="socialMediaId">Video ID:</label>
                                        <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('socialMediaId') ? 'bg-red-300' : '' }}"
                                               id="socialMediaId" placeholder="Video ID" name="socialMediaId" value="{{ old('socialMediaId') ?? $report->filename }}">
                                        <small class="form-text text-danger">{!! $errors->first('socialMediaId') !!}</small>
                                    <div class="my-4" >
                                        <label for="player">Player:</label>
                                        <br>
                                        <select name="player" class="w-full border rounded shadow p-2 mr-2 my-2">
                                            @foreach($players as $player)
                                                <option value="{{ $player->id }}" {{ (old('player') == $player->id || $report->player == $player->id) ? 'selected' : '' }}>{{ $player->playername }}</option>
                                            @endforeach
                                        </select>
                                    </div>
                                    <div class="py-2">
                                        <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Änderungen speichern</button>
                                    </div>

                                </form>
                                <br>
                                <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Event/SocialMedia/{{ $report->event_id  }}"><i class="fas fa-arrow-circle-up"></i>Zurück</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
