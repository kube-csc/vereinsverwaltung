<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-900 leading-tight">Persönliche Antragsübersicht</h2>
    </x-slot>

    <div class="py-10">
        <div class="mx-auto max-w-7xl space-y-8 px-4 sm:px-6 lg:px-8">
            <section class="overflow-hidden rounded-xl bg-green-900 text-white shadow-lg">
                <div class="px-6 py-8 sm:px-10">
                    <h3 class="mt-1 text-2xl font-bold sm:text-3xl">Willkommen, {{ auth()->user()->vorname ?: auth()->user()->name }}</h3>
                    <p class="mt-2 max-w-2xl text-base text-white">Hier finden Sie passende Antragsarten, den Stand Ihrer Anträge und Ihre Bewilligungen.</p>
                </div>
            </section>

            @if($hasManagementAccess)
                <a href="{{ route('antraege.dashboard') }}" class="inline-flex items-center rounded-md border border-indigo-300 bg-indigo-50 px-4 py-2 text-sm font-semibold text-indigo-900 hover:bg-indigo-100">
                    Zur Verwaltungs- und Prüfungsübersicht
                </a>
            @endif

            <section>
                <h3 class="mb-3 text-sm font-semibold uppercase tracking-wide text-gray-700">Meine Anträge im Überblick</h3>
                <div class="grid grid-cols-2 gap-4 md:grid-cols-3 lg:grid-cols-6">
                    @php
                        $statusCards = [
                            [\App\Models\Application::STATUS_DRAFT, 'Entwürfe', 'border-amber-500'],
                            [\App\Models\Application::STATUS_SUBMITTED, 'Eingereicht', 'border-blue-500'],
                            [\App\Models\Application::STATUS_IN_REVIEW, 'In Prüfung', 'border-indigo-500'],
                            [\App\Models\Application::STATUS_QUESTION, 'Rückfragen', 'border-orange-500'],
                            [\App\Models\Application::STATUS_APPROVED, 'Bewilligt', 'border-green-600'],
                            [\App\Models\Application::STATUS_REJECTED, 'Abgelehnt', 'border-red-500'],
                        ];
                    @endphp
                    @foreach($statusCards as [$status, $label, $border])
                        <div class="rounded-xl border-l-4 {{ $border }} bg-white p-4 shadow">
                            <p class="text-sm text-gray-600">{{ $label }}</p>
                            <p class="mt-1 text-3xl font-bold text-gray-900">{{ $statusCounts[$status] }}</p>
                        </div>
                    @endforeach
                </div>
                <div class="mt-4 rounded-xl border border-green-200 bg-green-50 p-5 shadow-sm">
                    <p class="text-sm font-medium text-green-900">Tatsächlich bewilligte Fördermittel</p>
                    <p class="mt-1 text-3xl font-bold text-green-950">{{ number_format($approvedAmount, 2, ',', '.') }} €</p>
                    <p class="mt-1 text-xs text-green-800">Summe der endgültig bewilligten Beträge Ihrer nicht archivierten Förderanträge.</p>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Förderanträge</h3>
                            <p class="text-sm text-gray-600">Antragsarten mit zugeordnetem Fördertopf; Fördermittelbeträge werden hier nicht angezeigt.</p>
                        </div>
                        <span class="rounded-full bg-blue-100 px-3 py-1 text-sm font-semibold text-blue-900">{{ $availableTypesWithPot->count() }} verfügbar</span>
                    </div>
                    <div class="space-y-3">
                        @forelse($availableTypesWithPot as $type)
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-200 p-4">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $type->name }}</h4>
                                    @if($type->frist)
                                        <p class="mt-1 text-sm text-gray-600">Frist: {{ $type->frist->format('d.m.Y') }}</p>
                                    @endif
                                </div>
                                <a href="{{ route('antraege.neu', $type->slug) }}" class="rounded-md bg-blue-700 px-4 py-2 text-sm font-semibold text-white hover:bg-blue-800">Antrag starten</a>
                            </div>
                        @empty
                            <p class="rounded-lg bg-gray-50 p-4 text-sm text-gray-600">Zurzeit sind keine Förderanträge geöffnet.</p>
                        @endforelse
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="mb-4 flex items-start justify-between gap-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Weitere Anträge</h3>
                            <p class="text-sm text-gray-600">Antragsarten ohne verpflichtende Fördertopfzuordnung.</p>
                        </div>
                        <span class="rounded-full bg-purple-100 px-3 py-1 text-sm font-semibold text-purple-900">{{ $availableTypesWithoutPot->count() }} verfügbar</span>
                    </div>
                    <div class="space-y-3">
                        @forelse($availableTypesWithoutPot as $type)
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg border border-gray-200 p-4">
                                <div>
                                    <h4 class="font-semibold text-gray-900">{{ $type->name }}</h4>
                                    @if($type->frist)
                                        <p class="mt-1 text-sm text-gray-600">Frist: {{ $type->frist->format('d.m.Y') }}</p>
                                    @endif
                                </div>
                                <a href="{{ route('antraege.neu', $type->slug) }}" class="rounded-md bg-purple-700 px-4 py-2 text-sm font-semibold text-white hover:bg-purple-800">Antrag starten</a>
                            </div>
                        @empty
                            <p class="rounded-lg bg-gray-50 p-4 text-sm text-gray-600">Zurzeit sind keine weiteren Antragsarten geöffnet.</p>
                        @endforelse
                    </div>
                </div>
            </section>

            <section class="grid grid-cols-1 gap-6 lg:grid-cols-2">
                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Nächste Schritte</h3>
                            <p class="text-sm text-gray-600">Entwürfe fortsetzen und Fristen im Blick behalten.</p>
                        </div>
                        <div class="flex flex-wrap items-center gap-3">
                            @if($statusCounts[\App\Models\Application::STATUS_QUESTION] > 0)
                                <a href="{{ route('antraege.meine') }}" class="rounded-md bg-amber-700 px-3 py-2 text-sm font-semibold text-white hover:bg-amber-800">Rückfragen beantworten ({{ $statusCounts[\App\Models\Application::STATUS_QUESTION] }})</a>
                            @endif
                            <a href="{{ route('antraege.meine') }}" class="text-sm font-semibold text-blue-700 hover:underline">Alle eigenen Anträge</a>
                        </div>
                    </div>
                    <div class="mt-4 space-y-3">
                        @forelse($draftApplications as $application)
                            <div class="flex flex-wrap items-center justify-between gap-3 rounded-lg bg-amber-50 p-3">
                                <span class="text-sm text-gray-800">Entwurf: {{ $application->applicationType->name }}</span>
                                <a href="{{ route('antraege.bearbeiten', $application->id) }}" class="text-sm font-semibold text-blue-700 hover:underline">Weiterbearbeiten</a>
                            </div>
                        @empty
                            <p class="text-sm text-gray-600">Sie haben keine offenen Entwürfe.</p>
                        @endforelse
                        @foreach($upcomingDeadlines as $type)
                            <p class="rounded-lg bg-orange-50 p-3 text-sm text-orange-900">
                                Frist für {{ $type->name }}: <strong>{{ $type->frist->format('d.m.Y') }}</strong>
                            </p>
                        @endforeach
                    </div>
                </div>

                <div class="rounded-xl border border-gray-200 bg-white p-5 shadow-sm">
                    <div class="flex items-center justify-between gap-3">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">Zuletzt aktualisierte Anträge</h3>
                            <p class="text-sm text-gray-600">Persönliche Statusübersicht.</p>
                        </div>
                        <a href="{{ route('antraege.meine') }}" class="text-sm font-semibold text-blue-700 hover:underline">Alle anzeigen</a>
                    </div>
                    <ul class="mt-4 divide-y divide-gray-100">
                        @forelse($recentApplications as $application)
                            <li class="flex items-center justify-between gap-4 py-3">
                                <span class="text-sm font-medium text-gray-900">{{ $application->applicationType->name }}</span>
                                <span class="text-right text-sm text-gray-600">
                                    {{ match($application->status) {
                                        \App\Models\Application::STATUS_SUBMITTED => 'Eingereicht',
                                        \App\Models\Application::STATUS_IN_REVIEW => 'In Prüfung',
                                        \App\Models\Application::STATUS_QUESTION => 'Rückfrage',
                                        \App\Models\Application::STATUS_APPROVED => 'Bewilligt',
                                        \App\Models\Application::STATUS_REJECTED => 'Abgelehnt',
                                        default => $application->status,
                                    } }}
                                    <span class="block text-xs text-gray-500">{{ $application->updated_at->format('d.m.Y') }}</span>
                                </span>
                            </li>
                        @empty
                            <li class="py-3 text-sm text-gray-600">Sie haben noch keine Anträge eingereicht.</li>
                        @endforelse
                    </ul>
                </div>
            </section>

            <a href="{{ route('antraege.bankkonten') }}" class="inline-flex items-center rounded-md border border-gray-300 bg-white px-4 py-3 text-sm font-semibold text-gray-800 shadow-sm hover:bg-gray-50">
                <i class="fas fa-university mr-2 text-amber-700"></i> Meine Bankkonten verwalten
            </a>
        </div>
    </div>
</x-app-layout>
