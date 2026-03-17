<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Trainerverwaltung
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="p-6 sm:px-20 bg-white border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <div>
                            <div class="mt-2 text-2xl">User auswählen</div>
                            <div class="mt-2 text-gray-500">Wähle einen Benutzer aus und hinterlege Trainerfunktionen (Mehrfachauswahl möglich).</div>
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

                    <form method="GET" action="{{ route('admin.trainer.index') }}" class="mt-6">
                        <div class="flex gap-2">
                            <input type="text" name="q" value="{{ $q }}" class="w-full rounded border-gray-300" placeholder="Suche nach Vorname, Nachname oder E-Mail" />
                            <button class="p-2 bg-blue-500 rounded shadow text-white" type="submit">Suchen</button>
                        </div>
                    </form>
                </div>

                <div class="p-6">
                    <table class="min-w-full text-sm">
                        <thead>
                        <tr class="text-left border-b">
                            <th class="py-2">Name</th>
                            <th class="py-2">E-Mail</th>
                            <th class="py-2">Aktion</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($users as $user)
                            <tr class="border-b">
                                <td class="py-2">{{ trim(($user->vorname ?? '') . ' ' . ($user->nachname ?? '')) }}</td>
                                <td class="py-2">{{ $user->email }}</td>
                                <td class="py-2">
                                    <a class="p-2 bg-blue-500 rounded shadow text-white" href="{{ route('admin.trainer.edit', $user->id) }}">Trainerfunktionen bearbeiten</a>
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

