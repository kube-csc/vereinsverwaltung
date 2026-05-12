<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Mitglieder einladen') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                @livewire('invitation-manager')
            </div>

            <div class="mt-4">
                <a href="{{ route('adminmenu') }}" class="p-2 bg-blue-500 w-40 rounded shadow text-white">
                    <i class="fas fa-arrow-left"></i> Zurück zum Adminmenü
                </a>
            </div>
        </div>
    </div>
</x-app-layout>
