<?php

namespace Tests\Feature;

use App\Http\Livewire\AntragForm;
use App\Http\Controllers\AntraegeController;
use App\Models\Application;
use App\Models\ApplicationType;
use App\Models\BankAccount;
use App\Models\Club;
use App\Models\FundingPot;
use App\Models\FundingProgram;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tests\Concerns\PreparesRoleDatabase;
use Tests\TestCase;

class ApplicationSubmissionTest extends TestCase
{
    use PreparesRoleDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareRoleDatabase();
    }

    private function applicant(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::ANTRAGSTELLER);

        return $user;
    }

    private function applicationType(array $fields = [], bool $requiresClub = false, bool $requiresFundingPot = false): ApplicationType
    {
        $slug = 'foerderantrag-' . (ApplicationType::count() + 1);
        $type = ApplicationType::create([
            'name' => 'Förderantrag',
            'slug' => $slug,
            'aktiv' => true,
            'requires_club' => $requiresClub,
            'requires_funding_pot' => $requiresFundingPot,
        ]);

        foreach ($fields as $order => $field) {
            $type->fields()->create($field + [
                'sort_order' => $order,
                'aktiv' => true,
            ]);
        }

        return $type->load(['fields' => fn ($query) => $query->where('aktiv', true)]);
    }

    private function clubFor(User $user, string $name = 'Sportverein'): Club
    {
        return Club::create([
            'clubname' => $name,
            'user_id' => $user->id,
            'bearbeiter_id' => $user->id,
        ]);
    }

    private function pot(ApplicationType $type, array $overrides = []): FundingPot
    {
        $program = FundingProgram::create(['name' => 'Sportförderung']);
        $pot = $program->pots()->create($overrides + [
            'name' => 'Jugendprojekt',
            'laufzeit' => FundingPot::FORTLAUFEND,
            'budget' => 5000,
        ]);
        $pot->applicationTypes()->attach($type);

        return $pot;
    }

    public function test_only_applicants_can_open_their_application_portal(): void
    {
        $applicant = $this->applicant();
        $admin = User::factory()->create();
        $admin->assignRole(Role::ADMINISTRATOR);

        $this->actingAs($applicant)->get(route('antraege.dashboard'))
            ->assertOk()
            ->assertSee('Persönliche Antragsübersicht')
            ->assertDontSee('Förderprogramme und Fördertöpfe')
            ->assertDontSee('Konten ohne Rolle');
        $this->get(route('dashboard'))
            ->assertOk()
            ->assertSee(route('antraege.dashboard.antragsteller'))
            ->assertDontSee(route('antraege.meine'));
        $this->get(route('antraege.meine'))->assertOk()->assertSee('Meine gespeicherten Anträge');
        $this->actingAs($admin)->get(route('antraege.meine'))->assertOk();
    }

    public function test_applicant_can_select_any_active_club_but_only_their_own_bank_accounts(): void
    {
        $applicant = $this->applicant();
        $type = $this->applicationType([
            ['key' => 'projekt', 'label' => 'Projekt', 'type' => 'text', 'required' => true],
        ], true);
        $club = $this->clubFor($applicant, 'Eigener Verein');
        $ownAccount = $applicant->bankAccounts()->create([
            'kontoinhaber' => 'Max Mustermann',
            'iban' => 'DE89370400440532013000',
            'bic' => null,
        ]);
        $other = $this->applicant();
        $otherClub = $this->clubFor($other, 'Fremder Verein');
        $otherAccount = $other->bankAccounts()->create([
            'kontoinhaber' => 'Andere Person',
            'iban' => 'DE89370400440532013001',
            'bic' => null,
        ]);

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->set('clubId', (string) $club->id)
            ->set('bankAccountId', (string) $ownAccount->id)
            ->set('formData.projekt', 'Jugendtraining')
            ->call('saveDraft')
            ->assertHasNoErrors()
            ->assertRedirect(route('antraege.meine'));

        $draft = $applicant->applications()->firstOrFail();
        $this->assertSame(Application::STATUS_DRAFT, $draft->status);
        $this->assertSame($club->id, $draft->club_id);
        $this->assertSame($ownAccount->id, $draft->bank_account_id);
        $this->assertNull($draft->bank_account_snapshot);

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->assertSee('Eigener Verein')
            ->assertSee('Fremder Verein')
            ->set('clubId', (string) $otherClub->id)
            ->call('saveDraft')
            ->assertHasNoErrors()
            ->assertRedirect(route('antraege.meine'));

        $secondDraft = $applicant->applications()->latest('id')->firstOrFail();
        $this->assertSame($otherClub->id, $secondDraft->club_id);
        $this->assertNull($secondDraft->bank_account_id);

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->set('clubId', (string) $otherClub->id)
            ->set('bankAccountId', (string) $otherAccount->id)
            ->call('saveDraft')
            ->assertHasErrors(['bankAccountId']);
        $this->assertSame(2, $applicant->applications()->count());
    }

    public function test_submit_requires_fields_and_a_club_when_the_template_requires_one(): void
    {
        $applicant = $this->applicant();
        $type = $this->applicationType([
            ['key' => 'projekt', 'label' => 'Projekt', 'type' => 'text', 'required' => true],
        ], true);

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->call('submitApplication')
            ->assertHasErrors(['clubId', 'formData.projekt']);

        $this->assertSame(0, Application::count());
    }

    public function test_incomplete_application_can_be_saved_as_a_draft(): void
    {
        $applicant = $this->applicant();
        $type = $this->applicationType([
            ['key' => 'projekt', 'label' => 'Projekt', 'type' => 'text', 'required' => true],
        ]);

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->call('saveDraft')
            ->assertHasNoErrors()
            ->assertRedirect(route('antraege.meine'));

        $draft = $applicant->applications()->firstOrFail();
        $this->assertSame(Application::STATUS_DRAFT, $draft->status);
        $this->assertSame([], $draft->answers);
    }

    public function test_submission_without_club_requirement_keeps_club_and_bank_account_unassigned(): void
    {
        $applicant = $this->applicant();
        $type = $this->applicationType([
            ['key' => 'anliegen', 'label' => 'Anliegen', 'type' => 'text', 'required' => true],
        ]);

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->set('formData.anliegen', 'Hallennutzung')
            ->set('formData.unbekannt', 'nicht speichern')
            ->call('submitApplication')
            ->assertHasNoErrors()
            ->assertRedirect(route('antraege.meine'));

        $application = $applicant->applications()->firstOrFail();
        $this->assertNull($application->club_id);
        $this->assertNull($application->bank_account_id);
        $this->assertNull($application->bank_account_snapshot);
        $this->assertSame(['anliegen' => 'Hallennutzung'], $application->answers);
    }

    public function test_funding_application_requires_an_open_mapped_pot_and_links_without_reserving_budget(): void
    {
        $applicant = $this->applicant();
        $type = $this->applicationType([
            ['key' => 'projekt', 'label' => 'Projekt', 'type' => 'text', 'required' => true],
        ], false, true);
        $mappedPot = $this->pot($type, ['budget' => 5000]);
        $unmappedPot = $this->pot($this->applicationType([], false, false), ['name' => 'Fremder Topf']);

        $this->actingAs($applicant)->get(route('antraege.meine'))
            ->assertOk()
            ->assertSee('Förderantrag')
            ->assertSee('Jugendprojekt');

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->assertSee('Jugendprojekt')
            ->assertDontSee('5.000,00 €')
            ->set('formData.projekt', 'Jugendtraining')
            ->call('submitApplication')
            ->assertHasErrors(['fundingPotId']);

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->set('fundingPotId', (string) $unmappedPot->id)
            ->set('formData.projekt', 'Jugendtraining')
            ->call('submitApplication')
            ->assertHasErrors(['fundingPotId']);

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->set('fundingPotId', (string) $mappedPot->id)
            ->set('formData.projekt', 'Jugendtraining')
            ->call('submitApplication')
            ->assertHasNoErrors()
            ->assertRedirect(route('antraege.meine'));

        $application = $applicant->applications()->firstOrFail();
        $this->assertSame($mappedPot->id, $application->funding_pot_id);
        $this->assertSame(0, $mappedPot->bindings()->count());
        $this->assertSame(5000.0, $mappedPot->verfuegbar());
    }

    public function test_closed_funding_pot_cannot_be_selected_or_submitted(): void
    {
        $applicant = $this->applicant();
        $type = $this->applicationType([
            ['key' => 'projekt', 'label' => 'Projekt', 'type' => 'text', 'required' => true],
        ], false, true);
        $pot = $this->pot($type, ['geschlossen' => true]);

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->set('fundingPotId', (string) $pot->id)
            ->set('formData.projekt', 'Jugendtraining')
            ->call('submitApplication')
            ->assertHasErrors(['fundingPotId']);

        $this->assertSame(0, Application::count());

        $this->expectException(NotFoundHttpException::class);
        $this->actingAs($applicant);
        app(AntraegeController::class)->create($type->slug);
    }

    public function test_submission_limits_ignore_drafts_and_enforce_the_club_limit(): void
    {
        $applicant = $this->applicant();
        $type = $this->applicationType([
            ['key' => 'projekt', 'label' => 'Projekt', 'type' => 'text', 'required' => true],
        ], true, true);
        $club = $this->clubFor($applicant);
        $pot = $this->pot($type, [
            'max_applications_per_user' => 2,
            'max_applications_per_club' => 1,
        ]);
        $draft = Application::create([
            'application_type_id' => $type->id,
            'funding_pot_id' => $pot->id,
            'user_id' => $applicant->id,
            'club_id' => $club->id,
            'status' => Application::STATUS_DRAFT,
            'answers' => [],
        ]);

        Livewire::actingAs($applicant)->test(AntragForm::class, [
            'typeId' => $type->id,
            'applicationId' => $draft->id,
        ])
            ->set('formData.projekt', 'Entwurf wird eingereicht')
            ->call('submitApplication')
            ->assertHasNoErrors()
            ->assertRedirect(route('antraege.meine'));

        $this->assertSame(1, Application::whereNotNull('submitted_at')->count());
        $draft->refresh()->update(['status' => 'abgelehnt']);

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->set('fundingPotId', (string) $pot->id)
            ->set('clubId', (string) $club->id)
            ->set('formData.projekt', 'Weiterer Antrag')
            ->call('submitApplication')
            ->assertHasErrors(['fundingPotId']);

        $this->assertSame(1, Application::whereNotNull('submitted_at')->count());
    }

    public function test_submission_limit_is_per_applicant_account_across_clubs(): void
    {
        $applicant = $this->applicant();
        $type = $this->applicationType([
            ['key' => 'projekt', 'label' => 'Projekt', 'type' => 'text', 'required' => true],
        ], true, true);
        $firstClub = $this->clubFor($applicant, 'Erster Verein');
        $secondClub = $this->clubFor($applicant, 'Zweiter Verein');
        $pot = $this->pot($type, ['max_applications_per_user' => 1]);

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->set('fundingPotId', (string) $pot->id)
            ->set('clubId', (string) $firstClub->id)
            ->set('formData.projekt', 'Erster Antrag')
            ->call('submitApplication')
            ->assertHasNoErrors()
            ->assertRedirect(route('antraege.meine'));

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->set('fundingPotId', (string) $pot->id)
            ->set('clubId', (string) $secondClub->id)
            ->set('formData.projekt', 'Zweiter Antrag')
            ->call('submitApplication')
            ->assertHasErrors(['fundingPotId']);

        $this->assertSame(1, Application::where('status', Application::STATUS_SUBMITTED)->count());
    }

    public function test_applicant_can_submit_multiple_applications_with_the_default_unlimited_user_limit(): void
    {
        $applicant = $this->applicant();
        $type = $this->applicationType([
            ['key' => 'projekt', 'label' => 'Projekt', 'type' => 'text', 'required' => true],
        ], true, true);
        $club = $this->clubFor($applicant);
        $pot = $this->pot($type, [
            'max_applications_per_club' => null,
        ]);

        foreach (['Erster Antrag', 'Zweiter Antrag'] as $project) {
            Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
                ->set('fundingPotId', (string) $pot->id)
                ->set('clubId', (string) $club->id)
                ->set('formData.projekt', $project)
                ->call('submitApplication')
                ->assertHasNoErrors()
                ->assertRedirect(route('antraege.meine'));
        }

        $this->assertSame(2, Application::where('status', Application::STATUS_SUBMITTED)->count());
    }

    public function test_submitted_application_has_encrypted_bank_snapshot_and_required_private_attachment(): void
    {
        Storage::fake('local');
        $applicant = $this->applicant();
        $type = $this->applicationType([
            ['key' => 'projekt', 'label' => 'Projekt', 'type' => 'text', 'required' => true],
            ['key' => 'nachweis', 'label' => 'Nachweis', 'type' => 'file', 'required' => true],
        ], true);
        $club = $this->clubFor($applicant);
        $account = $applicant->bankAccounts()->create([
            'kontoinhaber' => 'Max Mustermann',
            'bank_name' => 'Musterbank',
            'iban' => 'DE89370400440532013000',
            'bic' => 'COBADEFFXXX',
        ]);

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->set('clubId', (string) $club->id)
            ->set('bankAccountId', (string) $account->id)
            ->set('formData.projekt', 'Jugendtraining')
            ->call('submitApplication')
            ->assertHasErrors(['uploads.nachweis']);

        $this->assertSame(0, Application::count());

        Livewire::actingAs($applicant)->test(AntragForm::class, ['typeId' => $type->id])
            ->set('clubId', (string) $club->id)
            ->set('bankAccountId', (string) $account->id)
            ->set('formData.projekt', 'Jugendtraining')
            ->set('uploads.nachweis', UploadedFile::fake()->create('nachweis.pdf', 20, 'application/pdf'))
            ->call('submitApplication')
            ->assertHasNoErrors()
            ->assertRedirect(route('antraege.meine'));

        $application = $applicant->applications()->firstOrFail();
        $this->assertSame(Application::STATUS_SUBMITTED, $application->status);
        $this->assertNotNull($application->submitted_at);
        $this->assertSame($club->id, $application->club_id);
        $this->assertSame([
            'kontoinhaber' => 'Max Mustermann',
            'bank_name' => 'Musterbank',
            'iban' => 'DE89370400440532013000',
            'bic' => 'COBADEFFXXX',
        ], $application->bank_account_snapshot);
        $this->assertSame('Projekt', $application->form_snapshot[0]['label']);
        $encryptedSnapshot = DB::table('applications')->where('id', $application->id)->value('bank_account_snapshot');
        $this->assertStringNotContainsString('DE89370400440532013000', $encryptedSnapshot);

        $type->fields()->where('key', 'projekt')->update(['label' => 'Geänderte Bezeichnung']);
        $account->update(['iban' => 'DE89370400440532013001']);
        $account->delete();
        $this->assertSame('DE89370400440532013000', $application->fresh()->bank_account_snapshot['iban']);
        $this->assertSame('Projekt', $application->fresh()->form_snapshot[0]['label']);

        $document = $application->documents()->firstOrFail();
        Storage::disk('local')->assertExists($document->path);
        $this->actingAs($applicant)->get(route('antraege.dokumente.download', $document->id))->assertOk();
        $this->actingAs($this->applicant())->get(route('antraege.dokumente.download', $document->id))->assertForbidden();
    }

    public function test_applicant_cannot_edit_another_users_or_already_submitted_application(): void
    {
        $owner = $this->applicant();
        $other = $this->applicant();
        $type = $this->applicationType([
            ['key' => 'projekt', 'label' => 'Projekt', 'type' => 'text', 'required' => false],
        ]);
        $draft = $owner->applications()->create([
            'application_type_id' => $type->id,
            'answers' => [],
            'status' => Application::STATUS_DRAFT,
        ]);
        $submitted = $owner->applications()->create([
            'application_type_id' => $type->id,
            'answers' => [],
            'status' => Application::STATUS_SUBMITTED,
            'submitted_at' => now(),
        ]);

        $this->actingAs($other);
        try {
            app(AntraegeController::class)->edit($draft->id);
            $this->fail('Ein fremder Entwurf wurde geöffnet.');
        } catch (ModelNotFoundException) {
            $this->assertTrue(true);
        }

        $this->actingAs($owner);
        try {
            app(AntraegeController::class)->edit($submitted->id);
            $this->fail('Ein eingereichter Antrag wurde zum Bearbeiten geöffnet.');
        } catch (ModelNotFoundException) {
            $this->assertTrue(true);
        }
    }
}
