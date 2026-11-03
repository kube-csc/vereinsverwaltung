<div class="space-y-6 bg-white p-6">
    <header class="rounded-xl border-l-4 border-gray-600 bg-gray-300 px-5 py-4 text-gray-900">
        <p class="text-xs font-bold uppercase tracking-widest text-gray-700">Auszahlung</p>
        <h3 class="mt-1 text-lg font-bold">Meine Bankkonten</h3>
        <p class="mt-1 text-sm text-gray-800">Diese Konten können Sie in Ihren Anträgen für die Auszahlung auswählen. Sie sind nur für Sie und berechtigte Personen sichtbar.</p>
    </header>

    @if (session()->has('message'))
        <div class="mb-4 text-sm font-medium text-green-600">{{ session('message') }}</div>
    @endif

    <div class="overflow-hidden rounded-lg border border-gray-200">
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="bg-green-50 px-4 py-3 text-left text-xs font-semibold uppercase text-green-900">Kontoinhaber</th>
                    <th class="bg-green-50 px-4 py-3 text-left text-xs font-semibold uppercase text-green-900">Bank</th>
                    <th class="bg-green-50 px-4 py-3 text-left text-xs font-semibold uppercase text-green-900">IBAN</th>
                    <th class="bg-green-50 px-4 py-3 text-right text-xs font-semibold uppercase text-green-900">Aktionen</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($accounts as $account)
                    <tr wire:key="konto-{{ $account->id }}">
                        <td class="px-4 py-3 text-sm text-gray-900">{{ $account->kontoinhaber }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700">{{ $account->bank_name ?: '-' }}</td>
                        <td class="px-4 py-3 text-sm text-gray-700 font-mono">{{ $account->maskedIban() }}</td>
                        <td class="px-4 py-3 text-right text-sm font-medium space-x-3">
                            <button wire:click="edit({{ $account->id }})" class="text-blue-700 hover:text-blue-900">Bearbeiten</button>
                            <button wire:click="delete({{ $account->id }})" wire:confirm="Dieses Bankkonto wirklich entfernen?" class="text-red-600 hover:text-red-900">Entfernen</button>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="4" class="px-4 py-3 text-center text-sm text-gray-500">Noch keine Bankkonten hinterlegt.</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>

    <form wire:submit.prevent="save" class="grid grid-cols-1 gap-4 rounded-xl border-2 border-green-200 bg-green-50 p-5 md:grid-cols-2">
        <div class="md:col-span-2 border-b border-green-200 pb-3">
            <h4 class="text-lg font-bold text-green-900">{{ $editingId ? 'Bankkonto bearbeiten' : 'Neues Bankkonto' }}</h4>
            <p class="mt-1 text-sm text-green-800">IBAN und BIC werden geschützt gespeichert.</p>
        </div>
        <div>
            <x-label value="Kontoinhaber" />
            <x-input type="text" wire:model="kontoinhaber" class="w-full" />
            <x-input-error for="kontoinhaber" class="mt-1" />
        </div>
        <div>
            <x-label value="Bank (optional)" />
            <x-input type="text" wire:model="bank_name" class="w-full" />
            <x-input-error for="bank_name" class="mt-1" />
        </div>
        <div>
            <x-label value="IBAN" />
            <x-input type="text" wire:model="iban" class="w-full font-mono" autocomplete="off" />
            <x-input-error for="iban" class="mt-1" />
        </div>
        <div>
            <x-label value="BIC (optional)" />
            <x-input type="text" wire:model="bic" class="w-full font-mono" autocomplete="off" />
            <x-input-error for="bic" class="mt-1" />
        </div>
        <div class="md:col-span-2 flex items-center space-x-4 border-t border-green-200 pt-4">
            <x-button>{{ $editingId ? 'Änderungen speichern' : 'Bankkonto speichern' }}</x-button>
            @if($editingId)
                <button type="button" wire:click="cancel" class="text-sm text-gray-700 underline">Abbrechen</button>
            @endif
        </div>
    </form>
</div>
