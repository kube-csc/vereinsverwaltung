<div class="p-6 space-y-8">
    @if (session()->has('message'))
        <div class="text-sm font-medium text-green-700 bg-green-50 border border-green-200 rounded p-3">{{ session('message') }}</div>
    @endif

    <section class="space-y-3">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Verfügbare Antragsarten</h3>
            <p class="text-sm text-gray-600">Sie können einen Antrag zunächst als Entwurf speichern und später einreichen.</p>
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @forelse($applicationTypes as $type)
                <article wire:key="typ-{{ $type->id }}" class="border border-gray-300 rounded-lg p-4 flex flex-col gap-3">
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

    <section class="space-y-3 border-t border-gray-200 pt-6">
        <div>
            <h3 class="text-lg font-semibold text-gray-900">Meine gespeicherten Anträge</h3>
            <p class="text-sm text-gray-600">Eingereichte Anträge können hier nicht mehr geändert werden.</p>
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
                                <span class="inline-flex rounded-full px-2 py-1 text-xs font-semibold {{ $application->status === $draftStatus ? 'bg-amber-100 text-amber-900' : 'bg-green-100 text-green-900' }}">
                                    {{ $application->status === $draftStatus ? 'Entwurf' : 'Eingereicht' }}
                                </span>
                            </td>
                            <td class="px-4 py-3 text-gray-700">
                                {{ $application->submitted_at?->format('d.m.Y H:i') ?? $application->updated_at->format('d.m.Y H:i') }}
                            </td>
                            <td class="px-4 py-3 text-right">
                                @if($application->status === $draftStatus)
                                    <a href="{{ route('antraege.bearbeiten', $application->id) }}" class="font-medium text-blue-700 hover:underline">Weiterbearbeiten</a>
                                @else
                                    <span class="text-gray-500">Abgegeben</span>
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
