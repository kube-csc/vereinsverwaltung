<?php

namespace Tests\Feature;

use App\Http\Livewire\BenutzerRollen;
use App\Http\Livewire\InvitationManager;
use App\Models\Invitation;
use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Mail;
use Livewire\Livewire;
use Tests\Concerns\PreparesRoleDatabase;
use Tests\TestCase;

class UserManagementTest extends TestCase
{
    use PreparesRoleDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->prepareRoleDatabase();
    }

    private function userWithRole(string $role): User
    {
        $user = User::factory()->create();
        $user->assignRole($role);

        return $user;
    }

    public function test_assignable_roles_depend_on_the_role_of_the_actor(): void
    {
        $this->assertEqualsCanonicalizing(
            [Role::ADMINISTRATOR, Role::FOERDERTOPFVERWALTER, Role::GREMIUM, Role::ANTRAGSTELLER],
            $this->userWithRole(Role::ADMINISTRATOR)->assignableRoles()
        );
        $this->assertSame(
            [Role::GREMIUM, Role::ANTRAGSTELLER],
            $this->userWithRole(Role::FOERDERTOPFVERWALTER)->assignableRoles()
        );
        $this->assertSame([], $this->userWithRole(Role::GREMIUM)->assignableRoles());
        $this->assertSame([], $this->userWithRole(Role::ANTRAGSTELLER)->assignableRoles());
        $this->assertSame([], User::factory()->create()->assignableRoles());
    }

    public function test_user_page_is_only_visible_for_administrator_and_funding_manager(): void
    {
        $this->actingAs($this->userWithRole(Role::ANTRAGSTELLER))
            ->get(route('antraege.benutzer'))->assertForbidden();

        $this->actingAs($this->userWithRole(Role::GREMIUM))
            ->get(route('antraege.benutzer'))->assertForbidden();

        $this->actingAs($this->userWithRole(Role::FOERDERTOPFVERWALTER))
            ->get(route('antraege.benutzer'))->assertOk();

        $this->actingAs($this->userWithRole(Role::ADMINISTRATOR))
            ->get(route('antraege.benutzer'))->assertOk();
    }

    public function test_legacy_admin_level_four_can_open_user_management_and_assign_any_role(): void
    {
        $legacyAdmin = User::factory()->create(['admin' => 4]);

        $this->actingAs($legacyAdmin)->get(route('antraege.benutzer'))->assertOk();
        $this->assertEqualsCanonicalizing(Role::pluck('name')->all(), $legacyAdmin->assignableRoles());

        $target = User::factory()->create();
        Livewire::actingAs($legacyAdmin)->test(BenutzerRollen::class)
            ->call('toggleRole', $target->id, Role::ADMINISTRATOR)
            ->assertHasNoErrors();

        $this->assertTrue($target->fresh()->hasRole(Role::ADMINISTRATOR));
    }

    public function test_administrator_can_toggle_roles(): void
    {
        $admin = $this->userWithRole(Role::ADMINISTRATOR);
        $target = User::factory()->create();

        Livewire::actingAs($admin)->test(BenutzerRollen::class)
            ->call('toggleRole', $target->id, Role::GREMIUM);
        $this->assertTrue($target->fresh()->hasRole(Role::GREMIUM));

        Livewire::actingAs($admin)->test(BenutzerRollen::class)
            ->call('toggleRole', $target->id, Role::GREMIUM);
        $this->assertFalse($target->fresh()->hasRole(Role::GREMIUM));
    }

    public function test_funding_manager_may_only_assign_gremium_and_antragsteller(): void
    {
        $manager = $this->userWithRole(Role::FOERDERTOPFVERWALTER);
        $target = User::factory()->create();

        Livewire::actingAs($manager)->test(BenutzerRollen::class)
            ->call('toggleRole', $target->id, Role::ANTRAGSTELLER);
        $this->assertTrue($target->fresh()->hasRole(Role::ANTRAGSTELLER));

        Livewire::actingAs($manager)->test(BenutzerRollen::class)
            ->call('toggleRole', $target->id, Role::GREMIUM);
        $this->assertTrue($target->fresh()->hasRole(Role::GREMIUM));

        Livewire::actingAs($manager)->test(BenutzerRollen::class)
            ->call('toggleRole', $target->id, Role::ADMINISTRATOR)
            ->assertForbidden();
        $this->assertFalse($target->fresh()->hasRole(Role::ADMINISTRATOR));
    }

    public function test_search_finds_users_by_first_name_last_name_and_email(): void
    {
        $admin = $this->userWithRole(Role::ADMINISTRATOR);
        User::factory()->create(['vorname' => 'Max', 'nachname' => 'Mustermann', 'email' => 'max@example.org']);
        User::factory()->create(['vorname' => 'Erika', 'nachname' => 'Beispiel', 'email' => 'erika@example.org']);

        foreach (['Max', 'mustermann', 'Mustermann Max', 'max@example'] as $term) {
            Livewire::actingAs($admin)->test(BenutzerRollen::class)
                ->set('search', $term)
                ->assertSee('max@example.org')
                ->assertDontSee('erika@example.org');
        }
    }

    public function test_list_is_paginated_with_adjustable_page_size(): void
    {
        $admin = $this->userWithRole(Role::ADMINISTRATOR);
        User::factory()->count(30)->create();

        $component = Livewire::actingAs($admin)->test(BenutzerRollen::class)
            ->set('perPage', 10);
        $this->assertCount(10, $component->viewData('users')->items());
        $this->assertSame(31, $component->viewData('users')->total());

        $component->set('perPage', 999);
        $this->assertSame(25, $component->get('perPage'));
    }

    public function test_last_administrator_cannot_remove_own_role(): void
    {
        $admin = $this->userWithRole(Role::ADMINISTRATOR);

        Livewire::actingAs($admin)->test(BenutzerRollen::class)
            ->call('toggleRole', $admin->id, Role::ADMINISTRATOR);

        $this->assertTrue($admin->fresh()->hasRole(Role::ADMINISTRATOR));
    }

    public function test_invitation_stores_only_roles_the_inviter_may_assign(): void
    {
        Mail::fake();
        $manager = $this->userWithRole(Role::FOERDERTOPFVERWALTER);

        Livewire::actingAs($manager)->test(InvitationManager::class)
            ->set('label', 'Max Mustermann')
            ->set('selectedRoles', [Role::ANTRAGSTELLER, Role::ADMINISTRATOR])
            ->call('sendInvitation');

        $invitation = Invitation::firstOrFail();
        $this->assertSame([Role::ANTRAGSTELLER], $invitation->roles->pluck('name')->all());
    }

    public function test_invitation_without_roles_creates_no_roles(): void
    {
        $admin = $this->userWithRole(Role::ADMINISTRATOR);

        Livewire::actingAs($admin)->test(InvitationManager::class)
            ->set('label', 'Ohne Rolle')
            ->call('sendInvitation');

        $this->assertCount(0, Invitation::firstOrFail()->roles);
    }
}
