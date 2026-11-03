<div class="space-y-6 bg-white p-6">
    <header class="rounded-xl bg-gray-300 px-5 py-4 text-gray-900">
        <p class="text-xs font-bold uppercase tracking-widest text-gray-700">Zugänge</p>
        <h3 class="mt-1 text-lg font-bold">Mitglieder zur {{ config('app.verein_name') }} einladen</h3>
        <p class="mt-1 text-sm text-gray-800">Senden Sie einen Registrierungslink für {{ config('app.verein_name') }} an ein neues Mitglied.</p>
    </header>

    @if (session()->has('message'))
        <div class="mb-4 text-sm font-medium text-green-600">
            {{ session('message') }}
        </div>
    @endif

    <form wire:submit.prevent="sendInvitation" class="grid grid-cols-1 items-end gap-4 rounded-xl border-2 border-blue-200 bg-blue-50 p-5 lg:grid-cols-2">
        <div class="flex-grow">
            <x-input type="email" wire:model="email" placeholder="E-Mail Adresse" class="w-full" />
            <x-input-error for="email" class="mt-2" />
        </div>
        <div class="flex-grow">
            <x-input type="text" wire:model="label" placeholder="Name / Label (optional wenn E-Mail)" class="w-full" />
            <x-input-error for="label" class="mt-2" />
        </div>
        @if($assignableRoles->isNotEmpty())
            <div class="text-sm text-gray-700 lg:col-span-2">
                <span class="block text-xs text-gray-500">Rollen nach der Registrierung</span>
                @foreach($assignableRoles as $role)
                    <label class="inline-flex items-center mr-3">
                        <input type="checkbox" wire:model="selectedRoles" value="{{ $role->name }}" class="h-5 w-5 rounded border-2 border-gray-600 text-blue-600 focus:ring-blue-500 cursor-pointer">
                        <span class="ml-1">{{ $role->label ?? $role->name }}</span>
                    </label>
                @endforeach
            </div>
        @endif
        <div class="lg:col-span-2">
        <x-button>
            Einladung erstellen
        </x-button>
        </div>
    </form>

    <div class="overflow-hidden rounded-xl border border-gray-200">
        <h4 class="bg-blue-50 px-4 py-3 text-md font-bold text-blue-900">Einladungen</h4>
        <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="bg-blue-50 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-blue-900">Email / Label</th>
                    <th class="bg-blue-50 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-blue-900">Erstellt am</th>
                    <th class="bg-blue-50 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-blue-900">Status</th>
                    <th class="bg-blue-50 px-6 py-3 text-left text-xs font-semibold uppercase tracking-wider text-blue-900">Link / WhatsApp</th>
                    <th class="bg-blue-50 px-6 py-3 text-right text-xs font-semibold uppercase tracking-wider text-blue-900">Aktionen</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($invitations as $invitation)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        @if($invitation->email)
                            {{ $invitation->email }}
                        @endif
                        @if($invitation->label)
                            <span class="text-xs text-gray-500 block italic">Name: {{ $invitation->label }}</span>
                        @endif
                        @if($invitation->roles->isNotEmpty())
                            <span class="text-xs text-gray-500 block">Rollen: {{ $invitation->roles->map(fn ($r) => $r->label ?? $r->name)->implode(', ') }}</span>
                        @endif
                        @if(!$invitation->email && !$invitation->label)
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">{{ $invitation->created_at->format('d.m.Y H:i') }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if($invitation->registered_at)
                            <span class="inline-flex rounded-full bg-green-100 px-2 py-1 text-xs font-semibold text-green-800">Registriert am {{ $invitation->registered_at->format('d.m.Y') }}</span>
                        @else
                            <span class="inline-flex rounded-full bg-yellow-100 px-2 py-1 text-xs font-semibold text-yellow-800">Offen</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                        @if(!$invitation->registered_at)
                            @php
                                $registerUrl = route('register', ['token' => $invitation->token]);
                                $shareText = "Hallo, hier ist dein Link zur Registrierung bei " . config('app.verein_name') . ": " . $registerUrl;
                                $whatsappUrl = "https://wa.me/?text=" . urlencode($shareText);
                            @endphp
                            <div class="flex items-center space-x-2">
                                <input type="text" readonly value="{{ $registerUrl }}"
                                       class="text-xs border-gray-300 rounded-md shadow-sm w-48"
                                       onclick="this.select();">
                                <a href="{{ $whatsappUrl }}" target="_blank" title="Per WhatsApp teilen" class="text-green-500 hover:text-green-600">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-6 w-6" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M.057 24l1.687-6.163c-1.041-1.804-1.588-3.849-1.587-5.946.003-6.556 5.338-11.891 11.893-11.891 3.181.001 6.167 1.24 8.413 3.488 2.246 2.248 3.484 5.232 3.484 8.412-.003 6.557-5.338 11.892-11.893 11.892-1.997-.001-3.951-.5-5.688-1.448l-6.309 1.656zm6.224-3.82c1.516.903 3.125 1.383 4.773 1.384h.005c5.338 0 9.682-4.344 9.685-9.684.001-2.586-1.006-5.017-2.834-6.848-1.828-1.831-4.259-2.839-6.846-2.84h-.005c-5.338 0-9.682 4.344-9.685 9.686 0 1.761.477 3.479 1.381 4.996l-1.056 3.856 3.961-1.04zm11.433-7.01c-.303-.151-1.794-.885-2.072-.985-.278-.1-.482-.151-.683.151-.202.303-.783.985-.96.1.18-.303.18-.303.18.101-.303.05-.606-.025-.1-.202-.303-.151-.834-.885-1.137-1.186-.303-.301-.684-.101-.985-.1.303-.151.202-.404.1-.505-.101-.101-.482-.151-.684.151zM8.533 8.35c-.202-.303-.422-.31-.616-.318-.16-.007-.343-.008-.526-.008-.184 0-.482.069-.733.344-.251.275-.959.937-.959 2.285 0 1.348.981 2.65 1.118 2.834.137.184 1.93 2.947 4.676 4.136.654.283 1.164.453 1.562.58.657.208 1.254.179 1.727.108.527-.079 1.794-.733 2.047-1.442.253-.709.253-1.314.177-1.442-.076-.128-.278-.203-.581-.354z"/>
                                    </svg>
                                </a>
                            </div>
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
</div>
