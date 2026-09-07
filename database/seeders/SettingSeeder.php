<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Setting;

class SettingSeeder extends Seeder
{
    public function run(): void
    {
        $settings = [
            ['group' => 'admin', 'key' => 'site_name', 'value' => 'SkillNest', 'type' => 'text'],
            ['group' => 'admin', 'key' => 'site_email', 'value' => 'admin@skillnest.com', 'type' => 'text'],
            ['group' => 'admin', 'key' => 'site_logo', 'value' => 'logo.png', 'type' => 'image'],
            ['group' => 'admin', 'key' => 'currency', 'value' => 'USD', 'type' => 'text'],
            ['group' => 'admin', 'key' => 'currency_symbol', 'value' => '$', 'type' => 'text'],
            ['group' => 'admin', 'key' => 'timezone', 'value' => 'UTC', 'type' => 'text'],
            ['group' => 'seller', 'key' => 'platform_fee', 'value' => '10', 'type' => 'number'],
            ['group' => 'seller', 'key' => 'min_withdraw', 'value' => '50', 'type' => 'number'],
            ['group' => 'seller', 'key' => 'max_services', 'value' => '20', 'type' => 'number'],
            ['group' => 'buyer', 'key' => 'min_order', 'value' => '5', 'type' => 'number'],
            ['group' => 'buyer', 'key' => 'max_active_orders', 'value' => '10', 'type' => 'number'],
            ['group' => 'frontend', 'key' => 'hero_title', 'value' => 'Find the perfect freelancer for your project', 'type' => 'text'],
            ['group' => 'frontend', 'key' => 'hero_subtitle', 'value' => 'SkillNest connects you with talented professionals worldwide', 'type' => 'text'],
            ['group' => 'frontend', 'key' => 'footer_text', 'value' => 'All rights reserved.', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'registration_enabled', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'auth', 'key' => 'email_verification', 'value' => '1', 'type' => 'boolean'],
            ['group' => 'auth', 'key' => 'login.title', 'value' => 'SkillNest Login', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.badge', 'value' => 'Welcome to SkillNest', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.hero_heading', 'value' => 'Work. Hire. Grow.', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.hero_text', 'value' => 'Join thousands of freelancers and businesses building the future together.', 'type' => 'textarea'],
            ['group' => 'auth', 'key' => 'login.heading', 'value' => 'Welcome Back', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.subheading', 'value' => 'Login to continue your journey.', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.button', 'value' => 'Login', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.remember', 'value' => 'Remember Me', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.forgot_link', 'value' => 'Forgot Password?', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.new_here', 'value' => "Don't have an account?", 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.create_account', 'value' => 'Register', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.brand_heading_1', 'value' => 'Hire Experts.', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.brand_heading_2', 'value' => 'Grow Faster.', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.brand_text', 'value' => 'Connect with talented freelancers, manage projects, and build your digital business with Skillnest.', 'type' => 'textarea'],
            ['group' => 'auth', 'key' => 'register.badge', 'value' => 'Join SkillNest', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'register.hero_heading', 'value' => 'Start Your Journey Today', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'register.hero_text', 'value' => 'Create your free account and connect with thousands of freelancers and clients worldwide.', 'type' => 'textarea'],
            ['group' => 'auth', 'key' => 'register.heading', 'value' => 'Create Account', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'register.subheading', 'value' => "It's free and takes less than a minute.", 'type' => 'text'],
            ['group' => 'auth', 'key' => 'register.button', 'value' => 'Create Account', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'register.login_link', 'value' => 'Already have an account?', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'register.login', 'value' => 'Login', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'forgot.badge', 'value' => 'Account Recovery', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'forgot.hero_heading', 'value' => 'Forgot Your Password?', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'forgot.hero_text', 'value' => "Don't worry. Enter your registered email and we'll send you a password reset link.", 'type' => 'textarea'],
            ['group' => 'auth', 'key' => 'forgot.heading', 'value' => 'Reset Password', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'forgot.subheading', 'value' => 'Enter your email address below.', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'forgot.button', 'value' => 'Send Reset Link', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'forgot.back', 'value' => 'Back to Login', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'forgot.sent_heading', 'value' => 'Check Your Email', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'reset.badge', 'value' => 'Secure Password', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'reset.hero_heading', 'value' => 'Create New Password', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'reset.hero_text', 'value' => 'Your new password should be strong and different from your previous password.', 'type' => 'textarea'],
            ['group' => 'auth', 'key' => 'reset.heading', 'value' => 'Reset Password', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'reset.subheading', 'value' => 'Enter your new password below.', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'reset.button', 'value' => 'Reset Password', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'reset.sent_heading', 'value' => 'Password Reset!', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'verify.badge', 'value' => 'Verify Your Email', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'verify.hero_heading', 'value' => 'One More Step Left', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'verify.hero_text', 'value' => "We've sent a verification email to your registered email address. Please verify your account before continuing.", 'type' => 'textarea'],
            ['group' => 'auth', 'key' => 'verify.heading', 'value' => 'Check Your Inbox', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'verify.subheading', 'value' => "Click the verification link we've sent to your email.", 'type' => 'text'],
            ['group' => 'auth', 'key' => 'verify.button', 'value' => 'Resend Verification Email', 'type' => 'text'],
        ];

        foreach ($settings as $setting) {
            Setting::firstOrCreate(
                ['group' => $setting['group'], 'key' => $setting['key']],
                $setting
            );
        }
    }
}
