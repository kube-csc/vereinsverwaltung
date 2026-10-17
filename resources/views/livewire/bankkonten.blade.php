<div class="p-6 bg-white">
    <h3 class="text-lg font-medium text-gray-900">Meine Bankkonten</h3>
    <p class="text-sm text-gray-600 mb-4">Diese Konten können Sie in Ihren Anträgen für die Auszahlung auswählen. Sie sind nur für Sie und berechtigte Personen sichtbar.</p>

    @if (session()->has('message'))
        <div class="mb-4 text-sm font-medium text-green-600">{{ session('message') }}</div>
    @endif

    <div class="overflow-x-auto mb-6">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Kontoinhaber</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Bank</th>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">IBAN</th>
                    <th class="px-4 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase">Aktionen</th>
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

    <h4 class="text-md font-medium text-gray-900 mb-2">{{ $editingId ? 'Bankkonto bearbeiten' : 'Neues Bankkonto' }}</h4>
    <form wire:submit.prevent="save" class="grid grid-cols-1 md:grid-cols-2 gap-4">
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
        <div class="md:col-span-2 flex items-center space-x-4">
            <x-button>{{ $editingId ? 'Änderungen speichern' : 'Bankkonto speichern' }}</x-button>
            @if($editingId)
                <button type="button" wire:click="cancel" class="text-sm text-gray-700 underline">Abbrechen</button>
            @endif
        </div>
    </form>
</div>
