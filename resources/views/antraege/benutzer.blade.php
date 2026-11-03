<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Benutzer und Einladungen
        </h2>
    </x-slot>

    <div class="bg-gray-100 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-xl border-l-4 border-gray-600 bg-gray-300 px-6 py-5 text-gray-900 shadow-lg">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-700">Antragswesen · Verwaltung</p>
                <h3 class="mt-1 text-2xl font-bold">Benutzer und Einladungen</h3>
                <p class="mt-1 text-sm text-gray-800">Zugänge einladen und fachliche Rollen sowie Berechtigungen verwalten.</p>
            </section>
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
                @livewire('invitation-manager')
            </div>

            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
                @livewire('benutzer-rollen')
            </div>

            <a href="{{ route('antraege.dashboard') }}" class="inline-flex items-center rounded-md border border-purple-200 bg-white px-4 py-2 text-sm font-semibold text-purple-800 shadow-sm hover:bg-purple-50">
                Zurück zur Antragsverwaltung
            </a>
        </div>
    </div>
</x-app-layout>
