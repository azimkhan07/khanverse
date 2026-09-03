<?php

namespace App\Services;

use App\Models\Setting;

/**
 * Generates sequential order and invoice numbers from an admin-configurable
 * pattern. Placeholders supported:
 *   {prefix}   -> configured prefix
 *   {suffix}   -> configured suffix
 *   {year}     -> current year (e.g. 2026)
 *   {yy}       -> two-digit year
 *   {month}    -> two-digit month
 *   {seq:N}    -> zero-padded running sequence (N = padding width)
 */
class NumberService
{
    /**
     * Build the next sequential number for a given counter group.
     */
    public static function next(string $settingsGroup, string $pattern, string $counterKey): string
    {
        $nextSeq = self::nextSequence($counterKey);

        $prefix = self::setting($settingsGroup, 'prefix', '');
        $suffix = self::setting($settingsGroup, 'suffix', '');
        $seqWidth = (int) self::setting($settingsGroup, 'seq_padding', 4);

        return self::render($pattern, [
            'prefix' => $prefix,
            'suffix' => $suffix,
            'seq' => $nextSeq,
            'seqWidth' => $seqWidth,
        ]);
    }

    /**
     * Render a pattern replacing placeholders.
     */
    public static function render(string $pattern, array $vars): string
    {
        $seqWidth = $vars['seqWidth'] ?? 4;
        $seq = $vars['seq'] ?? 0;

        $replacements = [
            '{prefix}' => $vars['prefix'] ?? '',
            '{suffix}' => $vars['suffix'] ?? '',
            '{year}' => date('Y'),
            '{yy}' => date('y'),
            '{month}' => date('m'),
            '{seq}' => str_pad((string) $seq, $seqWidth, '0', STR_PAD_LEFT),
        ];

        $out = $pattern;
        foreach ($replacements as $token => $value) {
            $out = str_replace($token, $value, $out);
        }

        // Support {seq:N} with explicit width.
        $out = preg_replace_callback('/\{seq:(\d+)\}/', function ($m) use ($seq) {
            return str_pad((string) $seq, (int) $m[1], '0', STR_PAD_LEFT);
        }, $out);

        return $out;
    }

    /**
     * Get the next sequence for a counter key (stored in settings group 'counters').
     * Keeps a per-key running number in DB so it never repeats.
     */
    public static function nextSequence(string $counterKey): int
    {
        $row = Setting::firstOrCreate(
            ['group' => 'counters', 'key' => $counterKey, 'type' => 'number'],
            ['value' => '0']
        );

        $next = ((int) $row->value) + 1;
        $row->update(['value' => (string) $next]);

        return $next;
    }

    private static function setting(string $group, string $key, $default = null)
    {
        return setting($group, $key, $default);
    }
}
