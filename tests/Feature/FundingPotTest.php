<?php

namespace Tests\Feature;

use App\Http\Livewire\Foerdertoepfe;
use App\Models\ApplicationType;
use App\Models\FundingPot;
use App\Models\FundingPotBinding;
use App\Models\FundingProgram;
use App\Models\Role;
use App\Models\User;
use DomainException;
use Livewire\Livewire;
use Tests\Concerns\PreparesRoleDatabase;
use Tests\TestCase;

class FundingPotTest extends TestCase
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

    private function pot(array $overrides = []): FundingPot
    {
        $program = FundingProgram::create(['name' => 'Sportförderung']);

        return $program->pots()->create($overrides + [
            'name' => 'Topf',
            'laufzeit' => FundingPot::FORTLAUFEND,
            'budget' => 1000,
        ]);
    }

    public function test_administrators_and_funding_managers_may_manage_funding(): void
    {
        $this->actingAs(User::factory()->create())->get(route('antraege.foerderprogramme'))->assertForbidden();
        $this->actingAs($this->admin())->get(route('antraege.foerderprogramme'))->assertOk();
        $this->get(route('antraege.dashboard'))->assertOk()->assertSee('Offene Fördertöpfe');

        $manager = User::factory()->create();
        $manager->assignRole(Role::FOERDERTOPFVERWALTER);
        $this->actingAs($manager)->get(route('antraege.foerderprogramme'))->assertOk();
        Livewire::actingAs($manager)->test(Foerdertoepfe::class)
            ->set('programName', 'Gremium-Programm')
            ->call('saveProgram')
            ->assertHasNoErrors();
        $this->assertDatabaseHas('funding_programs', ['name' => 'Gremium-Programm']);
    }

    public function test_bindings_reduce_available_budget(): void
    {
        $pot = $this->pot();
        $pot->bind(FundingPotBinding::RESERVIERT, 250.50);
        $pot->bind(FundingPotBinding::BEWILLIGT, 100);

        $this->assertSame(250.5, $pot->reserviert());
        $this->assertSame(100.0, $pot->bewilligt());
        $this->assertSame(350.5, $pot->gebunden());
        $this->assertSame(649.5, $pot->verfuegbar());
    }

    public function test_binding_more_than_available_is_rejected_and_exhausted_pot_closes(): void
    {
        $pot = $this->pot(['budget' => 100]);

        try {
            $pot->bind(FundingPotBinding::BEWILLIGT, 100.01);
            $this->fail('Überbuchung war möglich.');
        } catch (DomainException) {
            $this->assertSame(0, FundingPotBinding::count());
        }

        $pot->bind(FundingPotBinding::BEWILLIGT, 100);
        $this->assertTrue($pot->istErschoepft());
        $this->assertFalse($pot->istOffen());
        $this->assertSame('Erschöpft', $pot->statusLabel());

        $this->expectException(DomainException::class);
        $pot->bind(FundingPotBinding::RESERVIERT, 1);
    }

    public function test_closed_pot_accepts_no_bindings_and_can_be_reopened(): void
    {
        $pot = $this->pot(['geschlossen' => true]);
        $this->assertFalse($pot->istOffen());

        Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)->call('toggleGeschlossen', $pot->id);

        $this->assertTrue($pot->fresh()->istOffen());
    }

    public function test_time_window_of_limited_pots(): void
    {
        $pot = $this->pot([
            'laufzeit' => FundingPot::BEFRISTET,
            'beginn' => '2026-03-01',
            'ende' => '2026-04-30',
        ]);

        $this->assertFalse($pot->istOffen(now()->setDate(2026, 2, 28)));
        $this->assertTrue($pot->istOffen(now()->setDate(2026, 4, 30)));
        $this->assertFalse($pot->istOffen(now()->setDate(2026, 5, 1)));
    }

    public function test_admin_creates_program_and_pot(): void
    {
        $type = ApplicationType::create([
            'name' => 'Jugendprojekt',
            'slug' => 'jugendprojekt',
            'aktiv' => true,
            'requires_club' => true,
        ]);
        $component = Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)
            ->set('programName', 'Jugendförderung')
            ->call('saveProgram')
            ->assertHasNoErrors();

        $program = FundingProgram::firstOrFail();

        $component
            ->set('potProgramId', $program->id)
            ->set('potName', 'Jugendprojekt 2026')
            ->set('potJahr', '2026')
            ->set('potLaufzeit', FundingPot::BEFRISTET)
            ->set('potBeginn', '2026-01-01')
            ->set('potEnde', '2026-03-31')
            ->set('potBudget', '1.500,50')
            ->set('potMaxApplicationsPerUser', '2')
            ->set('potMaxApplicationsPerClub', '5')
            ->set('potApplicationTypeIds', [(string) $type->id])
            ->call('savePot')
            ->assertHasNoErrors();

        $pot = FundingPot::firstOrFail();
        $this->assertSame('1500.50', $pot->budget);
        $this->assertSame(2, $pot->max_applications_per_user);
        $this->assertSame(5, $pot->max_applications_per_club);
        $this->assertSame('2026-03-31', $pot->ende->format('Y-m-d'));
        $this->assertTrue($pot->applicationTypes->contains($type));
    }

    public function test_new_pots_default_to_unlimited_applications_per_applicant(): void
    {
        $program = FundingProgram::create(['name' => 'P']);
        $component = Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)
            ->set('potProgramId', $program->id)
            ->set('potName', 'Standardtopf')
            ->set('potLaufzeit', FundingPot::FORTLAUFEND)
            ->set('potBudget', '100')
            ->call('savePot')
            ->assertHasNoErrors();

        $pot = FundingPot::firstOrFail();
        $this->assertNull($pot->max_applications_per_user);
        $this->assertNull($pot->max_applications_per_club);

        $this->assertSame('', $component->get('potMaxApplicationsPerUser'));

        $seededPot = $program->pots()->create([
            'name' => 'Seeder-Topf',
            'laufzeit' => FundingPot::FORTLAUFEND,
            'budget' => 100,
        ]);
        $this->assertNull($seededPot->max_applications_per_user);
    }

    public function test_funding_pot_can_have_unlimited_application_limits(): void
    {
        $program = FundingProgram::create(['name' => 'Jugendförderung']);

        Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)
            ->set('potProgramId', $program->id)
            ->set('potName', 'Unbegrenzter Topf')
            ->set('potLaufzeit', FundingPot::FORTLAUFEND)
            ->set('potBudget', '1000')
            ->set('potMaxApplicationsPerUser', '')
            ->set('potMaxApplicationsPerClub', '')
            ->call('savePot')
            ->assertHasNoErrors();

        $pot = FundingPot::firstOrFail();
        $this->assertNull($pot->max_applications_per_user);
        $this->assertNull($pot->max_applications_per_club);
    }

    public function test_funding_programs_show_only_active_assigned_application_types(): void
    {
        $program = FundingProgram::create(['name' => 'Jugendförderung']);
        $pot = $program->pots()->create([
            'name' => 'Fördertopf',
            'laufzeit' => FundingPot::FORTLAUFEND,
            'budget' => 1000,
        ]);
        $activeType = ApplicationType::create([
            'name' => 'Aktiver Antrag',
            'slug' => 'aktiver-antrag',
            'aktiv' => true,
        ]);
        $inactiveType = ApplicationType::create([
            'name' => 'Inaktiver Antrag',
            'slug' => 'inaktiver-antrag',
            'aktiv' => false,
        ]);
        $pot->applicationTypes()->attach([$activeType->id, $inactiveType->id]);

        Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)
            ->assertSee('Aktiver Antrag')
            ->assertDontSee('Inaktiver Antrag');
    }

    public function test_club_limit_requires_club_assignment_for_every_mapped_type(): void
    {
        $program = FundingProgram::create(['name' => 'P']);
        $type = ApplicationType::create([
            'name' => 'Antrag ohne Verein',
            'slug' => 'antrag-ohne-verein',
            'aktiv' => true,
            'requires_club' => false,
        ]);
        $this->assertTrue(
            ApplicationType::whereKey($type->id)->where('requires_club', false)->exists()
        );

        Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)
            ->set('potProgramId', $program->id)
            ->set('potName', 'Topf ohne Antragsart')
            ->set('potLaufzeit', FundingPot::FORTLAUFEND)
            ->set('potBudget', '100')
            ->set('potMaxApplicationsPerClub', '2')
            ->call('savePot')
            ->assertHasErrors(['potMaxApplicationsPerClub']);

        Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)
            ->set('potProgramId', $program->id)
            ->set('potName', 'Topf')
            ->set('potLaufzeit', FundingPot::FORTLAUFEND)
            ->set('potBudget', '100')
            ->set('potMaxApplicationsPerClub', '2')
            ->set('potApplicationTypeIds', [(string) $type->id])
            ->call('savePot')
            ->assertHasErrors(['potMaxApplicationsPerClub']);

        $this->assertSame(0, FundingPot::count());
    }

    public function test_limited_pot_requires_dates_and_end_after_start(): void
    {
        $program = FundingProgram::create(['name' => 'P']);

        Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)
            ->set('potProgramId', $program->id)
            ->set('potName', 'X')
            ->set('potBudget', '10')
            ->call('savePot')
            ->assertHasErrors(['potBeginn', 'potEnde']);

        Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)
            ->set('potProgramId', $program->id)
            ->set('potName', 'X')
            ->set('potBudget', '10')
            ->set('potBeginn', '2026-05-01')
            ->set('potEnde', '2026-04-01')
            ->call('savePot')
            ->assertHasErrors(['potEnde']);
    }

    public function test_budget_cannot_drop_below_bound_amount(): void
    {
        $pot = $this->pot();
        $pot->bind(FundingPotBinding::BEWILLIGT, 600);
        $this->assertSame(60000, $pot->budgetInCent() - (int) round($pot->verfuegbar() * 100));

        Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)
            ->call('editPot', $pot->id)
            ->set('potBudget', '500')
            ->call('savePot')
            ->assertHasErrors('potBudget');

        $this->assertSame('1000.00', $pot->fresh()->budget);
    }

    public function test_pot_with_bindings_cannot_be_deleted(): void
    {
        $pot = $this->pot();
        $pot->bind(FundingPotBinding::RESERVIERT, 10);

        Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)->call('deletePot', $pot->id);

        $this->assertNotNull($pot->fresh());
    }

    public function test_pot_associated_with_application_type_cannot_be_deleted(): void
    {
        $pot = $this->pot();
        $type = ApplicationType::create([
            'name' => 'Antrag',
            'slug' => 'antrag',
            'aktiv' => true,
        ]);
        $pot->applicationTypes()->attach($type);

        Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)->call('deletePot', $pot->id);

        $this->assertNotNull($pot->fresh());
        $this->assertTrue($pot->applicationTypes()->whereKey($type->id)->exists());
    }

    public function test_program_with_pots_cannot_be_deleted(): void
    {
        $pot = $this->pot();

        Livewire::actingAs($this->admin())->test(Foerdertoepfe::class)->call('deleteProgram', $pot->funding_program_id);

        $this->assertNotNull(FundingProgram::find($pot->funding_program_id));
    }

    public function test_non_administrators_cannot_use_the_component(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::ANTRAGSTELLER);
        Livewire::actingAs($user)->test(Foerdertoepfe::class)->assertForbidden();
    }
}
