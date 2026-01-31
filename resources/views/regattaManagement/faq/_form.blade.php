@php
    /** @var \App\Models\Faq|null $faq */
    /** @var \Illuminate\Support\Collection|array|null $categories */
    /** @var int|null $eventGroupId */
    $categories = $categories ?? collect();
    $eventGroupId = $eventGroupId ?? ($faq->eventGroup_id ?? null);

    // Checkbox ist aktiv, wenn: old('use_event') true ODER (kein old vorhanden und im Model event_id gesetzt)
    $useEventOld = old('use_event');
    $useEvent = $useEventOld !== null
        ? (bool) $useEventOld
        : !empty($faq?->event_id);

    // Event-ID kommt aus der ausgewählten Regatta in der Session
    $sessionEventId = \Illuminate\Support\Facades\Session::get('regattaSelectId');
@endphp

{{-- eventGroup ist Pflicht (kommt aus der gewählten Regatta / Session) --}}
<input type="hidden" id="eventGroup_id" name="eventGroup_id" value="{{ old('eventGroup_id', $eventGroupId) }}">

{{-- event_id ist nicht auswählbar: wird nur über die Checkbox gesetzt/geleert --}}
<input type="hidden" id="event_id" name="event_id" value="{{ old('event_id', $faq->event_id ?? '') }}">

<script>
    document.addEventListener('DOMContentLoaded', function () {
        var useEvent = document.getElementById('use_event');
        var eventIdInput = document.getElementById('event_id');

        // Session-Regatta-ID (fest)
        var sessionEventId = @json($sessionEventId);

        function syncEventId() {
            if (!useEvent || !eventIdInput) return;

            if (useEvent.checked) {
                eventIdInput.value = sessionEventId || '';
            } else {
                // beim Abwählen: serverseitig wird event_id ebenfalls auf NULL erzwungen
                eventIdInput.value = '';
            }
        }

        syncEventId();
        if (useEvent) useEvent.addEventListener('change', syncEventId);
    });
</script>

<div class="my-4">
    <label for="category">Kategorie:</label>
    <input type="text"
           list="faq_categories"
           class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('category') ? 'bg-red-300' : '' }}"
           id="category" name="category" value="{{ old('category', $faq->category ?? '') }}" maxlength="100"
           placeholder="Kategorie wählen oder neu eingeben">

    <datalist id="faq_categories">
        @foreach($categories as $cat)
            <option value="{{ $cat }}"></option>
        @endforeach
    </datalist>

    <small class="form-text text-danger">{!! $errors->first('category') !!}</small>
</div>

<div class="my-4">
    <label for="question">Frage:</label>
    <input type="text" class="w-full border rounded shadow p-2 mr-2 my-2 {{ $errors->has('question') ? 'bg-red-300' : '' }}"
           id="question" name="question" value="{{ old('question', $faq->question ?? '') }}">
    <small class="form-text text-danger">{!! $errors->first('question') !!}</small>
</div>

<div class="my-4">
    <label for="answer_html">Antwort (HTML):</label>
    <textarea rows="10" name="answer_html" class="w-full rounded border shadow p-2 mr-2 my-2 {{ $errors->has('answer_html') ? 'bg-red-300' : '' }}">{{ old('answer_html', $faq->answer_html ?? '') }}</textarea>
    <small class="form-text text-danger">{!! $errors->first('answer_html') !!}</small>
</div>

{{-- Sortierung (sort_order) wird beim Anlegen automatisch ermittelt --}}

<div class="my-4">
    <label class="font-semibold">Zuordnung:</label>

    <div class="mt-2">
        <label for="use_event" class="inline-flex items-center">
            <input type="checkbox"
                   id="use_event" name="use_event" value="1"
                   class="border rounded shadow p-2 mr-2 {{ $errors->has('event_id') ? 'bg-red-300' : '' }}"
                   {{ $useEvent ? 'checked' : '' }}>
            <span>Event zuordnen</span>
        </label>

        <div class="text-xs text-gray-500 mt-1">
            Event wird automatisch aus der ausgewählten Regatta übernommen.
        </div>

        <small class="form-text text-danger">{!! $errors->first('event_id') !!}</small>
        <small class="form-text text-danger">{!! $errors->first('eventGroup_id') !!}</small>
    </div>
</div>

<div class="my-4">
    <label for="is_active">Aktiv:</label>
    <input type="checkbox"
           class="border rounded shadow p-2 mr-2 my-2 {{ $errors->has('is_active') ? 'bg-red-300' : '' }}"
            id="is_active" name="is_active" value="1" {{ old('is_active', ($faq->is_active ?? true)) ? 'checked' : '' }}>
    <small class="form-text text-danger">{!! $errors->first('is_active') !!}</small>
</div>
