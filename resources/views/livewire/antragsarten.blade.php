<div class="space-y-8 bg-white p-6">
    @if (session()->has('message'))
        <div class="text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded p-3">{{ session('message') }}</div>
    @endif

    <section class="space-y-4">
        <div class="flex flex-wrap items-center justify-between gap-3 rounded-xl bg-gray-300 px-5 py-4 text-gray-900 shadow-sm">
            <div>
                <h3 class="text-lg font-bold">Vorhandene Antragsarten</h3>
                <p class="text-sm text-gray-800">Deaktivieren Sie nicht mehr verwendete Formulare, statt sie zu löschen.</p>
            </div>
            <x-button wire:click="newType">Antragsart anlegen</x-button>
        </div>

        <div class="grid gap-3 rounded-lg border border-gray-200 bg-gray-50 p-4 sm:grid-cols-2 lg:grid-cols-4">
            <label class="text-sm font-medium text-gray-700">Suche
                <input type="search" wire:model.live.debounce.300ms="search" placeholder="Name oder Beschreibung" class="mt-1 w-full rounded-md border-gray-300 text-sm">
            </label>
            <label class="text-sm font-medium text-gray-700">Status
                <select wire:model.live="filterStatus" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                    <option value="">Alle</option>
                    <option value="aktiv">Aktiv</option>
                    <option value="inaktiv">Inaktiv</option>
                </select>
            </label>
            <label class="text-sm font-medium text-gray-700">Vereinsauswahl
                <select wire:model.live="filterClub" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                    <option value="">Alle</option>
                    <option value="ja">Erforderlich</option>
                    <option value="nein">Nicht erforderlich</option>
                </select>
            </label>
            <label class="text-sm font-medium text-gray-700">Fördertopf
                <select wire:model.live="filterPot" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                    <option value="">Alle</option>
                    <option value="ja">Erforderlich</option>
                    <option value="nein">Optional</option>
                </select>
            </label>
            @if($filtersActive)
                <div class="sm:col-span-2 lg:col-span-4">
                    <button type="button" wire:click="resetFilters" class="rounded-md border border-gray-300 bg-white px-4 py-2 text-sm font-semibold text-gray-700 hover:bg-gray-50">Filter zurücksetzen</button>
                </div>
            @endif
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($applicationTypes as $applicationType)
                <article wire:key="antragsart-{{ $applicationType->id }}" class="space-y-3 rounded-xl border border-indigo-200 bg-indigo-50 p-4 shadow-sm">
                    <div class="flex items-start justify-between gap-3">
                        <div>
                            <h4 class="font-semibold text-gray-900">{{ $applicationType->name }}</h4>
                            <p class="text-sm text-gray-600">{{ $applicationType->beschreibung ?: 'Keine Beschreibung hinterlegt.' }}</p>
                        </div>
                        <span class="shrink-0 rounded-full px-2 py-1 text-xs font-semibold {{ $applicationType->aktiv ? 'bg-green-100 text-green-800' : 'bg-gray-200 text-gray-700' }}">
                            {{ $applicationType->aktiv ? 'Aktiv' : 'Inaktiv' }}
                        </span>
                    </div>
                    <p class="text-sm text-gray-700">
                        Frist: {{ $applicationType->frist?->format('d.m.Y') ?? 'Keine Frist festgelegt' }}
                        <span class="mx-1">·</span>
                        {{ $applicationType->fields->where('aktiv', true)->count() }} aktive Formularfelder
                        <span class="mx-1">·</span>
                        {{ $applicationType->requires_club ? 'Vereinsauswahl erforderlich' : 'Keine Vereinsauswahl' }}
                        <span class="mx-1">·</span>
                        {{ $applicationType->requires_funding_pot ? 'Fördertopf erforderlich' : 'Fördertopf optional' }}
                    </p>
                    <div class="flex gap-4 text-sm">
                        <button type="button" wire:click="editType({{ $applicationType->id }})" class="font-medium text-blue-700 hover:underline">Bearbeiten</button>
                        <button type="button" wire:click="toggleType({{ $applicationType->id }})" class="font-medium text-gray-700 hover:underline">
                            {{ $applicationType->aktiv ? 'Deaktivieren' : 'Aktivieren' }}
                        </button>
                    </div>
                </article>
            @empty
                @if($filtersActive)
                    <p class="text-sm text-gray-600">Keine Antragsarten für diese Filter.</p>
                @else
                    <p class="text-sm text-gray-600">Noch keine Antragsarten angelegt. Sie können Beispiele über `php artisan db:seed --class=ApplicationExamplesSeeder` einfügen.</p>
                @endif
            @endforelse
        </div>
    </section>

    <form wire:submit.prevent="saveType" class="space-y-6 rounded-xl border-2 border-blue-200 bg-blue-50 p-5">
        <div class="border-b border-blue-200 pb-3">
            <p class="text-xs font-bold uppercase tracking-wider text-blue-700">Formularbaukasten</p>
            <h3 class="mt-1 text-lg font-bold text-blue-900">{{ $typeId ? 'Antragsart bearbeiten' : 'Neue Antragsart' }}</h3>
            <p class="text-sm text-blue-800">Felder können als Pflichtangabe, Auswahl oder Anlage definiert und später deaktiviert werden.</p>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div>
                <x-label value="Name der Antragsart" />
                <x-input type="text" wire:model="name" class="w-full" />
                <x-input-error for="name" class="mt-1" />
            </div>
            <div>
                <x-label value="Abgabefrist (optional)" />
                <x-input type="date" wire:model="frist" class="w-full" />
                <x-input-error for="frist" class="mt-1" />
            </div>
            <div class="md:col-span-2">
                <x-label value="Beschreibung (optional)" />
                <textarea wire:model="beschreibung" rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
                <x-input-error for="beschreibung" class="mt-1" />
            </div>
            <div class="md:col-span-2">
                <label class="inline-flex items-start text-sm text-gray-700">
                    <input type="checkbox" wire:model="requiresClub" class="mt-0.5 h-5 w-5 rounded border-2 border-gray-600 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2">
                        <span class="block font-medium">Bei dieser Antragsart einen Verein verlangen</span>
                        <span class="block text-gray-600">Bei der Antragstellung wird dann ein Verein anhand von <code>clubs.clubname</code> ausgewählt und dem Antrag zugeordnet.</span>
                    </span>
                </label>
                <x-input-error for="requiresClub" class="mt-1" />
            </div>
            <div class="md:col-span-2">
                <label class="inline-flex items-start text-sm text-gray-700">
                    <input type="checkbox" wire:model="requiresFundingPot" class="mt-0.5 h-5 w-5 rounded border-2 border-gray-600 text-blue-600 focus:ring-blue-500">
                    <span class="ml-2">
                        <span class="block font-medium">Bei dieser Antragsart einen Fördertopf verlangen</span>
                        <span class="block text-gray-600">Ordnen Sie passende Fördertöpfe in der Verwaltung „Förderprogramme und Fördertöpfe“ zu. Antragstellende können dann nur einen zugeordneten, aktuell offenen Topf auswählen.</span>
                    </span>
                </label>
                <x-input-error for="requiresFundingPot" class="mt-1" />
            </div>
        </div>

        <label class="inline-flex items-center text-sm text-gray-700">
            <input type="checkbox" wire:model="aktiv" class="h-5 w-5 rounded border-2 border-gray-600 text-blue-600 focus:ring-blue-500">
            <span class="ml-2">Antragsart ist aktiv</span>
        </label>

        <section class="space-y-4">
            <div class="flex items-center justify-between gap-3 rounded-lg bg-blue-100 px-4 py-3">
                <h4 class="font-semibold text-blue-900">Formularfelder</h4>
                <button type="button" wire:click="addField" class="rounded-md bg-blue-700 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-800">Feld hinzufügen</button>
            </div>

            @foreach($fields as $index => $field)
                <div wire:key="formularfeld-{{ $field['id'] ?? 'neu-' . $index }}" class="rounded-lg border border-blue-200 p-4 space-y-3 {{ empty($field['aktiv']) ? 'bg-gray-100' : 'bg-white' }}">
                    <div class="flex items-center justify-between">
                        <h5 class="font-medium text-gray-900">Feld {{ $index + 1 }}{{ empty($field['aktiv']) ? ' (deaktiviert)' : '' }}</h5>
                        @if(empty($field['aktiv']))
                            <button type="button" wire:click="restoreField({{ $index }})" class="text-sm text-green-700 hover:underline">Reaktivieren</button>
                        @else
                            <button type="button" wire:click="removeField({{ $index }})" class="text-sm text-red-700 hover:underline">{{ empty($field['id']) ? 'Entfernen' : 'Deaktivieren' }}</button>
                        @endif
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-3">
                        <div>
                            <x-label value="Bezeichnung" />
                            <x-input type="text" wire:model="fields.{{ $index }}.label" class="w-full" />
                            <x-input-error for="fields.{{ $index }}.label" class="mt-1" />
                        </div>
                        <div>
                            <x-label value="Schlüssel (z. B. projekt_titel)" />
                            <x-input type="text" wire:model="fields.{{ $index }}.key" class="w-full" />
                            <x-input-error for="fields.{{ $index }}.key" class="mt-1" />
                        </div>
                        <div>
                            <x-label value="Feldtyp" />
                            <select wire:model="fields.{{ $index }}.type" class="w-full border-gray-300 rounded-md shadow-sm text-sm">
                                <option value="text">Text</option>
                                <option value="textarea">Mehrzeiliger Text</option>
                                <option value="number">Zahl / Betrag</option>
                                <option value="date">Datum</option>
                                <option value="email">E-Mail</option>
                                <option value="select">Auswahlliste</option>
                                <option value="checkbox">Bestätigung</option>
                                <option value="file">Dateianlage</option>
                            </select>
                            <x-input-error for="fields.{{ $index }}.type" class="mt-1" />
                        </div>
                        <label class="inline-flex items-center self-end text-sm text-gray-700 pb-2">
                            <input type="checkbox" wire:model="fields.{{ $index }}.required" class="h-5 w-5 rounded border-2 border-gray-600 text-blue-600 focus:ring-blue-500">
                            <span class="ml-2">Pflichtfeld</span>
                        </label>
                    </div>

                    @if($field['type'] === 'select')
                        <div>
                            <x-label value="Auswahlmöglichkeiten (je eine pro Zeile)" />
                            <textarea wire:model="fields.{{ $index }}.options_text" rows="3" class="w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
                            <x-input-error for="fields.{{ $index }}.options_text" class="mt-1" />
                        </div>
                    @endif
                    <div>
                        <x-label value="Hinweis zum Feld (optional)" />
                        <textarea wire:model="fields.{{ $index }}.help_text" rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
                        <x-input-error for="fields.{{ $index }}.help_text" class="mt-1" />
                    </div>
                </div>
            @endforeach
            <x-input-error for="fields" class="mt-1" />
        </section>

        <div class="flex items-center gap-4 border-t border-blue-200 pt-4">
            <x-button>{{ $typeId ? 'Änderungen speichern' : 'Antragsart speichern' }}</x-button>
            @if($typeId || $name !== '' || count($fields))
                <button type="button" wire:click="cancel" class="text-sm text-gray-700 underline">Abbrechen</button>
            @endif
        </div>
    </form>
</div>
