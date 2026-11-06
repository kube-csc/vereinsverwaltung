<?php

namespace Tests\Feature;

use App\Http\Livewire\Foerdertoepfe;
use App\Http\Livewire\MeineAntraege;
use App\Models\Application;
use App\Models\ApplicationType;
use App\Models\BankAccount;
use App\Models\FundingPot;
use App\Models\FundingPotBinding;
use App\Models\FundingProgram;
use App\Models\Role;
use App\Models\User;
use Livewire\Livewire;
use Tests\Concerns\PreparesRoleDatabase;
use Tests\TestCase;

class PayoutExportTest extends TestCase
{
    use PreparesRoleDatabase;

    private const IBAN = 'DE89370400440532013000';

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareRoleDatabase();
        \App\Models\BankAccount::create([
            'bezeichnung' => 'Standard', 'kontoinhaber' => 'SSV Datteln', 'iban' => 'DE02120300000000202051',
        ]);
    }

    private function settledPot(?array $snapshot, float $amount = 100): array
    {
        $pot = FundingProgram::create(['name' => 'Jugend'])->pots()->create([
            'name' => 'Töpfchen Ü', 'laufzeit' => FundingPot::FORTLAUFEND, 'budget' => 1000, 'geschlossen' => true,
        ]);
        $type = ApplicationType::firstOrCreate(['slug' => 'jugend'], [
            'name' => 'Jugend', 'aktiv' => true, 'requires_club' => false, 'requires_funding_pot' => true,
        ]);
        $applicant = User::factory()->create();
        $applicant->assignRole(Role::ANTRAGSTELLER);
        $application = Application::create([
            'application_type_id' => $type->id,
            'funding_pot_id' => $pot->id,
            'user_id' => $applicant->id,
            'status' => Application::STATUS_APPROVED,
            'submitted_at' => now(),
            'answers' => [],
            'approved_amount' => $amount,
            'bank_account_snapshot' => $snapshot,
        ]);
        $pot->bindings()->create(['art' => FundingPotBinding::BEWILLIGT, 'betrag' => $amount, 'application_id' => $application->id]);

        $manager = User::factory()->create();
        $manager->assignRole(Role::FOERDERTOPFVERWALTER);
        $pot->settle($manager);

        return [$pot->fresh(), $application->fresh(), $manager, $applicant];
    }

    public function test_export_creates_valid_sepa_file_and_marks_payments(): void
    {
        [$pot, $application, $manager] = $this->settledPot([
            'kontoinhaber' => 'Jörg Müller', 'bank_name' => 'Sparkasse', 'iban' => self::IBAN, 'bic' => null,
        ], 123.45);

        $xml = $pot->exportPayouts($manager);

        $doc = new \DOMDocument();
        $doc->loadXML($xml);
        $xpath = new \DOMXPath($doc);
        $xpath->registerNamespace('p', 'urn:iso:std:iso:20022:tech:xsd:pain.001.001.03');
        $this->assertSame('123.45', $xpath->evaluate('string(//p:CdtTrfTxInf/p:Amt/p:InstdAmt)'));
        $this->assertSame('123.45', $xpath->evaluate('string(//p:GrpHdr/p:CtrlSum)'));
        $this->assertSame('Joerg Mueller', $xpath->evaluate('string(//p:Cdtr/p:Nm)'));
        $this->assertSame(self::IBAN, $xpath->evaluate('string(//p:CdtrAcct//p:IBAN)'));

        $this->assertNotNull($application->fresh()->payout_exported_at);
        $this->assertDatabaseHas('application_events', ['application_id' => $application->id, 'event_type' => Application::EVENT_PAYOUT_EXPORTED]);
        $this->assertCount(0, $pot->openPayouts()['ready']);

        $this->expectException(\DomainException::class);
        $pot->exportPayouts($manager);
    }

    public function test_application_without_bank_data_is_skipped_until_applicant_adds_it(): void
    {
        [$pot, $application, $manager, $applicant] = $this->settledPot(null);

        $this->assertCount(1, $pot->openPayouts()['missing']);
        Livewire::actingAs($manager)->test(Foerdertoepfe::class)
            ->assertSee('Bankverbindung fehlt')
            ->call('downloadPayoutFile', $pot->id)
            ->assertNoRedirect();
        $this->assertNull($application->fresh()->payout_exported_at);

        $account = $applicant->bankAccounts()->create(['kontoinhaber' => 'Max', 'iban' => self::IBAN, 'bic' => 'COBADEFFXXX']);
        Livewire::actingAs($applicant)->test(MeineAntraege::class)
            ->set('payoutAccounts.' . $application->id, (string) $account->id)
            ->call('updatePayoutAccount', $application->id)
            ->assertHasNoErrors();

        $this->assertCount(1, $pot->fresh()->openPayouts()['ready']);
        $this->assertStringContainsString('COBADEFFXXX', $pot->fresh()->exportPayouts($manager));
    }

    public function test_changed_bank_data_after_export_creates_new_payment(): void
    {
        [$pot, $application, $manager, $applicant] = $this->settledPot([
            'kontoinhaber' => 'Max', 'bank_name' => null, 'iban' => self::IBAN, 'bic' => null,
        ]);
        $pot->exportPayouts($manager);

        $account = $applicant->bankAccounts()->create(['kontoinhaber' => 'Max', 'iban' => 'DE02120300000000202051']);
        Livewire::actingAs($applicant)->test(MeineAntraege::class)
            ->set('payoutAccounts.' . $application->id, (string) $account->id)
            ->call('updatePayoutAccount', $application->id);

        $this->assertNull($application->fresh()->payout_exported_at);
        $this->assertStringContainsString('DE02120300000000202051', $pot->fresh()->exportPayouts($manager));
        $this->assertDatabaseHas('application_events', ['application_id' => $application->id, 'event_type' => Application::EVENT_BANK_CHANGED]);
    }

    public function test_single_bank_account_is_used_automatically_but_two_accounts_are_not(): void
    {
        [$pot, $application, $manager, $applicant] = $this->settledPot(null);

        $applicant->bankAccounts()->create(['kontoinhaber' => 'Max', 'iban' => self::IBAN]);
        $this->assertCount(1, $pot->fresh()->openPayouts()['ready']);
        $this->assertStringContainsString(self::IBAN, $pot->fresh()->exportPayouts($manager));
        $this->assertSame(self::IBAN, $application->fresh()->bank_account_snapshot['iban']);

        [$pot2, , , $applicant2] = $this->settledPot(null);
        $applicant2->bankAccounts()->create(['kontoinhaber' => 'A', 'iban' => self::IBAN]);
        $applicant2->bankAccounts()->create(['kontoinhaber' => 'B', 'iban' => 'DE02120300000000202051']);
        $this->assertCount(1, $pot2->fresh()->openPayouts()['missing']);
    }

    public function test_pot_bank_account_is_saved_and_used_as_debtor(): void
    {
        \App\Models\BankAccount::query()->forceDelete();
        [$pot, , $manager] = $this->settledPot([
            'kontoinhaber' => 'Max', 'bank_name' => null, 'iban' => self::IBAN, 'bic' => null,
        ]);

        // Ohne Auftraggeberkonto ist kein Export möglich
        try {
            $pot->fresh()->exportPayouts($manager);
            $this->fail('Export ohne Auftraggeberkonto darf nicht gelingen.');
        } catch (\DomainException $e) {
            $this->assertStringContainsString('Auftraggeberkonto', $e->getMessage());
        }

        $account = \App\Models\BankAccount::create([
            'bezeichnung' => 'Jugend Sparkasse', 'kontoinhaber' => 'SSV Datteln', 'iban' => 'DE02120300000000202051',
        ]);
        $userAccount = User::factory()->create()->bankAccounts()->create(['kontoinhaber' => 'X', 'iban' => self::IBAN]);

        // Genau ein Vereinskonto: wird automatisch genutzt
        $this->assertSame($account->id, $pot->fresh()->debtorAccount()->id);

        $other = \App\Models\BankAccount::create([
            'bezeichnung' => 'Anderes Konto', 'kontoinhaber' => 'SSV', 'iban' => self::IBAN,
        ]);
        $this->assertNull($pot->fresh()->debtorAccount());

        Livewire::actingAs($manager)->test(Foerdertoepfe::class)
            ->assertSee('Jugend Sparkasse')
            ->assertSee('Anderes Konto')
            ->call('assignPotAccount', $pot->id, $account->id);
        $this->assertSame($account->id, $pot->fresh()->bank_account_id);

        $pot = $pot->fresh();
        $xml = $pot->exportPayouts($manager);
        $this->assertStringContainsString('<IBAN>DE02120300000000202051</IBAN>', $xml);
        $this->assertStringContainsString('SSV Datteln', $xml);

        Livewire::actingAs($manager)->test(Foerdertoepfe::class)
            ->call('assignPotAccount', $pot->id, $other->id)
            ->assertHasErrors('potAccount' . $pot->id);
        $this->assertSame($account->id, $pot->fresh()->bank_account_id);

        Livewire::actingAs($manager)->test(Foerdertoepfe::class)
            ->call('deleteAccount', $account->id);
        $this->assertNotNull($account->fresh());
        $this->assertNull($account->fresh()->deleted_at);

        Livewire::actingAs($manager)->test(Foerdertoepfe::class)
            ->set('accountBezeichnung', 'Neu')->set('accountKontoinhaber', 'V')->set('accountIban', 'DE00123')
            ->call('saveAccount')
            ->assertHasErrors('accountIban');
    }

    public function test_foreign_bank_account_is_rejected_and_revoke_blocked_after_export(): void
    {
        [$pot, $application, $manager, $applicant] = $this->settledPot([
            'kontoinhaber' => 'Max', 'bank_name' => null, 'iban' => self::IBAN, 'bic' => null,
        ]);
        $foreign = User::factory()->create()->bankAccounts()->create(['kontoinhaber' => 'Fremd', 'iban' => 'DE02120300000000202051']);

        Livewire::actingAs($applicant)->test(MeineAntraege::class)
            ->set('payoutAccounts.' . $application->id, (string) $foreign->id)
            ->call('updatePayoutAccount', $application->id)
            ->assertHasErrors();

        $pot->exportPayouts($manager);
        $this->expectException(\DomainException::class);
        $pot->fresh()->revokeSettlement($manager);
    }
}
