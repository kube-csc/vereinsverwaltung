<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regattaverwaltung') }}: {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
    </x-slot>

    <div class="max-w-4xl mx-auto py-8">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Teams aus Textdatei importieren</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Regatta-ID: {{ $regattaId }}
                    </p>
                </div>

                <div class="shrink-0">
                    <a href="{{ route('regattaTeamManager.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold py-2 px-4 rounded inline-block text-center">
                        Zurück zur Übersicht
                    </a>
                </div>
            </div>

            @if ($errors->any())
                <div class="mt-4 p-4 rounded bg-red-50 border border-red-200 text-red-800">
                    <ul class="list-disc pl-5 space-y-1 text-sm">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="mt-6 space-y-4 text-sm text-gray-700">
                <div class="p-4 rounded bg-blue-50 border border-blue-200">
                    <div class="font-semibold mb-2">Hinweise</div>
                    <ul class="list-disc pl-5 space-y-1">
                        <li>Pro Zeile ein Teamname.</li>
                        <li>Wenn die Datei weitere Spalten enthält, wird nur die erste Spalte verwendet.</li>
                        <li>Wenn ein Team schon beim letzten Event gemeldet war, werden möglichst viele Daten von dort übernommen.</li>
                        <li>Pflichtfelder ohne vorhandene Werte werden automatisch mit Platzhaltern gefüllt.</li>
                    </ul>
                </div>

                <div class="p-4 rounded bg-yellow-50 border border-yellow-200">
                    <div class="font-semibold mb-2">Automatisch gesetzte Platzhalter-Werte:</div>
                    <ul class="list-disc pl-5 space-y-1 text-xs">
                        <li><strong>Verein, Teamcaptain, Straße, PLZ, Ort, Telefon:</strong> Leerzeichen ( )</li>
                        <li><strong>E-Mail:</strong> import@invalid.local</li>
                        <li><strong>Status:</strong> Neuanmeldung</li>
                        <li><strong>Werbung:</strong> 0</li>
                        <li><strong>Teamlink:</strong> 0 (wird ggf. bei Alt-Teams synchronisiert)</li>
                    </ul>
                </div>

                <div class="p-4 rounded bg-green-50 border border-green-200">
                    <div class="font-semibold mb-2">Beispiel:</div>
                    <code class="text-xs whitespace-pre-wrap">Team Alpha
Team Beta
Team Gamma</code>
                </div>

                @if($raceTypes->isEmpty())
                    <div class="p-4 rounded bg-yellow-50 border border-yellow-200 text-yellow-900">
                        Für diese Regatta sind noch keine <code>race_types</code> angelegt. Bitte zuerst mindestens eine Zuordnung erstellen.
                    </div>
                @endif
            </div>

            <form method="POST" action="{{ route('regattaTeamManager.importStore') }}" class="mt-6 space-y-5">
                @csrf

                <div>
                    <label for="team_names" class="block font-semibold mb-1">Teamnamen (einer pro Zeile)</label>
                    <textarea name="team_names" id="team_names" rows="12" class="form-input w-full font-mono" placeholder="Team Alpha&#10;Team Beta&#10;Team Gamma" {{ $raceTypes->isEmpty() ? 'disabled' : '' }}></textarea>
                    @error('team_names') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label for="default_race_type_id" class="block font-semibold mb-1">Bootsklasse / race_type für neue Teams</label>
                    <select name="default_race_type_id" id="default_race_type_id" class="form-select w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50" {{ $raceTypes->isEmpty() ? 'disabled' : '' }}>
                        <option value="">Bitte wählen</option>
                        @foreach($raceTypes as $raceType)
                            <option value="{{ $raceType->id }}" {{ old('default_race_type_id', $raceTypes->first()->id ?? null) == $raceType->id ? 'selected' : '' }}>
                                {{ $raceType->typ }} (#{{ $raceType->id }})
                            </option>
                        @endforeach
                    </select>
                    @error('default_race_type_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center gap-3">
                    <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2 px-4 rounded" {{ $raceTypes->isEmpty() ? 'disabled' : '' }}>
                        Import starten
                    </button>
                    <a href="{{ route('regattaTeamManager.index') }}" class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold py-2 px-4 rounded inline-block text-center">
                        Abbrechen
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>


