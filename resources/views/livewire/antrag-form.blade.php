<div class="p-6 space-y-6">
    <div>
        <h3 class="text-xl font-semibold text-gray-900">{{ $applicationType->name }}</h3>
        @if($applicationType->beschreibung)
            <p class="mt-1 text-sm text-gray-600">{{ $applicationType->beschreibung }}</p>
        @endif
        @if($applicationType->frist)
            <p class="mt-2 text-sm font-medium {{ $deadlinePassed ? 'text-red-700' : 'text-gray-700' }}">
                Abgabefrist: {{ $applicationType->frist->format('d.m.Y') }}
                @if($deadlinePassed) – abgelaufen @endif
            </p>
        @endif
    </div>

    @error('submission')
        <div class="rounded border border-red-300 bg-red-50 p-3 text-sm font-medium text-red-800">{{ $message }}</div>
    @enderror

    <form wire:submit.prevent="saveDraft" class="space-y-6">
        @if($applicationType->requires_club)
            <div>
                <x-label value="Antragstellender Verein *" />
                <select wire:model="clubId" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">– bitte eigenen Verein wählen –</option>
                    @foreach($clubs as $club)
                        <option value="{{ $club->id }}">{{ $club->clubname }}</option>
                    @endforeach
                </select>
                <x-input-error for="clubId" class="mt-1" />
                @if($clubs->isEmpty())
                    <p class="mt-1 text-sm text-amber-800">Ihrem Benutzerkonto ist kein Verein zugeordnet. Bitte wenden Sie sich an die Vereinsverwaltung.</p>
                @endif
            </div>
        @endif

        @if($fundingPots->isNotEmpty() || $applicationType->requires_funding_pot)
            <div>
                <x-label value="Fördertopf{{ $applicationType->requires_funding_pot ? ' *' : '' }}" />
                <select wire:model="fundingPotId" class="w-full border-gray-300 rounded-md shadow-sm">
                    <option value="">– bitte offenen Fördertopf wählen –</option>
                    @foreach($fundingPots as $pot)
                        <option value="{{ $pot->id }}" @disabled(! $pot->istOffen())>
                            {{ $pot->program->name }} – {{ $pot->name }}{{ $pot->jahr ? ' (' . $pot->jahr . ')' : '' }}
                        </option>
                    @endforeach
                </select>
                @if($applicationType->requires_funding_pot && $fundingPots->filter(fn ($pot) => $pot->istOffen())->isEmpty())
                    <p class="mt-1 text-sm text-amber-800">Für diese Antragsart ist derzeit kein offener zugeordneter Fördertopf verfügbar.</p>
                @endif
                <x-input-error for="fundingPotId" class="mt-1" />
                <p class="mt-1 text-xs text-gray-600">Der Fördertopf ordnet den Antrag einem Programm/Budget zu. Eine Budgetreservierung erfolgt erst bei einer späteren Entscheidung.</p>
            </div>
        @endif

        @foreach($applicationType->fields as $field)
            <div wire:key="feld-{{ $field->id }}">
                @if($field->type === 'checkbox')
                    <label class="inline-flex items-start text-sm text-gray-800">
                        <input type="checkbox" wire:model="formData.{{ $field->key }}" class="mt-0.5 h-5 w-5 rounded border-gray-400 text-blue-700 focus:ring-blue-600">
                        <span class="ml-2">{{ $field->label }}@if($field->required) * @endif</span>
                    </label>
                @elseif($field->type === 'textarea')
                    <x-label value="{{ $field->label }}{{ $field->required ? ' *' : '' }}" />
                    <textarea wire:model="formData.{{ $field->key }}" rows="4" class="w-full border-gray-300 rounded-md shadow-sm"></textarea>
                @elseif($field->type === 'select')
                    <x-label value="{{ $field->label }}{{ $field->required ? ' *' : '' }}" />
                    <select wire:model="formData.{{ $field->key }}" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="">– bitte wählen –</option>
                        @foreach($field->options ?? [] as $option)
                            <option value="{{ $option }}">{{ $option }}</option>
                        @endforeach
                    </select>
                @elseif($field->type === 'file')
                    <x-label value="{{ $field->label }}{{ $field->required ? ' *' : '' }}" />
                    <input type="file" wire:model="uploads.{{ $field->key }}" class="block w-full text-sm text-gray-700">
                    <p class="mt-1 text-xs text-gray-600">PDF, JPG, PNG, DOC, DOCX, XLS oder XLSX; maximal 10 MB.</p>
                    <div wire:loading wire:target="uploads.{{ $field->key }}" class="mt-1 text-sm text-gray-600">Anlage wird hochgeladen …</div>
                    @foreach($documents->where('field_key', $field->key) as $document)
                        <div class="mt-2 flex items-center gap-3 text-sm">
                            <a href="{{ route('antraege.dokumente.download', $document->id) }}" class="text-blue-700 underline">{{ $document->original_name }}</a>
                            <button type="button" wire:click="removeDocument({{ $document->id }})" class="text-red-700 hover:underline">Entfernen</button>
                        </div>
                    @endforeach
                @else
                    <x-label value="{{ $field->label }}{{ $field->required ? ' *' : '' }}" />
                    <x-input
                        type="{{ in_array($field->type, ['number', 'date', 'email'], true) ? $field->type : 'text' }}"
                        wire:model="formData.{{ $field->key }}"
                        class="w-full"
                    />
                @endif

                @if($field->help_text)
                    <p class="mt-1 text-sm text-gray-600">{{ $field->help_text }}</p>
                @endif
                <x-input-error for="formData.{{ $field->key }}" class="mt-1" />
                <x-input-error for="uploads.{{ $field->key }}" class="mt-1" />
            </div>
        @endforeach

        <div>
            <x-label value="Bankkonto für eine mögliche Auszahlung (optional)" />
            <select wire:model="bankAccountId" class="w-full border-gray-300 rounded-md shadow-sm">
                <option value="">– kein Bankkonto auswählen –</option>
                @foreach($accounts as $account)
                    <option value="{{ $account->id }}">{{ $account->kontoinhaber }} · {{ $account->maskedIban() }}</option>
                @endforeach
            </select>
            <p class="mt-1 text-sm text-gray-600">Beim Einreichen wird der gewählte Bankdatenstand unveränderlich am Antrag gespeichert.</p>
            <x-input-error for="bankAccountId" class="mt-1" />
            @if($accounts->isEmpty())
                <a href="{{ route('antraege.bankkonten') }}" class="mt-1 inline-block text-sm text-blue-700 underline">Eigenes Bankkonto hinterlegen</a>
            @endif
        </div>

        <div class="flex flex-wrap items-center gap-4 border-t border-gray-200 pt-5">
            <x-button wire:loading.attr="disabled" wire:target="saveDraft,submitApplication,uploads">Entwurf speichern</x-button>
            <button
                type="button"
                wire:click="submitApplication"
                wire:loading.attr="disabled"
                wire:target="saveDraft,submitApplication,uploads"
                @disabled($deadlinePassed)
                class="inline-flex items-center rounded-md border border-transparent bg-green-700 px-4 py-2 text-xs font-semibold uppercase tracking-widest text-white hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-600 focus:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
            >Antrag verbindlich einreichen</button>
            <span wire:loading wire:target="saveDraft,submitApplication" class="text-sm text-gray-600">Wird gespeichert …</span>
        </div>
        <p class="text-xs text-gray-600">Nach dem Einreichen kann der Antrag nicht mehr geändert werden.</p>
    </form>
</div>
