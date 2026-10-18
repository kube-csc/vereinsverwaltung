<?php

namespace Database\Seeders;

use App\Models\ApplicationType;
use Illuminate\Database\Seeder;

abstract class ApplicationExampleSeeder extends Seeder
{
    /**
     * @return array{name: string, slug: string, beschreibung: string, frist: ?string, requires_club: bool, aktiv: bool, fields: array<int, array<string, mixed>>}
     */
    abstract protected function definition(): array;

    public function run(): void
    {
        $definition = $this->definition();
        $type = ApplicationType::firstOrCreate(
            ['slug' => $definition['slug']],
            [
                'name' => $definition['name'],
                'beschreibung' => $definition['beschreibung'],
                'frist' => $definition['frist'],
                'requires_club' => $definition['requires_club'],
                'aktiv' => $definition['aktiv'],
            ]
        );

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
                ]
            );
        }
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
            'aktiv' => false,
            'fields' => [],
        ];
    }
}
