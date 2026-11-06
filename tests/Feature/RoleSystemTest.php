<?php

namespace Tests\Feature;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Tests\Concerns\PreparesRoleDatabase;
use Tests\TestCase;

class RoleSystemTest extends TestCase
{
    use PreparesRoleDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareRoleDatabase();
    }

    public function test_the_four_default_roles_exist(): void
    {
        $this->assertEqualsCanonicalizing(
            [Role::ADMINISTRATOR, Role::FOERDERTOPFVERWALTER, Role::GREMIUM, Role::ANTRAGSTELLER],
            Role::pluck('name')->all()
        );
    }

    public function test_new_users_have_no_roles(): void
    {
        $user = User::factory()->create();

        $this->assertCount(0, $user->roles);
        $this->assertFalse($user->hasRole(Role::ADMINISTRATOR, Role::FOERDERTOPFVERWALTER, Role::GREMIUM, Role::ANTRAGSTELLER));
        $this->assertSame(0, (int) $user->fresh()->admin);
    }

    public function test_user_can_hold_any_combination_of_roles(): void
    {
        $user = User::factory()->create();
        $user->assignRole(Role::GREMIUM);
        $user->assignRole(Role::ANTRAGSTELLER);
        $user->assignRole(Role::ANTRAGSTELLER);

        $this->assertCount(2, $user->roles);
        $this->assertTrue($user->hasRole(Role::GREMIUM));
        $this->assertTrue($user->hasRole(Role::ADMINISTRATOR, Role::ANTRAGSTELLER));
        $this->assertFalse($user->hasRole(Role::ADMINISTRATOR));

        $user->removeRole(Role::GREMIUM);
        $this->assertFalse($user->hasRole(Role::GREMIUM));
        $this->assertTrue($user->hasRole(Role::ANTRAGSTELLER));
    }

    public function test_default_role_permissions_are_enforced_by_gates(): void
    {
        $noRole = User::factory()->create();
        $this->assertFalse(Gate::forUser($noRole)->allows('antraege.foerdermittel_verwalten'));
        $this->assertFalse(Gate::forUser($noRole)->allows('antraege.pruefen'));
        $this->assertFalse(Gate::forUser($noRole)->allows('antraege.stellen'));

        $admin = User::factory()->create();
        $admin->assignRole(Role::ADMINISTRATOR);
        $this->assertTrue(Gate::forUser($admin)->allows('antraege.verwalten'));
        $this->assertTrue(Gate::forUser($admin)->allows('antraege.foerdermittel_verwalten'));
        $this->assertTrue(Gate::forUser($admin)->allows('antraege.entscheiden'));
        $this->assertTrue(Gate::forUser($admin)->allows('antraege.stellen'));

        $manager = User::factory()->create();
        $manager->assignRole(Role::FOERDERTOPFVERWALTER);
        $this->assertTrue(Gate::forUser($manager)->allows('antraege.foerdermittel_verwalten'));
        $this->assertFalse(Gate::forUser($manager)->allows('antraege.entscheiden'));
        $this->assertFalse(Gate::forUser($manager)->allows('antraege.verwalten'));

        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $this->assertTrue(Gate::forUser($gremium)->allows('antraege.pruefen'));
        $this->assertTrue(Gate::forUser($gremium)->allows('antraege.entscheiden'));
        $this->assertFalse(Gate::forUser($gremium)->allows('antraege.foerdermittel_verwalten'));
        $this->assertFalse(Gate::forUser($gremium)->allows('antraege.verwalten'));
    }

    public function test_manager_can_assign_only_gremium_and_applicant_roles(): void
    {
        $manager = User::factory()->create();
        $manager->assignRole(Role::FOERDERTOPFVERWALTER);
        $this->assertSame([Role::GREMIUM, Role::ANTRAGSTELLER], $manager->assignableRoles());

        $gremium = User::factory()->create();
        $gremium->assignRole(Role::GREMIUM);
        $this->assertSame([], $gremium->assignableRoles());
    }

    public function test_role_middleware_allows_only_matching_roles(): void
    {
        $this->app['router']->middleware(['web', 'auth', 'role:gremium,administrator'])
            ->get('/_role-test', fn () => 'ok');

        $this->get('/_role-test')->assertRedirect();

        $user = User::factory()->create();
        $this->actingAs($user)->get('/_role-test')->assertForbidden();

        $user->assignRole(Role::ANTRAGSTELLER);
        $this->actingAs($user)->get('/_role-test')->assertForbidden();

        $user->assignRole(Role::GREMIUM);
        $this->actingAs($user)->get('/_role-test')->assertOk();
    }

    public function test_command_assigns_and_removes_roles(): void
    {
        $user = User::factory()->create();

        $this->artisan('antraege:rolle', ['email' => $user->email, 'rolle' => Role::ADMINISTRATOR])->assertSuccessful();
        $this->assertTrue($user->fresh()->hasRole(Role::ADMINISTRATOR));

        $this->artisan('antraege:rolle', ['email' => $user->email, 'rolle' => Role::ADMINISTRATOR, '--entziehen' => true])->assertSuccessful();
        $this->assertFalse($user->fresh()->hasRole(Role::ADMINISTRATOR));

        $this->artisan('antraege:rolle', ['email' => $user->email, 'rolle' => 'unbekannt'])->assertFailed();
        $this->artisan('antraege:rolle', ['email' => 'niemand@example.test', 'rolle' => Role::GREMIUM])->assertFailed();
    }
}
