<div class="space-y-8 bg-white p-6">
    @if (session()->has('message'))
        <div class="text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded p-3">{{ session('message') }}</div>
    @endif
    <section class="space-y-4">
        <div class="rounded-xl border-l-4 border-gray-600 bg-gray-300 px-5 py-4 text-gray-900">
            <h3 class="text-lg font-bold">Verfügbare Antragsarten</h3>
            <p class="text-sm text-gray-800">Sie können einen Antrag zunächst als Entwurf speichern und später einreichen.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($applicationTypes as $type)
                <article wire:key="typ-{{ $type->id }}" class="flex flex-col gap-3 rounded-xl border border-blue-200 bg-blue-50 p-4 shadow-sm">
                    <div>
                        <h4 class="font-semibold text-gray-900">{{ $type->name }}</h4>
                        <p class="text-sm text-gray-600">{{ $type->beschreibung }}</p>
                        @if($type->requires_funding_pot)
                            <p class="mt-1 text-sm text-gray-700">
                                Offene Fördertöpfe:
                                {{ $type->fundingPots->filter(fn ($pot) => $pot->istOffen())->pluck('name')->join(', ') }}
                            </p>
                        @endif
                        @if($type->frist)
                            <p class="mt-1 text-sm text-gray-700">Abgabefrist: {{ $type->frist->format('d.m.Y') }}</p>
                        @endif
                    </div>
                    <a href="{{ route('antraege.neu', $type->slug) }}" class="self-start inline-flex items-center rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:ring-offset-2">
                        Antrag beginnen
                    </a>
                </article>
            @empty
                <p class="text-sm text-gray-600">Zurzeit sind keine Antragsarten geöffnet.</p>
            @endforelse
        </div>
    </section>

    <section class="space-y-4 border-t border-gray-200 pt-6">
        <div class="rounded-xl border-l-4 border-gray-600 bg-gray-300 px-5 py-4 text-gray-900">
            <h3 class="text-lg font-bold">Meine gespeicherten Anträge</h3>
            <p class="text-sm text-gray-800">Eingereichte Anträge können hier nicht mehr geändert werden.</p>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200 text-sm">
                <thead>
                    <tr class="text-left text-xs font-semibold uppercase text-gray-600">
                        <th class="px-4 py-3">Antragsart</th>
                        <th class="px-4 py-3">Fördertopf</th>
                        <th class="px-4 py-3">Verein</th>
                        <th class="px-4 py-3">Stand</th>
                        <th class="px-4 py-3">Datum</th>
                        <th class="px-4 py-3 text-right">Aktion</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-gray-100">
                    @forelse($applications as $application)
                        <tr wire:key="antrag-{{ $application->id }}">
                            <td class="px-4 py-3 font-medium text-gray-900">{{ $application->applicationType->name }}</td>
                            <td class="px-4 py-3 text-gray-700">
                                @if($application->fundingPot)
                                    {{ $application->fundingPot->program->name }} – {{ $application->fundingPot->name }}
                                    @if($application->fundingPot->jahr) ({{ $application->fundingPot->jahr }}) @endif
                                @else
                                    –
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">{{ $application->club?->clubname ?? '–' }}</td>
                            <td class="px-4 py-3">
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ match($application->status) {
                                    \App\Models\Application::STATUS_DRAFT => 'bg-gray-100 text-gray-800',
                                    \App\Models\Application::STATUS_APPROVED => 'bg-green-100 text-green-800',
                                    \App\Models\Application::STATUS_REJECTED => 'bg-red-100 text-red-800',
                                    \App\Models\Application::STATUS_SUBMITTED => 'bg-blue-100 text-blue-800',
                                    \App\Models\Application::STATUS_IN_REVIEW => 'bg-yellow-100 text-yellow-800',
                                    \App\Models\Application::STATUS_QUESTION => 'bg-orange-100 text-orange-800',
                                    default => 'bg-gray-100 text-gray-800',
                                } }}">
                                    {{ match($application->status) {
                                        \App\Models\Application::STATUS_DRAFT => 'Entwurf',
                                        \App\Models\Application::STATUS_SUBMITTED => 'Eingereicht',
                                        \App\Models\Application::STATUS_IN_REVIEW => 'In Prüfung',
                                        \App\Models\Application::STATUS_QUESTION => 'Rückfrage – Antwort erforderlich',
                                        \App\Models\Application::STATUS_APPROVED => 'Bewilligt',
                                        \App\Models\Application::STATUS_REJECTED => 'Abgelehnt',
                                        default => $application->status,
                                    } }}
                                </span>
                                @if($application->decision_reason)
                                    <span class="mt-1 block max-w-xs text-xs text-gray-600">{{ $application->decision_reason }}</span>
                                @endif
                                @php($publicEvents = $application->events->whereIn('event_type', [
                                    \App\Models\Application::EVENT_QUESTION,
                                    \App\Models\Application::EVENT_ANSWER,
                                ]))
                                @if($publicEvents->isNotEmpty())
                                    <ol class="mt-2 max-w-sm space-y-2">
                                        @foreach($publicEvents as $event)
                                            <li class="rounded bg-gray-50 p-2 text-xs text-gray-700">
                                                <span class="font-semibold">{{ $event->event_type === \App\Models\Application::EVENT_QUESTION ? 'Rückfrage' : 'Ihre Antwort' }}</span>
                                                <span> · {{ $event->created_at->format('d.m.Y H:i') }}</span>
                                                <p class="mt-1 whitespace-pre-wrap">{{ $event->message }}</p>
                                            </li>
                                        @endforeach
                                    </ol>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $application->submitted_at?->format('d.m.Y H:i') ?? $application->updated_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($application->status === $draftStatus)
                                    <a href="{{ route('antraege.bearbeiten', $application->id) }}" class="font-medium text-blue-700 hover:underline">Weiterbearbeiten</a>
                                @elseif($application->status === \App\Models\Application::STATUS_QUESTION)
                                    <label class="block text-left text-xs font-semibold text-gray-700">
                                        Ihre Antwort
                                        <textarea wire:model="questionResponses.{{ $application->id }}" rows="3" class="mt-1 w-64 rounded-md border-gray-300 text-sm"></textarea>
                                        @error('questionResponses.' . $application->id)<span class="mt-1 block text-red-700">{{ $message }}</span>@enderror
                                    </label>
                                    <button wire:click="respondToQuestion({{ $application->id }})" class="mt-2 rounded-md bg-blue-700 px-3 py-2 text-sm font-semibold text-white hover:bg-blue-800">Antwort übermitteln</button>
                                @else
                                    <span class="text-gray-500">Abgegeben</span>
                                    @if(in_array($application->status, [\App\Models\Application::STATUS_APPROVED, \App\Models\Application::STATUS_REJECTED], true))
                                        <a href="{{ route('antraege.bescheid', $application->id) }}" class="mt-1 block font-medium text-blue-700 hover:underline">Bescheid (PDF)</a>
                                    @endif
                                    @if($application->status === \App\Models\Application::STATUS_APPROVED && $application->funding_pot_id)
                                        <div class="mt-2 text-left text-xs text-gray-700">
                                            @php($payoutAccount = $application->payoutAccountData())
                                            @if($application->payout_exported_at && $payoutAccount)
                                                <p class="mb-1 font-semibold text-green-800">Überweisung veranlasst</p>
                                            @endif
                                            <p class="font-semibold">Bankverbindung für die Auszahlung</p>
                                            <p>{{ $payoutAccount ? 'Aktuell: ' . ($payoutAccount['kontoinhaber'] ?? '') . ', ' . substr($payoutAccount['iban'], 0, 4) . ' •••• ' . substr($payoutAccount['iban'], -4) : 'Noch keine Bankverbindung hinterlegt.' }}</p>
                                            @if($accounts->count() === 1 && $payoutAccount && ($payoutAccount['iban'] ?? null) === $accounts->first()->iban)
                                                <p class="mt-1">Ihr einziges Bankkonto wird automatisch verwendet.</p>
                                            @elseif($accounts->isNotEmpty())
                                                <select wire:model="payoutAccounts.{{ $application->id }}" class="mt-1 w-64 rounded-md border-gray-300 text-sm">
                                                <select wire:model="payoutAccounts.{{ $application->id }}" class="mt-1 w-64 rounded-md border-gray-300 text-sm"> class="mt-1 w-64 rounded-md border-gray-300 text-sm">                                                    <option value="">Bankkonto wählen</option>
                                                    @foreach($accounts as $account)
                                                        <option value="{{ $account->id }}">{{ $account->kontoinhaber }} – {{ $account->maskedIban() }}</option>
                                                    @endforeach
                                                </select>
                                                @error('payoutAccounts.' . $application->id)<span class="mt-1 block text-red-700">{{ $message }}</span>@enderror
                                                <button wire:click="updatePayoutAccount({{ $application->id }})" class="mt-1 block rounded-md bg-blue-700 px-3 py-1.5 text-sm font-semibold text-white hover:bg-blue-800">Bankverbindung übernehmen</button>
                                            @else
                                                <p class="mt-1">Legen Sie zuerst unter „Bankkonten“ ein Konto an.</p>
                                            @endif
                                        </div>
                                    @endif
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="6" class="px-4 py-4 text-center text-gray-600">Sie haben noch keine Anträge gespeichert.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </section>
</div>
