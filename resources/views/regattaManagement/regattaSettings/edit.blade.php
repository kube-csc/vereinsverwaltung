<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regatta Verwaltung') }} {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="mt-8 text-2xl">
                        Regatta Einstellungen
                    </div>

                    <div class="mt-6 text-gray-500">
                        Hier können Teilnehmerzahl und Teilnehmermax eingestellt werden.
                    </div>
                </div>

                <div class="bg-gray-200 bg-opacity-25 grid grid-cols-1 md:grid-cols-2">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="ml-4 text-lg text-gray-600 leading-7 font-semibold">Regatta Einstellungen</div>
                        </div>

                        <div class="ml-12">
                            <div class="mt-2 text-sm text-gray-500">

                                <div>
                                    @if (session()->has('success'))
                                        <div class="p-3 bg-green-300 text-green-800 rounded shadow-sm">
                                            {!! session('success') !!}
                                        </div>
                                    @endif
                                </div>

                                <form class="my-4" method="POST" action="{{ route('regattaSettings.update') }}" autocomplete="off">
                                    @csrf

                                    <div>
                                        <label for="teilnehmer">Max. Teilnehmer des Event:</label>
                                        <input type="number"
                                               min="0"
                                               class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('teilnehmer') ? 'bg-red-300' : '' }}"
                                               id="teilnehmer" name="teilnehmer"
                                               value="{{ old('teilnehmer', $event->teilnehmer ?? 0) }}">
                                        <div class="text-xs text-gray-500 mt-1">
                                            Wird nur verwendet, wenn bei Wartelistenregeln  eine Option mit Warteliste gewählt ist.
                                        </div>
                                        <small class="form-text text-danger">{!! $errors->first('teilnehmer') !!}</small>
                                    </div>

                                    <div>
                                        <label for="teilnehmermax">Wartelistenregeln:</label>
                                        <select id="teilnehmermax" name="teilnehmermax"
                                                class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('teilnehmermax') ? 'bg-red-300' : '' }}">
                                            @foreach(($teilnehmermaxOptions ?? []) as $value => $label)
                                                <option value="{{ $value }}" {{ (string) old('teilnehmermax', $event->teilnehmermax ?? 0) === (string) $value ? 'selected' : '' }}>
                                                    {{ $label }}
                                                </option>
                                            @endforeach
                                        </select>
                                        <small class="form-text text-danger">{!! $errors->first('teilnehmermax') !!}</small>
                                    </div>

                                    <div class="py-2">
                                        <button type="submit" class="p-2 bg-blue-500 w-40 rounded shadow text-white">Speichern</button>
                                    </div>
                                </form>

                                <br>
                                <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Regattamenu">
                                    <i class="fas fa-arrow-circle-up"></i>Zurück
                                </a>

                            </div>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </div>
</x-app-layout>

