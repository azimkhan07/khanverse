<?php

namespace App\Services;

use App\Models\EmailTemplate;
use Illuminate\Support\Facades\Mail;
use Illuminate\Mail\Message;

class MailService
{
    /**
     * Push SMTP + From settings into the runtime mailer config.
     * When $fields is null the values come from the admin SMTP settings table.
     *
     * @return bool false when SMTP is disabled or the host is empty
     */
    public static function applyConfig(?array $fields = null): bool
    {
        $smtp = $fields ?? self::activeSmtp();

        if (! $smtp || empty($smtp['host']) || empty($smtp['from_address'])) {
            return false;
        }

        config([
            'mail.default' => 'smtp',
            'mail.mailers.smtp.host' => $smtp['host'],
            'mail.mailers.smtp.port' => (int) $smtp['port'],
            'mail.mailers.smtp.encryption' => $smtp['encryption'] ?: null,
            'mail.mailers.smtp.username' => $smtp['username'] ?? null,
            'mail.mailers.smtp.password' => $smtp['password'] ?? null,
            'mail.from.address' => $smtp['from_address'],
            'mail.from.name' => $smtp['from_name'] ?: null,
        ]);

        return true;
    }

    /**
     * Read the active SMTP configuration stored via the admin settings.
     */
    public static function activeSmtp(): ?array
    {
        if (! (bool) setting('smtp', 'enabled', false)) {
            return null;
        }

        $host = setting('smtp', 'host', '');

        if ($host === '') {
            return null;
        }

        return [
            'host' => $host,
            'port' => (int) setting('smtp', 'port', 587),
            'encryption' => setting('smtp', 'encryption', 'tls') ?: null,
            'username' => setting('smtp', 'username', ''),
            'password' => setting('smtp', 'password', ''),
            'from_address' => setting('smtp', 'from_address', ''),
            'from_name' => setting('smtp', 'from_name', config('app.name', 'SkillNest')),
            'enabled' => true,
        ];
    }

    /**
     * Replace {placeholder} / {{placeholder}} tokens with the given data.
     */
    public static function render(string $content, array $data = []): string
    {
        foreach ($data as $key => $value) {
            $text = (string) ($value ?? '');
            $content = str_replace(['{'.$key.'}', '{{'.$key.'}}'], $text, $content);
        }

        return $content;
    }

    /**
     * Wrap template body inside the official branded email layout.
     */
    public static function wrapper(EmailTemplate $template, string $body): string
    {
        $brand = $template->logo_url
            ? '<img src="'.e($template->logo_url).'" alt="" style="max-height:64px; max-width:260px; width:auto; display:inline-block;" />'
            : '<span style="font-size:22px; font-weight:800; color:#ffffff; letter-spacing:.4px;">'.e($template->name).'</span>';

        $footer = '&copy; '.date('Y').' '.e(config('app.name', 'SkillNest'));
        if ($template->description) {
            $footer .= ' &middot; '.e($template->description);
        }

        return <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>{$template->subject}</title>
</head>
<body style="margin:0; padding:0; background-color:#F1F5F9; font-family:Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#F1F5F9; padding:26px 12px;">
<tr><td align="center">
<table role="presentation" cellpadding="0" cellspacing="0" style="max-width:640px; width:100%; background-color:#ffffff; border-radius:12px; overflow:hidden; border:1px solid #E2E8F0;">
<tr><td style="background-color:#1C2434; padding:22px 26px; text-align:center; border-radius:12px 12px 0 0;">{$brand}</td></tr>
<tr><td style="padding:30px 32px; color:#334155; font-size:14px; line-height:1.65;">{$body}</td></tr>
<tr><td style="background-color:#F8FAFC; padding:16px 26px; text-align:center; color:#94A3B8; font-size:12px; border-top:1px solid #E2E8F0;">{$footer}</td></tr>
</table>
</td></tr>
</table>
</body>
</html>
HTML;
    }

    /**
     * Send a message built from a stored email template.
     * Returns true when queued/sent, false when the template is missing or sending failed.
     */
    public static function sendTemplate(
        string $key,
        string|array $to,
        array $data = [],
        array $replyTo = []
    ): bool {
        $template = EmailTemplate::where('key', $key)->where('is_active', true)->first();

        if (! $template) {
            return false;
        }

        self::applyConfig();

        $subject = self::render($template->subject, $data);
        $body = self::render($template->content, $data);
        $html = self::wrapper($template, $body);

        try {
            Mail::html($html, function (Message $message) use ($to, $subject, $replyTo) {
                $message->to($to)->subject($subject);

                if (isset($replyTo['address']) && $replyTo['address']) {
                    $message->replyTo($replyTo['address'], $replyTo['name'] ?? null);
                }
            });

            return true;
        } catch (\Exception $e) {
            report($e);

            return false;
        }
    }

    /**
     * Verify SMTP credentials by sending a real test email.
     */
    public static function testSmtp(array $fields, ?string $to = null): array
    {
        if (! self::applyConfig($fields)) {
            return ['success' => false, 'message' => 'SMTP is disabled or the host / from address is missing.'];
        }

        $recipient = $to ?: ($fields['from_address'] ?? null);

        if (! $recipient) {
            return ['success' => false, 'message' => 'No recipient provided for the test email.'];
        }

        try {
            Mail::raw(
                'This is a test email from '.config('app.name').".\n\nYour SMTP settings are configured correctly and emails are being sent through this server.",
                function (Message $message) use ($recipient) {
                    $message->to($recipient)->subject('SMTP Test — '.now()->format('d M Y h:i A'));
                }
            );

            return ['success' => true, 'message' => "Test email sent successfully to {$recipient}."];
        } catch (\Exception $e) {
            return ['success' => false, 'message' => $e->getMessage()];
        }
    }
}