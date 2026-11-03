<div class="space-y-5 p-6">
    @if (session()->has('message'))
        <div class="rounded border border-green-200 bg-green-50 p-3 text-sm text-green-800">{{ session('message') }}</div>
    @endif

    <div class="flex flex-wrap items-center justify-between gap-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">{{ $showArchived ? 'Archivierte Förderanträge' : 'Eingereichte Anträge' }}</h3>
            @unless($canReview)
                <p class="text-sm text-gray-600">Fördertopfverwalter sehen hier nur die zur Archivierung erforderlichen Angaben.</p>
            @endunless
        </div>
        <label class="inline-flex items-center gap-2 text-sm text-gray-700">
            <input type="checkbox" wire:model.live="showArchived" class="rounded border-gray-400 text-blue-700 focus:ring-blue-600">
            Archiv anzeigen
        </label>
    </div>

    <section class="space-y-4 rounded-xl border-2 border-blue-200 bg-blue-50 p-4 shadow-sm">
        <header class="border-b border-blue-200 pb-3">
            <h4 class="font-semibold text-blue-900">Anträge filtern</h4>
            <p class="mt-1 text-sm text-blue-700">Die Filter lassen sich miteinander kombinieren.</p>
        </header>
        <div class="grid grid-cols-1 gap-3 sm:grid-cols-2 xl:grid-cols-5">
        <label class="text-sm text-gray-700">
            Antragsteller
            <select wire:model.live="filterUserId" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                <option value="">Alle Antragstellenden</option>
                @foreach($filterUsers as $filterUser)
                    <option value="{{ $filterUser->id }}">{{ trim($filterUser->vorname . ' ' . $filterUser->nachname) ?: $filterUser->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="text-sm text-gray-700">
            Verein
            <select wire:model.live="filterClubId" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                <option value="">Alle Vereine</option>
                @foreach($filterClubs as $filterClub)
                    <option value="{{ $filterClub->id }}">{{ $filterClub->clubname }}</option>
                @endforeach
            </select>
        </label>
        <label class="text-sm text-gray-700">
            Fördertopf
            <select wire:model.live="filterFundingPotId" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                <option value="">Alle Fördertöpfe</option>
                @foreach($filterFundingPots as $filterFundingPot)
                    <option value="{{ $filterFundingPot->id }}">{{ $filterFundingPot->program->name }} — {{ $filterFundingPot->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="text-sm text-gray-700">
            Antragsart
            <select wire:model.live="filterApplicationTypeId" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                <option value="">Alle Antragsarten</option>
                @foreach($filterApplicationTypes as $filterApplicationType)
                    <option value="{{ $filterApplicationType->id }}">{{ $filterApplicationType->name }}</option>
                @endforeach
            </select>
        </label>
        <label class="text-sm text-gray-700">
            Status
            <select wire:model.live="filterStatus" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                <option value="">Alle Status</option>
                @foreach($filterStatuses as $statusValue => $statusLabel)
                    <option value="{{ $statusValue }}">{{ $statusLabel }}</option>
                @endforeach
            </select>
        </label>
        </div>
        <div class="flex justify-end border-t border-blue-200 pt-3">
            <button type="button" wire:click="resetFilters" class="inline-flex min-h-10 items-center justify-center rounded-md border border-blue-300 bg-white px-4 py-2 text-sm font-semibold text-blue-700 shadow-sm transition hover:bg-blue-100 hover:text-blue-900 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2">
                Filter zurücksetzen
            </button>
        </div>
    </section>

    @if($applications->isNotEmpty())
        <section class="overflow-hidden rounded-xl border-2 border-indigo-200 bg-white shadow-sm">
            <header class="bg-indigo-800 px-4 py-3 text-white">
                <h4 class="font-semibold">Anträge auswählen</h4>
                <p class="mt-1 text-sm text-indigo-100">Offene Anträge zur Prüfung stehen zuerst. Der ausgewählte Antrag ist farbig markiert und wird darunter angezeigt.</p>
            </header>
            <ul class="grid grid-cols-1 gap-2 p-3 md:grid-cols-2">
                @foreach($applications as $applicationOption)
                    @php($isSelected = $selectedApplicationId === $applicationOption->id)
                    @php($applicationOptionStatus = $statusPresentation[$applicationOption->status] ?? ['label' => $applicationOption->status, 'class' => 'bg-gray-100 text-gray-800'])
                    <li wire:key="pruef-auswahl-{{ $applicationOption->id }}">
                        <button
                            type="button"
                            wire:click="selectApplication({{ $applicationOption->id }})"
                            aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                            class="block w-full rounded-xl border-2 px-4 py-3 text-left transition-all duration-150 focus:outline-none focus:ring-2 focus:ring-indigo-600 focus:ring-offset-1 {{ $isSelected ? 'border-indigo-600 bg-indigo-100 shadow-md ring-2 ring-indigo-200' : 'border-gray-200 bg-gray-50 hover:border-indigo-400 hover:bg-indigo-50 hover:shadow-sm' }}"
                        >
                            <span class="flex flex-wrap items-center justify-between gap-2">
                                <span class="font-semibold text-gray-900">
                                    {{ trim($applicationOption->user->vorname . ' ' . $applicationOption->user->nachname) ?: $applicationOption->user->name }}
                                </span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ $applicationOptionStatus['class'] }}">{{ $applicationOptionStatus['label'] }}</span>
                            </span>
                            <span class="mt-1 block text-sm text-gray-700">
                                {{ $applicationOption->applicationType->name }}
                                @if($applicationOption->club) · {{ $applicationOption->club->clubname }} @endif
                                @if($applicationOption->fundingPot) · {{ $applicationOption->fundingPot->program->name }} — {{ $applicationOption->fundingPot->name }} @endif
                            </span>
                            @if($isSelected)
                                <span class="mt-2 inline-block text-xs font-bold uppercase tracking-wide text-indigo-900">Gerade ausgewählt</span>
                            @endif
                        </button>
                    </li>
                @endforeach
            </ul>
        </section>
    @else
        <p class="rounded-lg border border-gray-200 bg-white p-4 text-sm text-gray-600">Keine Anträge entsprechen den gewählten Filtern.</p>
    @endif

    @if($selectedApplication)
        @php($application = $selectedApplication)
        @php($applicationStatusLabel = match($application->status) {
            \App\Models\Application::STATUS_SUBMITTED => 'Eingereicht',
            \App\Models\Application::STATUS_IN_REVIEW => 'In Prüfung',
            \App\Models\Application::STATUS_QUESTION => 'Rückfrage',
            \App\Models\Application::STATUS_APPROVED => 'Bewilligt',
            \App\Models\Application::STATUS_REJECTED => 'Abgelehnt',
            default => $application->status,
        })
        @php($applicationStatus = $statusPresentation[$application->status] ?? ['label' => $applicationStatusLabel, 'class' => 'bg-gray-100 text-gray-800'])
        <article id="pruef-antrag-{{ $application->id }}" wire:key="pruef-antrag-{{ $application->id }}" class="overflow-hidden rounded-xl border-4 border-green-600 bg-white shadow-2xl ring-4 ring-green-100">
            <header class="flex flex-wrap items-start justify-between gap-3 bg-green-800 px-5 py-4 text-white">
                <div class="min-w-0">
                    <p class="mb-1 text-xs font-bold uppercase tracking-widest text-green-100">Aktuell zur Bearbeitung ausgewählt</p>
                </div>
                @unless($canReview)
                    <h4 class="text-xl font-bold text-white">
                        Antrag von {{ trim($application->user->vorname . ' ' . $application->user->nachname) ?: $application->user->name }}
                    </h4>
                <dl class="grid w-full grid-cols-1 gap-x-6 gap-y-3 border-t border-green-600 pt-3 text-sm sm:grid-cols-2 lg:grid-cols-4">
                    <div>
                        <dt class="text-xs font-medium text-green-200">Status</dt>
                        <dd class="mt-1">
                            <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-bold {{ $applicationStatus['class'] }}">{{ $applicationStatus['label'] }}</span>
                        </dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-green-200">Antragsart</dt>
                        <dd class="mt-1 font-semibold text-white">{{ $application->applicationType->name }}</dd>
                    </div>
                    @if($application->club)
                        <div>
                            <dt class="text-xs font-medium text-green-200">Verein</dt>
                            <dd class="mt-1 font-semibold text-white">{{ $application->club->clubname }}</dd>
                        </div>
                    @endif
                    @if($application->fundingPot)
                        <div>
                            <dt class="text-xs font-medium text-green-200">Fördertopf</dt>
                            <dd class="mt-1 font-semibold text-white">{{ $application->fundingPot->program->name }} — {{ $application->fundingPot->name }}</dd>
                        </div>
                    @endif
                    <div>
                        <dt class="text-xs font-medium text-green-200">Eingereicht</dt>
                        <dd class="mt-1 font-semibold text-white">{{ $application->submitted_at?->format('d.m.Y H:i') }}</dd>
                    </div>
                </dl>
                @endunless
                @if($application->trashed())
                    <div class="w-full border-t border-green-600 pt-2 text-sm text-green-50">
                        Archiviert {{ $application->deleted_at?->format('d.m.Y H:i') }} von {{ $application->deleter?->name ?? 'unbekannt' }}.
                        <p class="mt-1">{{ $application->deletion_reason }}</p>
                    </div>
                @endif
            </header>

            <div class="space-y-4 p-4">
                @if($canReview)
                <section class="overflow-hidden rounded-lg border-2 border-blue-200 bg-blue-50">
                    <header class="border-b border-blue-200 bg-blue-100 px-4 py-3">
                        <p class="text-xs font-bold uppercase tracking-wider text-blue-700">Antragsformular</p>
                        <h5 class="mt-1 text-lg font-bold text-blue-900">Formulardaten</h5>
                        <dl class="mt-3 grid grid-cols-1 gap-3 sm:grid-cols-2 lg:grid-cols-3">
                            <div class="rounded-md border border-blue-200 bg-white px-3 py-2">
                                <dt class="text-xs font-semibold uppercase text-gray-500">Status</dt>
                                <dd class="mt-1">
                                    <span class="inline-flex rounded-full px-2 py-0.5 text-xs font-bold {{ $applicationStatus['class'] }}">{{ $applicationStatus['label'] }}</span>
                                </dd>
                            </div>
                            <div class="rounded-md border border-blue-200 bg-white px-3 py-2">
                                <dt class="text-xs font-semibold uppercase text-gray-500">Antragsformular</dt>
                                <dd class="mt-1 break-words text-sm font-medium text-gray-900">{{ $application->applicationType->name }}</dd>
                            </div>
                            <div class="rounded-md border border-blue-200 bg-white px-3 py-2">
                                <dt class="text-xs font-semibold uppercase text-gray-500">Antragsteller</dt>
                                <dd class="mt-1 break-words text-sm font-medium text-gray-900">{{ trim($application->user->vorname . ' ' . $application->user->nachname) ?: $application->user->name }}</dd>
                            </div>
                            @if($application->fundingPot)
                                <div class="rounded-md border border-blue-200 bg-white px-3 py-2">
                                    <dt class="text-xs font-semibold uppercase text-gray-500">Fördertopf</dt>
                                    <dd class="mt-1 break-words text-sm font-medium text-gray-900">{{ $application->fundingPot->program->name }} — {{ $application->fundingPot->name }}</dd>
                                </div>
                            @endif
                            @if($application->club)
                                <div class="rounded-md border border-blue-200 bg-white px-3 py-2">
                                    <dt class="text-xs font-semibold uppercase text-gray-500">Verein</dt>
                                    <dd class="mt-1 break-words text-sm font-medium text-gray-900">{{ $application->club->clubname }}</dd>
                                </div>
                            @endif
                        </dl>
                    </header>
                    <dl class="grid grid-cols-1 gap-3 p-4 pt-2 sm:grid-cols-2">
                        @foreach($application->form_snapshot ?? [] as $field)
                            @php($value = $application->answers[$field['key']] ?? null)
                            @if($value !== null && $value !== '')
                                @php($displayValue = ($field['type'] ?? null) === 'date' ? \Illuminate\Support\Carbon::parse($value)->format('d.m.Y') : $value)
                                <div class="rounded-md border border-blue-100 bg-white px-3 py-2">
                                    <dt class="text-xs font-semibold uppercase text-gray-500">{{ $field['label'] ?? $field['key'] }}</dt>
                                    <dd class="mt-1 whitespace-pre-wrap break-words text-sm text-gray-900">{{ is_bool($displayValue) ? ($displayValue ? 'Ja' : 'Nein') : (is_array($displayValue) ? implode(', ', $displayValue) : $displayValue) }}</dd>
                                </div>
                            @endif
                        @endforeach
                    </dl>
                </section>
                @if($application->documents->isNotEmpty())
                    <div class="rounded-md border border-gray-200 p-3">
                        <p class="text-sm font-semibold text-gray-800">Anlagen</p>
                        <ul class="mt-1 list-inside list-disc text-sm">
                            @foreach($application->documents as $document)
                                <li><a class="text-blue-700 underline" href="{{ route('antraege.dokumente.download', $document) }}">{{ $document->original_name }}</a></li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                @if($application->review_note)
                    <p class="mt-3 text-sm text-gray-700"><span class="font-semibold">Prüfnotiz:</span> {{ $application->review_note }}</p>
                @endif
                @if($application->decision_reason)
                    @php($deciderName = $application->decider ? (trim($application->decider->vorname . ' ' . $application->decider->nachname) ?: $application->decider->name) : 'unbekannt')
                    @php($deciderRoles = $application->decider?->roles->map(fn ($role) => $role->label ?: $role->name)->implode(', '))
                    <p class="mt-3 text-sm text-gray-700"><span class="font-semibold">Entscheidungsbegründung:</span> {{ $application->decision_reason }} ({{ $deciderName }}@if($deciderRoles), {{ $deciderRoles }}@endif, {{ $application->decided_at?->format('d.m.Y H:i') }})</p>
                @endif
                @if($application->events->isNotEmpty())
                    <div class="border-t border-gray-200 pt-3">
                        <h5 class="text-sm font-semibold text-gray-800">Verlauf und Rückfragen</h5>
                        <ol class="mt-2 space-y-2">
                            @foreach($application->events as $event)
                                <li class="rounded-md bg-gray-50 p-3">
                                    <p class="text-xs font-semibold text-gray-700">
                                        @php($eventActorName = $event->actor ? (trim($event->actor->vorname . ' ' . $event->actor->nachname) ?: $event->actor->name) : ($event->actor_name ?: 'unbekannt'))
                                        @php($eventActorRoles = $event->actor?->roles->map(fn ($role) => $role->label ?: $role->name)->implode(', '))
                                        {{ match($event->event_type) {
                                            \App\Models\Application::EVENT_SUBMITTED => 'Eingereicht',
                                            \App\Models\Application::EVENT_REVIEW_STARTED => 'Prüfung aufgenommen',
                                            \App\Models\Application::EVENT_QUESTION => 'Rückfrage gestellt',
                                            \App\Models\Application::EVENT_ANSWER => 'Antwort eingegangen',
                                            \App\Models\Application::EVENT_APPROVED => 'Bewilligt',
                                            \App\Models\Application::EVENT_REJECTED => 'Abgelehnt',
                                            \App\Models\Application::EVENT_ARCHIVED => 'Archiviert',
                                            default => $event->event_type,
                                        } }}
                                        · {{ $eventActorName }}@if($eventActorRoles), {{ $eventActorRoles }}@endif · {{ $event->created_at->format('d.m.Y H:i') }}
                                    </p>
                                    <p class="mt-1 whitespace-pre-wrap text-sm text-gray-800">{{ $event->message }}</p>
                                </li>
                            @endforeach
                        </ol>
                    </div>
                @endif
            @endif

            @if(! $application->trashed())
                <div class="flex flex-col gap-3 border-t border-gray-200 pt-4">
                    @if($canReview && $application->status === \App\Models\Application::STATUS_SUBMITTED)
                        <div class="flex flex-wrap items-end gap-3">
                            <label class="min-w-64 flex-1 text-sm text-gray-700">
                                Prüfnotiz (optional)
                                <textarea wire:model="reviewNotes.{{ $application->id }}" rows="2" class="mt-1 w-full rounded-md border-gray-300 text-sm"></textarea>
                            </label>
                            <button wire:click="markForReview({{ $application->id }})" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">In Prüfung nehmen</button>
                        </div>
                    @endif

                    @if($canReview && in_array($application->status, [\App\Models\Application::STATUS_SUBMITTED, \App\Models\Application::STATUS_IN_REVIEW], true))
                        <label class="text-sm text-gray-700">
                            Rückfrage an Antragstellende
                            <textarea wire:model="questions.{{ $application->id }}" rows="2" class="mt-1 w-full rounded-md border-gray-300 text-sm"></textarea>
                            @error('questions.' . $application->id)<span class="text-red-700">{{ $message }}</span>@enderror
                        </label>
                        <button wire:click="requestInformation({{ $application->id }})" class="self-start rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">Rückfrage senden</button>
                    @endif

                    @if($canDecide && in_array($application->status, [\App\Models\Application::STATUS_SUBMITTED, \App\Models\Application::STATUS_IN_REVIEW], true))
                        @if($application->funding_pot_id || array_key_exists('beantragter_zuschuss', $application->answers ?? []))
                            <label class="text-sm text-gray-700">
                                Tatsächlich bewilligter Betrag
                                <select wire:model.live="decisionAmountModes.{{ $application->id }}" class="mt-1 w-full rounded-md border-gray-300 text-sm">
                                    <option value="amount">Betrag in €</option>
                                    @if(is_numeric($application->answers['beantragter_zuschuss'] ?? null) && (float) $application->answers['beantragter_zuschuss'] > 0)
                                        <option value="percent">Prozent vom beantragten Betrag</option>
                                    @endif
                                </select>
                                <input
                                    type="number"
                                    min="0.01"
                                    max="{{ ($decisionAmountModes[$application->id] ?? 'amount') === 'percent' ? '100' : '9999999999.99' }}"
                                    step="0.01"
                                    wire:model.live="decisionAmounts.{{ $application->id }}"
                                    class="mt-1 w-full rounded-md border-gray-300 text-sm"
                                >
                                <span class="mt-1 block text-xs text-gray-600">
                                    {{ ($decisionAmountModes[$application->id] ?? 'amount') === 'percent' ? 'Eingabe in Prozent' : 'Eingabe in Euro' }}
                                </span>
                                @if(is_numeric($application->answers['beantragter_zuschuss'] ?? null))
                                    <span class="mt-1 block text-xs text-gray-600">
                                        Beantragt: {{ number_format((float) $application->answers['beantragter_zuschuss'], 2, ',', '.') }} €
                                        @if(($decisionAmountModes[$application->id] ?? 'amount') === 'percent')
                                            · Prozentangabe wird auf diesen Betrag angewendet; Rundung auf Cent.
                                        @endif
                                    </span>
                                @endif
                                @if(
                                    ($decisionAmountModes[$application->id] ?? 'amount') === 'percent'
                                    && is_numeric($decisionAmounts[$application->id] ?? null)
                                    && is_numeric($application->answers['beantragter_zuschuss'] ?? null)
                                )
                                    <span class="mt-1 block font-semibold text-gray-800">
                                        Bewilligter Betrag: {{ number_format(round((float) $application->answers['beantragter_zuschuss'] * (float) $decisionAmounts[$application->id] / 100, 2), 2, ',', '.') }} €
                                    </span>
                                @elseif(
                                    ($decisionAmountModes[$application->id] ?? 'amount') === 'amount'
                                    && is_numeric($decisionAmounts[$application->id] ?? null)
                                    && is_numeric($application->answers['beantragter_zuschuss'] ?? null)
                                    && (float) $application->answers['beantragter_zuschuss'] > 0
                                )
                                    <span class="mt-1 block font-semibold text-gray-800">
                                        Entspricht {{ number_format((float) $decisionAmounts[$application->id] / (float) $application->answers['beantragter_zuschuss'] * 100, 2, ',', '.') }} % des beantragten Betrags
                                    </span>
                                @endif
                                @error('decisionAmounts.' . $application->id)<span class="text-red-700">{{ $message }}</span>@enderror
                                @error('decisionAmountModes.' . $application->id)<span class="text-red-700">{{ $message }}</span>@enderror
                            </label>
                        @endif
                        <label class="text-sm text-gray-700">
                            Begründung für die Entscheidung (Pflichtfeld)
                            <textarea wire:model="decisionReasons.{{ $application->id }}" rows="2" class="mt-1 w-full rounded-md border-gray-300 text-sm"></textarea>
                            @error('decisionReasons.' . $application->id)<span class="text-red-700">{{ $message }}</span>@enderror
                        </label>
                        <div class="flex flex-wrap gap-2">
                            <button wire:click="decide({{ $application->id }}, 'bewilligt')" class="rounded-md bg-green-700 px-4 py-2 text-sm font-semibold text-white hover:bg-green-800">Bewilligen</button>
                            <button wire:click="decide({{ $application->id }}, 'abgelehnt')" class="rounded-md bg-red-700 px-4 py-2 text-sm font-semibold text-white hover:bg-red-800">Ablehnen</button>
                        </div>
                    @endif

                    @if($canArchive && $application->funding_pot_id && $application->status !== \App\Models\Application::STATUS_APPROVED)
                        <label class="text-sm text-gray-700">
                            Archivierungsgrund (Pflichtfeld)
                            <textarea wire:model="deletionReasons.{{ $application->id }}" rows="2" class="mt-1 w-full rounded-md border-gray-300 text-sm"></textarea>
                            @error('deletionReasons.' . $application->id)<span class="text-red-700">{{ $message }}</span>@enderror
                        </label>
                        <button wire:click="archive({{ $application->id }})" wire:confirm="Diesen Förderantrag archivieren? Die Einreichung bleibt nachvollziehbar erhalten." class="self-start rounded-md border border-red-700 px-4 py-2 text-sm font-semibold text-red-800 hover:bg-red-50">Förderantrag archivieren</button>
                    @endif
                </div>
            @endif
            </div>
        </article>
    @else
        <p class="rounded-lg border border-gray-200 bg-gray-50 p-5 text-sm text-gray-600">
            {{ $showArchived ? 'Keine archivierten Anträge vorhanden.' : 'Keine eingereichten Anträge vorhanden.' }}
        </p>
    @endif
</div>
