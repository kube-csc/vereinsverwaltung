@php
    /** @var \App\Models\Faq|null $faq */
    /** @var \Illuminate\Support\Collection|array|null $categories */
    $categories = $categories ?? collect();
@endphp

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
    <label for="is_active">Aktiv:</label>
    <input type="checkbox"
           class="border rounded shadow p-2 mr-2 my-2 {{ $errors->has('is_active') ? 'bg-red-300' : '' }}"
            id="is_active" name="is_active" value="1" {{ old('is_active', ($faq->is_active ?? true)) ? 'checked' : '' }}>
    <small class="form-text text-danger">{!! $errors->first('is_active') !!}</small>
</div>
