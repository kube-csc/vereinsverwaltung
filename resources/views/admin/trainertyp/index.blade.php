<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Trainertypen verwalten</h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">

                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="mt-2 text-2xl">Trainertypen</div>
                            <div class="mt-2 text-gray-500">Hier definierst du die verfügbaren Trainertypen inkl. Default-Werte für neue Zuordnungen.</div>
                        </div>
                        <div class="flex gap-2">
                            <a class="p-2 bg-blue-500 rounded shadow text-white" href="{{ route('admin.trainertyp.create') }}">Neu</a>
                            <a class="p-2 bg-blue-500 rounded shadow text-white" href="{{ route('admin.trainer.types.index') }}">Übersicht Zuordnungen</a>
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

                    <div class="mt-6">
                        <a class="p-2 rounded shadow text-white {{ $showDeleted ? 'bg-gray-500' : 'bg-blue-500' }}"
                           href="{{ route('admin.trainertyp.index', ['deleted' => 0]) }}">
                            nur aktive
                        </a>
                        <a class="p-2 rounded shadow text-white {{ $showDeleted ? 'bg-blue-500' : 'bg-gray-500' }}"
                           href="{{ route('admin.trainertyp.index', ['deleted' => 1]) }}">
                            aktive + deaktivierte
                        </a>
                    </div>
                </div>

                <div class="p-6 overflow-x-auto">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">Trainerfunktion</th>
                            <th class="py-2">aktiv</th>
                            <th class="py-2">Trainer öffentlich</th>
                             <th class="py-2">Trainer-Veranstaltung</th>
                            <th class="py-2">Aktion</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($types as $t)
                            <tr class="border-b {{ $t->deleted_at ? 'text-gray-500' : '' }}">
                                <td class="py-2">{{ $t->trainerfunktion }}</td>
                                <td class="py-2">
                                    @if($t->deleted_at)
                                        nein
                                    @else
                                        {{ (int)$t->status === 1 ? 'ja' : 'nein' }}
                                    @endif
                                </td>
                                <td class="py-2">{{ (int)($t->default_sichtbar ?? 1) === 1 ? 'ja' : 'nein' }}</td>
                                @php
                                    $organiser = ($organisers ?? collect())->firstWhere('id', (int)($t->organiser_id ?? 0));
                                    $organiserLabel = $organiser
                                        ? trim(($organiser->veranstaltung ?? '') . ((isset($organiser->veranstaltungDomain) && $organiser->veranstaltungDomain) ? ' (' . $organiser->veranstaltungDomain . ')' : ''))
                                        : '';
                                @endphp
                                <td class="py-2">
                                    {{ $organiserLabel !== '' ? $organiserLabel : '—' }}
                                </td>
                                <td class="py-2">
                                    <div class="flex gap-2">
                                        <a class="p-2 bg-blue-500 rounded shadow text-white" href="{{ route('admin.trainertyp.edit', $t->id) }}">Bearbeiten</a>

                                        @if(!$t->deleted_at)
                                            <form method="POST" action="{{ route('admin.trainertyp.destroy', $t->id) }}">
                                                @csrf
                                                <button type="submit" class="p-2 bg-red-500 rounded shadow text-white">Deaktivieren</button>
                                            </form>
                                        @else
                                            <form method="POST" action="{{ route('admin.trainertyp.restore', $t->id) }}">
                                                @csrf
                                                <button type="submit" class="p-2 bg-green-600 rounded shadow text-white">Reaktivieren</button>
                                            </form>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>

