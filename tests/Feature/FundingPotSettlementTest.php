<?php

namespace Tests\Feature;

use App\Http\Livewire\Foerdertoepfe;
use App\Models\Application;
use App\Models\ApplicationType;
use App\Models\FundingPot;
use App\Models\FundingPotBinding;
use App\Models\FundingProgram;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;
use Tests\Concerns\PreparesRoleDatabase;
use Tests\TestCase;

class FundingPotSettlementTest extends TestCase
{
    use PreparesRoleDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareRoleDatabase();
    }

    private function pot(float $budget, bool $closed = true): FundingPot
    {
        $program = FundingProgram::create(['name' => 'Jugendförderung']);

        return $program->pots()->create([
            'name' => 'Fördertopf',
            'laufzeit' => FundingPot::FORTLAUFEND,
            'budget' => $budget,
            'geschlossen' => $closed,
        ]);
    }

    private function application(FundingPot $pot, string $status, ?float $approved = null): Application
    {
        $type = ApplicationType::firstOrCreate(['slug' => 'jugend'], [
            'name' => 'Jugendprojekt', 'aktiv' => true, 'requires_club' => false, 'requires_funding_pot' => true,
        ]);

        $application = Application::create([
            'application_type_id' => $type->id,
            'funding_pot_id' => $pot->id,
            'user_id' => User::factory()->create()->id,
            'status' => $status,
            'submitted_at' => now(),
            'decided_at' => now(),
            'answers' => ['beantragter_zuschuss' => (string) ($approved ?? 100)],
            'approved_amount' => $approved,
        ]);

        if ($approved !== null) {
            $pot->bindings()->create([
                'art' => FundingPotBinding::BEWILLIGT,
                'betrag' => $approved,
                'application_id' => $application->id,
            ]);
        }

        return $application;
    }

    private function manager(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::FOERDERTOPFVERWALTER);

        return $user;
    }

    public function test_settlement_distributes_budget_proportionally_without_exceeding_it(): void
    {
        $pot = $this->pot(100);
        $a = $this->application($pot, Application::STATUS_APPROVED, 100);
        $b = $this->application($pot, Application::STATUS_APPROVED, 100);
        $c = $this->application($pot, Application::STATUS_APPROVED, 100);
        $rejected = $this->application($pot, Application::STATUS_REJECTED);

        Livewire::actingAs($this->manager())->test(Foerdertoepfe::class)
            ->call('prepareSettlement', $pot->id)
            ->assertSee('Vorschau der Endabrechnung')
            ->call('settlePot', $pot->id)
            ->assertHasNoErrors();

        $finals = collect([$a, $b, $c])->map(fn ($application) => $application->fresh()->final_amount)->all();
        $this->assertEqualsCanonicalizing(['33.33', '33.33', '33.34'], $finals);
        $this->assertNull($rejected->fresh()->final_amount);
        $this->assertEquals(100.0, $pot->fresh()->bewilligt());
        $this->assertTrue($pot->fresh()->isSettled());
        $this->assertSame('Abgerechnet', $pot->fresh()->statusLabel());
        $this->assertDatabaseHas('application_events', ['application_id' => $a->id, 'event_type' => Application::EVENT_SETTLED]);
    }

    public function test_settlement_keeps_amounts_when_budget_is_sufficient(): void
    {
        $pot = $this->pot(1000);
        $a = $this->application($pot, Application::STATUS_APPROVED, 250);

        $pot->settle($this->manager());

        $this->assertSame('250.00', $a->fresh()->final_amount);
    }

    public function test_settlement_is_blocked_while_applications_are_in_progress_and_only_runs_once(): void
    {
        $pot = $this->pot(100);
        $this->application($pot, Application::STATUS_APPROVED, 150);
        $open = $this->application($pot, Application::STATUS_IN_REVIEW);

        $manager = $this->manager();
        Livewire::actingAs($manager)->test(Foerdertoepfe::class)
            ->call('settlePot', $pot->id);
        $this->assertFalse($pot->fresh()->isSettled());

        $open->update(['status' => Application::STATUS_REJECTED]);
        $pot->fresh()->settle($manager);

        $this->expectException(\DomainException::class);
        $pot->fresh()->settle($manager);
    }

    public function test_settlement_requires_a_closed_pot(): void
    {
        $pot = $this->pot(100, false);
        $this->application($pot, Application::STATUS_APPROVED, 150);
        $manager = $this->manager();

        Livewire::actingAs($manager)->test(Foerdertoepfe::class)
            ->call('settlePot', $pot->id);
        $this->assertFalse($pot->fresh()->isSettled());

        $pot->update(['geschlossen' => true]);
        $pot->fresh()->settle($manager);
        $this->assertTrue($pot->fresh()->isSettled());
    }

    public function test_closing_before_the_end_date_asks_for_confirmation(): void
    {
        $pot = $this->pot(100, false);
        $pot->update(['laufzeit' => FundingPot::BEFRISTET, 'beginn' => now()->subMonth(), 'ende' => now()->addMonth()]);
        $this->assertTrue($pot->fresh()->endeNochNichtErreicht());

        Livewire::actingAs($this->manager())->test(Foerdertoepfe::class)
            ->assertSeeHtml('wire:confirm="Die Endzeit dieses');

        $pot->update(['ende' => now()->subDay()]);
        $this->assertFalse($pot->fresh()->endeNochNichtErreicht());
    }

    public function test_only_administrators_can_revoke_a_settlement(): void
    {
        $pot = $this->pot(100);
        $application = $this->application($pot, Application::STATUS_APPROVED, 150);
        $manager = $this->manager();
        $pot->fresh()->settle($manager);
        $this->assertEquals('100.00', $application->fresh()->final_amount);

        Livewire::actingAs($manager)->test(Foerdertoepfe::class)
            ->call('revokeSettlement', $pot->id)
            ->assertForbidden();
        $this->assertTrue($pot->fresh()->isSettled());

        $admin = User::factory()->create();
        $admin->assignRole(Role::ADMINISTRATOR);
        Livewire::actingAs($admin)->test(Foerdertoepfe::class)
            ->call('revokeSettlement', $pot->id);

        $this->assertFalse($pot->fresh()->isSettled());
        $this->assertNull($application->fresh()->final_amount);
        $this->assertEquals(150.0, $pot->fresh()->bewilligt());
        $this->assertDatabaseHas('application_events', [
            'application_id' => $application->id,
            'event_type' => Application::EVENT_SETTLEMENT_REVOKED,
        ]);

        $pot->fresh()->settle($manager);
        $this->assertTrue($pot->fresh()->isSettled());
    }

    public function test_only_funding_managers_may_settle(): void
    {
        $pot = $this->pot(100);
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);

        Livewire::actingAs($gremium)->test(Foerdertoepfe::class)->assertForbidden();
    }

    public function test_applicants_see_final_amount_only_after_settlement(): void
    {
        $pot = $this->pot(100);
        $application = $this->application($pot, Application::STATUS_APPROVED, 200);
        $applicant = $application->user;
        $applicant->assignRole(Role::ANTRAGSTELLER);

        $this->actingAs($applicant)->get(route('antraege.dashboard.antragsteller'))
            ->assertOk()
            ->assertDontSee('200,00')
            ->assertSee('nach der Endabrechnung');

        $pot->settle($this->manager());

        $this->actingAs($applicant)->get(route('antraege.dashboard.antragsteller'))
            ->assertOk()
            ->assertSee('100,00 €')
            ->assertDontSee('200,00');
    }
}
