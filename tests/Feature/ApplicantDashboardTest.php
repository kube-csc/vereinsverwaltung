<?php

namespace Tests\Feature;

use App\Models\Application;
use App\Models\ApplicationType;
use App\Models\FundingPot;
use App\Models\FundingProgram;
use App\Models\Role;
use App\Models\User;
use Tests\Concerns\PreparesRoleDatabase;
use Tests\TestCase;

class ApplicantDashboardTest extends TestCase
{
    use PreparesRoleDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareRoleDatabase();
    }

    public function test_applicant_sees_only_personal_dashboard_statistics_and_available_types(): void
    {
        $applicant = User::factory()->create();
        $applicant->assignRole(Role::ANTRAGSTELLER);

        $fundingType = ApplicationType::create([
            'name' => 'Jugendprojekt',
            'slug' => 'jugendprojekt',
            'aktiv' => true,
            'requires_funding_pot' => true,
        ]);
        $otherType = ApplicationType::create([
            'name' => 'Sonstiger Antrag',
            'slug' => 'sonstiger-antrag',
            'aktiv' => true,
            'requires_funding_pot' => false,
        ]);
        $closedType = ApplicationType::create([
            'name' => 'Geschlossene Förderung',
            'slug' => 'geschlossen',
            'aktiv' => true,
            'requires_funding_pot' => true,
        ]);

        $program = FundingProgram::create(['name' => 'Jugendförderung']);
        $openPot = $program->pots()->create([
            'name' => 'Jugendtopf',
            'laufzeit' => FundingPot::FORTLAUFEND,
            'budget' => 1000,
        ]);
        $openPot->applicationTypes()->attach($fundingType);

        $closedProgram = FundingProgram::create(['name' => 'Geschlossene Förderung']);
        $closedPot = $closedProgram->pots()->create([
            'name' => 'Geschlossener Topf',
            'laufzeit' => FundingPot::FORTLAUFEND,
            'budget' => 1000,
            'geschlossen' => true,
        ]);
        $closedPot->applicationTypes()->attach($closedType);

        $this->application($applicant, $fundingType, Application::STATUS_DRAFT);
        $this->application($applicant, $fundingType, Application::STATUS_SUBMITTED);
        $this->application($applicant, $fundingType, Application::STATUS_IN_REVIEW);
        $this->application($applicant, $fundingType, Application::STATUS_QUESTION);
        $approved = $this->application($applicant, $fundingType, Application::STATUS_APPROVED, '325.50');
        $this->application($applicant, $fundingType, Application::STATUS_REJECTED);

        $otherApplicant = User::factory()->create();
        $this->application($otherApplicant, $fundingType, Application::STATUS_APPROVED, '900.00');

        $this->actingAs($applicant)->get(route('antraege.dashboard'))
            ->assertOk()
            ->assertSee('Persönliche Antragsübersicht')
            ->assertSee('Jugendprojekt')
            ->assertSee('Sonstiger Antrag')
            ->assertSee('Rückfragen beantworten')
            ->assertDontSee('Geschlossene Förderung')
            ->assertDontSee('Förderprogramme und Fördertöpfe')
            ->assertDontSee('Benutzer und Einladungen')
            ->assertDontSee('325,50 €')
            ->assertDontSee('900,00 €')
            ->assertSee('Alle eigenen Anträge');

        $approved->update(['final_amount' => '300.25']);

        $this->get(route('antraege.dashboard'))
            ->assertSee('300,25 €')
            ->assertDontSee('325,50 €');
    }

    public function test_multi_role_user_can_open_both_distinct_dashboards(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::ANTRAGSTELLER);
        $user->assignRole(Role::FOERDERTOPFVERWALTER);

        $this->actingAs($user)->get(route('antraege.dashboard'))
            ->assertOk()
            ->assertSee('Förderprogramme und Fördertöpfe')
            ->assertSee('Mein Antragsteller-Dashboard')
            ->assertSee('Benutzer und Einladungen');

        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('antraege.dashboard'), false)
            ->assertDontSee(route('antraege.dashboard.antragsteller'), false);

        $this->get(route('antraege.dashboard.antragsteller'))
            ->assertOk()
            ->assertSee('Persönliche Antragsübersicht')
            ->assertDontSee('Zur Verwaltungs- und Prüfungsübersicht');
    }

    private function application(
        User $user,
        ApplicationType $type,
        string $status,
        ?string $approvedAmount = null
    ): Application {
        return Application::create([
            'application_type_id' => $type->id,
            'user_id' => $user->id,
            'status' => $status,
            'submitted_at' => $status === Application::STATUS_DRAFT ? null : now(),
            'decided_at' => $approvedAmount !== null ? now() : null,
            'approved_amount' => $approvedAmount,
            'answers' => [],
        ]);
    }
}
