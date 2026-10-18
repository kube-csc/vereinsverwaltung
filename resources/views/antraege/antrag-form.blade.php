<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Antrag ausfüllen</h2>
    </x-slot>

    <div class="py-10">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @livewire('antrag-form', ['typeId' => $typeId, 'applicationId' => $applicationId ?? null])
            </div>
            <a href="{{ route('antraege.meine') }}" class="inline-block text-blue-700 underline">Zurück zu meinen Anträgen</a>
        </div>
    </div>
</x-app-layout>
