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

    @if($applications->isNotEmpty())
        <section class="overflow-hidden rounded-lg border border-indigo-200 bg-white">
            <header class="bg-indigo-50 px-4 py-3">
                <h4 class="font-semibold text-gray-900">Anträge auswählen</h4>
                <p class="mt-1 text-sm text-gray-700">Offene Anträge zur Prüfung stehen zuerst. Der ausgewählte Antrag ist farbig markiert und wird darunter angezeigt.</p>
            </header>
            <ul class="grid grid-cols-1 gap-2 p-3 md:grid-cols-2">
                @foreach($applications as $applicationOption)
                    @php($isSelected = $selectedApplicationId === $applicationOption->id)
                    @php($applicationOptionStatusLabel = match($applicationOption->status) {
                        \App\Models\Application::STATUS_SUBMITTED => 'Eingereicht',
                        \App\Models\Application::STATUS_IN_REVIEW => 'In Prüfung',
                        \App\Models\Application::STATUS_QUESTION => 'Rückfrage',
                        \App\Models\Application::STATUS_APPROVED => 'Bewilligt',
                        \App\Models\Application::STATUS_REJECTED => 'Abgelehnt',
                        default => $applicationOption->status,
                    })
                    <li wire:key="pruef-auswahl-{{ $applicationOption->id }}">
                        <button
                            type="button"
                            wire:click="selectApplication({{ $applicationOption->id }})"
                            aria-pressed="{{ $isSelected ? 'true' : 'false' }}"
                            class="block w-full rounded-lg border px-4 py-3 text-left focus:outline-none focus:ring-2 focus:ring-indigo-600 {{ $isSelected ? 'border-indigo-600 bg-indigo-100 ring-2 ring-indigo-300' : 'border-gray-200 bg-gray-50 hover:border-indigo-300 hover:bg-indigo-50' }}"
                        >
                            <span class="flex flex-wrap items-center justify-between gap-2">
                                <span class="font-semibold text-gray-900">
                                    {{ trim($applicationOption->user->vorname . ' ' . $applicationOption->user->nachname) ?: $applicationOption->user->name }}
                                </span>
                                <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ match($applicationOption->status) {
                                    \App\Models\Application::STATUS_APPROVED => 'bg-green-100 text-green-800',
                                    \App\Models\Application::STATUS_REJECTED => 'bg-red-100 text-red-800',
                                    \App\Models\Application::STATUS_IN_REVIEW => 'bg-amber-100 text-amber-800',
                                    \App\Models\Application::STATUS_QUESTION => 'bg-orange-100 text-orange-800',
                                    default => 'bg-blue-100 text-blue-800',
                                } }}">{{ $applicationOptionStatusLabel }}</span>
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
        <article id="pruef-antrag-{{ $application->id }}" wire:key="pruef-antrag-{{ $application->id }}" class="overflow-hidden rounded-lg border border-gray-200 bg-white shadow-sm">
            <header class="flex flex-wrap items-start justify-between gap-3 bg-gray-50 px-4 py-3">
                <div class="min-w-0">
                    <div class="flex flex-wrap items-center gap-2">
                        <h4 class="text-lg font-semibold text-gray-900">
                            Antrag von {{ trim($application->user->vorname . ' ' . $application->user->nachname) ?: $application->user->name }}
                        </h4>
                        <span class="rounded-full px-2 py-0.5 text-xs font-medium {{ match($application->status) {
                            \App\Models\Application::STATUS_APPROVED => 'bg-green-100 text-green-800',
                            \App\Models\Application::STATUS_REJECTED => 'bg-red-100 text-red-800',
                            \App\Models\Application::STATUS_IN_REVIEW => 'bg-amber-100 text-amber-800',
                            \App\Models\Application::STATUS_QUESTION => 'bg-orange-100 text-orange-800',
                            default => 'bg-blue-100 text-blue-800',
                        } }}">{{ $applicationStatusLabel }}</span>
                    </div>
                    <p class="mt-1 text-sm text-gray-700">
                        Antragsart: {{ $application->applicationType->name }}
                        @if($application->club) · Verein: {{ $application->club->clubname }} @endif
                    </p>
                </div>
                <div class="text-sm text-gray-600">
                    <p>Eingereicht {{ $application->submitted_at?->format('d.m.Y H:i') }}</p>
                    @if($application->fundingPot)
                        <p>Fördertopf: {{ $application->fundingPot->program->name }} — {{ $application->fundingPot->name }}</p>
                    @endif
                </div>
                @if($application->trashed())
                    <div class="w-full border-t border-gray-200 pt-2 text-sm text-gray-700">
                        Archiviert {{ $application->deleted_at?->format('d.m.Y H:i') }} von {{ $application->deleter?->name ?? 'unbekannt' }}.
                        <p class="mt-1">{{ $application->deletion_reason }}</p>
                    </div>
                @endif
            </header>

            <div class="space-y-4 p-4">
                @if($canReview)
                <dl class="grid grid-cols-1 gap-3 sm:grid-cols-2">
                    @foreach($application->form_snapshot ?? [] as $field)
                        @php($value = $application->answers[$field['key']] ?? null)
                        @if($value !== null && $value !== '')
                            @php($displayValue = ($field['type'] ?? null) === 'date' ? \Illuminate\Support\Carbon::parse($value)->format('d.m.Y') : $value)
                            <div class="rounded-md border border-gray-100 bg-gray-50 px-3 py-2">
                                <dt class="text-xs font-semibold uppercase text-gray-500">{{ $field['label'] ?? $field['key'] }}</dt>
                                <dd class="mt-1 whitespace-pre-wrap break-words text-sm text-gray-900">{{ is_bool($displayValue) ? ($displayValue ? 'Ja' : 'Nein') : (is_array($displayValue) ? implode(', ', $displayValue) : $displayValue) }}</dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
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
