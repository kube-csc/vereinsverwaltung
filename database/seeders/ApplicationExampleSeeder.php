<?php

namespace Database\Seeders;

use App\Models\ApplicationType;
use Illuminate\Database\Seeder;

abstract class ApplicationExampleSeeder extends Seeder
{
    /**
     * @return array{name: string, slug: string, beschreibung: string, frist: ?string, requires_club: bool, requires_funding_pot: bool, aktiv: bool, fields: array<int, array<string, mixed>>}
     */
    abstract protected function definition(): array;

    public function run(): void
    {
        $definition = $this->definition();
        $type = ApplicationType::where('slug', $definition['slug'])->first();

        if ($type && ! $this->isUnchangedPlaceholder($type, $definition)) {
            return;
        }

        $attributes = [
            'name' => $definition['name'],
            'beschreibung' => $definition['beschreibung'],
            'frist' => $definition['frist'],
            'requires_club' => $definition['requires_club'],
            'requires_funding_pot' => $definition['requires_funding_pot'],
            'aktiv' => $definition['aktiv'],
            'reviewer_note' => $definition['reviewer_note'] ?? null,
        ];

        if ($type) {
            $type->update($attributes);
        } else {
            $type = ApplicationType::create($attributes + ['slug' => $definition['slug']]);
        }

        // Antragsteller-Felder
        foreach ($definition['fields'] as $order => $field) {
            $type->fields()->firstOrCreate(
                ['key' => $field['key']],
                [
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'required' => $field['required'] ?? false,
                    'options' => $field['options'] ?? null,
                    'help_text' => $field['help_text'] ?? null,
                    'sort_order' => $order,
                    'aktiv' => true,
                    'for_reviewer' => false,
                ]
            );
        }

        // Prüfer-Felder
        $reviewerFields = $definition['reviewer_fields'] ?? [];
        foreach ($reviewerFields as $order => $field) {
            $type->fields()->firstOrCreate(
                ['key' => $field['key']],
                [
                    'label' => $field['label'],
                    'type' => $field['type'],
                    'required' => $field['required'] ?? false,
                    'options' => $field['options'] ?? null,
                    'help_text' => $field['help_text'] ?? null,
                    'sort_order' => $order,
                    'aktiv' => true,
                    'for_reviewer' => true,
                ]
            );
        }
    }

    private function isUnchangedPlaceholder(ApplicationType $type, array $definition): bool
    {
        $placeholder = $this->placeholderDefinition($definition['name'], $definition['slug']);

        return $type->name === $placeholder['name']
            && $type->beschreibung === $placeholder['beschreibung']
            && $type->frist === null
            && ! $type->requires_club
            && ! $type->requires_funding_pot
            && ! $type->aktiv
            && ! $type->fields()->exists()
            && ! $type->fundingPots()->exists();
    }

    protected function sharedFields(): array
    {
        return [
            ['key' => 'verein_name', 'label' => 'Name des Vereins', 'type' => 'text', 'required' => true],
            ['key' => 'verein_anschrift', 'label' => 'Anschrift des Vereins', 'type' => 'textarea', 'required' => true],
            ['key' => 'antragsort', 'label' => 'Ort der Antragstellung', 'type' => 'text', 'required' => true],
            ['key' => 'antragsdatum', 'label' => 'Antragsdatum', 'type' => 'date', 'required' => true],
            ['key' => 'vertretungsberechtigte_person', 'label' => 'Vorsitzende/r gemäß BGB § 26 bzw. Vereinssatzung', 'type' => 'text', 'required' => true],
        ];
    }

    protected function projectFields(string $projectLabel): array
    {
        return [
            ['key' => 'projekt_name', 'label' => 'Name des Projekts', 'type' => 'text', 'required' => true],
            ['key' => 'beantragter_zuschuss', 'label' => 'Beantragter Zuschuss in Euro', 'type' => 'number', 'required' => true],
            ['key' => 'gesamtkosten', 'label' => 'Gesamtkosten in Euro', 'type' => 'number', 'required' => true],
            ['key' => 'projektbeschreibung', 'label' => 'Projektbeschreibung und Begründung', 'type' => 'textarea', 'required' => true, 'help_text' => $projectLabel],
            ['key' => 'projektbeginn', 'label' => 'Projektbeginn', 'type' => 'date', 'required' => false],
            ['key' => 'projektende', 'label' => 'Projektende', 'type' => 'date', 'required' => false],
            ['key' => 'teilnehmerzahl', 'label' => 'Ungefähre Teilnehmerzahl', 'type' => 'number', 'required' => true],
            ['key' => 'verwendungsnachweis_zusage', 'label' => 'Ich sage zu, nach Projektabschluss einen schriftlichen Verwendungsnachweis vorzulegen.', 'type' => 'checkbox', 'required' => true],
            ['key' => 'projektbeschreibung_anlage', 'label' => 'Projektbeschreibung als Anlage', 'type' => 'file', 'required' => false],
        ];
    }

    protected function placeholderDefinition(string $name, string $slug): array
    {
        return [
            'name' => $name,
            'slug' => $slug,
            'beschreibung' => 'Noch nicht konfiguriert. Ergänzen Sie Formularfelder und gegebenenfalls eine Frist, bevor Sie diese Antragsart aktivieren.',
            'frist' => null,
            'requires_club' => false,
            'requires_funding_pot' => false,
            'aktiv' => false,
            'fields' => [],
        ];
    }

    /**
     * @param array<int, array<string, mixed>> $fields Antragsteller-Felder
     * @param array<int, array<string, mixed>> $reviewerFields Prüfer-Felder (optional)
     * @param string|null $reviewerNote Hilfstext für Prüfer (optional)
     */
    protected function exampleDefinition(string $name, string $slug, string $purpose, array $fields, array $reviewerFields = [], ?string $reviewerNote = null): array
    {
        $definition = $this->placeholderDefinition($name, $slug);
        $definition['beschreibung'] = "Unverbindliche Beispielvorlage für {$purpose}. Vor Aktivierung fachlich prüfen und anpassen.";
        $definition['fields'] = $fields;
        $definition['reviewer_fields'] = $reviewerFields;
        $definition['reviewer_note'] = $reviewerNote;

        return $definition;
    }
}
