<div class="space-y-8 bg-white p-6">
    <header class="rounded-xl border-l-4 border-gray-600 bg-gray-300 px-5 py-4 text-gray-900">
        <p class="text-xs font-bold uppercase tracking-widest text-gray-700">Fördermittelverwaltung</p>
        <h3 class="mt-1 text-lg font-bold">Bescheidvorlagen</h3>
        <p class="mt-1 text-sm text-gray-800">Texte der Bescheide (PDF und E-Mail-Anhang) mit Platzhaltern anpassen.</p>
    </header>
    @if(session()->has('message'))
        <div class="rounded-md border border-green-200 bg-green-50 px-4 py-3 text-sm text-green-800">{{ session('message') }}</div>
    @endif

    <div class="rounded-md bg-gray-50 p-4 text-sm text-gray-700">
        <p class="font-semibold">Verfügbare Platzhalter</p>
        <ul class="mt-1 grid gap-x-6 sm:grid-cols-2">
            @foreach($placeholders as $key => $description)
                <li><code>{{ $key }}</code> – {{ $description }}</li>
            @endforeach
        </ul>
    </div>

    @foreach($labels as $type => $label)
        <form wire:submit.prevent="save('{{ $type }}')" class="space-y-3 rounded-lg border border-gray-200 p-4">
            <h4 class="font-semibold text-gray-900">{{ $label }}</h4>
            <label class="block text-sm font-medium text-gray-700">Titel
                <input type="text" wire:model="titel.{{ $type }}" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                @error('titel.' . $type)<span class="text-red-700">{{ $message }}</span>@enderror
            </label>
            <label class="block text-sm font-medium text-gray-700">Text
                <textarea wire:model="text.{{ $type }}" rows="8" class="mt-1 w-full rounded-md border-gray-300 text-sm"></textarea>
                @error('text.' . $type)<span class="text-red-700">{{ $message }}</span>@enderror
            </label>
            <div class="flex gap-3">
                <button type="submit" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Speichern</button>
                <button type="button" wire:click="resetToDefault('{{ $type }}')" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Standardtext</button>
            </div>
        </form>
    @endforeach
</div>
