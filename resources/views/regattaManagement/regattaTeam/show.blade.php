<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regattaverwaltung') }}: {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
    </x-slot>
    <div class="max-w-2xl mx-auto py-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4">Team: {{ $regattaTeam->teamname }}</h1>
            <div class="mb-2"><strong>Verein:</strong> {{ $regattaTeam->verein }}</div>
            <div class="mb-2"><strong>Teamcaptain:</strong> {{ $regattaTeam->teamcaptain }}</div>
            <div class="mb-2"><strong>Straße:</strong> {{ $regattaTeam->strasse }}</div>
            <div class="mb-2"><strong>PLZ:</strong> {{ $regattaTeam->plz }}</div>
            <div class="mb-2"><strong>Ort:</strong> {{ $regattaTeam->ort }}</div>
            <div class="mb-2"><strong>Telefon:</strong> {{ $regattaTeam->telefon }}</div>
            <div class="mb-2"><strong>E-Mail:</strong> {{ $regattaTeam->email }}</div>
            <div class="mb-2"><strong>Homepage:</strong> {{ $regattaTeam->homepage }}</div>
            <div class="mb-2"><strong>Beschreibung:</strong> {{ $regattaTeam->beschreibung }}</div>
            <div class="mb-2"><strong>Kommentar:</strong> {{ $regattaTeam->kommentar }}</div>
            <div class="mb-2"><strong>Status:</strong> {{ $regattaTeam->status ?? '-' }}</div>
            <div class="mb-2"><strong>Wertungsgruppe:</strong> {{ $regattaTeam->teamWertungsGruppe->typ }}</div>
            <div class="mb-2"><strong>Werbungsquelle:</strong>
                @php
                    $werbungOptions = [
                        '0' => 'nicht ausgewählt',
                        '1' => 'kel-datteln.de Homepage',
                        '2' => 'Day of Dragons Homepage',
                        '3' => 'Kanucup-Datteln Homepage',
                        '4' => 'Plakatwerbung',
                        '5' => 'Flyer',
                        '6' => 'Empfehlung von Sportfreunden',
                        '7' => 'Radio',
                        '8' => 'Drachenboot-Liga',
                        '9' => 'Einladungsmail',
                        '10' => 'Presse',
                        '11' => 'Sonstiges',
                        '12' => 'dragonboat.online',
                        '13' => 'lokalkompass.de',
                    ];
                @endphp
                {{ $werbungOptions[$regattaTeam->werbung ?? '0'] ?? 'nicht ausgewählt' }}
            </div>
            <div class="mt-4">
                <a href="{{ route('regattaTeam.edit', $regattaTeam->id) }}" class="bg-blue-500 text-white px-4 py-2 rounded transition duration-150 ease-in-out hover:bg-blue-700 hover:scale-105">Bearbeiten</a>
                <a href="{{ route('regattaTeam.index') }}" class="ml-4 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded inline-block text-center transition duration-150 ease-in-out">Zurück zur Übersicht</a>
            </div>
        </div>
    </div>
</x-app-layout>
