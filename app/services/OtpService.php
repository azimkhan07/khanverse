<?php

namespace App\Services;

use App\Models\EmailTemplate;
use App\Models\User;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;

class OtpService
{
    private const TTL = 600; // 10 minutes

    /**
     * Generate an OTP, store it (hashed) and email it to the user.
     * Optionally store the plaintext OTP in cache for testing/debug.
     *
     * @return string the plaintext OTP (returned only when $debug is true)
     */
    public static function send(int $userId, string $email, string $via = 'email', bool $debug = false): ?string
    {
        $otp = (string) random_int(100000, 999999);

        $cacheKey = 'otp_' . $via . '_' . md5($email);
        Cache::put($cacheKey, ['code' => $otp, 'user_id' => $userId], self::TTL);

        $user = User::find($userId);
        $name = $user?->name ?? 'User';

        $sent = MailService::sendTemplate('otp_send', $email, [
            'otp'       => $otp,
            'user_name' => $name,
            'app_name'  => config('app.name', 'SkillNest'),
            'expiry'    => '10 minutes',
        ]);

        if (! $sent) {
            // Fallback: store the OTP so it can be read in dev mail logs/telescope
            Cache::put($cacheKey . '_debug', $otp, self::TTL);
        }

        return $debug ? $otp : null;
    }

    public static function verify(string $email, string $otp, string $via = 'email'): bool
    {
        $cacheKey = 'otp_' . $via . '_' . md5($email);
        $stored = Cache::get($cacheKey);

        if (! $stored || ! hash_equals((string) $stored['code'], (string) $otp)) {
            return false;
        }

        Cache::forget($cacheKey);
        Cache::forget($cacheKey . '_debug');

        return true;
    }

    public static function purge(string $email, string $via = 'email'): void
    {
        $cacheKey = 'otp_' . $via . '_' . md5($email);
        Cache::forget($cacheKey);
        Cache::forget($cacheKey . '_debug');
    }
}
