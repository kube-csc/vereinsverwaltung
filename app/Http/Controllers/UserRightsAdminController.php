<?php

namespace App\Http\Controllers;

use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class UserRightsAdminController extends Controller
{
    private const MIN_ADMIN_LEVEL = 4;

    public function index(Request $request): View
    {
        $this->authorizeRightsAdmin();

        $q = trim((string) $request->get('q', ''));
        $level = $request->get('level', '');
        $level = ($level !== '' && ctype_digit((string) $level)) ? (int) $level : null;
        $status = in_array($request->get('status'), ['aktiv', 'ausgetreten', 'alle'], true)
            ? $request->get('status')
            : 'aktiv';

        $users = User::query()
            ->when($status === 'aktiv', function ($query) {
                $query->where(function ($sub) {
                    $sub->whereNull('vereinsaustritt')
                        ->orWhere('vereinsaustritt', '>', Carbon::today());
                });
            })
            ->when($status === 'ausgetreten', function ($query) {
                $query->where('vereinsaustritt', '<=', Carbon::today());
            })
            ->when($level !== null, fn ($query) => $query->where('admin', $level))
            ->when($q !== '', function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('vorname', 'like', '%' . $q . '%')
                        ->orWhere('nachname', 'like', '%' . $q . '%')
                        ->orWhere('email', 'like', '%' . $q . '%');
                });
            })
            ->orderBy('nachname')
            ->orderBy('vorname')
            ->paginate(25)
            ->withQueryString();

        return view('admin.rights.index', [
            'users' => $users,
            'q' => $q,
            'level' => $level,
            'status' => $status,
            'maxLevel' => $this->maxAssignableLevel(),
        ]);
    }

    public function update(Request $request, User $user): RedirectResponse
    {
        $this->authorizeRightsAdmin();

        $maxLevel = $this->maxAssignableLevel();

        $data = $request->validate([
            'admin' => ['required', 'integer', 'min:0', 'max:' . $maxLevel],
            'vereinseintritt' => ['nullable', 'date'],
            'vereinsaustritt' => ['nullable', 'date', 'after_or_equal:vereinseintritt'],
        ]);

        $newLevel = (int) $data['admin'];

        // Nur wer mindestens die Stufe des Zielbenutzers hat, darf dessen Rechte ändern.
        if ((int) $user->admin > $maxLevel) {
            abort(403);
        }

        $eintritt = $data['vereinseintritt'] ?? null;
        $austritt = $data['vereinsaustritt'] ?? null;

        $error = DB::transaction(function () use ($user, $newLevel, $eintritt, $austritt) {
            $locked = User::query()->where('admin', '>=', self::MIN_ADMIN_LEVEL)->lockForUpdate()->pluck('id');

            $wasAdmin = (int) $user->admin >= self::MIN_ADMIN_LEVEL;
            $remains = $newLevel >= self::MIN_ADMIN_LEVEL;

            if ($wasAdmin && !$remains && $locked->count() <= 1) {
                return 'Es muss immer mindestens ein Benutzer mit Admin-Stufe ' . self::MIN_ADMIN_LEVEL . ' oder höher existieren.';
            }

            $user->admin = $newLevel;
            $user->vereinseintritt = $eintritt ?: null;
            $user->vereinsaustritt = $austritt ?: null;
            $user->save();

            return null;
        });

        if ($error) {
            return redirect()->route('admin.rights.index', $request->only('q', 'level', 'status', 'page'))
                ->withErrors(['admin' => $error]);
        }

        return redirect()->route('admin.rights.index', $request->only('q', 'level', 'status', 'page'))
            ->with('success', 'Rechte für ' . e($user->email) . ' gespeichert.');
    }

    private function authorizeRightsAdmin(): void
    {
        abort_unless((int) Auth::user()->admin >= self::MIN_ADMIN_LEVEL, 403);
    }

    private function maxAssignableLevel(): int
    {
        return (int) Auth::user()->admin;
    }
}
