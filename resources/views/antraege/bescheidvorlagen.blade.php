<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Bescheidvorlagen
        </h2>
    </x-slot>

    <div class="bg-gray-100 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-xl border-l-4 border-gray-600 bg-gray-300 px-6 py-5 text-gray-900 shadow-lg">
                <p class="text-xs font-bold uppercase tracking-widest text-gray-700">Antragswesen · Verwaltung</p>
                <h3 class="mt-1 text-2xl font-bold">Bescheidvorlagen</h3>
            </section>
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
                @livewire('bescheidvorlagen')
            </div>

            <a href="{{ route('antraege.dashboard') }}" class="inline-flex items-center rounded-md border border-indigo-200 bg-white px-4 py-2 text-sm font-semibold text-indigo-800 shadow-sm hover:bg-indigo-50">
                Zurück zur Antragsverwaltung
            </a>
        </div>
    </div>
</x-app-layout>
