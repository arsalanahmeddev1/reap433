<?php

namespace App\Http\Middleware\Api;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class Authorize
{
    /**
     * Ensure the authenticated user has one of the allowed roles.
     * Usage: api.role:admin or api.role:admin|user
     * Returns JSON (API), does not redirect or abort HTML.
     *
     * @param  Closure(Request): (Response)  $next
     */
    public function handle(Request $request, Closure $next, string ...$roles): Response
    {
        $user = $request->user();

        if (! $user) {
            return response()->json([
                'success' => false,
                'code' => 401,
                'message' => 'Unauthenticated.',
                'errors' => null,
            ], 401);
        }

        $allowed = collect($roles)
            ->flatMap(fn (string $role) => explode('|', $role))
            ->filter()
            ->values()
            ->all();

        if (! in_array($user->role, $allowed, true)) {
            return response()->json([
                'success' => false,
                'code' => 403,
                'message' => 'Forbidden.',
                'errors' => null,
            ], 403);
        }

        return $next($request);
    }
}
