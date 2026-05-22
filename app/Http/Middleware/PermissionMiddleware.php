<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PermissionMiddleware
{
    public function handle(Request $request, Closure $next, $permission): Response
    {
        $user = auth()->user();

        // USER LOGIN CHECK
        if (!$user) {

            abort(403, 'Unauthorized');
        }

        // ADMIN BYPASS
        if ($user->roleData && $user->roleData->slug == 'admin') {

            return $next($request);
        }

        // USER ROLE RELATION
        $role = $user->roleData;

        if (!$role) {

            abort(403, 'Role Not Found');
        }

        // CHECK PERMISSION
        $hasPermission = $role->permissions()
            ->where('slug', $permission)
            ->exists();

        if (!$hasPermission) {

            abort(403, 'Permission Denied');
        }

        return $next($request);
    }
}
