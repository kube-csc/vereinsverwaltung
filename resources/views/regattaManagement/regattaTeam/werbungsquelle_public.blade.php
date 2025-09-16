@extends('layouts.public')

@section('title' ,'Werbungsquellenauswertung')

@section('content')
    <div class="max-w-4xl mx-auto py-8">
        {{-- Event-Überschrift --}}
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="font-semibold text-2xl text-gray-800 leading-tight mb-4">
                {{ $regattaName }}
            </h1>
            <h2 class="text-xl font-bold mb-4">Werbungsquellenauswertung der Teams</h2>
            {{-- Auswertungsliste --}}
            <div class="mb-8">
                @php
                    $gesamt = collect($statistik)
                        ->filter(function($anzahl, $key) { return $key !== 0 && $key !== '0'; })
                        ->sum();
                    $gesamtTeams = $regattaTeams->filter(function($team) {
                        return strtolower($team->status) !== 'Gelöscht';
                    })->count();
                @endphp
                <div class="mb-2 font-bold">
                    Gesamtzahl der Meldungen mit Angabe der Werbungsquelle: {{ $gesamt }}<br>
                    Gesamtzahl Meldungen: {{ $gesamtTeams }}
                </div>
                <h2 class="text-lg font-semibold mb-2">Anzahl pro Werbungsquelle</h2>
                <ul class="list-disc pl-6">
                    @foreach($statistik as $key => $anzahl)
                        @if(($key !== 0 && $key !== '0') && $anzahl > 0)
                            <li><span class="font-semibold">{{ $werbungOptions[$key] ?? $key }}:</span> {{ $anzahl }}</li>
                        @endif
                    @endforeach
                </ul>
            </div>
            {{-- Diagramm --}}
            <div class="mb-8">
                <canvas id="werbungChart" width="400" height="200"></canvas>
            </div>
            <h2 class="text-xl font-semibold mb-2">Meldungen nach Werbungsquellen</h2>
            {{-- Tabellenähnliche Darstellung --}}
            <div class="w-full mb-6">
                <div class="w-full flex font-semibold text-gray-700 bg-gray-200 border border-gray-300 rounded-t">
                    <div class="w-1/2 px-2 py-2 border-r border-gray-300">Teamname</div>
                    <div class="w-1/2 px-2 py-2">Werbungsquelle</div>
                </div>
                @php
                    $teamsMitQuelle  = $regattaTeams->filter(function($team) { return $team->werbung != 0 && $team->werbung !== '0'; });
                    $teamsOhneQuelle = $regattaTeams->filter(function($team) { return $team->werbung == 0 || $team->werbung === '0'; });
                    $allTeams = $teamsMitQuelle->concat($teamsOhneQuelle)->values();
                @endphp
                @foreach($allTeams as $i => $regattaTeam)
                    <div class="flex border-l border-r border-b border-gray-300 @if($i%2==0) bg-gray-50 @else bg-white @endif">
                        <div class="w-1/2 px-2 py-2 border-r border-gray-200 break-words">
                            {{ $regattaTeam->teamname }}
                        </div>
                        <div class="w-1/2 px-2 py-2 break-words">
                            {{ $werbungOptions[$regattaTeam->werbung ?? '0'] ?? $werbungOptions['0'] }}
                        </div>
                    </div>
                @endforeach
            </div>
            {{-- Werbungsquellen-Optionen --}}
            <div class="mt-8">
                <h3 class="text-lg font-semibold mb-2">Mögliche Werbungsquellen-Optionen</h3>
                <ul class="list-disc pl-6">
                    @php
                        $inactiveOptions = include resource_path('views/textimport/werbung_options.php');
                        $inactive = $inactiveOptions['inactive'] ?? [];
                    @endphp
                    @foreach($werbungOptions as $key => $option)
                        @if($key !== '0' && $option && !in_array((string)$key, $inactive))
                            <li><span class="font-semibold">{{ $option }}</span></li>
                        @endif
                    @endforeach
                </ul>
            </div>
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
@endsection
