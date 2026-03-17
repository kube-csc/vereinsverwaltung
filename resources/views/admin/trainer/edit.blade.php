<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Trainerfunktionen bearbeiten
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="mt-2 text-2xl">{{ trim(($user->vorname ?? '') . ' ' . ($user->nachname ?? '')) }}</div>
                            <div class="mt-1 text-gray-500">{{ $user->email }}</div>
                        </div>
                        <div class="flex gap-2">
                            <a class="p-2 bg-blue-500 rounded shadow text-white" href="{{ route('admin.trainer.index') }}">Zur Liste</a>
                            <a class="p-2 bg-blue-500 rounded shadow text-white" href="/Adminmenu">Adminmenu</a>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mt-4 p-3 rounded bg-green-100 text-green-800">{!! session('success') !!}</div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-4 p-3 rounded bg-red-100 text-red-800">
                            <ul class="list-disc ml-5">
                                @foreach ($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                </div>

                <div class="p-6">
                    <h3 class="font-semibold text-lg mb-2">Trainerfunktion(en) hinzufügen</h3>
                    <form method="POST" action="{{ route('admin.trainer.update', $user->id) }}" class="space-y-4">
                        @csrf

                        <div>
                            <label class="block text-sm text-gray-600 mb-1">Trainerfunktionen (Mehrfachauswahl)</label>
                            <select name="trainertyp_ids[]" multiple class="w-full rounded border-gray-300" size="8">
                                @foreach($trainertyps as $typ)
                                    <option value="{{ $typ->id }}">{{ $typ->trainerfunktion }}</option>
                                @endforeach
                            </select>
                            <div class="text-xs text-gray-500 mt-1">Mit STRG/CTRL mehrere Funktionen auswählen.</div>
                        </div>

                        <button type="submit" class="p-2 bg-blue-500 rounded shadow text-white">Speichern</button>
                    </form>

                    <div class="mt-10">
                        <h3 class="font-semibold text-lg mb-2">Aktive Zuordnungen</h3>

                        @if($activeAssignments->isEmpty())
                            <div class="text-gray-500">Keine aktiven Trainerfunktionen vorhanden.</div>
                        @else
                            <table class="min-w-full text-sm">
                                <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2">Trainerfunktion</th>
                                    <th class="py-2">Öffentlich</th>
                                    <th class="py-2">Status</th>
                                    <th class="py-2">Aktion</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($activeAssignments as $a)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $a->trainertyp?->trainerfunktion ?? ('ID ' . $a->trainertyp_id) }}</td>
                                        <td class="py-2">
                                            {{ (int)$a->sichtbar === 1 ? 'ja' : 'nein' }}
                                        </td>
                                        <td class="py-2">{{ (int)$a->status === 1 ? 'aktiv' : 'inaktiv' }}</td>
                                        <td class="py-2">
                                            <div class="flex gap-2">
                                                <form method="POST" action="{{ route('admin.trainer.toggleVisible', [$user->id, $a->id]) }}">
                                                    @csrf
                                                    <button type="submit" class="p-2 {{ (int)$a->sichtbar === 1 ? 'bg-gray-600' : 'bg-blue-500' }} rounded shadow text-white">
                                                        {{ (int)$a->sichtbar === 1 ? 'unsichtbar' : 'sichtbar' }}
                                                    </button>
                                                </form>

                                                <form method="POST" action="{{ route('admin.trainer.deactivate', [$user->id, $a->id]) }}">
                                                    @csrf
                                                    <button type="submit" class="p-2 bg-red-500 rounded shadow text-white">Deaktivieren</button>
                                                </form>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                    <div class="mt-10">
                        <h3 class="font-semibold text-lg mb-2">Deaktivierte Zuordnungen</h3>

                        @if($inactiveAssignments->isEmpty())
                            <div class="text-gray-500">Keine deaktivierten Zuordnungen vorhanden.</div>
                        @else
                            <table class="min-w-full text-sm">
                                <thead>
                                <tr class="text-left border-b">
                                    <th class="py-2">Trainerfunktion</th>
                                    <th class="py-2">Deaktiviert am</th>
                                    <th class="py-2">Aktion</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($inactiveAssignments as $a)
                                    <tr class="border-b">
                                        <td class="py-2">{{ $a->trainertyp?->trainerfunktion ?? ('ID ' . $a->trainertyp_id) }}</td>
                                        <td class="py-2">{{ optional($a->deleted_at)->format('d.m.Y H:i') }}</td>
                                        <td class="py-2">
                                            <form method="POST" action="{{ route('admin.trainer.reactivate', [$user->id, $a->id]) }}">
                                                @csrf
                                                <button type="submit" class="p-2 bg-green-600 rounded shadow text-white">Reaktivieren</button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        @endif
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>

