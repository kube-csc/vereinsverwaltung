<?php

namespace App\Http\Livewire;

use App\Models\Role;
use App\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Gate;
use Livewire\Component;
use Livewire\WithPagination;

class BenutzerRollen extends Component
{
    use WithPagination;

    public const PRO_SEITE = [10, 25, 50, 100];

    public string $search = '';
    public int $perPage = 25;

    public function mount(): void
    {
        Gate::authorize('antraege.benutzer');
    }

    public function togglePermission(int $roleId, int $permissionId): void
    {
        Gate::authorize('antraege.rechte_verwalten');
        $role = Role::with('permissions')->findOrFail($roleId);
        abort_unless($role->name !== Role::ADMINISTRATOR, 422);
        $permission = Permission::findOrFail($permissionId);

        if ($role->permissions->contains('id', $permission->id)) {
            $role->permissions()->detach($permission);
        } else {
            $role->permissions()->syncWithoutDetaching([$permission->id]);
        }
    }

    public function updatingSearch(): void
    {
        $this->resetPage();
    }

    public function updatedPerPage(): void
    {
        if (! in_array($this->perPage, self::PRO_SEITE, true)) {
            $this->perPage = 25;
        }
        $this->resetPage();
    }

    public function toggleRole(int $userId, string $role): void
    {
        $actor = auth()->user();
        abort_unless(in_array($role, $actor->assignableRoles(), true), 403);

        $user = User::findOrFail($userId);

        if ($user->hasRole($role)) {
            // Der letzte Administrator darf sich die Rolle nicht selbst entziehen
            if ($role === Role::ADMINISTRATOR && $user->is($actor) && Role::where('name', $role)->first()->users()->count() <= 1) {
                session()->flash('message', 'Die letzte Administrator-Rolle kann nicht entzogen werden.');
                return;
            }
            $user->removeRole($role);
        } else {
            $user->assignRole($role);
        }
    }

    public function render()
    {
        Gate::authorize('antraege.benutzer');
        $words = preg_split('/\s+/', trim($this->search), -1, PREG_SPLIT_NO_EMPTY);

        $users = User::with('roles')
            ->when($words, function ($query) use ($words) {
                foreach ($words as $word) {
                    $like = '%' . addcslashes($word, '\\%_') . '%';
                    $query->where(fn ($q) => $q
                        ->where('vorname', 'like', $like)
                        ->orWhere('nachname', 'like', $like)
                        ->orWhere('email', 'like', $like));
                }
            })
            ->orderBy('nachname')->orderBy('vorname')
            ->paginate($this->perPage);

        return view('livewire.benutzer-rollen', [
            'users' => $users,
            'roles' => Role::with('permissions')->orderBy('id')->get(),
            'permissions' => Permission::orderBy('id')->get(),
            'assignable' => auth()->user()->assignableRoles(),
        ]);
    }
}
