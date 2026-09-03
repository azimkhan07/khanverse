<?php

namespace App\Services;

/**
 * Dependency-free Code 128 (subset B) barcode renderer.
 *
 * Produces the classic 1D barcode from a plain ASCII string. Used on the
 * invoice PDF so that scanning the barcode opens / downloads the invoice.
 * The encoded payload is the public invoice URL.
 */
class BarcodeService
{
    /**
     * Code 128 symbol widths (6 alternating bar/space widths each).
     */
    private const PATTERNS = [
        '212222', /*  00 */
        '222122', /*  01 */
        '222221', /*  02 */
        '121223', /*  03 */
        '121322', /*  04 */
        '131222', /*  05 */
        '122213', /*  06 */
        '122312', /*  07 */
        '132212', /*  08 */
        '221213', /*  09 */
        '221312', /*  10 */
        '231212', /*  11 */
        '112232', /*  12 */
        '122132', /*  13 */
        '122231', /*  14 */
        '113222', /*  15 */
        '123122', /*  16 */
        '123221', /*  17 */
        '223211', /*  18 */
        '221132', /*  19 */
        '221231', /*  20 */
        '213212', /*  21 */
        '223112', /*  22 */
        '312131', /*  23 */
        '311222', /*  24 */
        '321122', /*  25 */
        '321221', /*  26 */
        '312212', /*  27 */
        '322112', /*  28 */
        '322211', /*  29 */
        '212123', /*  30 */
        '212321', /*  31 */
        '232121', /*  32 */
        '111323', /*  33 */
        '131123', /*  34 */
        '131321', /*  35 */
        '112313', /*  36 */
        '132113', /*  37 */
        '132311', /*  38 */
        '211313', /*  39 */
        '231113', /*  40 */
        '231311', /*  41 */
        '112133', /*  42 */
        '112331', /*  43 */
        '132131', /*  44 */
        '113123', /*  45 */
        '113321', /*  46 */
        '133121', /*  47 */
        '313121', /*  48 */
        '211331', /*  49 */
        '231131', /*  50 */
        '213113', /*  51 */
        '213311', /*  52 */
        '213131', /*  53 */
        '311123', /*  54 */
        '311321', /*  55 */
        '331121', /*  56 */
        '312113', /*  57 */
        '312311', /*  58 */
        '332111', /*  59 */
        '314111', /*  60 */
        '221411', /*  61 */
        '431111', /*  62 */
        '111224', /*  63 */
        '111422', /*  64 */
        '121124', /*  65 */
        '121421', /*  66 */
        '141122', /*  67 */
        '141221', /*  68 */
        '112214', /*  69 */
        '112412', /*  70 */
        '122114', /*  71 */
        '122411', /*  72 */
        '142112', /*  73 */
        '142211', /*  74 */
        '241211', /*  75 */
        '221114', /*  76 */
        '413111', /*  77 */
        '241112', /*  78 */
        '134111', /*  79 */
        '111242', /*  80 */
        '121142', /*  81 */
        '121241', /*  82 */
        '114212', /*  83 */
        '124112', /*  84 */
        '124211', /*  85 */
        '411212', /*  86 */
        '421112', /*  87 */
        '421211', /*  88 */
        '212141', /*  89 */
        '214121', /*  90 */
        '412121', /*  91 */
        '111143', /*  92 */
        '111341', /*  93 */
        '131141', /*  94 */
        '114113', /*  95 */
        '114311', /*  96 */
        '411113', /*  97 */
        '411311', /*  98 */
        '113141', /*  99 */
        '114131', /* 100 */
        '311141', /* 101 */
        '411131', /* 102 */
        '211412', /* 103 START A */
        '211214', /* 104 START B */
        '211232', /* 105 START C */
        '233111', /* 106 STOP   */
        '200000', /* 107 END    */
    ];

    private const START_B = 104;
    private const STOP = 106;
    private const END = 107;

    /**
     * Symbol values (Code 128 B) including start, data, checksum, stop, end.
     */
    private static function symbolValues(string $code): array
    {
        $values = [];
        $len = strlen($code);

        for ($i = 0; $i < $len; $i++) {
            $ord = ord($code[$i]);
            if ($ord < 32 || $ord > 127) {
                continue;
            }
            $values[] = $ord - 32;
        }

        $sum = self::START_B;
        foreach ($values as $index => $value) {
            $sum += $value * ($index + 1);
        }
        $checksum = $sum % 103;

        return array_merge(
            [self::START_B],
            $values,
            [$checksum, self::STOP, self::END]
        );
    }

    /**
     * List of [bar => bool, w => int] modules drawn left to right.
     */
    private static function modules(string $code): array
    {
        $bars = [];

        foreach (self::symbolValues($code) as $value) {
            $seq = self::PATTERNS[$value];
            for ($j = 0; $j < 6; $j++) {
                $width = (int) $seq[$j];
                if ($width === 0) {
                    continue;
                }
                $bars[] = ['bar' => ($j % 2) === 0, 'w' => $width];
            }
        }

        return $bars;
    }

    /**
     * Total rendered width in modules.
     */
    private static function totalModules(string $code): int
    {
        $total = 0;
        foreach (self::modules($code) as $module) {
            $total += $module['w'];
        }

        return $total;
    }

    /**
     * Pure CSS/HTML bars so DomPDF renders them reliably without image support.
     */
    public static function html(string $code, int $height = 40): string
    {
        $html = '<div style="display:inline-block;white-space:nowrap;line-height:0;">';

        foreach (self::modules($code) as $module) {
            $color = $module['bar'] ? '#000' : '#fff';
            $html .= '<span style="display:inline-block;width:'
                . $module['w'] . 'px;height:' . $height . 'px;background:' . $color . ';"></span>';
        }

        return $html . '</div>';
    }

    /**
     * Standalone SVG string (used in the admin design preview iframe).
     */
    public static function svg(string $code, int $height = 44): string
    {
        $width = self::totalModules($code);
        $x = 0;
        $rects = '';

        foreach (self::modules($code) as $module) {
            if ($module['bar']) {
                $rects .= '<rect x="' . $x . '" y="0" width="' . $module['w']
                    . '" height="' . $height . '" fill="#000000"/>';
            }
            $x += $module['w'];
        }

        return '<svg xmlns="http://www.w3.org/2000/svg" width="' . $width
            . '" height="' . $height . '" viewBox="0 0 ' . $width . ' ' . $height . '">'
            . $rects . '</svg>';
    }
}