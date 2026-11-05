<div class="space-y-8 bg-white p-6">
    <header class="rounded-xl border-l-4 border-gray-600 bg-gray-300 px-5 py-4 text-gray-900">
        <p class="text-xs font-bold uppercase tracking-widest text-gray-700">Auswertungen</p>
        <h3 class="mt-1 text-lg font-bold">Anträge und Fördermittel</h3>
        <p class="mt-1 text-sm text-gray-800">Aggregierte Übersichten mit Filtern und CSV-Export. Entwürfe und archivierte Anträge sind nicht enthalten.</p>
    </header>

    <div class="grid gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:grid-cols-2 lg:grid-cols-5">
        <label class="text-sm font-medium text-gray-700">Fördertopf
            <select wire:model.live="pot" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                <option value="">Alle</option>
                @foreach($pots as $p)<option value="{{ $p->id }}">{{ $p->name }}</option>@endforeach
            </select>
        </label>
        <label class="text-sm font-medium text-gray-700">Antragsart
            <select wire:model.live="type" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                <option value="">Alle</option>
                @foreach($types as $t)<option value="{{ $t->id }}">{{ $t->name }}</option>@endforeach
            </select>
        </label>
        <label class="text-sm font-medium text-gray-700">Status
            <select wire:model.live="status" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                <option value="">Alle</option>
                @foreach($statusLabels as $key => $label)<option value="{{ $key }}">{{ $label }}</option>@endforeach
            </select>
        </label>
        <label class="text-sm font-medium text-gray-700">Eingereicht von
            <input type="date" wire:model.live="from" class="mt-1 w-full rounded-md border-gray-300 text-sm">
            @error('from')<span class="text-red-700">{{ $message }}</span>@enderror
        </label>
        <label class="text-sm font-medium text-gray-700">Eingereicht bis
            <input type="date" wire:model.live="to" class="mt-1 w-full rounded-md border-gray-300 text-sm">
            @error('to')<span class="text-red-700">{{ $message }}</span>@enderror
        </label>
        <div class="flex gap-3 sm:col-span-2 lg:col-span-5">
            <button type="button" wire:click="resetFilters" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Filter zurücksetzen</button>
            <button type="button" wire:click="exportCsv" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">CSV exportieren</button>
        </div>
    </div>

    <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
        <div class="rounded-lg border border-gray-200 p-4"><p class="text-xs uppercase text-gray-500">Anträge</p><p class="text-2xl font-bold">{{ $total }}</p></div>
        <div class="rounded-lg border border-gray-200 p-4"><p class="text-xs uppercase text-gray-500">Bewilligt</p><p class="text-2xl font-bold">{{ $byStatus['bewilligt']['count'] }}</p></div>
        <div class="rounded-lg border border-gray-200 p-4"><p class="text-xs uppercase text-gray-500">Bewilligte Summe</p><p class="text-2xl font-bold">{{ number_format($approvedSum, 2, ',', '.') }} €</p></div>
        <div class="rounded-lg border border-gray-200 p-4"><p class="text-xs uppercase text-gray-500">Endgültige Summe</p><p class="text-2xl font-bold">{{ number_format($finalSum, 2, ',', '.') }} €</p></div>
    </div>

    <div class="grid gap-6 lg:grid-cols-2">
        <div>
            <h4 class="mb-2 font-semibold text-gray-900">Nach Status</h4>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <tbody class="divide-y divide-gray-100">
                    @foreach($byStatus as $row)
                        <tr><td class="py-2">{{ $row['label'] }}</td><td class="py-2 text-right font-semibold">{{ $row['count'] }}</td></tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div>
            <h4 class="mb-2 font-semibold text-gray-900">Nach Antragsart</h4>
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <tbody class="divide-y divide-gray-100">
                    @forelse($perType as $row)
                        <tr><td class="py-2">{{ $row['name'] }}</td><td class="py-2 text-right font-semibold">{{ $row['count'] }}</td></tr>
                    @empty
                        <tr><td class="py-2 text-gray-500">Keine Anträge.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div>
        <h4 class="mb-2 font-semibold text-gray-900">Nach Fördertopf</h4>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead class="bg-gray-50 text-left text-xs uppercase text-gray-600">
                    <tr><th class="px-3 py-2">Fördertopf</th><th class="px-3 py-2 text-right">Budget</th><th class="px-3 py-2 text-right">Anträge</th><th class="px-3 py-2 text-right">Bewilligt</th><th class="px-3 py-2 text-right">Bewilligte Summe</th><th class="px-3 py-2 text-right">Endgültige Summe</th></tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($perPot as $row)
                        <tr>
                            <td class="px-3 py-2">{{ $row['name'] }}</td>
                            <td class="px-3 py-2 text-right">{{ $row['budget'] !== null ? number_format((float) $row['budget'], 2, ',', '.') . ' €' : '-' }}</td>
                            <td class="px-3 py-2 text-right">{{ $row['count'] }}</td>
                            <td class="px-3 py-2 text-right">{{ $row['approved_count'] }}</td>
                            <td class="px-3 py-2 text-right">{{ number_format($row['approved_sum'], 2, ',', '.') }} €</td>
                            <td class="px-3 py-2 text-right">{{ number_format($row['final_sum'], 2, ',', '.') }} €</td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-3 py-3 text-gray-500">Keine Anträge für diese Auswahl.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
