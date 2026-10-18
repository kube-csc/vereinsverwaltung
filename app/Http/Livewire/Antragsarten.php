<?php

namespace App\Http\Livewire;

use App\Models\ApplicationField;
use App\Models\ApplicationType;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Livewire\Component;

class Antragsarten extends Component
{
    public ?int $typeId = null;
    public string $name = '';
    public string $beschreibung = '';
    public string $frist = '';
    public bool $requiresClub = false;
    public bool $requiresFundingPot = false;
    public bool $aktiv = true;
    public array $fields = [];

    public function mount(): void
    {
        $this->authorizeVerwalten();
    }

    private function authorizeVerwalten(): void
    {
        Gate::authorize('antraege.foerdermittel_verwalten');
    }

    public function newType(): void
    {
        $this->authorizeVerwalten();
        $this->resetForm();
    }

    public function editType(int $id): void
    {
        $this->authorizeVerwalten();
        $type = ApplicationType::with('fields')->findOrFail($id);

        $this->resetValidation();
        $this->typeId = $type->id;
        $this->name = $type->name;
        $this->beschreibung = (string) $type->beschreibung;
        $this->frist = $type->frist?->format('Y-m-d') ?? '';
        $this->requiresClub = $type->requires_club;
        $this->requiresFundingPot = $type->requires_funding_pot;
        $this->aktiv = $type->aktiv;
        $this->fields = $type->fields->map(fn (ApplicationField $field) => [
            'id' => $field->id,
            'key' => $field->key,
            'label' => $field->label,
            'type' => $field->type,
            'required' => $field->required,
            'options_text' => implode("\n", $field->options ?? []),
            'help_text' => (string) $field->help_text,
            'aktiv' => $field->aktiv,
        ])->all();
    }

    public function addField(): void
    {
        $this->authorizeVerwalten();
        $this->fields[] = [
            'id' => null,
            'key' => '',
            'label' => '',
            'type' => 'text',
            'required' => false,
            'options_text' => '',
            'help_text' => '',
            'aktiv' => true,
        ];
    }

    public function removeField(int $index): void
    {
        $this->authorizeVerwalten();
        if (! array_key_exists($index, $this->fields)) {
            return;
        }

        if (! empty($this->fields[$index]['id'])) {
            $this->fields[$index]['aktiv'] = false;
            return;
        }

        unset($this->fields[$index]);
        $this->fields = array_values($this->fields);
    }

    public function restoreField(int $index): void
    {
        $this->authorizeVerwalten();
        if (isset($this->fields[$index])) {
            $this->fields[$index]['aktiv'] = true;
        }
    }

    public function saveType(): void
    {
        $this->authorizeVerwalten();

        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'beschreibung' => ['nullable', 'string', 'max:5000'],
            'frist' => ['nullable', 'date'],
            'requiresClub' => ['boolean'],
            'requiresFundingPot' => ['boolean'],
            'aktiv' => ['boolean'],
            'fields' => [
                'array',
                'max:100',
                function ($attribute, $value, $fail) {
                    if ($this->aktiv && collect($value)->where('aktiv', true)->isEmpty()) {
                        $fail('Eine aktive Antragsart benötigt mindestens ein aktives Formularfeld.');
                    }
                },
            ],
            'fields.*.id' => ['nullable', 'integer'],
            'fields.*.key' => ['required', 'string', 'max:100', 'regex:/^[a-z][a-z0-9_]*$/', 'distinct'],
            'fields.*.label' => ['required', 'string', 'max:255'],
            'fields.*.type' => ['required', Rule::in(ApplicationField::TYPES)],
            'fields.*.required' => ['boolean'],
            'fields.*.options_text' => ['nullable', 'string', 'max:5000'],
            'fields.*.help_text' => ['nullable', 'string', 'max:2000'],
            'fields.*.aktiv' => ['boolean'],
        ];

