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
            @php $selectedOrganiser = (string)old('default_organiser_id', $type->default_organiser_id ?? 0); @endphp
            <div class="flex gap-2">
                <select id="default_organiser_id" name="default_organiser_id" class="w-full rounded border-gray-300">
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

        <div>
            <label class="block text-sm text-gray-600 mb-1">Trainer-Abteilung</label>
            @php $selectedSection = (string)old('default_sportSection_id', $type->default_sportSection_id ?? 0); @endphp
            <select id="default_sportSection_id" name="default_sportSection_id" class="w-full rounded border-gray-300">
                <option value="0" {{ $selectedSection === '0' ? 'selected' : '' }}>keine</option>
                @foreach($sportSections ?? collect() as $s)
                    @php
                        $label = trim(($s->abteilung ?? '') . (isset($s->domain) && $s->domain ? ' (' . $s->domain . ')' : ''));
                    @endphp
                    <option value="{{ $s->id }}" {{ $selectedSection === (string)$s->id ? 'selected' : '' }}>
                        {{ $label !== '' ? $label : 'SportSection' }}
                    </option>
                @endforeach
            </select>
            @php
                $sportSectionsEmpty = ($sportSections ?? collect())->isEmpty();
            @endphp
            @if($sportSectionsEmpty)
                <div class="text-xs text-red-600 mt-1" id="sportSectionHint">Für den ausgewählten Organiser sind keine Abteilungen (SportSections) hinterlegt.</div>
            @else
                <div class="text-xs text-gray-500 mt-1" id="sportSectionHint"></div>
            @endif
        </div>
    </div>
</div>

<script>
    (function () {
        var organiserSelect = document.getElementById('default_organiser_id');
        var sectionSelect = document.getElementById('default_sportSection_id');
        var hint = document.getElementById('sportSectionHint');

        // Server-seitig vorselektierten Wert merken, damit er nach dem dynamischen Nachladen
        // wieder gesetzt werden kann (sonst bleibt immer die erste Option ausgewählt).
        var preselectedSectionId = sectionSelect ? String(sectionSelect.value || '0') : '0';

        if (!organiserSelect || !sectionSelect) {
            return;
        }

        function setHint(text, isError) {
            if (!hint) return;
            hint.textContent = text || '';
            hint.className = 'text-xs mt-1 ' + (isError ? 'text-red-600' : 'text-gray-500');
        }

        function clearOptions() {
            while (sectionSelect.options.length > 0) {
                sectionSelect.remove(0);
            }
        }

        async function loadSections() {
            var organiserId = organiserSelect.value;
            if (!organiserId || organiserId === '0') {
                // Organiser "keine" -> SportSections auf nur "keine" zurücksetzen
                clearOptions();
                var noneOptReset = document.createElement('option');
                noneOptReset.value = '0';
                noneOptReset.textContent = 'keine';
                sectionSelect.appendChild(noneOptReset);
                // Bei "keine" ist die Abteilung immer 0.
                sectionSelect.value = '0';
                setHint('', false);
                return;
            }

            setHint('Lade SportSections…', false);

            try {
                var url = '/admin/trainertypen/organiser/' + encodeURIComponent(organiserId) + '/sportsections';
                var res = await fetch(url, { headers: { 'Accept': 'application/json' } });
                if (!res.ok) {
                    throw new Error('HTTP ' + res.status);
                }

                var data = await res.json();
                clearOptions();

                // "keine" bleibt immer als erste Option verfügbar
                var noneOpt = document.createElement('option');
                noneOpt.value = '0';
                noneOpt.textContent = 'keine';
                sectionSelect.appendChild(noneOpt);

                if (!Array.isArray(data) || data.length === 0) {
                    setHint('Für den ausgewählten Organiser sind keine Abteilungen (SportSections) hinterlegt.', true);
                    return;
                }

                data.forEach(function (s) {
                    var opt = document.createElement('option');
                    opt.value = String(s.id);
                    opt.textContent = String(s.label || 'SportSection');
                    sectionSelect.appendChild(opt);
                });

                // Falls ein Wert vorselektiert war und im neuen Options-Set vorhanden ist, setzen.
                // (z.B. beim Edit-Formular oder nach Validation-Errors via old())
                if (preselectedSectionId && preselectedSectionId !== '0') {
                    var hasOption = Array.prototype.some.call(sectionSelect.options, function (o) {
                        return String(o.value) === preselectedSectionId;
                    });
                    if (hasOption) {
                        sectionSelect.value = preselectedSectionId;
                    }
                }

                setHint('', false);
            } catch (e) {
                clearOptions();
                setHint('Fehler beim Laden der SportSections (' + (e && e.message ? e.message : 'unbekannt') + ').', true);
            }
        }

        organiserSelect.addEventListener('change', loadSections);
        // Initial laden, falls bereits ein Organiser ausgewählt ist
        loadSections();
    })();
</script>

