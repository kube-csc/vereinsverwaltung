<div class="space-y-8 bg-white p-6">
    @if (session()->has('message'))
        <div class="text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded p-3">{{ session('message') }}</div>
    @endif
    @if (session()->has('locked'))
        <div class="text-sm font-medium text-amber-700 bg-amber-50 border border-amber-200 rounded p-3">{{ session('locked') }}</div>
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
                @php($lockReason = $lockReasons[$applicationType->id] ?? null)
                <article wire:key="antragsart-{{ $applicationType->id }}" class="space-y-3 rounded-xl border p-4 shadow-sm {{ $lockReason ? 'border-gray-300 bg-gray-100' : 'border-indigo-200 bg-indigo-50' }}">
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
                    @if($lockReason)
                        <div class="rounded-lg bg-amber-50 border border-amber-200 p-3 text-sm text-amber-800">
                            <svg class="inline-block w-4 h-4 mr-1 -mt-0.5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
                            {{ $lockReason }}
                        </div>
                    @endif
                    <div class="flex gap-3">
                        @if(!$lockReason)
                            <button type="button" wire:click="editType({{ $applicationType->id }})" class="rounded-md bg-blue-700 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-800">Bearbeiten</button>
                        @endif
                        @if($applicationType->aktiv)
                            <button type="button" wire:click="toggleType({{ $applicationType->id }})" wire:confirm="Möchten Sie diese Antragsart wirklich deaktivieren? Neue Anträge dieser Art können dann nicht mehr eingereicht werden." class="rounded-md bg-gray-600 px-3 py-2 text-sm font-semibold text-white hover:bg-gray-700">Deaktivieren</button>
                        @else
                            <button type="button" wire:click="toggleType({{ $applicationType->id }})" class="rounded-md bg-green-700 px-3 py-2 text-sm font-semibold text-white hover:bg-green-800">Aktivieren</button>
                        @endif
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
                <x-label value="Hilfstext für Prüfer (optional)" />
                <textarea wire:model="reviewerNote" rows="2" class="w-full border-gray-300 rounded-md shadow-sm text-sm"></textarea>
                <p class="mt-1 text-xs text-gray-500">Dieser Text erscheint als Hinweis bei der Prüfung und im Bescheid (PDF und E-Mail).</p>
                <x-input-error for="reviewerNote" class="mt-1" />
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

        <section class="space-y-6">
            <div class="rounded-lg bg-blue-100 px-4 py-3">
                <h4 class="font-semibold text-blue-900">Formularfelder</h4>
            </div>

            @if(count($fields) > 0)
                @php($hasApplicantFields = false)
                @php($hasReviewerFields = false)
                @foreach($fields as $index => $field)
                    @if(!($field['for_reviewer'] ?? false))
                        @php($hasApplicantFields = true)
                    @else
                        @php($hasReviewerFields = true)
                    @endif
                @endforeach

                @if($hasReviewerFields)
                <div class="space-y-3">
                    <h5 class="text-sm font-semibold text-amber-700">Prüfer-Felder (erscheinen nur bei der Prüfung und im Bescheid)</h5>
                    @foreach($fields as $index => $field)
                        @continue(!($field['for_reviewer'] ?? false))
                        <div wire:key="formularfeld-{{ $field['id'] ?? 'neu-' . $index }}" class="rounded-lg border-2 border-amber-200 p-4 space-y-3 {{ empty($field['aktiv']) ? 'bg-gray-100' : 'bg-amber-50' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="flex flex-col">
                                        <button type="button" wire:click="moveField({{ $index }}, 'up')" title="Nach oben" class="text-gray-500 hover:text-gray-800 text-xs leading-none">▲</button>
                                        <button type="button" wire:click="moveField({{ $index }}, 'down')" title="Nach unten" class="text-gray-500 hover:text-gray-800 text-xs leading-none">▼</button>
                                    </div>
                                    <h6 class="font-medium text-gray-900">Prüfer-Feld {{ $index + 1 }}{{ empty($field['aktiv']) ? ' (deaktiviert)' : '' }}</h6>
                                </div>
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
                                    <x-label value="Schlüssel (z. B. bootsplatz)" />
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
                                <div class="space-y-2">
                                    <label class="inline-flex items-center text-sm text-gray-700">
                                        <input type="checkbox" wire:model="fields.{{ $index }}.required" class="h-5 w-5 rounded border-2 border-gray-600 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2">Pflichtfeld</span>
                                    </label>
                                    <label class="inline-flex items-center text-sm text-gray-700">
                                        <input type="checkbox" wire:model="fields.{{ $index }}.for_reviewer" class="h-5 w-5 rounded border-2 border-gray-600 text-amber-600 focus:ring-amber-500">
                                        <span class="ml-2">Prüfer-Feld</span>
                                    </label>
                                </div>
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
                    <button type="button" wire:click="addField(true)" class="rounded-md bg-amber-700 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-800">Prüfer-Feld hinzufügen</button>
                </div>
                @endif

                @if($hasApplicantFields)
                <div class="space-y-3">
                    <h5 class="text-sm font-semibold text-gray-700">Antragsformular (erscheint im Online-Formular)</h5>
                    @foreach($fields as $index => $field)
                        @continue($field['for_reviewer'] ?? false)
                        <div wire:key="formularfeld-{{ $field['id'] ?? 'neu-' . $index }}" class="rounded-lg border border-blue-200 p-4 space-y-3 {{ empty($field['aktiv']) ? 'bg-gray-100' : 'bg-white' }}">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center gap-2">
                                    <div class="flex flex-col">
                                        <button type="button" wire:click="moveField({{ $index }}, 'up')" title="Nach oben" class="text-gray-500 hover:text-gray-800 text-xs leading-none">▲</button>
                                        <button type="button" wire:click="moveField({{ $index }}, 'down')" title="Nach unten" class="text-gray-500 hover:text-gray-800 text-xs leading-none">▼</button>
                                    </div>
                                    <h6 class="font-medium text-gray-900">Feld {{ $index + 1 }}{{ empty($field['aktiv']) ? ' (deaktiviert)' : '' }}</h6>
                                </div>
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
                                <div class="space-y-2">
                                    <label class="inline-flex items-center text-sm text-gray-700">
                                        <input type="checkbox" wire:model="fields.{{ $index }}.required" class="h-5 w-5 rounded border-2 border-gray-600 text-blue-600 focus:ring-blue-500">
                                        <span class="ml-2">Pflichtfeld</span>
                                    </label>
                                    <label class="inline-flex items-center text-sm text-gray-700">
                                        <input type="checkbox" wire:model="fields.{{ $index }}.for_reviewer" class="h-5 w-5 rounded border-2 border-gray-600 text-amber-600 focus:ring-amber-500">
                                        <span class="ml-2">Prüfer-Feld</span>
                                    </label>
                                </div>
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
                    <button type="button" wire:click="addField" class="rounded-md bg-blue-700 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-800">Feld hinzufügen</button>
                </div>
                @endif
            @else
                <p class="text-sm text-gray-600">Noch keine Felder angelegt.</p>
                <button type="button" wire:click="addField" class="rounded-md bg-blue-700 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-800">Feld hinzufügen</button>
            @endif

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