        foreach ($this->fields as $index => $field) {
            if (! empty($field['aktiv']) && ($field['type'] ?? null) === 'select') {
                $rules["fields.{$index}.options_text"] = ['required', 'string', 'max:5000', 'not_regex:/^\s*$/'];
            }

            if (! empty($field['id'])) {
                $rules["fields.{$index}.key"][] = Rule::unique('application_fields', 'key')
                    ->where('application_type_id', $this->typeId)
                    ->ignore((int) $field['id']);
            }
        }

        $data = $this->validate($rules, [
            'fields.*.key.regex' => 'Der Feldschlüssel darf nur Kleinbuchstaben, Zahlen und Unterstriche enthalten und muss mit einem Buchstaben beginnen.',
            'fields.*.key.distinct' => 'Jeder Feldschlüssel darf nur einmal vorkommen.',
            'fields.*.key.unique' => 'Dieser Feldschlüssel wird bereits für diese Antragsart verwendet.',
            'fields.*.options_text.required' => 'Für ein aktives Auswahlfeld muss mindestens eine Auswahlmöglichkeit angegeben werden.',
            'fields.*.options_text.not_regex' => 'Für ein aktives Auswahlfeld muss mindestens eine Auswahlmöglichkeit angegeben werden.',
        ]);

        if ($this->typeId && ! $data['requiresClub']
            && ApplicationType::whereKey($this->typeId)
                ->whereHas('fundingPots', fn ($query) => $query->whereNotNull('max_applications_per_club'))
                ->exists()) {
            $this->addError('requiresClub', 'Die Vereinszuordnung kann nicht deaktiviert werden, solange ein zugeordneter Fördertopf eine Höchstzahl je Verein festlegt.');
            return;
        }

        DB::transaction(function () use ($data) {
            if ($this->typeId) {
                $type = ApplicationType::findOrFail($this->typeId);
                $slug = $type->slug;
            } else {
                $slugBase = Str::slug($data['name']) ?: 'antragsart';
                $slug = $slugBase;
                $suffix = 2;
                while (ApplicationType::where('slug', $slug)->exists()) {
                    $slug = $slugBase . '-' . $suffix++;
                }
                $type = new ApplicationType();
            }

            $type->fill([
                'name' => $data['name'],
                'slug' => $slug,
                'beschreibung' => $data['beschreibung'] ?: null,
                'frist' => $data['frist'] ?: null,
                'requires_club' => $data['requiresClub'],
                'requires_funding_pot' => $data['requiresFundingPot'],
                'aktiv' => $data['aktiv'],
            ])->save();

            foreach ($data['fields'] as $index => $field) {
                $model = ! empty($field['id'])
                    ? $type->fields()->whereKey($field['id'])->firstOrFail()
                    : new ApplicationField();
                $options = in_array($field['type'], ['select'], true)
                    ? collect(preg_split('/\r\n|\r|\n/', $field['options_text'] ?? ''))
                        ->map(fn ($option) => trim($option))
                        ->filter()
                        ->unique()
                        ->values()
                        ->all()
                    : null;

                $model->fill([
                    'key' => $field['key'],
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'required' => (bool) $field['required'],
                    'options' => $options ?: null,
                    'help_text' => $field['help_text'] ?: null,
                    'sort_order' => $index,
                    'aktiv' => (bool) $field['aktiv'],
                ]);
                $type->fields()->save($model);
            }
        });

        $this->resetForm();
        session()->flash('message', 'Antragsart und Formularfelder gespeichert.');
    }

    public function toggleType(int $id): void
    {
        $this->authorizeVerwalten();
        $type = ApplicationType::findOrFail($id);
        $type->update(['aktiv' => ! $type->aktiv]);
    }

    public function cancel(): void
    {
        $this->authorizeVerwalten();
        $this->resetForm();
    }

    private function resetForm(): void
    {
        $this->reset(['typeId', 'name', 'beschreibung', 'frist', 'requiresClub', 'requiresFundingPot', 'fields']);
        $this->aktiv = true;
        $this->resetValidation();
    }

    public function render()
    {
        return view('livewire.antragsarten', [
            'applicationTypes' => ApplicationType::with('fields')->orderBy('name')->get(),
            'fieldTypes' => ApplicationField::TYPES,
        ]);
    }
}
