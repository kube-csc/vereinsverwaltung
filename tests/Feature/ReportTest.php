<?php

namespace Tests\Feature;

use App\Http\Livewire\Auswertungen;
use App\Models\Application;
use App\Models\ApplicationType;
use App\Models\FundingPot;
use App\Models\FundingProgram;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;
use Tests\Concerns\PreparesRoleDatabase;
use Tests\TestCase;

class ReportTest extends TestCase
{
    use PreparesRoleDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareRoleDatabase();
    }

    private function application(FundingPot $pot, string $status, ?float $amount = null, string $name = 'Max'): Application
    {
        $type = ApplicationType::firstOrCreate(['slug' => 'jugend'], [
            'name' => 'Jugend', 'aktiv' => true, 'requires_club' => false, 'requires_funding_pot' => true,
        ]);
        $applicant = User::factory()->create(['name' => $name]);

        return Application::create([
            'application_type_id' => $type->id, 'funding_pot_id' => $pot->id, 'user_id' => $applicant->id,
            'status' => $status, 'submitted_at' => now(), 'answers' => [], 'approved_amount' => $amount,
        ]);
    }

    private function pot(string $name): FundingPot
    {
        return FundingProgram::firstOrCreate(['name' => 'Jugend'])->pots()->create([
            'name' => $name, 'laufzeit' => FundingPot::FORTLAUFEND, 'budget' => 1000,
        ]);
    }

    public function test_access_is_restricted_to_reviewing_roles(): void
    {
        $applicant = User::factory()->create();
        $applicant->assignRole(Role::ANTRAGSTELLER);
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);

        $this->actingAs($applicant)->get(route('antraege.auswertungen'))->assertForbidden();
        $this->actingAs($gremium)->get(route('antraege.auswertungen'))->assertOk();
    }

    public function test_statistics_and_filters(): void
    {
        $potA = $this->pot('Topf A');
        $potB = $this->pot('Topf B');
        $this->application($potA, Application::STATUS_APPROVED, 100);
        $this->application($potA, Application::STATUS_REJECTED);
        $this->application($potB, Application::STATUS_APPROVED, 50);
        $this->application($potB, Application::STATUS_DRAFT);

        $manager = User::factory()->create();
        $manager->assignRole(Role::FOERDERTOPFVERWALTER);

        Livewire::actingAs($manager)->test(Auswertungen::class)
            ->assertViewHas('total', 3)
            ->assertViewHas('approvedSum', 150.0)
            ->set('pot', (string) $potA->id)
            ->assertViewHas('total', 2)
            ->assertViewHas('approvedSum', 100.0)
            ->set('status', Application::STATUS_REJECTED)
            ->assertViewHas('total', 1);
    }

    public function test_csv_export_respects_filter_and_neutralises_formulas(): void
    {
        $potA = $this->pot('Topf A');
        $potB = $this->pot('Topf B');
        $this->application($potA, Application::STATUS_APPROVED, 100, '=cmd');
        $this->application($potB, Application::STATUS_APPROVED, 50, 'Berta');

        $manager = User::factory()->create();
        $manager->assignRole(Role::FOERDERTOPFVERWALTER);

        $component = Livewire::actingAs($manager)->test(Auswertungen::class)
            ->set('pot', (string) $potA->id)
            ->call('exportCsv');

        $component->assertFileDownloaded();
    }
}
