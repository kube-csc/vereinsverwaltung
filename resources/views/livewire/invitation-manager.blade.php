<div class="p-6 bg-white border-b border-gray-200">
    <div class="mb-4">
        <h3 class="text-lg font-medium text-gray-900">Mitglieder einladen</h3>
        <p class="text-sm text-gray-600">Senden Sie einen Registrierungslink an ein neues Mitglied.</p>
    </div>

    @if (session()->has('message'))
        <div class="mb-4 text-sm font-medium text-green-600">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="sendInvitation" class="flex items-center space-x-4">
        <div class="flex-grow">
            <x-jet-input type="email" wire:model.defer="email" placeholder="E-Mail Adresse" class="w-full" />
            <x-jet-input-error for="email" class="mt-2" />
        </div>
        <x-jet-button>
            Einladung senden
        </x-jet-button>
    </form>

    <div class="mt-8">
        <h4 class="text-md font-medium text-gray-900 mb-4">Einladungen</h4>
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Erstellt am</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link</th>
                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invitations as $invitation)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $invitation->email }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invitation->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($invitation->registered_at)
                            <span class="text-green-600">Registriert am {{ $invitation->registered_at->format('d.m.Y') }}</span>
                        @else
                            <span class="text-orange-600">Offen</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if(!$invitation->registered_at)
                            <input type="text" readonly value="{{ route('register', ['token' => $invitation->token]) }}"
                                   class="text-xs border-gray-300 rounded-md shadow-sm w-64"
                                   onclick="this.select();">
                        @else
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                        <button wire:click="deleteInvitation({{ $invitation->id }})" class="text-red-600 hover:text-red-900">Löschen</button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="px-6 py-4 text-center text-sm text-gray-500">Keine Einladungen gefunden.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
