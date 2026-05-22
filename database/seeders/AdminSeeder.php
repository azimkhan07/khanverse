<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Admin;
use Illuminate\Support\Facades\Hash;

class AdminSeeder extends Seeder
{
    public function run()
    {

        // USER LOGIN ACCOUNT

        $user = User::create([

            'username' => 'admin',

            'email' => 'admin@khanverse.com',

            'password' => Hash::make('admin123'),

            'role' => 'admin',

            'phone' => '9145547285',

            'status' => 1,

            'is_verified' => 1
        ]);


        // ADMIN PROFILE

        Admin::create([

            'user_id' => $user->id,

            'full_name' => 'Khanverse Super Admin'

        ]);
    }
}