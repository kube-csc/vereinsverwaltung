<?php

namespace Tests\Feature;

use App\Http\Livewire\Bankkonten;
use App\Models\BankAccount;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Livewire;
use Tests\Concerns\PreparesRoleDatabase;
use Tests\TestCase;

class BankAccountTest extends TestCase
{
    use PreparesRoleDatabase;

    private const IBAN = 'DE89370400440532013000';

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareRoleDatabase();
    }

    private function antragsteller(): User
    {
        $user = User::factory()->create();
        $user->assignRole(Role::ANTRAGSTELLER);

        return $user;
    }

    public function test_iban_validation(): void
    {
        $this->assertTrue(BankAccount::isValidIban('DE89 3704 0044 0532 0130 00'));
        $this->assertTrue(BankAccount::isValidIban('gb82west12345698765432'));
        $this->assertFalse(BankAccount::isValidIban('DE89370400440532013001'));
        $this->assertFalse(BankAccount::isValidIban('1234'));
    }

    public function test_page_requires_antragsteller_role(): void
    {
        $this->actingAs(User::factory()->create())->get(route('antraege.bankkonten'))->assertForbidden();
        $this->actingAs($this->antragsteller())->get(route('antraege.bankkonten'))->assertOk();
    }

    public function test_user_can_create_account_and_iban_is_stored_encrypted(): void
    {
        $user = $this->antragsteller();

        Livewire::actingAs($user)->test(Bankkonten::class)
            ->set('bezeichnung', 'Konto')
            ->set('kontoinhaber', 'TV Beispiel e.V.')
            ->set('iban', 'de89 3704 0044 0532 0130 00')
            ->set('bic', 'cobadeffxxx')
            ->call('save')
            ->assertHasNoErrors();

        $account = $user->bankAccounts()->firstOrFail();
        $this->assertSame(self::IBAN, $account->iban);
        $this->assertSame('COBADEFFXXX', $account->bic);

        $raw = DB::table('bank_accounts')->value('iban');
        $this->assertStringNotContainsString(self::IBAN, $raw);
    }

    public function test_invalid_iban_is_rejected(): void
    {
        Livewire::actingAs($this->antragsteller())->test(Bankkonten::class)
            ->set('bezeichnung', 'Konto')
            ->set('kontoinhaber', 'Test')
            ->set('iban', 'DE89370400440532013001')
            ->call('save')
            ->assertHasErrors('iban');

        $this->assertSame(0, BankAccount::count());
    }

    public function test_user_can_have_multiple_accounts_and_edit_them(): void
    {
        $user = $this->antragsteller();
        $user->bankAccounts()->create(['kontoinhaber' => 'A', 'iban' => self::IBAN]);
        $second = $user->bankAccounts()->create(['kontoinhaber' => 'B', 'iban' => 'GB82WEST12345698765432']);

        Livewire::actingAs($user)->test(Bankkonten::class)
            ->call('edit', $second->id)
            ->set('bezeichnung', 'Konto')
            ->set('kontoinhaber', 'B neu')
            ->call('save')
            ->assertHasNoErrors();

        $this->assertSame(2, $user->bankAccounts()->count());
        $this->assertSame('B neu', $second->fresh()->kontoinhaber);
    }

    public function test_user_cannot_access_foreign_accounts(): void
    {
        $owner = $this->antragsteller();
        $foreign = $owner->bankAccounts()->create(['kontoinhaber' => 'Fremd', 'iban' => self::IBAN]);

        $other = $this->antragsteller();

        $component = Livewire::actingAs($other)->test(Bankkonten::class)->assertDontSee('Fremd');

        foreach (['edit', 'delete'] as $action) {
            try {
                $component->call($action, $foreign->id);
                $this->fail("Aktion {$action} auf fremdes Konto war möglich.");
            } catch (\Illuminate\Database\Eloquent\ModelNotFoundException) {
                $this->assertTrue(true);
            }
        }

        $this->assertNotNull($foreign->fresh());
    }

    public function test_delete_is_a_soft_delete(): void
    {
        $user = $this->antragsteller();
        $account = $user->bankAccounts()->create(['kontoinhaber' => 'A', 'iban' => self::IBAN]);

        Livewire::actingAs($user)->test(Bankkonten::class)->call('delete', $account->id);

        $this->assertSame(0, $user->bankAccounts()->count());
        $this->assertNotNull(BankAccount::withTrashed()->find($account->id));
    }

    public function test_iban_is_not_serialized(): void
    {
        $account = $this->antragsteller()->bankAccounts()->create(['kontoinhaber' => 'A', 'iban' => self::IBAN]);

        $this->assertArrayNotHasKey('iban', $account->toArray());
    }
}

