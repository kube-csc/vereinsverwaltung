<?php

namespace Tests\Feature;

use App\Http\Livewire\Antragsarten;
use App\Models\ApplicationType;
use App\Models\FundingPot;
use App\Models\FundingProgram;
use App\Models\Role;
use App\Models\User;
use Database\Seeders\ApplicationExamplesSeeder;
use Illuminate\Support\Facades\Artisan;
use Livewire\Livewire;
use Tests\Concerns\PreparesRoleDatabase;
use Tests\TestCase;

class ApplicationTypesTest extends TestCase
{
    use PreparesRoleDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareRoleDatabase();
    }

    private function admin(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::ADMINISTRATOR);

        return $user;
    }

    public function test_administrators_and_funding_managers_can_manage_application_types(): void
    {
        $this->actingAs(User::factory()->create())
            ->get(route('antraege.antragsarten'))
            ->assertForbidden();

        $this->actingAs($this->admin())
            ->get(route('antraege.antragsarten'))
            ->assertOk()
            ->assertSee('Formularbaukasten');

        $manager = User::factory()->create();
        $manager->assignRole(Role::FOERDERTOPFVERWALTER);
        $this->actingAs($manager)->get(route('antraege.antragsarten'))->assertOk();
        Livewire::actingAs($manager)->test(Antragsarten::class)
            ->set('name', 'Manager-Formular')
            ->set('fields', [
                ['id' => null, 'key' => 'anliegen', 'label' => 'Anliegen', 'type' => 'text', 'required' => true, 'options_text' => '', 'help_text' => '', 'aktiv' => true],
            ])
            ->call('saveType')
            ->assertHasNoErrors();
        $this->assertDatabaseHas('application_types', ['name' => 'Manager-Formular']);
    }

    public function test_administrator_can_create_a_type_with_required_select_and_attachment_fields(): void
    {
        Livewire::actingAs($this->admin())->test(Antragsarten::class)
            ->set('name', 'Projektförderung')
            ->set('beschreibung', 'Projektbeschreibung')
            ->set('frist', '2026-11-13')
            ->set('fields', [
                [
                    'id' => null,
                    'key' => 'projekt_titel',
                    'label' => 'Projekttitel',
                    'type' => 'text',
                    'required' => true,
                    'options_text' => '',
                    'help_text' => '',
                    'aktiv' => true,
                ],
                [
                    'id' => null,
                    'key' => 'foerderbereich',
                    'label' => 'Förderbereich',
                    'type' => 'select',
                    'required' => true,
                    'options_text' => "Jugend\nIntegration\nJugend",
                    'help_text' => '',
                    'aktiv' => true,
                ],
                [
                    'id' => null,
                    'key' => 'nachweis',
                    'label' => 'Nachweis',
                    'type' => 'file',
                    'required' => false,
                    'options_text' => '',
                    'help_text' => '',
                    'aktiv' => true,
                ],
            ])
            ->call('saveType')
            ->assertHasNoErrors();

        $type = ApplicationType::with('fields')->firstOrFail();
        $this->assertSame('projektforderung', $type->slug);
        $this->assertSame('2026-11-13', $type->frist->format('Y-m-d'));
        $this->assertSame(['Jugend', 'Integration'], $type->fields->firstWhere('key', 'foerderbereich')->options);
        $this->assertTrue($type->fields->firstWhere('key', 'projekt_titel')->required);
        $this->assertSame('file', $type->fields->firstWhere('key', 'nachweis')->type);
    }

    public function test_application_types_can_be_filtered(): void
    {
        ApplicationType::create(['name' => 'Jugendprojekt', 'slug' => 'jugend', 'aktiv' => true, 'requires_club' => true, 'requires_funding_pot' => true]);
        ApplicationType::create(['name' => 'Hallennutzung', 'slug' => 'halle', 'aktiv' => false, 'requires_club' => false, 'requires_funding_pot' => false]);

        $names = fn ($component) => $component->viewData('applicationTypes')->pluck('name')->all();
        $component = Livewire::actingAs($this->admin())->test(Antragsarten::class);

        $this->assertSame(['Hallennutzung', 'Jugendprojekt'], $names($component));
        $this->assertSame(['Jugendprojekt'], $names($component->set('search', 'jugend')));
        $component->set('search', '');
        $this->assertSame(['Hallennutzung'], $names($component->set('filterStatus', 'inaktiv')));
        $component->set('filterStatus', '');
        $this->assertSame(['Jugendprojekt'], $names($component->set('filterClub', 'ja')));
        $this->assertSame([], $names($component->set('filterPot', 'nein')));
        $this->assertSame(['Hallennutzung', 'Jugendprojekt'], $names($component->call('resetFilters')));
    }

    public function test_existing_fields_can_be_deactivated_without_deleting_definitions(): void
    {
        $type = ApplicationType::create(['name' => 'Typ', 'slug' => 'typ', 'aktiv' => true]);
        $field = $type->fields()->create([
            'key' => 'titel',
            'label' => 'Titel',
            'type' => 'text',
            'required' => true,
            'sort_order' => 0,
            'aktiv' => true,
        ]);
        $type->fields()->create([
            'key' => 'beschreibung',
            'label' => 'Beschreibung',
            'type' => 'textarea',
            'required' => false,
            'sort_order' => 1,
            'aktiv' => true,
        ]);

        Livewire::actingAs($this->admin())->test(Antragsarten::class)
            ->call('editType', $type->id)
            ->call('removeField', 0)
            ->call('saveType')
            ->assertHasNoErrors();

        $this->assertDatabaseHas('application_fields', [
            'id' => $field->id,
            'application_type_id' => $type->id,
            'aktiv' => false,
        ]);
    }

    public function test_field_keys_must_be_unique_and_valid(): void
    {
        Livewire::actingAs($this->admin())->test(Antragsarten::class)
            ->set('name', 'Typ')
            ->set('fields', [
                ['id' => null, 'key' => 'invalid-key', 'label' => 'A', 'type' => 'text', 'required' => false, 'options_text' => '', 'help_text' => '', 'aktiv' => true],
                ['id' => null, 'key' => 'invalid-key', 'label' => 'B', 'type' => 'text', 'required' => false, 'options_text' => '', 'help_text' => '', 'aktiv' => true],
            ])
            ->call('saveType')
            ->assertHasErrors(['fields.0.key', 'fields.1.key']);

        $this->assertSame(0, ApplicationType::count());
    }

    public function test_active_select_fields_require_at_least_one_option(): void
    {
        Livewire::actingAs($this->admin())->test(Antragsarten::class)
            ->set('name', 'Typ')
            ->set('fields', [
                ['id' => null, 'key' => 'bereich', 'label' => 'Bereich', 'type' => 'select', 'required' => true, 'options_text' => " \n ", 'help_text' => '', 'aktiv' => true],
            ])
            ->call('saveType')
            ->assertHasErrors(['fields.0.options_text']);

        $this->assertSame(0, ApplicationType::count());
    }

    public function test_active_application_types_need_at_least_one_active_field(): void
    {
        Livewire::actingAs($this->admin())->test(Antragsarten::class)
            ->set('name', 'Typ')
            ->call('saveType')
            ->assertHasErrors(['fields']);

        $this->assertSame(0, ApplicationType::count());
    }

    public function test_administrator_can_configure_club_selection_only_for_required_templates(): void
    {
        Livewire::actingAs($this->admin())->test(Antragsarten::class)
            ->set('name', 'Mit Vereinszuordnung')
            ->set('requiresClub', true)
            ->set('requiresFundingPot', true)
            ->set('fields', [
                ['id' => null, 'key' => 'projekt', 'label' => 'Projekt', 'type' => 'text', 'required' => true, 'options_text' => '', 'help_text' => '', 'aktiv' => true],
            ])
            ->call('saveType')
            ->assertHasNoErrors();

        $type = ApplicationType::firstOrFail();
        $this->assertTrue($type->requires_club);
        $this->assertTrue($type->requires_funding_pot);

        Livewire::actingAs($this->admin())->test(Antragsarten::class)
            ->call('editType', $type->id)
            ->set('requiresClub', false)
            ->set('requiresFundingPot', false)
            ->call('saveType')
            ->assertHasNoErrors();

        $this->assertFalse($type->fresh()->requires_club);
        $this->assertFalse($type->fresh()->requires_funding_pot);
    }

    public function test_club_requirement_cannot_be_disabled_while_a_pot_uses_a_club_limit(): void
    {
        $type = ApplicationType::create([
            'name' => 'Mit Vereinszuordnung',
            'slug' => 'mit-verein',
            'aktiv' => true,
            'requires_club' => true,
        ]);
        $type->fields()->create([
            'key' => 'projekt',
            'label' => 'Projekt',
            'type' => 'text',
            'required' => true,
            'sort_order' => 0,
            'aktiv' => true,
        ]);
        $pot = FundingProgram::create(['name' => 'Programm'])->pots()->create([
            'name' => 'Topf',
            'laufzeit' => FundingPot::FORTLAUFEND,
            'budget' => 1000,
            'max_applications_per_club' => 2,
        ]);
        $pot->applicationTypes()->attach($type);

        Livewire::actingAs($this->admin())->test(Antragsarten::class)
            ->call('editType', $type->id)
            ->set('requiresClub', false)
            ->call('saveType')
            ->assertHasErrors(['requiresClub']);

        $this->assertTrue($type->fresh()->requires_club);
    }

    public function test_example_seeder_is_repeatable_and_does_not_overwrite_existing_data(): void
    {
        Artisan::call('db:seed', ['--class' => ApplicationExamplesSeeder::class, '--force' => true]);
        $type = ApplicationType::where('slug', 'foerderung-jugendprojekt')->firstOrFail();
        $field = $type->fields()->where('key', 'projekt_name')->firstOrFail();
        $field->update(['label' => 'Eigene Bezeichnung']);
        $type->update(['beschreibung' => 'Eigene Beschreibung']);

        Artisan::call('db:seed', ['--class' => ApplicationExamplesSeeder::class, '--force' => true]);

        $this->assertSame(15, ApplicationType::count());
        $this->assertSame('Eigene Beschreibung', $type->fresh()->beschreibung);
        $this->assertSame('Eigene Bezeichnung', $field->fresh()->label);
        $this->assertFalse(ApplicationType::where('slug', 'foerderung-uebungsleiter')->firstOrFail()->aktiv);
        $this->assertFalse(ApplicationType::where('slug', 'foerderung-sportgeraete')->firstOrFail()->aktiv);
        $this->assertFalse(ApplicationType::where('slug', 'hallennutzung')->firstOrFail()->aktiv);
        $this->assertTrue(ApplicationType::where('slug', 'foerderung-uebungsleiter')->firstOrFail()->requires_club);
        $this->assertTrue(ApplicationType::where('slug', 'foerderung-sportgeraete')->firstOrFail()->requires_club);
        $this->assertTrue(ApplicationType::where('slug', 'foerderung-jugendprojekt')->firstOrFail()->requires_club);
        $this->assertTrue(ApplicationType::where('slug', 'foerderung-jugendprojekt')->firstOrFail()->requires_funding_pot);
        $this->assertFalse(ApplicationType::where('slug', 'hallennutzung')->firstOrFail()->requires_club);
        $this->assertFalse(ApplicationType::where('slug', 'hallennutzung')->firstOrFail()->requires_funding_pot);
        $this->assertSame(3, ApplicationType::whereNotNull('frist')->count());
    }
}
