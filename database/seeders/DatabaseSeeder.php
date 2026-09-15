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
            CategoryFieldSeeder::class,
            ServiceTypeSeeder::class,
            ServiceSeeder::class,
            SettingSeeder::class,
            AuthSettingsSeeder::class,
            MenuSeeder::class,
            ContentSeeder::class,
            BlogPostSeeder::class,
            EmailTemplateSeeder::class,
            PaymentGatewaySeeder::class,
            GeoSeeder::class,
        ]);
    }
}
