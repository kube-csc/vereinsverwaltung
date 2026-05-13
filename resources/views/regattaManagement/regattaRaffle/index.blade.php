<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regattaverwaltung') }}: {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
    </x-slot>

    <div class="max-w-6xl mx-auto py-8">
        <div class="bg-white shadow rounded-lg p-6">
            <div class="flex items-start justify-between gap-4">
                <div>
                    <h1 class="text-2xl font-bold">Logik der Regatta-Rennplanerstellung</h1>
                    <p class="text-sm text-gray-600 mt-1">
                        Diese Seite zeigt die Logik-Spezifikation für die automatisierte Rennplanerstellung.
                    </p>
                </div>

                <div class="shrink-0">
                    <a href="/Regattamenu" class="bg-gray-200 hover:bg-gray-300 text-gray-900 font-semibold py-2 px-4 rounded">
                        Zurück zum Regattamenü
                    </a>
                </div>
            </div>

            <div class="mt-8 grid grid-cols-1 lg:grid-cols-2 gap-8">
                <!-- Linke Seite: Daten-Übersicht -->
                <div class="space-y-4">
                    <h3 class="font-bold text-lg">Daten-Übersicht</h3>
                    <div class="bg-gray-50 rounded-lg p-4 border">
                        <p class="text-sm text-gray-700 mb-4">
                            Diese Daten dienen als Test- und Referenzdaten für die Entwicklung und Validierung der Planungs-Logik.
                        </p>

                        <div class="overflow-x-auto">
                            <table class="min-w-full text-xs">
                                <thead class="bg-gray-200">
                                    <tr>
                                        <th class="px-2 py-1 text-left">Team</th>
                                        <th class="px-2 py-1 text-left">Klasse</th>
                                        <th class="px-2 py-1 text-left">Verein/Ort</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-200">
                                    @foreach($teams as $team)
                                        <tr>
                                            <td class="px-2 py-1">{{ $team->teamname }}</td>
                                            <td class="px-2 py-1 text-gray-500">{{ optional($team->teamWertungsGruppe)->typ }}</td>
                                            <td class="px-2 py-1 text-gray-500">{{ $team->ort }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

                <!-- Rechte Seite: Generator -->

                    <div class="bg-yellow-50 border-l-4 border-yellow-400 p-4">
                        <h3 class="font-bold text-yellow-800 flex items-center">
                            <box-icon name='terminal' class="mr-2" color="#854d0e"></box-icon>
                            Rennplan generieren
                        </h3>
                        <form action="{{ route('regattaRaffle.generate') }}" method="POST" class="mt-4 space-y-4">
                            @csrf
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Anzahl der Vorläufe</label>
                                <input type="number" name="heats_count" value="3" min="1" max="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Startzeit</label>
                                <input type="time" name="start_time" value="10:00" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Intervall (Minuten)</label>
                                <input type="number" name="interval" value="10" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700">Wertungsmodus</label>
                                <select name="wertungsart" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                    <option value="1">Punktwertung</option>
                                    <option value="2">Zeitwertung</option>
                                </select>
                            </div>
                            <button type="submit" class="w-full bg-yellow-600 hover:bg-yellow-700 text-white font-bold py-2 px-4 rounded">
                                Vorschlag generieren
                            </button>
                        </form>
                    </div>

                    @if(isset($previewData) && count($previewData) > 0)
                        <div class="bg-green-50 border-l-4 border-green-400 p-4 mt-6">
                            <h3 class="font-bold text-green-800 flex items-center">
                                <box-icon name='table' class="mr-2" color="#166534"></box-icon>
                                Vorschau des Rennplans
                            </h3>
                            <div class="mt-4 space-y-8">
                                @php
                                    $groupedPreview = collect($previewData)->groupBy('gruppe_name');
                                @endphp

                                @foreach($groupedPreview as $gruppeName => $rows)
                                    <div class="border rounded-lg overflow-hidden">
                                        <div class="bg-green-100 px-4 py-2 font-bold text-green-800 border-b">
                                            Wertungsgruppe: {{ $gruppeName }}
                                        </div>
                                        <div class="overflow-x-auto">
                                            <table class="min-w-full text-xs">
                                                <thead class="bg-gray-50">
                                                    <tr>
                                                        <th class="px-2 py-1 text-left">Zeit</th>
                                                        <th class="px-2 py-1 text-left">Pause</th>
                                                        <th class="px-2 py-1 text-left">Vorlauf</th>
                                                        <th class="px-2 py-1 text-left">Rennen</th>
                                                        <th class="px-2 py-1 text-left">Bahn</th>
                                                        <th class="px-2 py-1 text-left">Team</th>
                                                    </tr>
                                                </thead>
                                                <tbody class="divide-y divide-green-200 bg-white">
                                                    @foreach($rows as $row)
                                                        <tr>
                                                            <td class="px-2 py-1 font-semibold">{{ $row['time'] }}</td>
                                                            <td class="px-2 py-1 text-gray-500">{{ $row['pause'] ?? '-' }}</td>
                                                            <td class="px-2 py-1">{{ $row['heat_index'] ?? 1 }}. VL</td>
                                                            <td class="px-2 py-1">Lauf {{ $row['race_number'] }}</td>
                                                            <td class="px-2 py-1">Bahn {{ $row['lane'] }}</td>
                                                            <td class="px-2 py-1">{{ $row['team_name'] }}</td>
                                                        </tr>
                                                    @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <form action="{{ route('regattaRaffle.store') }}" method="POST" class="mt-4">
                                @csrf
                                <button type="submit" class="w-full bg-green-600 hover:bg-green-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Soll der Rennplan so gespeichert werden?')">
                                    Plan übernehmen & speichern
                                </button>
                            </form>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

</x-app-layout>
