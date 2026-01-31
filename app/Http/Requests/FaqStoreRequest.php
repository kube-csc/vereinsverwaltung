<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class FaqStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category' => ['required', 'string', 'max:100'],
            'question' => ['required', 'string', 'max:255'],
            'answer_html' => ['required', 'string'],
            'is_active' => ['nullable', 'boolean'],

            // Zuordnung (kommt beim Speichern aus der Session/Controller-Logik)
            'eventGroup_id' => ['nullable', 'integer', 'exists:event_groups,id'],
            'use_event' => ['nullable', 'boolean'],
            'event_id' => ['nullable', 'integer', 'exists:events,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $useEvent = $this->boolean('use_event');

        $this->merge([
            'is_active' => $this->boolean('is_active'),
            'use_event' => $useEvent,

            // Wenn Checkbox nicht aktiv ist, erzwingen wir serverseitig event_id = null
            'event_id' => $useEvent ? $this->input('event_id') : null,
        ]);
    }
}
