<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Antragsprüfung</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="overflow-hidden rounded-xl bg-white shadow-xl">
                @livewire('antraege-pruefung')
            </div>
            <a href="{{ route('antraege.dashboard') }}" class="inline-block text-blue-700 underline">Zurück zur Antragsverwaltung</a>
        </div>
    </div>
</x-app-layout>
