<div class="p-6 bg-white">
    <h3 class="text-lg font-medium text-gray-900">Benutzer und Rollen</h3>
    <p class="text-sm text-gray-600 mb-4">Konten ohne Rolle haben keine fachlichen Berechtigungen in der Antragsverwaltung.</p>

    @if (session()->has('message'))
        <div class="mb-4 text-sm font-medium text-red-600">{{ session('message') }}</div>
    @endif

    <div class="flex flex-wrap items-center gap-4 mb-4">
        <x-input type="search" wire:model.live.debounce.300ms="search" placeholder="Vorname, Nachname oder E-Mail suchen" class="w-full md:w-1/2" />
        <label class="text-sm text-gray-700">
            Zeilen pro Seite
            <select wire:model.live="perPage" class="ml-1 border-gray-300 rounded-md shadow-sm text-sm">
                @foreach(\App\Http\Livewire\BenutzerRollen::PRO_SEITE as $option)
                    <option value="{{ $option }}">{{ $option }}</option>
                @endforeach
            </select>
        </label>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-4 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase">Benutzer</th>
                    @foreach($roles as $role)
                        <th class="px-4 py-3 bg-gray-50 text-center text-xs font-medium text-gray-500 uppercase">{{ $role->label ?? $role->name }}</th>
                    @endforeach
                </tr>
            </thead>
            <tbody class="divide-y divide-gray-200">
                @forelse($users as $user)
                    <tr wire:key="user-{{ $user->id }}">
                        <td class="px-4 py-3 text-sm text-gray-900">
                            {{ trim($user->vorname . ' ' . $user->nachname) ?: $user->name }}
                            <span class="block text-xs text-gray-500">{{ $user->email }}</span>
                        </td>
                        @foreach($roles as $role)
                            <td class="px-4 py-3 text-center">
                                <input type="checkbox"
                                       class="h-5 w-5 rounded border-2 border-gray-600 text-blue-600 focus:ring-blue-500 cursor-pointer disabled:cursor-not-allowed disabled:border-gray-400 disabled:bg-gray-200"
                                       @checked($user->hasRole($role->name))
                                       @disabled(!in_array($role->name, $assignable, true))
                                       wire:click="toggleRole({{ $user->id }}, '{{ $role->name }}')">
                            </td>
                        @endforeach
                    </tr>
                @empty
                    <tr><td colspan="{{ $roles->count() + 1 }}" class="px-4 py-3 text-center text-sm text-gray-500">Keine Benutzer gefunden.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $users->links() }}
    </div>

    @can('antraege.rechte_verwalten')
        <section class="mt-8 border-t border-gray-200 pt-6">
            <h4 class="text-lg font-semibold text-gray-900">Berechtigungen je Rolle</h4>
            <p class="mb-4 text-sm text-gray-600">Administratoren besitzen immer alle Rechte. Fördertopfverwalter können ausschließlich Gremium- und Antragstellerrollen vergeben.</p>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead>
                        <tr>
                            <th class="bg-gray-50 px-4 py-3 text-left text-xs font-medium uppercase text-gray-500">Rolle</th>
                            @foreach($permissions as $permission)
                                <th class="bg-gray-50 px-4 py-3 text-center text-xs font-medium text-gray-500">{{ $permission->label }}</th>
                            @endforeach
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-200">
                        @foreach($roles as $role)
                            <tr wire:key="role-permissions-{{ $role->id }}">
                                <td class="px-4 py-3 text-sm font-medium text-gray-900">{{ $role->label ?? $role->name }}</td>
                                @foreach($permissions as $permission)
                                    <td class="px-4 py-3 text-center">
                                        <input type="checkbox"
                                               class="h-5 w-5 rounded border-2 border-gray-600 text-blue-600 focus:ring-blue-500 disabled:cursor-not-allowed disabled:bg-gray-200"
                                               @checked($role->name === \App\Models\Role::ADMINISTRATOR || $role->permissions->contains('id', $permission->id))
                                               @disabled($role->name === \App\Models\Role::ADMINISTRATOR)
                                               wire:click="togglePermission({{ $role->id }}, {{ $permission->id }})">
                                    </td>
                                @endforeach
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </section>
    @endcan
</div>
