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
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string...$roles): Response
    {
        $user = $request->user();

        // user not logged in
        if (! $user) {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        // check role
        foreach ($roles as $role) {
            if ($user->hasRole($role)) {
                return $next($request);
            }
        }

        // role not found
        return response()->json(['message' => 'Forbidden'], 403);
    }
}
