<?php

namespace Tests\Feature;

use App\Http\Livewire\AntraegePruefung;
use App\Http\Livewire\MeineAntraege;
use App\Models\Application;
use App\Models\ApplicationType;
use App\Models\Club;
use App\Models\FundingPot;
use App\Models\FundingProgram;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Livewire;
use Tests\Concerns\PreparesRoleDatabase;
use Tests\TestCase;

class ApplicationReviewTest extends TestCase
{
    use PreparesRoleDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->prepareRoleDatabase();
    }

    private function fundingApplication(
        array $answers = ['beantragter_zuschuss' => '250'],
        array $potOverrides = []
    ): Application
    {
        $applicant = User::factory()->create();
        $type = ApplicationType::create([
            'name' => 'Jugendprojekt',
            'slug' => 'jugendprojekt',
            'aktiv' => true,
            'requires_club' => false,
            'requires_funding_pot' => true,
        ]);
        $program = FundingProgram::create(['name' => 'Jugendförderung']);
        $pot = $program->pots()->create(array_replace([
            'name' => 'Fördertopf',
            'laufzeit' => FundingPot::FORTLAUFEND,
            'budget' => 1000,
            'geschlossen' => true,
        ], $potOverrides));
        $pot->applicationTypes()->attach($type);

        return Application::create([
            'application_type_id' => $type->id,
            'funding_pot_id' => $pot->id,
            'user_id' => $applicant->id,
            'status' => Application::STATUS_SUBMITTED,
            'submitted_at' => now(),
            'answers' => $answers + ['projektbeschreibung' => 'Vertraulicher Antragstext'],
            'form_snapshot' => [
                ['key' => 'projektbeschreibung', 'label' => 'Projektbeschreibung'],
                ['key' => 'beantragter_zuschuss', 'label' => 'Beantragter Zuschuss'],
            ],
        ]);
    }

    public function test_gremium_can_review_and_approve_with_a_reason_and_budget_binding(): void
    {
        $gremium = User::factory()->create([
            'name' => 'admin',
            'vorname' => 'Ada',
            'nachname' => 'Lovelace',
        ]);
        $gremium->assignRole(Role::GREMIUM);
        $application = $this->fundingApplication();
        $pot = $application->fundingPot;

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->set('reviewNotes.' . $application->id, 'Antrag vollständig geprüft.')
            ->call('markForReview', $application->id)
            ->assertHasNoErrors()
            ->set('decisionReasons.' . $application->id, 'Das Projekt erfüllt die Förderkriterien.')
            ->set('decisionAmounts.' . $application->id, '200')
            ->assertSee('Entspricht 80,00 % des beantragten Betrags')
            ->call('decide', $application->id, Application::STATUS_APPROVED)
            ->assertHasNoErrors()
            ->assertSee('Entscheidungsbegründung:')
            ->assertSee('Ada Lovelace')
            ->assertSee('Gremium')
            ->assertDontSee('admin ·');

        $this->assertSame(Application::STATUS_APPROVED, $application->fresh()->status);
        $this->assertSame($gremium->id, $application->fresh()->decided_by);
        $this->assertSame('Das Projekt erfüllt die Förderkriterien.', $application->fresh()->decision_reason);
        $this->assertSame('200.00', $application->fresh()->approved_amount);
        $this->assertSame(0.0, $pot->fresh()->reserviert());
        $this->assertSame(200.0, $pot->fresh()->bewilligt());
        $this->assertDatabaseCount('funding_pot_bindings', 1);
        $this->assertDatabaseHas('funding_pot_bindings', [
            'application_id' => $application->id,
            'art' => \App\Models\FundingPotBinding::BEWILLIGT,
            'betrag' => '200.00',
        ]);
    }

    public function test_starting_review_reserves_the_requested_amount(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $application = $this->fundingApplication(['beantragter_zuschuss' => '250']);

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->call('markForReview', $application->id)
            ->assertHasNoErrors();

        $this->assertSame(250.0, $application->fundingPot->fresh()->reserviert());
        $this->assertSame(0.0, $application->fundingPot->fresh()->bewilligt());
        $this->assertDatabaseHas('funding_pot_bindings', [
            'application_id' => $application->id,
            'art' => \App\Models\FundingPotBinding::RESERVIERT,
            'betrag' => '250.00',
        ]);
    }

    public function test_requesting_information_releases_the_review_reservation(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $application = $this->fundingApplication(['beantragter_zuschuss' => '250']);
        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->call('markForReview', $application->id)
            ->set('questions.' . $application->id, 'Bitte ergänzen Sie die Angaben zum Projekt.')
            ->call('requestInformation', $application->id)
            ->assertHasNoErrors();

        $this->assertSame(0.0, $application->fundingPot->fresh()->reserviert());
        $this->assertDatabaseHas('funding_pot_bindings', [
            'application_id' => $application->id,
            'art' => \App\Models\FundingPotBinding::FREIGEGEBEN,
        ]);
    }

    public function test_rejecting_an_application_releases_its_review_reservation(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $application = $this->fundingApplication(['beantragter_zuschuss' => '250']);

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->call('markForReview', $application->id)
            ->set('decisionReasons.' . $application->id, 'Der Antrag erfüllt die Förderkriterien nicht.')
            ->call('decide', $application->id, Application::STATUS_REJECTED)
            ->assertHasNoErrors();

        $this->assertSame(0.0, $application->fundingPot->fresh()->reserviert());
        $this->assertSame(0.0, $application->fundingPot->fresh()->bewilligt());
    }

    public function test_starting_review_reserves_even_when_the_requested_amount_exceeds_available_budget(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $application = $this->fundingApplication(
            ['beantragter_zuschuss' => '250'],
            ['budget' => 200]
        );

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->call('markForReview', $application->id)
            ->assertHasNoErrors();

        $this->assertSame(Application::STATUS_IN_REVIEW, $application->fresh()->status);
        $this->assertDatabaseCount('funding_pot_bindings', 1);
    }

    public function test_review_list_displays_the_applications_club(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $application = $this->fundingApplication();
        $club = Club::create([
            'clubname' => 'Sportverein Muster',
            'bearbeiter_id' => $gremium->id,
            'user_id' => $gremium->id,
        ]);
        $application->update(['club_id' => $club->id]);

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->assertSee('Verein')
            ->assertSee('Sportverein Muster');
    }

    public function test_review_list_can_filter_by_applicant_club_funding_pot_and_application_type(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $firstApplication = $this->fundingApplication();
        $firstApplication->user->update(['vorname' => 'Anna', 'nachname' => 'Muster']);
        $firstApplication->applicationType->update(['name' => 'Jugendprojekt']);
        $firstApplication->fundingPot->program->update(['name' => 'Jugendförderung']);
        $firstClub = Club::create([
            'clubname' => 'Sportverein Nord',
            'bearbeiter_id' => $gremium->id,
            'user_id' => $gremium->id,
        ]);
        $firstApplication->update(['club_id' => $firstClub->id]);

        $secondApplicant = User::factory()->create(['vorname' => 'Berta', 'nachname' => 'Beispiel']);
        $secondType = ApplicationType::create([
            'name' => 'Kulturprojekt',
            'slug' => 'kulturprojekt',
            'aktiv' => true,
            'requires_club' => false,
            'requires_funding_pot' => true,
        ]);
        $secondProgram = FundingProgram::create(['name' => 'Kulturförderung']);
        $secondPot = $secondProgram->pots()->create([
            'name' => 'Kulturtopf',
            'laufzeit' => FundingPot::FORTLAUFEND,
            'budget' => 1000,
            'geschlossen' => true,
        ]);
        $secondPot->applicationTypes()->attach($secondType);
        $secondApplication = $firstApplication->replicate();
        $secondApplication->user()->associate($secondApplicant);
        $secondApplication->applicationType()->associate($secondType);
        $secondApplication->fundingPot()->associate($secondPot);
        $secondApplication->status = Application::STATUS_IN_REVIEW;
        $secondApplication->save();
        $secondClub = Club::create([
            'clubname' => 'Kulturverein Süd',
            'bearbeiter_id' => $gremium->id,
            'user_id' => $gremium->id,
        ]);
        $secondApplication->update(['club_id' => $secondClub->id]);

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->assertSee('Alle Antragstellenden')
            ->assertSee('Alle Vereine')
            ->assertSee('Alle Fördertöpfe')
            ->assertSee('Alle Antragsarten')
            ->assertSee('Alle Status')
            ->assertSee('In Prüfung')
            ->set('filterStatus', Application::STATUS_IN_REVIEW)
            ->assertSee('Berta Beispiel')
            ->assertDontSeeHtml('wire:click="selectApplication(' . $firstApplication->id . ')"')
            ->set('filterUserId', $secondApplication->user_id)
            ->set('filterClubId', $secondClub->id)
            ->set('filterFundingPotId', $secondApplication->funding_pot_id)
            ->set('filterApplicationTypeId', $secondApplication->application_type_id)
            ->assertSee('Berta Beispiel')
            ->assertDontSeeHtml('wire:click="selectApplication(' . $firstApplication->id . ')"')
            ->call('resetFilters')
            ->assertSee('Anna Muster')
            ->assertSee('Berta Beispiel');
    }

    public function test_review_displays_application_date_answers_in_german_format(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $application = $this->fundingApplication();
        $application->update([
            'answers' => $application->answers + ['projektbeginn' => '2026-11-11'],
            'form_snapshot' => [
                ['key' => 'projektbeginn', 'label' => 'Projektbeginn', 'type' => 'date'],
            ],
        ]);

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->assertSee('11.11.2026')
            ->assertSee('Formulardaten')
            ->assertSee('Antragsformular')
            ->assertSee('Status')
            ->assertSee('Fördertopf')
            ->assertSee('Antragsteller')
            ->assertDontSee('Status und Zuordnung des Antrags sowie die eingereichten Angaben.')
            ->assertDontSee('2026-11-11');
    }

    public function test_review_list_shows_multiple_applications_for_the_same_funding_pot(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $firstApplication = $this->fundingApplication();
        $secondApplication = $firstApplication->replicate();
        $secondApplication->user()->associate(User::factory()->create());
        $secondApplication->save();
        $firstApplication->user->update(['vorname' => 'Erste', 'nachname' => 'Person']);
        $secondApplication->user->update(['vorname' => 'Zweite', 'nachname' => 'Person']);

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->assertSee('Erste Person')
            ->assertSee('Zweite Person');
    }

    public function test_review_list_shows_applications_beyond_the_first_twenty_for_a_pot(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $firstApplication = $this->fundingApplication();

        for ($index = 1; $index <= 20; $index++) {
            $application = $firstApplication->replicate();
            $application->user()->associate(User::factory()->create([
                'vorname' => 'Antrag',
                'nachname' => (string) ($index + 1),
            ]));
            $application->submitted_at = now()->subSeconds($index);
            $application->save();
        }

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->assertSee('Antrag 21');
    }

    public function test_review_cards_identify_the_applicant_and_application_type(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $submitted = $this->fundingApplication();
        $inReview = $submitted->replicate();
        $inReview->user()->associate(User::factory()->create([
            'vorname' => 'Noch',
            'nachname' => 'Prüfen',
        ]));
        $inReview->status = Application::STATUS_IN_REVIEW;
        $inReview->save();
        $waitingForApplicant = $submitted->replicate();
        $waitingForApplicant->user()->associate(User::factory()->create([
            'vorname' => 'Rückfrage',
            'nachname' => 'Offen',
        ]));
        $waitingForApplicant->status = Application::STATUS_QUESTION;
        $waitingForApplicant->save();

        $component = Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->assertSee('Antragsformular')
            ->assertSee('Jugendprojekt')
            ->assertSee('Jugendförderung — Fördertopf')
            ->assertSee('Noch Prüfen')
            ->assertSee('Eingereicht')
            ->assertSee('In Prüfung')
            ->assertSee('Rückfrage Offen')
            ->assertSeeHtml('bg-blue-100 text-blue-800')
            ->assertSeeHtml('bg-yellow-100 text-yellow-800')
            ->assertSeeHtml('bg-orange-100 text-orange-800')
            ->assertSeeHtml('wire:click="selectApplication(' . $inReview->id . ')"')
            ->assertDontSeeHtml('id="pruef-antrag-' . $waitingForApplicant->id . '"');

        $component->call('selectApplication', $inReview->id)
            ->assertSee('Noch Prüfen')
            ->assertSee('Status')
            ->assertSee('Antragsformular')
            ->assertSee('Antragsteller')
            ->assertSee('Fördertopf')
            ->assertSeeHtml('id="pruef-antrag-' . $inReview->id . '"')
            ->assertDontSeeHtml('id="pruef-antrag-' . $submitted->id . '"')
            ->assertSeeHtml('aria-pressed="true"')
            ->assertSee('Gerade ausgewählt');
    }

    public function test_approval_above_budget_is_allowed_and_cut_by_the_final_settlement(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $application = $this->fundingApplication(
            ['beantragter_zuschuss' => '250'],
            ['budget' => 100]
        );

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->set('decisionReasons.' . $application->id, 'Der Antrag ist förderwürdig.')
            ->set('decisionAmounts.' . $application->id, '250')
            ->call('decide', $application->id, Application::STATUS_APPROVED)
            ->assertHasNoErrors();

        $this->assertSame(Application::STATUS_APPROVED, $application->fresh()->status);
        $this->assertNull($application->fresh()->final_amount);
    }

    public function test_gremium_can_enter_approved_amount_as_percentage_of_requested_amount(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $application = $this->fundingApplication(['beantragter_zuschuss' => '250']);

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->set('decisionAmountModes.' . $application->id, 'percent')
            ->set('decisionAmounts.' . $application->id, '80')
            ->assertSee('Bewilligter Betrag: 200,00 €')
            ->set('decisionReasons.' . $application->id, 'Der Antrag wird teilweise bewilligt.')
            ->call('decide', $application->id, Application::STATUS_APPROVED)
            ->assertHasNoErrors();

        $this->assertSame('200.00', $application->fresh()->approved_amount);
        $this->assertSame(200.0, $application->fundingPot->fresh()->bewilligt());
    }

    public function test_percentage_approval_cannot_exceed_the_requested_amount(): void
    {
        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $application = $this->fundingApplication(['beantragter_zuschuss' => '250']);

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->set('decisionAmountModes.' . $application->id, 'percent')
            ->set('decisionAmounts.' . $application->id, '100.01')
            ->set('decisionReasons.' . $application->id, 'Der Antrag wird teilweise bewilligt.')
            ->call('decide', $application->id, Application::STATUS_APPROVED)
            ->assertHasErrors('decisionAmounts.' . $application->id);

        $this->assertSame(Application::STATUS_SUBMITTED, $application->fresh()->status);
    }

    public function test_gremium_can_request_information_and_applicant_can_answer_with_private_history(): void
    {
        $gremium = User::factory()->create(['name' => 'Gremiumsmitglied']);
        $gremium->assignRole(Role::GREMIUM);
        $application = $this->fundingApplication();
        $applicant = $application->user;
        $applicant->assignRole(Role::ANTRAGSTELLER);

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->set('reviewNotes.' . $application->id, 'Interne Prüfnotiz nicht für Antragstellende.')
            ->call('markForReview', $application->id)
            ->set('questions.' . $application->id, 'Bitte erläutern Sie den geplanten Eigenanteil.')
            ->call('requestInformation', $application->id)
            ->assertHasNoErrors();

        $this->assertSame(Application::STATUS_QUESTION, $application->fresh()->status);

        Livewire::actingAs($applicant)->test(MeineAntraege::class)
            ->assertSee('Rückfrage – Antwort erforderlich')
            ->assertSee('Bitte erläutern Sie den geplanten Eigenanteil.')
            ->assertDontSee('Interne Prüfnotiz nicht für Antragstellende.')
            ->set('questionResponses.' . $application->id, 'Der Eigenanteil wird durch Vereinsbeiträge gedeckt.')
            ->call('respondToQuestion', $application->id)
            ->assertHasNoErrors()
            ->assertSee('Ihre Antwort wurde übermittelt');

        $this->assertSame(Application::STATUS_SUBMITTED, $application->fresh()->status);
        $this->assertDatabaseHas('application_events', [
            'application_id' => $application->id,
            'event_type' => Application::EVENT_ANSWER,
            'actor_id' => $applicant->id,
            'message' => 'Der Eigenanteil wird durch Vereinsbeiträge gedeckt.',
        ]);

        Livewire::actingAs($gremium)->test(AntraegePruefung::class)
            ->assertSee('Bitte erläutern Sie den geplanten Eigenanteil.')
            ->assertSee('Der Eigenanteil wird durch Vereinsbeiträge gedeckt.')
            ->assertSee('Interne Prüfnotiz nicht für Antragstellende.');
    }

    public function test_applicant_cannot_answer_another_users_question(): void
    {
        $applicant = User::factory()->create();
        $applicant->assignRole(Role::ANTRAGSTELLER);
        $otherApplicant = User::factory()->create();
        $otherApplication = $this->fundingApplication();
        $otherApplication->update([
            'user_id' => $otherApplicant->id,
            'status' => Application::STATUS_QUESTION,
        ]);

        $this->expectException(\Illuminate\Database\Eloquent\ModelNotFoundException::class);
        Livewire::actingAs($applicant)->test(MeineAntraege::class)
            ->set('questionResponses.' . $otherApplication->id, 'Meine Antwort.')
            ->call('respondToQuestion', $otherApplication->id)
            ->assertHasNoErrors();
    }

    public function test_applicant_cannot_answer_a_question_twice(): void
    {
        $applicant = User::factory()->create();
        $applicant->assignRole(Role::ANTRAGSTELLER);
        $ownApplication = $this->fundingApplication();
        $ownApplication->update([
            'user_id' => $applicant->id,
            'status' => Application::STATUS_QUESTION,
        ]);
        Livewire::actingAs($applicant)->test(MeineAntraege::class)
            ->set('questionResponses.' . $ownApplication->id, 'Meine Antwort.')
            ->call('respondToQuestion', $ownApplication->id)
            ->assertHasNoErrors();

        Livewire::actingAs($applicant)->test(MeineAntraege::class)
            ->set('questionResponses.' . $ownApplication->id, 'Noch eine Antwort.')
            ->call('respondToQuestion', $ownApplication->id)
            ->assertStatus(409);
    }

    public function test_funding_manager_can_archive_a_rejected_application_but_cannot_review_it(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole(Role::FOERDERTOPFVERWALTER);
        $application = $this->fundingApplication();
        $application->update(['status' => Application::STATUS_REJECTED]);
        $document = $application->documents()->create([
            'field_key' => 'nachweis',
            'original_name' => 'nachweis.pdf',
            'path' => 'application-documents/test/nachweis.pdf',
            'mime_type' => 'application/pdf',
            'size' => 100,
        ]);

        $this->assertFalse(Gate::forUser($manager)->allows('antraege.pruefen'));
        Livewire::actingAs($manager)->test(AntraegePruefung::class)
            ->assertDontSee('Vertraulicher Antragstext')
            ->set('deletionReasons.' . $application->id, 'Doppelter Antrag auf Wunsch des Vereins.')
            ->call('archive', $application->id)
            ->assertHasNoErrors();

        $this->assertSoftDeleted('applications', ['id' => $application->id]);
        $archived = Application::withTrashed()->findOrFail($application->id);
        $this->assertSame($manager->id, $archived->deleted_by);
        $this->assertSame('Doppelter Antrag auf Wunsch des Vereins.', $archived->deletion_reason);
        $this->assertDatabaseHas('application_documents', ['id' => $document->id, 'application_id' => $application->id]);
        $this->assertSame(0, Application::whereNotNull('submitted_at')->count());
    }

    public function test_archived_application_releases_its_quota_for_a_new_submission(): void
    {
        $application = $this->fundingApplication([], ['geschlossen' => false]);
        $applicant = $application->user;
        $applicant->assignRole(Role::ANTRAGSTELLER);
        $manager = User::factory()->create();
        $manager->assignRole(Role::FOERDERTOPFVERWALTER);
        $application->update(['status' => Application::STATUS_REJECTED]);

        Livewire::actingAs($manager)->test(AntraegePruefung::class)
            ->set('deletionReasons.' . $application->id, 'Der Verein reicht einen korrigierten Antrag ein.')
            ->call('archive', $application->id)
            ->assertHasNoErrors();

        Livewire::actingAs($applicant)->test(\App\Http\Livewire\AntragForm::class, [
            'typeId' => $application->application_type_id,
        ])
            ->set('fundingPotId', (string) $application->funding_pot_id)
            ->call('submitApplication')
            ->assertHasNoErrors()
            ->assertRedirect(route('antraege.meine'));

        $this->assertSame(1, Application::whereNotNull('submitted_at')->count());
        $this->assertSame(2, Application::withTrashed()->whereNotNull('submitted_at')->count());
    }

    public function test_administrator_can_configure_role_permissions(): void
    {
        $admin = User::factory()->create();
        $admin->assignRole(Role::ADMINISTRATOR);
        $managerRole = Role::where('name', Role::FOERDERTOPFVERWALTER)->firstOrFail();
        $permission = \App\Models\Permission::where('name', 'antraege.pruefen')->firstOrFail();

        Livewire::actingAs($admin)->test(\App\Http\Livewire\BenutzerRollen::class)
            ->call('togglePermission', $managerRole->id, $permission->id)
            ->assertHasNoErrors();

        $manager = User::factory()->create();
        $manager->assignRole(Role::FOERDERTOPFVERWALTER);
        $this->assertTrue(Gate::forUser($manager)->allows('antraege.pruefen'));

        Livewire::actingAs($admin)->test(\App\Http\Livewire\BenutzerRollen::class)
            ->call('togglePermission', $managerRole->id, $permission->id)
            ->assertHasNoErrors();
        $this->assertFalse(Gate::forUser($manager)->allows('antraege.pruefen'));
    }
}
