<div class="p-6 bg-white border-b border-gray-200">
    <div class="mb-4">
        <h3 class="text-lg font-medium text-gray-900">Mitglieder zur {{ config('app.verein_name') }} einladen</h3>
        <p class="text-sm text-gray-600">Senden Sie einen Registrierungslink für {{ config('app.verein_name') }} an ein neues Mitglied.</p>
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
        <div class="flex-grow">
            <x-jet-input type="text" wire:model.defer="label" placeholder="Name / Label (optional wenn E-Mail)" class="w-full" />
            <x-jet-input-error for="label" class="mt-2" />
        </div>
        <x-jet-button>
            Einladung erstellen
        </x-jet-button>
    </form>

    <div class="mt-8">
        <h4 class="text-md font-medium text-gray-900 mb-4">Einladungen</h4>
        <table class="min-w-full divide-y divide-gray-200">
            <thead>
                <tr>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email / Label</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Erstellt am</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                    <th class="px-6 py-3 bg-gray-50 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Link / WhatsApp</th>
                    <th class="px-6 py-3 bg-gray-50 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Aktionen</th>
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
                        @if(!$invitation->email && !$invitation->label)
                            <span class="text-gray-400">-</span>
                        @endif
                    </td>
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
