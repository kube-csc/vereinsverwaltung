<div class="p-6 bg-white space-y-8">
    @if (session()->has('message'))
        <div class="text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded p-3">{{ session('message') }}</div>
    @endif
    @if (session()->has('error'))
        <div class="text-sm font-medium text-red-700 bg-red-50 border border-red-200 rounded p-3">{{ session('error') }}</div>
    @endif

    @forelse($programs as $program)
        <section wire:key="programm-{{ $program->id }}" class="border border-gray-200 rounded-lg">
            <header class="flex flex-wrap items-start justify-between gap-2 bg-gray-50 px-4 py-3 rounded-t-lg">
                <div>
                    <h3 class="text-lg font-semibold text-gray-900">
                        {{ $program->name }}
                        @unless($program->aktiv)
                            <span class="ml-2 text-xs font-medium bg-gray-200 text-gray-700 rounded-full px-2 py-0.5">inaktiv</span>
                        @endunless
                    </h3>
                    @if($program->beschreibung)
                        <p class="text-sm text-gray-600">{{ $program->beschreibung }}</p>
                    @endif
                </div>
                <div class="text-sm space-x-3">
                    <button wire:click="newPot({{ $program->id }})" class="text-blue-700 hover:underline">Fördertopf anlegen</button>
                    <button wire:click="editProgram({{ $program->id }})" class="text-blue-700 hover:underline">Bearbeiten</button>
                    <button wire:click="deleteProgram({{ $program->id }})" wire:confirm="Förderprogramm wirklich löschen?" class="text-red-600 hover:underline">Löschen</button>
                </div>
            </header>

            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200 text-sm">
                    <thead>
                        <tr class="text-left text-xs font-medium text-gray-500 uppercase">
                            <th class="px-4 py-2">Fördertopf</th>
                            <th class="px-4 py-2">Laufzeit</th>
                            <th class="px-4 py-2 text-right">Budget</th>
                            <th class="px-4 py-2 text-right">Reserviert</th>
                            <th class="px-4 py-2 text-right">Bewilligt</th>
                            <th class="px-4 py-2 text-right">Verfügbar</th>
                            <th class="px-4 py-2">Status</th>
                            <th class="px-4 py-2 text-right">Aktionen</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse($program->pots->sortByDesc('jahr') as $pot)
                            <tr wire:key="topf-{{ $pot->id }}">
                                <td class="px-4 py-2 text-gray-900">{{ $pot->name }}@if($pot->jahr) <span class="text-gray-500">({{ $pot->jahr }})</span>@endif</td>
                                <td class="px-4 py-2 text-gray-700">
                                    @if($pot->laufzeit === 'fortlaufend')
                                        Fortlaufend
                                    @else
                                        {{ $pot->beginn?->format('d.m.Y') }} – {{ $pot->ende?->format('d.m.Y') }}
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right">{{ number_format((float) $pot->budget, 2, ',', '.') }} €</td>
                                <td class="px-4 py-2 text-right">{{ number_format($pot->reserviert(), 2, ',', '.') }} €</td>
                                <td class="px-4 py-2 text-right">{{ number_format($pot->bewilligt(), 2, ',', '.') }} €</td>
                                <td class="px-4 py-2 text-right font-semibold">{{ number_format($pot->verfuegbar(), 2, ',', '.') }} €</td>
                                <td class="px-4 py-2">
                                    <span class="inline-block text-xs font-medium rounded-full px-2 py-0.5 {{ $pot->istOffen() ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">{{ $pot->statusLabel() }}</span>
                                </td>
                                <td class="px-4 py-2 text-right whitespace-nowrap space-x-3">
                                    <button wire:click="editPot({{ $pot->id }})" class="text-blue-700 hover:underline">Bearbeiten</button>
                                    <button wire:click="toggleGeschlossen({{ $pot->id }})" class="text-gray-700 hover:underline">{{ $pot->geschlossen ? 'Öffnen' : 'Schließen' }}</button>
                                    <button wire:click="deletePot({{ $pot->id }})" wire:confirm="Fördertopf wirklich löschen?" class="text-red-600 hover:underline">Löschen</button>
                                </td>
                            </tr>
                        @empty
                            <tr><td colspan="8" class="px-4 py-3 text-center text-gray-500">Noch keine Fördertöpfe.</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>
    @empty
        <p class="text-sm text-gray-500">Noch keine Förderprogramme angelegt.</p>
    @endforelse

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
        <form wire:submit.prevent="saveProgram" class="space-y-3 border border-gray-200 rounded-lg p-4">
            <h4 class="font-medium text-gray-900">{{ $programId ? 'Förderprogramm bearbeiten' : 'Neues Förderprogramm' }}</h4>
            <div>
                <x-label value="Name" />
                <x-input type="text" wire:model="programName" class="w-full" />
                <x-input-error for="programName" class="mt-1" />
            </div>
            <div>
                <x-label value="Beschreibung (optional)" />
                <textarea wire:model="programBeschreibung" rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
            </div>
            <label class="inline-flex items-center text-sm text-gray-700">
                <input type="checkbox" wire:model="programAktiv" class="h-5 w-5 rounded border-2 border-gray-600 text-blue-600 focus:ring-blue-500">
                <span class="ml-2">Programm ist aktiv</span>
            </label>
            <div class="flex items-center space-x-4">
                <x-button>{{ $programId ? 'Änderungen speichern' : 'Programm speichern' }}</x-button>
                @if($programId)
                    <button type="button" wire:click="cancelProgram" class="text-sm text-gray-700 underline">Abbrechen</button>
                @endif
            </div>
        </form>

        <form wire:submit.prevent="savePot" class="space-y-3 border border-gray-200 rounded-lg p-4">
            <h4 class="font-medium text-gray-900">{{ $potId ? 'Fördertopf bearbeiten' : 'Neuer Fördertopf' }}</h4>
            <div>
                <x-label value="Förderprogramm" />
                <select wire:model="potProgramId" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="">– bitte wählen –</option>
                    @foreach($programs as $program)
                        <option value="{{ $program->id }}">{{ $program->name }}</option>
                    @endforeach
                </select>
                <x-input-error for="potProgramId" class="mt-1" />
            </div>
            <div class="grid grid-cols-3 gap-3">
                <div class="col-span-2">
                    <x-label value="Name" />
                    <x-input type="text" wire:model="potName" class="w-full" />
                    <x-input-error for="potName" class="mt-1" />
                </div>
                <div>
                    <x-label value="Jahr" />
                    <x-input type="text" wire:model="potJahr" class="w-full" />
                    <x-input-error for="potJahr" class="mt-1" />
                </div>
            </div>
            <div>
                <x-label value="Laufzeit" />
                <select wire:model.live="potLaufzeit" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                    <option value="befristet">Befristete Antragsrunde</option>
                    <option value="fortlaufend">Fortlaufend geöffnet (bis das Budget erschöpft ist)</option>
                </select>
            </div>
            @if($potLaufzeit === 'befristet')
                <div class="grid grid-cols-2 gap-3">
                    <div>
                        <x-label value="Beginn" />
                        <x-input type="date" wire:model="potBeginn" class="w-full" />
                        <x-input-error for="potBeginn" class="mt-1" />
                    </div>
                    <div>
                        <x-label value="Frist (Ende)" />
                        <x-input type="date" wire:model="potEnde" class="w-full" />
                        <x-input-error for="potEnde" class="mt-1" />
                    </div>
                </div>
            @endif
            <div>
                <x-label value="Budget in Euro" />
                <x-input type="text" wire:model="potBudget" class="w-full" placeholder="z. B. 15.000,00" />
                <x-input-error for="potBudget" class="mt-1" />
            </div>
            <div>
                <x-label value="Beschreibung (optional)" />
                <textarea wire:model="potBeschreibung" rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
            </div>
            <div class="flex items-center space-x-4">
                <x-button>{{ $potId ? 'Änderungen speichern' : 'Fördertopf speichern' }}</x-button>
                @if($potId || $potProgramId)
                    <button type="button" wire:click="cancelPot" class="text-sm text-gray-700 underline">Abbrechen</button>
                @endif
            </div>
        </form>
    </div>
</div>
