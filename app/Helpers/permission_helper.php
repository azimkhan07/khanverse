<?php

use App\Models\Permission;

if (!function_exists('hasPermission')) {

    function hasPermission($permission)
    {
        $user = auth()->user();

        // LOGIN CHECK
        if (!$user) {

            return false;
        }

        // ADMIN BYPASS
        if ($user->roleData && $user->roleData->slug == 'admin') {

            return true;
        }

        // ROLE CHECK
        if (!$user->roleData) {

            return false;
        }

        // PERMISSION CHECK
        return $user->roleData
            ->permissions()
            ->where('slug', $permission)
            ->exists();
    }
}