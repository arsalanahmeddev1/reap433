<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$roles)
    {
        $user = auth()->user();

        // user login check
        if (!$user) {
            return redirect('/');
        }

        $roles = collect($roles)
            ->flatMap(fn($role) => explode('|', $role))
            ->toArray();

        if (!in_array($user->role, $roles)) {
            abort(403);
        }

        return $next($request);
    }
}
