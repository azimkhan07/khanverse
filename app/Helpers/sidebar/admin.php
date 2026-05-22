<?php

use App\Models\MenuItem;
use Illuminate\Support\Facades\Auth;

if (!function_exists('getAdminSidebarMenu')) {

    function getAdminSidebarMenu()
    {
        $user = Auth::user();
        $role = $user->role ?? 'guest';

        return MenuItem::where('is_active', 1)
            ->where(function ($q) use ($role) {
                $q->whereJsonContains('roles', $role)
                  ->orWhereNull('roles');
            })
            ->orderBy('sort_order')
            ->get();
    }
}