<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            RolePermissionSeeder::class,
            AdminSeeder::class,
            UserSeeder::class,
            CategorySeeder::class,
            SettingSeeder::class,
            MenuSeeder::class,
            ContentSeeder::class,
        ]);
    }
}
