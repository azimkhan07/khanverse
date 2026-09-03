<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\EmailTemplate;

class EmailTemplateSeeder extends Seeder
{
    public function run(): void
    {
        $templates = [
            [
                'key' => 'user_registered',
                'name' => 'Welcome / Registration',
                'subject' => 'Welcome to {app_name}, {user_name}!',
                'description' => 'Sent when a new user registers.',
                'content' => '<h2>Hi {user_name},</h2><p>Welcome to <strong>{app_name}</strong>! We&apos;re excited to have you on board.</p><p>Complete your profile and start exploring services today.</p>',
            ],
            [
                'key' => 'order_created',
                'name' => 'Order Confirmation',
                'subject' => 'Your order {order_number} has been confirmed',
                'description' => 'Sent when an order is created.',
                'content' => '<h2>Thank you for your order!</h2><p>Your order <strong>{order_number}</strong> has been confirmed.</p><p>We&apos;ll keep you updated on its progress.</p>',
            ],
            [
                'key' => 'project_completed',
                'name' => 'Project Completed',
                'subject' => 'Project "{project_title}" has been completed',
                'description' => 'Sent when a project is marked complete.',
                'content' => '<h2>Project Completed</h2><p>Your project <strong>{project_title}</strong> has been completed. Please review the delivered work.</p>',
            ],
            [
                'key' => 'contact_form',
                'name' => 'Contact Form Notification',
                'subject' => 'New contact message: {subject}',
                'description' => 'Sent to the site owner when a contact form is submitted.',
                'content' => '<p>You have received a new contact message.</p><p><strong>Subject:</strong> {subject}<br /><strong>Email:</strong> {email}<br /><strong>Name:</strong> {name}</p><p>{message}</p>',
            ],
            [
                'key' => 'otp_send',
                'name' => 'Email OTP',
                'subject' => 'Your {app_name} verification code',
                'description' => 'Sent with a one-time password for email verification.',
                'content' => '<h2>Hi {user_name},</h2><p>Your one-time verification code is:</p><p style="font-size:28px; font-weight:800; letter-spacing:6px; color:#4F46E5; background:#EEF2FF; display:inline-block; padding:12px 22px; border-radius:10px;">{otp}</p><p>This code is valid for <strong>{expiry}</strong>. If you didn&apos;t request this, you can safely ignore this email.</p>',
            ],
        ];

        foreach ($templates as $template) {
            EmailTemplate::updateOrCreate(
                ['key' => $template['key']],
                $template
            );
        }
    }
}
