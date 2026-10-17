<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Rechteverwaltung
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="mt-2 text-2xl">Admin-Stufe der Benutzer</div>
                            <div class="mt-2 text-gray-500">
                                Ab Stufe 3 ist die Regattaverwaltung, ab Stufe 4 das Adminmenü verfügbar.
                                Es muss immer mindestens ein Benutzer mit Stufe 4 oder höher existieren.
                                Du kannst höchstens Stufe {{ $maxLevel }} vergeben.
                            </div>
                        </div>
                        <div>
                            <a class="p-2 bg-blue-500 w-40 rounded shadow text-white" href="/Adminmenu">
                                Zurück
                            </a>
                        </div>
                    </div>

                    @if (session('success'))
                        <div class="mt-4 p-3 rounded bg-green-100 text-green-800">{!! session('success') !!}</div>
                    @endif

                    @if ($errors->any())
                        <div class="mt-4 p-3 rounded bg-red-100 text-red-800">{{ $errors->first() }}</div>
                    @endif

                    <form method="GET" action="{{ route('admin.rights.index') }}" class="mt-6">
                        <div class="flex flex-wrap gap-2">
                            <input type="text" name="q" value="{{ $q }}" class="flex-grow rounded border-gray-300" placeholder="Suche nach Vorname, Nachname oder E-Mail" />
                            <select name="level" class="rounded border-gray-300">
                                <option value="">Alle Admin-Stufen</option>
                                @for($l = 0; $l <= max($maxLevel, 4); $l++)
                                    <option value="{{ $l }}" @selected($level === $l)>Stufe {{ $l }}</option>
                                @endfor
                            </select>
                            <select name="status" class="rounded border-gray-300">
                                <option value="aktiv" @selected($status === 'aktiv')>Aktive Mitglieder</option>
                                <option value="ausgetreten" @selected($status === 'ausgetreten')>Ausgetretene</option>
                                <option value="alle" @selected($status === 'alle')>Alle</option>
                            </select>
                            <button class="p-2 bg-blue-500 rounded shadow text-white" type="submit">Filtern</button>
                            <a class="p-2 bg-gray-300 rounded shadow" href="{{ route('admin.rights.index') }}">Zurücksetzen</a>
                        </div>
                    </form>
                </div>

                <div class="p-6">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">Name</th>
                            <th class="py-2">E-Mail</th>
                            <th class="py-2">Admin-Stufe / Eintritt / Austritt</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr class="border-b">
                                <td class="py-2">{{ trim(($user->vorname ?? '') . ' ' . ($user->nachname ?? '')) }}</td>
                                <td class="py-2">{{ $user->email }}</td>
                                <td class="py-2">
                                    @if((int) $user->admin > $maxLevel)
                                        Stufe {{ $user->admin }} (nicht änderbar)
                                    @else
                                        <form method="POST" action="{{ route('admin.rights.update', $user->id) }}" class="flex flex-wrap items-center gap-2">
                                            @csrf
                                            <input type="hidden" name="q" value="{{ $q }}" />
                                            <input type="hidden" name="level" value="{{ $level }}" />
                                            <input type="hidden" name="status" value="{{ $status }}" />
                                            <input type="hidden" name="page" value="{{ $users->currentPage() }}" />
                                            <select name="admin" class="rounded border-gray-300">
                                                @for($l = 0; $l <= $maxLevel; $l++)
                                                    <option value="{{ $l }}" @selected((int) $user->admin === $l)>{{ $l }}</option>
                                                @endfor
                                            </select>
                                            <label>Eintritt
                                                <input type="date" name="vereinseintritt" value="{{ $user->vereinseintritt?->format('Y-m-d') }}" class="rounded border-gray-300" />
                                            </label>
                                            <label>Austritt
                                                <input type="date" name="vereinsaustritt" value="{{ $user->vereinsaustritt?->format('Y-m-d') }}" class="rounded border-gray-300" />
                                            </label>
                                            <button class="p-2 bg-blue-500 rounded shadow text-white" type="submit">Speichern</button>
                                        </form>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>

                    <div class="mt-4">
                        {{ $users->links() }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
