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
        // dd($user);
        if (!$user) {
            abort(403, 'Unauthorized');
        }

        if (!$user->roleData) {
            abort(403, 'Role Not Found');
        }

        if ($user->roleData->slug !== $role) {
            abort(403, 'Unauthorized Role');
        }

        return $next($request);
    }
}
