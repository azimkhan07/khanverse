<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Buyer;
use App\Models\Seller;
use App\Models\Role;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $sellerRole = Role::where('slug', 'seller')->first();
        $buyerRole = Role::where('slug', 'buyer')->first();

        $sellerUser = User::firstOrCreate(
            ['email' => 'seller@skillnest.com'],
            [
                'name' => 'Demo Seller',
                'username' => 'demoseller',
                'email' => 'seller@skillnest.com',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'role_id' => $sellerRole?->id,
                'phone' => '9145547286',
                'status' => 1,
                'is_verified' => 1,
            ]
        );

        Seller::firstOrCreate(
            ['user_id' => $sellerUser->id],
            [
                'user_id' => $sellerUser->id,
                'full_name' => 'Demo Seller',
                'bio' => 'Professional freelancer with 5+ years of experience.',
                'skills' => '["laravel","react","javascript"]',
                'hourly_rate' => 25.00,
                'experience_level' => 'mid',
                'available_for_work' => true,
            ]
        );

        $buyerUser = User::firstOrCreate(
            ['email' => 'buyer@skillnest.com'],
            [
                'name' => 'Demo Buyer',
                'username' => 'demobuyer',
                'email' => 'buyer@skillnest.com',
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'role_id' => $buyerRole?->id,
                'phone' => '9145547287',
                'status' => 1,
                'is_verified' => 1,
            ]
        );

        Buyer::firstOrCreate(
            ['user_id' => $buyerUser->id],
            [
                'user_id' => $buyerUser->id,
                'full_name' => 'Demo Buyer',
                'company_name' => 'Demo Company',
            ]
        );

        $seller2User = User::firstOrCreate(
            ['email' => 'seller2@skillnest.com'],
            [
                'name' => 'Seller Two',
                'username' => 'sellertwo',
                'email' => 'seller2@skillnest.com',
                'password' => Hash::make('password'),
                'role' => 'seller',
                'role_id' => $sellerRole?->id,
                'phone' => '9145547288',
                'status' => 1,
                'is_verified' => 1,
            ]
        );

        Seller::firstOrCreate(
            ['user_id' => $seller2User->id],
            [
                'user_id' => $seller2User->id,
                'full_name' => 'Seller Two',
                'bio' => 'Expert in UI/UX design and frontend development.',
                'skills' => '["figma","css","tailwind"]',
                'hourly_rate' => 30.00,
                'experience_level' => 'senior',
                'available_for_work' => true,
            ]
        );

        $buyer2User = User::firstOrCreate(
            ['email' => 'buyer2@skillnest.com'],
            [
                'name' => 'Buyer Two',
                'username' => 'buyertwo',
                'email' => 'buyer2@skillnest.com',
                'password' => Hash::make('password'),
                'role' => 'buyer',
                'role_id' => $buyerRole?->id,
                'phone' => '9145547289',
                'status' => 1,
                'is_verified' => 1,
            ]
        );

        Buyer::firstOrCreate(
            ['user_id' => $buyer2User->id],
            [
                'user_id' => $buyer2User->id,
                'full_name' => 'Buyer Two',
                'company_name' => 'Another Company',
            ]
        );
    }
}
