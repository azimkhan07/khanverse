<?php

namespace App\Support;

class UserAgentParser
{
    public static function parse(?string $userAgent): array
    {
        $ua = strtolower($userAgent ?? '');

        return [
            'browser'  => self::browser($ua),
            'platform' => self::platform($ua),
            'device'   => self::device($ua),
        ];
    }

    private static function browser(string $ua): string
    {
        if (str_contains($ua, 'edg/') || str_contains($ua, 'edge/')) return 'Edge';
        if (str_contains($ua, 'opr/') || str_contains($ua, 'opera')) return 'Opera';
        if (str_contains($ua, 'chrome')) return 'Chrome';
        if (str_contains($ua, 'safari') && !str_contains($ua, 'chrome')) return 'Safari';
        if (str_contains($ua, 'firefox')) return 'Firefox';
        if (str_contains($ua, 'msie') || str_contains($ua, 'trident')) return 'Internet Explorer';
        if (str_contains($ua, 'samsungbrowser')) return 'Samsung Internet';
        if (str_contains($ua, 'crios')) return 'Chrome (iOS)';
        if (str_contains($ua, 'fxios')) return 'Firefox (iOS)';
        return 'Unknown';
    }

    private static function platform(string $ua): string
    {
        if (str_contains($ua, 'windows')) return 'Windows';
        if (str_contains($ua, 'android')) return 'Android';
        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipad') || str_contains($ua, 'ios')) return 'iOS';
        if (str_contains($ua, 'mac os x') || str_contains($ua, 'macintosh')) return 'macOS';
        if (str_contains($ua, 'linux')) return 'Linux';
        if (str_contains($ua, 'freebsd')) return 'FreeBSD';
        return 'Unknown';
    }

    private static function device(string $ua): string
    {
        if (str_contains($ua, 'ipad') || (str_contains($ua, 'tablet'))) return 'Tablet';
        if (str_contains($ua, 'iphone') || str_contains($ua, 'ipod') || (str_contains($ua, 'mobile') && str_contains($ua, 'android'))) return 'Mobile';
        if (str_contains($ua, 'bot') || str_contains($ua, 'spider') || str_contains($ua, 'crawler')) return 'Bot';
        if (str_contains($ua, 'tv') || str_contains($ua, 'smarttv')) return 'Smart TV';
        return 'Desktop';
    }
}
