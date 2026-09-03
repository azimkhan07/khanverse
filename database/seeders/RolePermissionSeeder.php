<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolePermissionSeeder extends Seeder
{
    public function run(): void
    {
        $adminRole = Role::firstOrCreate(
            ['slug' => 'admin'],
            ['name' => 'Admin', 'slug' => 'admin', 'status' => true]
        );

        $sellerRole = Role::firstOrCreate(
            ['slug' => 'seller'],
            ['name' => 'Seller', 'slug' => 'seller', 'status' => true]
        );

        $buyerRole = Role::firstOrCreate(
            ['slug' => 'buyer'],
            ['name' => 'Buyer', 'slug' => 'buyer', 'status' => true]
        );

        $permissions = [
            ['name' => 'View Users', 'slug' => 'users.view', 'group' => 'users', 'module' => 'users', 'status' => true],
            ['name' => 'Manage Users', 'slug' => 'users.manage', 'group' => 'users', 'module' => 'users', 'status' => true],
            ['name' => 'View Orders', 'slug' => 'orders.view', 'group' => 'orders', 'module' => 'orders', 'status' => true],
            ['name' => 'Manage Orders', 'slug' => 'orders.manage', 'group' => 'orders', 'module' => 'orders', 'status' => true],
            ['name' => 'View Services', 'slug' => 'services.view', 'group' => 'services', 'module' => 'services', 'status' => true],
            ['name' => 'Manage Services', 'slug' => 'services.manage', 'group' => 'services', 'module' => 'services', 'status' => true],
            ['name' => 'View Projects', 'slug' => 'projects.view', 'group' => 'projects', 'module' => 'projects', 'status' => true],
            ['name' => 'Manage Projects', 'slug' => 'projects.manage', 'group' => 'projects', 'module' => 'projects', 'status' => true],
            ['name' => 'View Categories', 'slug' => 'categories.view', 'group' => 'categories', 'module' => 'categories', 'status' => true],
            ['name' => 'Manage Categories', 'slug' => 'categories.manage', 'group' => 'categories', 'module' => 'categories', 'status' => true],
            ['name' => 'View Wallet', 'slug' => 'wallet.view', 'group' => 'wallet', 'module' => 'wallet', 'status' => true],
            ['name' => 'Manage Wallet', 'slug' => 'wallet.manage', 'group' => 'wallet', 'module' => 'wallet', 'status' => true],
            ['name' => 'View Reviews', 'slug' => 'reviews.view', 'group' => 'reviews', 'module' => 'reviews', 'status' => true],
            ['name' => 'Manage Reviews', 'slug' => 'reviews.manage', 'group' => 'reviews', 'module' => 'reviews', 'status' => true],
            ['name' => 'View Settings', 'slug' => 'settings.view', 'group' => 'settings', 'module' => 'settings', 'status' => true],
            ['name' => 'Manage Settings', 'slug' => 'settings.manage', 'group' => 'settings', 'module' => 'settings', 'status' => true],
            ['name' => 'Manage Roles', 'slug' => 'roles.manage', 'group' => 'roles', 'module' => 'roles', 'status' => true],
            ['name' => 'Manage Permissions', 'slug' => 'permissions.manage', 'group' => 'permissions', 'module' => 'permissions', 'status' => true],
            ['name' => 'Manage Website', 'slug' => 'website.manage', 'group' => 'website', 'module' => 'website', 'status' => true],
            ['name' => 'View Reports', 'slug' => 'reports.view', 'group' => 'reports', 'module' => 'reports', 'status' => true],
        ];

        foreach ($permissions as $perm) {
            Permission::firstOrCreate(
                ['slug' => $perm['slug']],
                $perm
            );
        }

        $allPermissions = Permission::pluck('id')->toArray();
        $adminRole->permissions()->sync($allPermissions);

        $sellerPerms = Permission::whereIn('slug', [
            'orders.view', 'services.view', 'services.manage',
            'projects.view', 'wallet.view', 'reviews.view',
        ])->pluck('id')->toArray();
        $sellerRole->permissions()->sync($sellerPerms);

        $buyerPerms = Permission::whereIn('slug', [
            'orders.view', 'projects.view', 'wallet.view', 'reviews.view', 'reviews.manage',
        ])->pluck('id')->toArray();
        $buyerRole->permissions()->sync($buyerPerms);
    }
}
