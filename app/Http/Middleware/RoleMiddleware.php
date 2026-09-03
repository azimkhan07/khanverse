<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    public function handle(Request $request, Closure $next, $role): Response
    {
        $user = auth()->user();
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        $userRole = $user->role ?? optional($user->roleData)->slug;

        if ($userRole !== $role) {
            abort(403, 'Unauthorized Role');
        }

        return $next($request);
    }
}
