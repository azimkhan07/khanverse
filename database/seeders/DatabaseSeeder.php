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
            ServiceSeeder::class,
            SettingSeeder::class,
            AuthSettingsSeeder::class,
            MenuSeeder::class,
            ContentSeeder::class,
            EmailTemplateSeeder::class,
        ]);
    }
}
