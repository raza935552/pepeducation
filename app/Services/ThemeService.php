<?php

namespace App\Services;

use App\Models\Setting;
use Illuminate\Support\Facades\Cache;

class ThemeService
{
    public const DEFAULTS = [
        'primary'   => '#9A7B4F',
        'secondary' => '#A67B5B',
        'bg'        => '#FDFCFA',
        'heading'   => '#1A1714',
        'text'      => '#4A433C',
        'dark'      => '#1A1714',
    ];

    protected const SHADE_LIGHTNESS = [
        50  => 96,
        100 => 91,
        200 => 82,
        300 => 73,
        400 => 59,
        // 500 = original
        600 => 37,
        700 => 28,
        800 => 19,
        900 => 12,
        950 => 7,
    ];

    protected const CACHE_KEY = 'theme_css_variables';
    protected const CACHE_TTL = 3600;

    /**
     * Get the 6 master theme colors from settings (with defaults).
     */
    public static function getThemeColors(): array
    {
        $colors = [];
        foreach (self::DEFAULTS as $key => $default) {
            $colors[$key] = Setting::getValue('theme', $key, $default);
        }
        return $colors;
    }

    /**
     * Generate the full CSS :root block with all theme variables.
     * Cached for 1 hour, busted on save.
     */
    public static function generateCssVariables(): string
    {
        return Cache::remember(self::CACHE_KEY, self::CACHE_TTL, function () {
            $colors = self::getThemeColors();
            $lines = [];

            foreach ($colors as $name => $hex) {
                $rgb = self::hexToRgb($hex);
                $shades = self::generateShades($hex);

                // The DEFAULT shade (used as --primary, --secondary, etc.)
                $lines[] = "  --{$name}: {$rgb[0]} {$rgb[1]} {$rgb[2]};";

                // All numbered shades
                foreach ($shades as $shade => $shadeRgb) {
                    $lines[] = "  --{$name}-{$shade}: {$shadeRgb[0]} {$shadeRgb[1]} {$shadeRgb[2]};";
                }
            }

            return ":root {\n" . implode("\n", $lines) . "\n}";
        });
    }

    /**
     * Generate 11 shade variations (50-950) from a single hex color.
     * The input hex becomes the 500 shade.
     */
    public static function generateShades(string $hex): array
    {
        [$h, $s, $l] = self::hexToHsl($hex);
        $shades = [];

        foreach (self::SHADE_LIGHTNESS as $shade => $targetL) {
            // Adjust saturation: boost for lighter shades, reduce for darker
            $satAdjust = 0;
            if ($targetL > $l) {
                // Lighter shade — slight saturation boost
                $satAdjust = min(10, ($targetL - $l) * 0.15);
            } else {
                // Darker shade — slight saturation reduction
                $satAdjust = max(-10, ($targetL - $l) * 0.1);
            }

            $newS = max(0, min(100, $s + $satAdjust));
            $rgb = self::hslToRgb($h, $newS, $targetL);
            $shades[$shade] = $rgb;
        }

        // 500 = the original color
        $shades[500] = self::hexToRgb($hex);

        ksort($shades);
        return $shades;
    }

    /**
     * Clear the cached CSS variables (called on theme save).
     */
    public static function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY);
    }

    /**
     * Convert hex to RGB array [r, g, b].
     */
    public static function hexToRgb(string $hex): array
    {
        $hex = ltrim($hex, '#');
        if (strlen($hex) === 3) {
            $hex = $hex[0].$hex[0].$hex[1].$hex[1].$hex[2].$hex[2];
        }
        return [
            hexdec(substr($hex, 0, 2)),
            hexdec(substr($hex, 2, 2)),
            hexdec(substr($hex, 4, 2)),
        ];
    }

    /**
     * Convert hex to HSL array [h, s, l] where h=0-360, s=0-100, l=0-100.
     */
    public static function hexToHsl(string $hex): array
    {
        [$r, $g, $b] = self::hexToRgb($hex);
        $r /= 255; $g /= 255; $b /= 255;

        $max = max($r, $g, $b);
        $min = min($r, $g, $b);
        $l = ($max + $min) / 2;

        if ($max === $min) {
            return [0, 0, round($l * 100, 1)];
        }

        $d = $max - $min;
        $s = $l > 0.5 ? $d / (2 - $max - $min) : $d / ($max + $min);

        $h = match ($max) {
            $r => (($g - $b) / $d + ($g < $b ? 6 : 0)) / 6,
            $g => (($b - $r) / $d + 2) / 6,
            $b => (($r - $g) / $d + 4) / 6,
        };

        return [
            round($h * 360, 1),
            round($s * 100, 1),
            round($l * 100, 1),
        ];
    }

    /**
     * Convert HSL to RGB array [r, g, b].
     * h=0-360, s=0-100, l=0-100
     */
    public static function hslToRgb(float $h, float $s, float $l): array
    {
        $h /= 360; $s /= 100; $l /= 100;

        if ($s === 0.0) {
            $v = (int) round($l * 255);
            return [$v, $v, $v];
        }

        $q = $l < 0.5 ? $l * (1 + $s) : $l + $s - $l * $s;
        $p = 2 * $l - $q;

        $hue2rgb = function ($p, $q, $t) {
            if ($t < 0) $t += 1;
            if ($t > 1) $t -= 1;
            if ($t < 1/6) return $p + ($q - $p) * 6 * $t;
            if ($t < 1/2) return $q;
            if ($t < 2/3) return $p + ($q - $p) * (2/3 - $t) * 6;
            return $p;
        };

        return [
            (int) round($hue2rgb($p, $q, $h + 1/3) * 255),
            (int) round($hue2rgb($p, $q, $h) * 255),
            (int) round($hue2rgb($p, $q, $h - 1/3) * 255),
        ];
    }
}
