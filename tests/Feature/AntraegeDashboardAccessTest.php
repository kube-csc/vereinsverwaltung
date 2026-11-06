<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationType;
use App\Models\Role;
use App\Models\User;
use Tests\Concerns\PreparesRoleDatabase;
use Tests\TestCase;

class AntraegeDashboardAccessTest extends TestCase
{
    use PreparesRoleDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareRoleDatabase();
    }

    public function test_administrator_can_open_antraege_dashboard_from_dashboard(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::ADMINISTRATOR);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('antraege.dashboard'), false)
            ->assertDontSee(route('antraege.dashboard.antragsteller'), false);

        $this->get(route('antraege.dashboard'))
            ->assertOk()
            ->assertSee('Förder- und Vereinsanträge im Überblick');
        $this->get(route('antraege.dashboard'))
            ->assertSee('Benutzer und Einladungen');
    }

    public function test_gremium_can_open_review_dashboard_without_funding_management(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);

        $this->actingAs($gremium)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('antraege.dashboard'), false)
            ->assertDontSee(route('antraege.dashboard.antragsteller'), false);

        $this->get(route('antraege.dashboard'))
            ->assertOk()
            ->assertSee('Antragsprüfung und Archiv')
            ->assertDontSee('Förderprogramme und Fördertöpfe')
            ->assertDontSee('Mein Antragsteller-Dashboard')
            ->assertDontSee('Benutzer und Einladungen')
            ->assertDontSee('Konten ohne Rolle');
    }

    public function test_users_without_relevant_roles_cannot_open_management_dashboard(): void
    {
        $user = User::factory()->create();

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertDontSee(route('antraege.dashboard'));

        $this->get(route('antraege.dashboard'))->assertForbidden();
    }

    public function test_applicant_sees_one_antragsverwaltung_entry_linking_to_their_dashboard(): void
    {
        $applicant = User::factory()->create(['admin' => 4]);
        $applicant->assignRole(Role::ANTRAGSTELLER);

        $this->actingAs($applicant)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('antraege.dashboard.antragsteller'), false)
            ->assertDontSee(route('antraege.dashboard'), false)
            ->assertSee('Antragsverwaltung')
            ->assertDontSee('Mein Antragsbereich')
            ->assertDontSee('href="' . route('antraege.dashboard') . '"', false);

        $this->get(route('antraege.dashboard.antragsteller'))
            ->assertOk()
            ->assertSee('Persönliche Antragsübersicht');
    }

    public function test_funding_manager_without_applicant_permission_sees_neither_applicant_nor_admin_box(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole(Role::FOERDERTOPFVERWALTER);

        $this->actingAs($manager)->get(route('antraege.dashboard'))
            ->assertOk()
            ->assertSee('Förderprogramme und Fördertöpfe')
            ->assertDontSee('Mein Antragsteller-Dashboard')
            ->assertSee('Benutzer und Einladungen');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('antraege.dashboard'), false)
            ->assertDontSee(route('antraege.dashboard.antragsteller'), false);
    }

    public function test_legacy_admin_level_four_can_see_and_open_antraege_dashboard(): void
    {
        $user = User::factory()->create(['admin' => 4]);
        $type = ApplicationType::create([
            'name' => 'Jugendprojekt',
            'slug' => 'jugendprojekt',
            'aktiv' => true,
            'requires_funding_pot' => false,
        ]);
        Application::create([
            'application_type_id' => $type->id,
            'user_id' => $user->id,
            'status' => Application::STATUS_APPROVED,
            'decided_at' => now()->startOfYear()->addDays(10),
            'approved_amount' => '1000.50',
            'answers' => [],
        ]);
        Application::create([
            'application_type_id' => $type->id,
            'user_id' => $user->id,
            'status' => Application::STATUS_APPROVED,
            'decided_at' => now()->startOfYear()->subDay(),
            'approved_amount' => '500.00',
            'answers' => [],
        ]);
        Application::create([
            'application_type_id' => $type->id,
            'user_id' => $user->id,
            'status' => Application::STATUS_REJECTED,
            'decided_at' => now(),
            'approved_amount' => '750.00',
            'answers' => [],
        ]);

        $this->actingAs($user)
            ->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('antraege.dashboard'), false)
            ->assertDontSee(route('antraege.dashboard.antragsteller'), false)
            ->assertSee('Antragsverwaltung');

        $this->get(route('antraege.dashboard'))
            ->assertOk()
            ->assertDontSee('Konten ohne Rolle')
            ->assertSee('Bewilligte Fördergelder')
            ->assertSee('1.000,50 €')
            ->assertSee('bisherigen Admin-Stufe');
    }
}
