<?php

namespace App\Http\Controllers\Admin\Api;

use App\Http\Controllers\Controller;
use App\Models\EmailTemplate;
use App\Models\Setting;
use App\Services\MailService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class AdminEmailSettingApiController extends Controller
{
    /* ------------------------------------------------------------------
       Email Templates
    ------------------------------------------------------------------- */

    public function templates(Request $request): JsonResponse
    {
        $this->ensureDefaults();

        $templates = EmailTemplate::latest()
            ->paginate($request->integer('per_page', 10));

        return response()->json($templates);
    }

    public function templateShow($id): JsonResponse
    {
        return response()->json([
            'template' => EmailTemplate::findOrFail($id),
        ]);
    }

    public function templatePreview($id): JsonResponse
    {
        $template = EmailTemplate::findOrFail($id);

        $sample = [
            'app_name' => config('app.name', 'KhanVerse'),
            'user_name' => 'John Doe',
            'customer_name' => 'John Doe',
            'buyer_name' => 'John Doe',
            'seller_name' => 'Alex Smith',
            'email' => 'john@example.com',
            'subject' => 'Sample subject line',
            'message' => 'This is a sample message so you can see how the template looks when it is sent.',
            'order_number' => 'ORD-2026-0042',
            'service_title' => 'Professional Logo Design',
            'project_title' => 'Company Website',
            'total' => '$120.00',
        ];

        $subject = MailService::render($template->subject, $sample);
        $body = MailService::render($template->content, $sample);

        // Overwrite the in-memory subject with the rendered preview subject.
        $template->subject = $subject;

        return response()->json([
            'html' => MailService::wrapper($template, $body),
        ]);
    }

    public function storeTemplate(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'key' => 'nullable|string|max:255|regex:/^[a-z0-9_]+$/',
            'subject' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content' => 'required|string',
            'logo_url' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $key = $validated['key'] ?: Str::slug($validated['name'], '_');

        if (EmailTemplate::where('key', $key)->exists()) {
            return response()->json([
                'message' => 'A template with this key already exists. Pick a unique key.',
            ], 422);
        }

        $template = EmailTemplate::create([
            'name' => $validated['name'],
            'key' => $key,
            'subject' => $validated['subject'],
            'description' => $validated['description'] ?? null,
            'content' => $validated['content'],
            'logo_url' => $validated['logo_url'] ?? null,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return response()->json([
            'message' => 'Email template created.',
            'template' => $template,
        ], 201);
    }

    public function updateTemplate(Request $request, $id): JsonResponse
    {
        $template = EmailTemplate::findOrFail($id);

        $validated = $request->validate([
            'name' => 'sometimes|required|string|max:255',
            'key' => 'nullable|string|max:255|regex:/^[a-z0-9_]+$/',
            'subject' => 'sometimes|required|string|max:255',
            'description' => 'nullable|string|max:1000',
            'content' => 'sometimes|required|string',
            'logo_url' => 'nullable|string|max:500',
            'is_active' => 'nullable|boolean',
        ]);

        $key = isset($validated['key']) && $validated['key']
            ? $validated['key']
            : Str::slug($validated['name'] ?? $template->name, '_');

        if (EmailTemplate::where('key', $key)->where('id', '!=', $template->id)->exists()) {
            return response()->json([
                'message' => 'A template with this key already exists. Pick a unique key.',
            ], 422);
        }

        $template->update([
            'name' => $validated['name'] ?? $template->name,
            'key' => $key,
            'subject' => $validated['subject'] ?? $template->subject,
            'description' => array_key_exists('description', $validated) ? $validated['description'] : $template->description,
            'content' => $validated['content'] ?? $template->content,
            'logo_url' => array_key_exists('logo_url', $validated) ? ($validated['logo_url'] ?? null) : $template->logo_url,
            'is_active' => $request->boolean('is_active', $template->is_active),
        ]);

        return response()->json([
            'message' => 'Email template updated.',
            'template' => $template->fresh(),
        ]);
    }

    public function deleteTemplate($id): JsonResponse
    {
        EmailTemplate::findOrFail($id)->delete();

        return response()->json([
            'message' => 'Email template deleted.',
        ]);
    }

    public function templateToggle($id): JsonResponse
    {
        $template = EmailTemplate::findOrFail($id);
        $template->update([
            'is_active' => ! $template->is_active,
        ]);

        return response()->json([
            'message' => $template->is_active
                ? 'Template activated.'
                : 'Template deactivated.',
            'template' => $template->fresh(),
        ]);
    }

    /* ------------------------------------------------------------------
       SMTP Configuration
    ------------------------------------------------------------------- */

    public function smtpSettings(): JsonResponse
    {
        return response()->json([
            'host' => setting('smtp', 'host', ''),
            'port' => (int) setting('smtp', 'port', 587),
            'encryption' => setting('smtp', 'encryption', 'tls'),
            'username' => setting('smtp', 'username', ''),
            'password' => setting('smtp', 'password', ''),
            'from_address' => setting('smtp', 'from_address', ''),
            'from_name' => setting('smtp', 'from_name', config('app.name', 'KhanVerse')),
            'enabled' => (bool) setting('smtp', 'enabled', false),
        ]);
    }

    public function updateSmtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'encryption' => 'nullable|in:tls,ssl',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'from_address' => 'required|email|max:255',
            'from_name' => 'nullable|string|max:255',
            'enabled' => 'nullable|boolean',
        ]);

        $this->setMany('smtp', [
            'host' => $validated['host'],
            'port' => $validated['port'],
            'encryption' => $validated['encryption'] ?? null,
            'username' => $validated['username'] ?? '',
            'password' => $validated['password'] ?? '',
            'from_address' => $validated['from_address'],
            'from_name' => $validated['from_name'] ?? '',
            'enabled' => $request->boolean('enabled', false),
        ]);

        return response()->json([
            'message' => 'SMTP settings saved. Emails will now send through this server.',
        ]);
    }

    public function testSmtp(Request $request): JsonResponse
    {
        $fields = $request->validate([
            'host' => 'required|string|max:255',
            'port' => 'required|integer|min:1|max:65535',
            'encryption' => 'nullable|in:tls,ssl',
            'username' => 'nullable|string|max:255',
            'password' => 'nullable|string|max:255',
            'from_address' => 'required|email|max:255',
            'from_name' => 'nullable|string|max:255',
            'enabled' => 'nullable|boolean',
            'test_email' => 'required|email|max:255',
        ]);

        $result = MailService::testSmtp($fields, $fields['test_email']);

        return response()->json($result, $result['success'] ? 200 : 422);
    }

    /* ------------------------------------------------------------------
       Defaults + helpers
    ------------------------------------------------------------------- */

    public function ensureDefaults(): void
    {
        if (EmailTemplate::exists()) {
            return;
        }

        $defaults = [
            [
                'name' => 'New User Registered',
                'key' => 'user_registered',
                'subject' => 'Welcome to {app_name}, {user_name}!',
                'description' => 'Sent to a new user right after registration.',
                'content' => '<h1 style="margin:0 0 14px; font-size:22px; color:#1C2434;">Welcome, {user_name}!</h1>'
                    .'<p>Thank you for joining <strong>{app_name}</strong>. Your account has been created successfully.</p>'
                    .'<p>Registered email: <strong>{email}</strong></p>'
                    .'<p>You can now explore services, place orders and track your projects from your dashboard.</p>',
            ],
            [
                'name' => 'Order Created',
                'key' => 'order_created',
                'subject' => 'Your order {order_number} has been confirmed',
                'description' => 'Sent to the buyer when a new order is created.',
                'content' => '<h1 style="margin:0 0 14px; font-size:22px; color:#1C2434;">Hi {customer_name},</h1>'
                    .'<p>Your order <strong>{order_number}</strong> for <strong>{service_title}</strong> has been created successfully.</p>'
                    .'<p>Order amount: <strong>{total}</strong></p>'
                    .'<p>You can track the progress of your order anytime from your dashboard.</p>',
            ],
            [
                'name' => 'Project Completed',
                'key' => 'project_completed',
                'subject' => 'Project "{project_title}" has been completed',
                'description' => 'Sent to the buyer when a project is marked complete.',
                'content' => '<h1 style="margin:0 0 14px; font-size:22px; color:#1C2434;">Congratulations {buyer_name}!</h1>'
                    .'<p>The project <strong>{project_title}</strong> by seller <strong>{seller_name}</strong> has been completed.</p>'
                    .'<p>Please review the delivery. If everything looks good you can accept it from your dashboard.</p>',
            ],
            [
                'name' => 'Contact Form Message',
                'key' => 'contact_form',
                'subject' => 'New contact message: {subject}',
                'description' => 'Notifies the site admin when the contact form is submitted.',
                'content' => '<h1 style="margin:0 0 14px; font-size:20px; color:#1C2434;">You received a new contact message</h1>'
                    .'<table style="border-collapse:collapse; width:100%; font-size:13px; color:#334155;">'
                    .'<tr><td style="padding:6px 0; color:#94A3B8; width:110px;">Name</td><td style="padding:6px 0;"><strong>{name}</strong></td></tr>'
                    .'<tr><td style="padding:6px 0; color:#94A3B8;">Email</td><td style="padding:6px 0;">{email}</td></tr>'
                    .'<tr><td style="padding:6px 0; color:#94A3B8;">Subject</td><td style="padding:6px 0;">{subject}</td></tr>'
                    .'</table>'
                    .'<p style="margin:12px 0 0;">{message}</p>',
            ],
        ];

        foreach ($defaults as $item) {
            EmailTemplate::create($item);
        }
    }

    private function setMany(string $group, array $values): void
    {
        foreach ($values as $key => $value) {
            Setting::updateOrCreate(
                ['group' => $group, 'key' => $key],
                ['value' => (string) $value, 'type' => 'text']
            );
        }
    }
}