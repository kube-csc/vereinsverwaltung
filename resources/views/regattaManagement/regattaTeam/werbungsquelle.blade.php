<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Werbungsquellenauswertung: {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
    </x-slot>
    <div class="max-w-4xl mx-auto py-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4">Werbungsquellenauswertung der Teams</h1>
            {{-- Auswertungsliste GANZ OBEN --}}
            <div class="mb-8">
                @php
                    // '0' ist immer "nicht ausgewählt"
                    $gesamt = collect($statistik)
                        ->filter(function($anzahl, $key) { return $key !== 0 && $key !== '0'; })
                        ->sum();
                    // Gesamtanzahl Teams ohne Status "gelöscht"
                    $gesamtTeams = $regattaTeams->filter(function($team) {
                        return strtolower($team->status) !== 'gelöscht';
                    })->count();
                @endphp
                <h2 class="text-lg font-semibold mb-2">Anzahl pro Werbungsquelle</h2>
                <div class="mb-2 font-bold">
                    Gesamtanzahl gemeldete Meldungen mit Werbungsquellenangabe: {{ $gesamt }}<br>
                    Gesamtanzahl gemeldete Meldungen: {{ $gesamtTeams }}
                </div>
                <ul class="list-disc pl-6">
                    @foreach($statistik as $key => $anzahl)
                        @if(($key !== 0 && $key !== '0') && $anzahl > 0)
                            <li><span class="font-semibold">{{ $werbungOptions[$key] ?? $key }}:</span> {{ $anzahl }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
            {{-- Diagramm darunter --}}
            <div class="mb-8">
                <canvas id="werbungChart" width="400" height="200"></canvas>
            </div>
            <h2 class="text-xl font-semibold mb-2">Meldung nach Werbungsquelle</h2>
            <table class="min-w-full mb-6">
                <thead>
                <tr>
                    <th class="px-4 py-2 border">Meldungen</th>
                    <th class="px-4 py-2 border">Werbungsquelle</th>
                </tr>
                </thead>
                <tbody>
                @php
                    // Teams mit 'nicht ausgewählt' (0) am Ende der Tabelle
                    $teamsMitQuelle = $regattaTeams->filter(function($team) { return $team->werbung != 0 && $team->werbung !== '0'; });
                    $teamsOhneQuelle = $regattaTeams->filter(function($team) { return $team->werbung == 0 || $team->werbung === '0'; });
                @endphp
                @foreach($teamsMitQuelle as $regattaTeam)
                    <tr>
                        <td class="px-4 py-2 border">{{ $regattaTeam->teamname }}</td>
                        <td class="px-4 py-2 border">{{ $werbungOptions[$regattaTeam->werbung ?? '0'] ?? $werbungOptions['0'] }}</td>
                    </tr>
                @endforeach
                @foreach($teamsOhneQuelle as $regattaTeam)
                    <tr>
                        <td class="px-4 py-2 border">{{ $regattaTeam->teamname }}</td>
                        <td class="px-4 py-2 border">{{ $werbungOptions['0'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
            @auth
            <a href="{{ route('regattaTeam.index') }}"
               class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded inline-block text-center transition duration-150 ease-in-out">Zurück
                zur Übersicht</a>
            @endauth
        </div>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const ctx = document.getElementById('werbungChart').getContext('2d');
        const werbungChart = new Chart(ctx, {
            type: 'pie',
            data: {
                labels: {!! json_encode(
                    collect($statistik)
                        ->filter(function($anzahl, $key) { return $key !== 0 && $key !== '0' && $anzahl > 0; })
                        ->map(function($anzahl, $key) use ($werbungOptions) { return $werbungOptions[$key] ?? $key; })
                        ->values()
                ) !!},
                datasets: [{
                    data: {!! json_encode(
                        collect($statistik)
                            ->filter(function($anzahl, $key) { return $key !== 0 && $key !== '0' && $anzahl > 0; })
                            ->values()
                    ) !!},
                    backgroundColor: [
                        '#a3e635', '#fbbf24', '#f87171', '#60a5fa', '#f472b6', '#34d399', '#facc15', '#818cf8', '#fb7185', '#38bdf8', '#f59e42', '#a78bfa', '#fcd34d', '#f472b6'
                    ],
                }]
            },
            options: {
                responsive: true,
                plugins: {
                    legend: {
                        position: 'right',
                    },
                    title: {
                        display: true,
                        text: 'Meldung nach Werbungsquelle'
                    }
                }
            }
        });
    </script>
</x-app-layout>
