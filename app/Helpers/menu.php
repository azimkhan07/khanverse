<?php

use App\Models\MenuItem;
use Illuminate\Support\Facades\Auth;


if (!function_exists('getSidebarMenu')) {

    function getSidebarMenu()
    {
        $user = auth()->user();

        if ($user && $user->roleData?->name == 'Admin') {
            return \App\Models\MenuItem::with('children')
                ->orderBy('sorting')
                ->get();
        }

        $permissions = [];

        if ($user) {
            $permissions = $user->rolePermissions()->pluck('name')->toArray();
        }

        return \App\Models\MenuItem::with('children')
            ->where(function ($query) use ($permissions) {

                $query->whereNull('permission')
                    ->orWhere(function ($q) use ($permissions) {

                        $q->whereNotNull('permission');

                        if (!empty($permissions)) {
                            $q->whereIn('permission', $permissions);
                        }
                    });
            })
            ->orderBy('sorting')
            ->get();
    }
}
