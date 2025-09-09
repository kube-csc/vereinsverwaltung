<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Regattaverwaltung') }}: {{ Session::get('regattaSelectUeberschrift') }}
        </h2>
    </x-slot>
    <div class="max-w-2xl mx-auto py-8">
        <div class="bg-white shadow rounded-lg p-6">
            <h1 class="text-2xl font-bold mb-4">Regatta Team bearbeiten</h1>
            <form action="{{ route('regattaTeam.update', $regattaTeam->id) }}" method="POST">
                @csrf
                <div class="mb-4">
                    <label for="name" class="block font-semibold mb-1">Teamname:</label>
                    <input type="text" name="name" id="name" value="{{ old('name', $regattaTeam->teamname) }}" class="form-input w-full" required>
                    @error('name') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="verein" class="block font-semibold mb-1">Verein:</label>
                    <input type="text" name="verein" id="verein" value="{{ old('verein', $regattaTeam->verein) }}" class="form-input w-full">
                    @error('verein') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="teamcaptain" class="block font-semibold mb-1">Teamcaptain:</label>
                    <input type="text" name="teamcaptain" id="teamcaptain" value="{{ old('teamcaptain', $regattaTeam->teamcaptain) }}" class="form-input w-full">
                    @error('teamcaptain') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="strasse" class="block font-semibold mb-1">Straße:</label>
                    <input type="text" name="strasse" id="strasse" value="{{ old('strasse', $regattaTeam->strasse) }}" class="form-input w-full">
                    @error('strasse') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="plz" class="block font-semibold mb-1">PLZ:</label>
                    <input type="text" name="plz" id="plz" value="{{ old('plz', $regattaTeam->plz) }}" class="form-input w-full">
                    @error('plz') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="ort" class="block font-semibold mb-1">Ort:</label>
                    <input type="text" name="ort" id="ort" value="{{ old('ort', $regattaTeam->ort) }}" class="form-input w-full">
                    @error('ort') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="telefon" class="block font-semibold mb-1">Telefon:</label>
                    <input type="text" name="telefon" id="telefon" value="{{ old('telefon', $regattaTeam->telefon) }}" class="form-input w-full">
                    @error('telefon') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="email" class="block font-semibold mb-1">E-Mail:</label>
                    <input type="email" name="email" id="email" value="{{ old('email', $regattaTeam->email) }}" class="form-input w-full">
                    @error('email') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="homepage" class="block font-semibold mb-1">Homepage:</label>
                    <input type="text" name="homepage" id="homepage" value="{{ old('homepage', $regattaTeam->homepage) }}" class="form-input w-full">
                    @error('homepage') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="beschreibung" class="block font-semibold mb-1">Beschreibung:</label>
                    <textarea name="beschreibung" id="beschreibung" class="form-input w-full" rows="6">{{ old('beschreibung', $regattaTeam->beschreibung) }}</textarea>
                    @error('beschreibung') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="kommentar" class="block font-semibold mb-1">Kommentar:</label>
                    <textarea name="kommentar" id="kommentar" class="form-input w-full">{{ old('kommentar', $regattaTeam->kommentar) }}</textarea>
                    @error('kommentar') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="gruppe_id" class="block font-semibold mb-1">Wertungsgruppe:</label>
                    <select name="gruppe_id" id="gruppe_id" class="form-input w-full">
                        @foreach($gruppen as $gruppe)
                            <option value="{{ $gruppe->id }}" @if(old('gruppe_id', $regattaTeam->gruppe_id) == $gruppe->id) selected @endif>
                                {{ $gruppe->typ }}
                            </option>
                        @endforeach
                    </select>
                    @error('gruppe_id') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                <div class="mb-4">
                    <label for="status" class="block font-semibold mb-1">Status:</label>
                    <select name="status" id="status" class="form-input w-full">
                        <option value="Neuanmeldung" @if(old('status', $regattaTeam->status ?? 'Neuanmeldung') == 'Neuanmeldung') selected @endif>Neuanmeldung</option>
                        <option value="Warteliste" @if(old('status', $regattaTeam->status) == 'Warteliste') selected @endif>Warteliste</option>
                        <option value="Nicht angetreten" @if(old('status', $regattaTeam->status) == 'Nicht angetreten') selected @endif>Nicht angetreten</option>
                        <option value="Disqualifiziert" @if(old('status', $regattaTeam->status) == 'Disqualifiziert') selected @endif>Disqualifiziert</option>
                        <option value="Ausgeschieden" @if(old('status', $regattaTeam->status) == 'Ausgeschieden') selected @endif>Ausgeschieden</option>
                        <option value="Gelöscht" @if(old('status', $regattaTeam->status) == 'Gelöscht') selected @endif>Gelöscht</option>
                        <option value="Abgemeldet" @if(old('status', $regattaTeam->status) == 'Abgemeldet') selected @endif>Abgemeldet</option>
                    </select>
                    @error('status') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                </div>
                @php
                    $werbungConfig = include base_path('resources/views/textimport/werbung_options.php');
                    $werbungOptions = $werbungConfig['options'];
                    $inactiveOptions = $werbungConfig['inactive'];
                @endphp
                <div class="mb-4">
                    <label for="werbung" class="block font-semibold mb-1">Wie wurde das Team auf die Regatta aufmerksam?</label>
                    <select name="werbung" id="werbung" class="form-input w-full">
                        @foreach($werbungOptions as $key => $option)
                            @php
                                $isInactive = in_array($key, $inactiveOptions);
                                $isSelected = old('werbung', $regattaTeam->werbung) == $key;
                            @endphp
                            @if(!$isInactive || $isSelected)
                                <option value="{{ $key }}" @if($isSelected) selected @endif>
                                    {{ $option }}@if($isInactive) [inaktiv]@endif
                                </option>
                            @endif
                        @endforeach
                    </select>
                    @error('werbung') <span class="text-red-500 text-sm">{{ $message }}</span> @enderror
                    @if(count($inactiveOptions))
                        <div class="text-xs text-gray-500 mt-1">
                            Hinweis: Mit [inaktiv] markierte Optionen können nicht mehr neu ausgewählt werden.
                        </div>
                    @endif
                </div>
                <button type="submit" class="bg-blue-500 text-white px-4 py-2 rounded transition duration-150 ease-in-out hover:bg-blue-700 hover:scale-105">Speichern</button>
                <a href="{{ url()->previous() }}" class="ml-4 bg-gray-300 hover:bg-gray-400 text-gray-800 px-4 py-2 rounded inline-block text-center transition duration-150 ease-in-out">Abbrechen</a>
            </form>
        </div>
    </div>
    @include('_partials.tinymce', ['selector' => '#beschreibung, #kommentar'])
</x-app-layout>
