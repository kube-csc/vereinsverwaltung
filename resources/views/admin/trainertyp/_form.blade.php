@csrf

<div class="space-y-4">
    <div>
        <label class="block text-sm text-gray-600 mb-1">Trainerfunktion</label>
        <input type="text" name="trainerfunktion" value="{{ old('trainerfunktion', $type->trainerfunktion ?? '') }}" class="w-full rounded border-gray-300" required />
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
            <label class="block text-sm text-gray-600 mb-1">Typ aktiv</label>
            <select name="status" class="w-full rounded border-gray-300">
                @php $v = (string)old('status', (string)($type->status ?? 1)); @endphp
                <option value="1" {{ $v === '1' ? 'selected' : '' }}>ja</option>
                <option value="0" {{ $v === '0' ? 'selected' : '' }}>nein</option>
            </select>
        </div>


        <div>
            <label class="block text-sm text-gray-600 mb-1">Trainer öffentlich</label>
            <select name="default_sichtbar" class="w-full rounded border-gray-300">
                @php $v = (string)old('default_sichtbar', (string)($type->default_sichtbar ?? 1)); @endphp
                <option value="1" {{ $v === '1' ? 'selected' : '' }}>ja</option>
                <option value="0" {{ $v === '0' ? 'selected' : '' }}>nein</option>
            </select>
        </div>

        <div>
            <label class="block text-sm text-gray-600 mb-1">Trainer-Veranstaltung</label>
            @php $selectedOrganiser = (string)old('organiser_id', $type->organiser_id ?? 0); @endphp
            <div class="flex gap-2">
                <select id="organiser_id" name="organiser_id" class="w-full rounded border-gray-300">
                <option value="0" {{ $selectedOrganiser === '0' ? 'selected' : '' }}>keine</option>
                @foreach($organisers ?? collect() as $o)
                    @php $label = trim(($o->veranstaltung ?? '') . (isset($o->veranstaltungDomain) && $o->veranstaltungDomain ? ' (' . $o->veranstaltungDomain . ')' : '')); @endphp
                    <option value="{{ $o->id }}" {{ $selectedOrganiser === (string)$o->id ? 'selected' : '' }}>
                        {{ $label !== '' ? $label : 'Organiser' }}
                    </option>
                @endforeach
                </select>
            </div>
        </div>
    </div>
</div>

