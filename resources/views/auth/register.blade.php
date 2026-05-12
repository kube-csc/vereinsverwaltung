<x-guest-layout>
    <x-jet-authentication-card>
        <x-slot name="logo">
            <x-jet-authentication-card-logo />
        </x-slot>

        <x-jet-validation-errors class="mb-4" />

        <form method="POST" action="{{ route('register') }}">
            @csrf
            <input type="hidden" name="token" value="{{ request('token') }}">

            @php
                $invitation = \App\Models\Invitation::where('token', request('token'))->first();
                $isValidInvitation = $invitation && !$invitation->registered_at;
                $sportSections = \App\Models\SportSection::where('status', '!=', 0)->orderBy('abteilung')->get();
            @endphp

            @if(!$invitation)
                <div class="mb-4 text-sm text-red-600">
                    {{ __('Eine Registrierung ist nur mit einer gültigen Einladung möglich.') }}
                </div>
            @elseif($invitation->registered_at)
                <div class="mb-4 text-sm text-red-600">
                    {{ __('Diese Einladung wurde bereits verwendet.') }}
                </div>
            @endif

            <div>
                <x-jet-label for="vorname" value="{{ __('Vorname') }}" />
                <x-jet-input id="vorname" class="block mt-1 w-full" type="text" name="vorname" :value="old('vorname')" required autofocus autocomplete="given-name" :disabled="!$isValidInvitation" />
            </div>

            <div class="mt-4">
                <x-jet-label for="nachname" value="{{ __('Nachname') }}" />
                <x-jet-input id="nachname" class="block mt-1 w-full" type="text" name="nachname" :value="old('nachname')" required autocomplete="family-name" :disabled="!$isValidInvitation" />
            </div>

            <div class="mt-4">
                <x-jet-label for="geschlecht" value="{{ __('Geschlecht') }}" />
                <select id="geschlecht" name="geschlecht" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" required {{ !$isValidInvitation ? 'disabled' : '' }}>
                    <option value="">{{ __('Bitte wählen...') }}</option>
                    <option value="m" {{ old('geschlecht') == 'm' ? 'selected' : '' }}>{{ __('Männlich') }}</option>
                    <option value="w" {{ old('geschlecht') == 'w' ? 'selected' : '' }}>{{ __('Weiblich') }}</option>
                    <option value="d" {{ old('geschlecht') == 'd' ? 'selected' : '' }}>{{ __('Divers') }}</option>
                </select>
            </div>

            <div class="mt-4">
                <x-jet-label for="sportSections_id" value="{{ __('Sportabteilung') }}" />
                <select id="sportSections_id" name="sportSections_id" class="border-gray-300 focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50 rounded-md shadow-sm block mt-1 w-full" required {{ !$isValidInvitation ? 'disabled' : '' }}>
                    <option value="">{{ __('Bitte wählen...') }}</option>
                    @foreach($sportSections as $section)
                        <option value="{{ $section->id }}" {{ old('sportSections_id') == $section->id ? 'selected' : '' }}>
                            {{ $section->abteilung }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-4">
                <x-jet-label for="email" value="{{ __('Email') }}" />
                <x-jet-input id="email" class="block mt-1 w-full" type="email" name="email" :value="old('email', $invitation?->email)" required :readonly="$invitation" :disabled="!$isValidInvitation" />
            </div>

            <div class="mt-4">
                <x-jet-label for="password" value="{{ __('Password') }}" />
                <x-jet-input id="password" class="block mt-1 w-full" type="password" name="password" required autocomplete="new-password" :disabled="!$isValidInvitation" />
            </div>

            <div class="mt-4">
                <x-jet-label for="password_confirmation" value="{{ __('Confirm Password') }}" />
                <x-jet-input id="password_confirmation" class="block mt-1 w-full" type="password" name="password_confirmation" required autocomplete="new-password" :disabled="!$isValidInvitation" />
            </div>

            @if (Laravel\Jetstream\Jetstream::hasTermsAndPrivacyPolicyFeature())
                <div class="mt-4">
                    <x-jet-label for="terms">
                        <div class="flex items-center">
                            <x-jet-checkbox name="terms" id="terms" :disabled="!$isValidInvitation" />

                            <div class="ml-2">
                                {!! __('I agree to the :terms_of_service and :privacy_policy', [
                                        'terms_of_service' => '<a target="_blank" href="'.route('terms.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Terms of Service').'</a>',
                                        'privacy_policy' => '<a target="_blank" href="'.route('policy.show').'" class="underline text-sm text-gray-600 hover:text-gray-900">'.__('Privacy Policy').'</a>',
                                ]) !!}
                            </div>
                        </div>
                    </x-jet-label>
                </div>
            @endif

            <div class="flex items-center justify-end mt-4">
                <a class="underline text-sm text-gray-600 hover:text-gray-900" href="{{ route('login') }}">
                    {{ __('Already registered?') }}
                </a>

                <x-jet-button class="ml-4" :disabled="!$isValidInvitation">
                    {{ __('Register') }}
                </x-jet-button>
            </div>
        </form>
    </x-jet-authentication-card>
</x-guest-layout>
