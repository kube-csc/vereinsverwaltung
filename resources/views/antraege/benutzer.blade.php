<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Benutzer und Einladungen
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @livewire('invitation-manager')
            </div>

            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @livewire('benutzer-rollen')
            </div>

            <a href="{{ route('antraege.dashboard') }}" class="inline-block text-blue-700 underline">
                Zurück zur Antragsverwaltung
            </a>
        </div>
    </div>
</x-app-layout>
