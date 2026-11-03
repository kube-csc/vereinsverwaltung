<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Meine Anträge</h2>
    </x-slot>

    <div class="bg-gray-100 py-8">
        <div class="mx-auto max-w-7xl space-y-6 px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-xl border-l-4 border-blue-300 bg-blue-800 px-6 py-5 text-white shadow-lg">
                <p class="text-xs font-bold uppercase tracking-widest text-blue-200">Persönlicher Bereich</p>
                <h3 class="mt-1 text-2xl font-bold">Meine Anträge</h3>
                <p class="mt-1 text-sm text-blue-100">Entwürfe fortsetzen, Rückfragen beantworten und den Bearbeitungsstand einsehen.</p>
            </section>
            <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-lg">
                @livewire('meine-antraege')
            </div>
            <a href="{{ route('antraege.dashboard') }}" class="inline-flex items-center rounded-md border border-blue-200 bg-white px-4 py-2 text-sm font-semibold text-blue-800 shadow-sm hover:bg-blue-50">Zurück zur Antragsverwaltung</a>
        </div>
    </div>
</x-app-layout>
