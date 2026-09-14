<?php

namespace App\Services;

use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Str;

class PdfService
{
    public static function privacyPolicy(array $data = []): string
    {
        $appName = config('app.name', 'SkillNest');
        $userName = $data['user_name'] ?? 'Customer';
        $role = $data['role'] ?? 'Member';
        $date = date('d F Y');
        $url = url('/privacy-policy');

        $html = <<<HTML
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; }
    body { font-family: Helvetica, Arial, sans-serif; color: #1E293B; margin: 0; padding: 0; }
    .brand { background: #1C2434; color: #fff; padding: 28px 36px; }
    .brand h1 { margin: 0; font-size: 24px; letter-spacing: .4px; }
    .brand p { margin: 6px 0 0; color: #CBD5E1; font-size: 12px; }
    .content { padding: 28px 36px; font-size: 12px; line-height: 1.7; color: #334155; }
    h2 { font-size: 15px; margin: 22px 0 8px; color: #0F172A; }
    h2:first-child { margin-top: 0; }
    p, li { font-size: 12px; }
    .meta { background: #F8FAFC; border: 1px solid #E2E8F0; border-radius: 10px; padding: 14px 18px; margin-bottom: 18px; }
    table { width: 100%; border-collapse: collapse; margin-top: 8px; }
    td, th { border: 1px solid #E2E8F0; padding: 8px 10px; text-align: left; font-size: 11px; }
    th { background: #F1F5F9; }
    .footer { padding: 16px 36px; color: #94A3B8; font-size: 10px; border-top: 1px solid #E2E8F0; }
</style>
</head>
<body>
    <div class="brand">
        <h1>{$appName} — Privacy Policy</h1>
        <p>Account verification &amp; personal data handling notice</p>
    </div>
    <div class="content">
        <div class="meta">
            <strong>Issued to:</strong> {$userName} ({$role})<br>
            <strong>Effective date:</strong> {$date}
        </div>

        <h2>1. Personal Information We Collect</h2>
        <p>
            If you choose to become a <strong>seller</strong>, we collect identity verification documents such as your Aadhaar and PAN details.
            If you place a <strong>consultancy order as a buyer</strong>, we may ask for supporting verification documents. We also collect
            your name, contact details, and the profile information you provide.
        </p>

        <h2>2. How Your Information Is Used</h2>
        <p>Your information helps us to:</p>
        <ul>
            <li>Verify your identity, keep the marketplace safe, and prevent fraud.</li>
            <li>Facilitate orders, payments, and communications between buyers and sellers.</li>
            <li>Provide support and improve our services.</li>
        </ul>

        <h2>3. Document Handling &amp; Retention</h2>
        <ul>
            <li>Verification documents are stored securely and are only visible to platform admins and moderators.</li>
            <li>They are never shared with other users and are not displayed on your public profile.</li>
            <li>Documents are retained while your account is active and deleted within a reasonable time after account closure, unless required otherwise by law.</li>
        </ul>

        <h2>4. Data Sharing</h2>
        <p>We do not sell your personal data. We share data only with:</p>
        <ul>
            <li>Payment processors, strictly to complete your transactions.</li>
            <li>Service providers that help us operate (hosting, email delivery), under confidentiality terms.</li>
            <li>Authorities, when required by law.</li>
        </ul>

        <h2>5. Your Rights</h2>
        <ul>
            <li>Access, correct, or delete your personal data.</li>
            <li>Withdraw consent for marketing communications.</li>
            <li>Request a copy or deletion of your verification documents.</li>
        </ul>
        <p>You can exercise these rights by contacting support from your account.</p>

        <h2>6. Security</h2>
        <p>We apply appropriate technical and organisational measures, including encryption of stored documents and restricted administrative access, to protect your data.</p>

        <h2>7. Changes &amp; Contact</h2>
        <p>
            This policy may be updated from time to time. The current version is always published at {$url}.
            By using the platform you agree to this policy as described above.
        </p>
    </div>
    <div class="footer">
        &copy; {$appName} {$date} &middot; This document was generated for your records. The latest version is always available at {$url}.
    </div>
</body>
</html>
HTML;

        return Pdf::loadHTML($html)->setPaper('a4')->output();
    }

    /**
     * Write the privacy policy PDF to a temporary file and return its absolute path.
     */
    public static function privacyPolicyFile(array $data = []): string
    {
        $dir = storage_path('app/temp');
        if (! is_dir($dir)) {
            mkdir($dir, 0775, true);
        }

        $path = $dir . '/privacy-policy-' . Str::uuid() . '.pdf';
        file_put_contents($path, self::privacyPolicy($data));

        return $path;
    }
}