<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class EnsureUserHasRole
{
    /**
     * Lässt nur Benutzer durch, die mindestens eine der angegebenen Rollen besitzen.
     */
    public function handle(Request $request, Closure $next, string ...$roles)
    {
        $user = $request->user();

        abort_unless($user && $user->hasRole(...$roles), 403);

        return $next($request);
    }
}
