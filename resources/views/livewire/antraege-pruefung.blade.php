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

    @forelse($applications as $application)
        <article wire:key="pruef-antrag-{{ $application->id }}" class="rounded-xl border border-gray-300 bg-white p-5 shadow-sm">
            <div class="flex flex-wrap items-start justify-between gap-4">
                <div>
                    <h4 class="font-semibold text-gray-900">{{ $application->applicationType->name }}</h4>
                    <p class="mt-1 text-sm text-gray-700">
                        {{ trim($application->user->vorname . ' ' . $application->user->nachname) ?: $application->user->name }}
                        · Eingereicht {{ $application->submitted_at?->format('d.m.Y H:i') }}
                        @if($application->fundingPot)
                            · {{ $application->fundingPot->program->name }} — {{ $application->fundingPot->name }}
                        @endif
                    </p>
                    <p class="mt-1 text-sm font-medium text-gray-800">Status: {{ str_replace('_', ' ', ucfirst($application->status)) }}</p>
                </div>
                @if($application->trashed())
                    <div class="text-sm text-gray-700">
                        Archiviert {{ $application->deleted_at?->format('d.m.Y H:i') }} von {{ $application->deleter?->name ?? 'unbekannt' }}.
                        <p class="mt-1">{{ $application->deletion_reason }}</p>
                    </div>
                @endif
            </div>

            @if($canReview)
                <dl class="mt-4 grid grid-cols-1 gap-3 border-t border-gray-200 pt-4 sm:grid-cols-2">
                    @foreach($application->form_snapshot ?? [] as $field)
                        @php($value = $application->answers[$field['key']] ?? null)
                        @if($value !== null && $value !== '')
                            <div>
                                <dt class="text-xs font-semibold uppercase text-gray-500">{{ $field['label'] ?? $field['key'] }}</dt>
                                <dd class="mt-1 whitespace-pre-wrap break-words text-sm text-gray-900">{{ is_bool($value) ? ($value ? 'Ja' : 'Nein') : (is_array($value) ? implode(', ', $value) : $value) }}</dd>
                            </div>
                        @endif
                    @endforeach
                </dl>
                @if($application->documents->isNotEmpty())
                    <div class="mt-4 border-t border-gray-200 pt-3">
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
                    <p class="mt-3 text-sm text-gray-700"><span class="font-semibold">Entscheidungsbegründung:</span> {{ $application->decision_reason }} ({{ $application->decider?->name ?? 'unbekannt' }}, {{ $application->decided_at?->format('d.m.Y H:i') }})</p>
                @endif
            @endif

            @if(! $application->trashed())
                <div class="mt-4 flex flex-col gap-3 border-t border-gray-200 pt-4">
                    @if($canReview && $application->status === \App\Models\Application::STATUS_SUBMITTED)
                        <div class="flex flex-wrap items-end gap-3">
                            <label class="min-w-64 flex-1 text-sm text-gray-700">
                                Prüfnotiz (optional)
                                <textarea wire:model="reviewNotes.{{ $application->id }}" rows="2" class="mt-1 w-full rounded-md border-gray-300 text-sm"></textarea>
                            </label>
                            <button wire:click="markForReview({{ $application->id }})" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">In Prüfung nehmen</button>
                        </div>
                    @endif

                    @if($canDecide && in_array($application->status, [\App\Models\Application::STATUS_SUBMITTED, \App\Models\Application::STATUS_IN_REVIEW], true))
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
        </article>
    @empty
        <p class="rounded-lg border border-gray-200 bg-gray-50 p-5 text-sm text-gray-600">
            {{ $showArchived ? 'Keine archivierten Anträge vorhanden.' : 'Keine eingereichten Anträge vorhanden.' }}
        </p>
    @endforelse

    {{ $applications->links() }}
</div>
