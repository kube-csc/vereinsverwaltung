<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Trainertypen – Übersicht
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="mt-2 text-2xl">Trainer je Trainertyp</div>
                            <div class="mt-2 text-gray-500">
                                Übersicht aller Trainerfunktionen. Zuordnungen werden beim Deaktivieren soft-gelöscht (Datensätze bleiben erhalten).
                            </div>
                        </div>
                        <div class="flex gap-2">
                            <a class="p-2 bg-blue-500 rounded shadow text-white" href="{{ route('admin.trainer.index') }}">Trainer verwalten</a>
                            <a class="p-2 bg-blue-500 rounded shadow text-white" href="/Adminmenu">Adminmenu</a>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mt-4 p-3 rounded bg-green-100 text-green-800">{!! session('success') !!}</div>
                    @endif

                    <div class="mt-6">
                        <a class="p-2 rounded shadow text-white {{ $showInactive ? 'bg-gray-500' : 'bg-blue-500' }}"
                           href="{{ route('admin.trainer.types.index', ['inactive' => 0]) }}">
                            nur aktive
                        </a>
                        <a class="p-2 rounded shadow text-white {{ $showInactive ? 'bg-blue-500' : 'bg-gray-500' }}"
                           href="{{ route('admin.trainer.types.index', ['inactive' => 1]) }}">
                            aktive + deaktivierte
                        </a>
                    </div>
                </div>

                <div class="p-6 space-y-8">
                    @foreach($trainertyps as $typ)
                        @php
                            $assignments = $groupedAssignments->get($typ->id, collect());
                            $typeOrganiser = collect($organisers ?? [])->firstWhere('id', $typ->organiser_id ?? null);
                            $typIsActive = !$typ->deleted_at && (int)($typ->status ?? 0) === 1;
                        @endphp

                        <div class="border rounded p-4 {{ $typIsActive ? '' : 'bg-gray-50' }}">
                            <div class="flex items-center justify-between">
                                <div>
                                    <div class="text-lg font-semibold">
                                        {{ $typ->trainerfunktion }}
                                        @if(!$typIsActive)
                                            <span class="ml-2 text-sm font-normal text-red-700">(Trainerfunktion deaktiviert)</span>
                                        @endif
                                    </div>
                                    <div class="mt-1 text-sm text-gray-600">
                                        Veranstaltung: <span class="font-medium">{{ $typeOrganiser?->veranstaltung ?? '—' }}</span>
                                    </div>
                                </div>
                                <div class="text-sm text-gray-500">Zuordnungen: {{ $assignments->count() }}</div>
                            </div>

                            @if($assignments->isEmpty())
                                <div class="mt-3 text-gray-500">Keine Trainer zugeordnet.</div>
                            @else
                                <div class="mt-4 overflow-x-auto">
                                    <table class="min-w-full text-sm">
                                        <thead>
                                        <tr class="text-left border-b">
                                            <th class="py-2">Trainer</th>
                                            <th class="py-2">E-Mail</th>
                                             <th class="py-2">Veranstaltung</th>
                                             <th class="py-2">Abteilung</th>
                                             <th class="py-2">Öffentlich</th>
                                            <th class="py-2">Status</th>
                                            <th class="py-2">Aktion</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        @foreach($assignments as $a)
                                            @php
                                                $fullName = trim(($a->user->vorname ?? '') . ' ' . ($a->user->nachname ?? ''));
                                                 $typeIsActive = !$a->trainertyp?->deleted_at && (int)($a->trainertyp?->status ?? 0) === 1;
                                            @endphp
                                            <tr class="border-b">
                                                <td class="py-2">{{ $fullName !== '' ? $fullName : ('User ID ' . $a->user_id) }}</td>
                                                <td class="py-2">{{ $a->user->email ?? '' }}</td>
                                                 <td class="py-2">
                                                     {{ $a->organiser?->veranstaltung ? $a->organiser->veranstaltung : '—' }}
                                                 </td>
                                                 <td class="py-2">
                                                     {{ $a->sportSection?->abteilung ? $a->sportSection->abteilung : '—' }}
                                                 </td>
                                                 <td class="py-2">
                                                     @if(!$a->deleted_at)
                                                         @if((int)$a->sichtbar === 1)
                                                             <span class="text-green-700">ja</span>
                                                         @else
                                                             <span class="text-gray-700">nein</span>
                                                         @endif
                                                     @else
                                                         <span class="text-gray-500">—</span>
                                                     @endif
                                                 </td>
                                                <td class="py-2">
                                                    @if($a->deleted_at)
                                                        <span class="text-red-700">deaktiviert</span>
                                                    @else
                                                        <span class="text-green-700">aktiv</span>
                                                    @endif
                                                </td>
                                                <td class="py-2">
                                                     @if(!$a->deleted_at)
                                                         <div class="flex gap-2">
                                                             <form method="POST" action="{{ route('admin.trainer.types.toggleVisible', $a->id) }}">
                                                                 @csrf
                                                                 <button type="submit" class="p-2 {{ (int)$a->sichtbar === 1 ? 'bg-gray-600' : 'bg-blue-500' }} rounded shadow text-white">
                                                                     {{ (int)$a->sichtbar === 1 ? 'unsichtbar' : 'sichtbar' }}
                                                                 </button>
                                                             </form>

                                                             <form method="POST" action="{{ route('admin.trainer.types.deactivate', $a->id) }}">
                                                                 @csrf
                                                                 <button type="submit" class="p-2 bg-red-500 rounded shadow text-white">Deaktivieren</button>
                                                             </form>
                                                         </div>
                                                    @else
                                                         @if($typeIsActive)
                                                             <form method="POST" action="{{ route('admin.trainer.types.reactivate', $a->id) }}">
                                                                 @csrf
                                                                 <button type="submit" class="p-2 bg-green-600 rounded shadow text-white">Reaktivieren</button>
                                                             </form>
                                                         @else
                                                             <span class="text-sm text-gray-500">(Zuordnung deaktiviert – Trainerfunktion ist deaktiviert)</span>
                                                         @endif
                                                    @endif
                                                </td>
                                            </tr>
                                        @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            @endif
                        </div>
                    @endforeach
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

