<?php

namespace Database\Seeders;

use App\Models\Setting;
use Illuminate\Database\Seeder;

class AuthSettingsSeeder extends Seeder
{
    public function run()
    {
        // Website-level settings used by auth pages
        $website = [
            ['group' => 'website', 'key' => 'site.name',    'value' => 'KhanVerse',  'type' => 'text'],
            ['group' => 'website', 'key' => 'site.logo',    'value' => null,          'type' => 'image'],
        ];

        $auth = [
            ['group' => 'auth', 'key' => 'auth.name',      'value' => 'KhanVerse', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'auth.logo',      'value' => null,         'type' => 'image'],
            ['group' => 'auth', 'key' => 'login.title',    'value' => 'KhanVerse Login',   'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.heading',  'value' => 'Welcome Back',       'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.subheading','value' => 'Login to continue your journey', 'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.button',   'value' => 'Login Now',          'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.remember', 'value' => 'Remember Me',        'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.forgot_link','value' => 'Forgot Password?',  'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.new_here', 'value' => 'New to Khanverse?',  'type' => 'text'],
            ['group' => 'auth', 'key' => 'login.create_account','value' => 'Create Account','type' => 'text'],
            ['group' => 'auth', 'key' => 'register.title',   'value' => 'Create Account',           'type' => 'text'],
            ['group' => 'auth', 'key' => 'register.heading', 'value' => 'Create Account',           'type' => 'text'],
            ['group' => 'auth', 'key' => 'register.subheading','value' => 'Join Khanverse today and start hiring or selling.','type' => 'text'],
            ['group' => 'auth', 'key' => 'register.button',  'value' => 'Create Account',           'type' => 'text'],
            ['group' => 'auth', 'key' => 'forgot.title',     'value' => 'Forgot Password',          'type' => 'text'],
            ['group' => 'auth', 'key' => 'forgot.heading',   'value' => 'Forgot Password?',         'type' => 'text'],
            ['group' => 'auth', 'key' => 'forgot.subheading','value' => 'No problem. Just let us know your email address and we will email you a password reset link.','type' => 'textarea'],
            ['group' => 'auth', 'key' => 'forgot.button',    'value' => 'Email Password Reset Link','type' => 'text'],
            ['group' => 'auth', 'key' => 'reset.title',      'value' => 'Reset Password',           'type' => 'text'],
            ['group' => 'auth', 'key' => 'reset.heading',    'value' => 'Reset Password',           'type' => 'text'],
            ['group' => 'auth', 'key' => 'reset.subheading', 'value' => 'Choose a new password for your account.','type' => 'text'],
            ['group' => 'auth', 'key' => 'reset.button',     'value' => 'Reset Password',           'type' => 'text'],
            ['group' => 'auth', 'key' => 'confirm.title',    'value' => 'Confirm Password',         'type' => 'text'],
            ['group' => 'auth', 'key' => 'confirm.heading',  'value' => 'Confirm Password',         'type' => 'text'],
            ['group' => 'auth', 'key' => 'confirm.subheading','value' => 'This is a secure area. Please confirm your password before continuing.','type' => 'text'],
            ['group' => 'auth', 'key' => 'confirm.button',   'value' => 'Confirm',                  'type' => 'text'],
            ['group' => 'auth', 'key' => 'verify.title',     'value' => 'Verify Email',             'type' => 'text'],
            ['group' => 'auth', 'key' => 'verify.heading',   'value' => 'Verify Your Email',        'type' => 'text'],
            ['group' => 'auth', 'key' => 'verify.subheading','value' => 'Thanks for signing up! Please verify your email address.','type' => 'textarea'],
            ['group' => 'auth', 'key' => 'verify.button',    'value' => 'Resend Verification Email','type' => 'text'],
        ];

        foreach (array_merge($website, $auth) as $item) {
            Setting::updateOrCreate(
                ['group' => $item['group'], 'key' => $item['key']],
                ['value' => $item['value'], 'type' => $item['type']]
            );
        }
    }
}
