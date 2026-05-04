<?php

namespace App\Services;

class BioLinxService
{
    public static function name(): string
    {
        return config('biolinx.name', 'BioLinx Labs');
    }

    public static function homeUrl(string $context = 'home'): string
    {
        return self::withUtm(config('biolinx.home_url'), $context);
    }

    public static function urlForSlug(?string $slug, string $context = 'peptide'): string
    {
        $base = self::resolveProductUrl($slug) ?? config('biolinx.home_url');

        return self::withUtm($base, $context);
    }

    public static function urlForPeptide($peptide, string $context = 'peptide'): string
    {
        $direct = is_object($peptide) ? trim((string) ($peptide->biolinx_url ?? '')) : '';
        if ($direct !== '') {
            return self::withUtm($direct, $context);
        }

        $slug = is_object($peptide) ? ($peptide->slug ?? null) : $peptide;

        return self::urlForSlug($slug, $context);
    }

    public static function hasProductFor(?string $slug): bool
    {
        return self::resolveProductUrl($slug) !== null;
    }

    public static function hasProductForPeptide($peptide): bool
    {
        if (is_object($peptide) && !empty(trim((string) ($peptide->biolinx_url ?? '')))) {
            return true;
        }

        $slug = is_object($peptide) ? ($peptide->slug ?? null) : $peptide;

        return self::resolveProductUrl($slug) !== null;
    }

    private static function resolveProductUrl(?string $slug): ?string
    {
        if (!$slug) {
            return null;
        }

        // Prefer DB value on the peptide row when available (admin-editable)
        try {
            $url = \App\Models\Peptide::where('slug', $slug)->value('biolinx_url');
            if (is_string($url) && trim($url) !== '') {
                return trim($url);
            }
        } catch (\Throwable $e) {
            // Schema may not be ready (migrations); fall back to config
        }

        $map = config('biolinx.product_map', []);

        return $map[$slug] ?? null;
    }

    public static function withUtm(string $url, string $context = 'general'): string
    {
        $utm = config('biolinx.utm', []);
        if (empty($utm)) {
            return $url;
        }

        $params = [
            'utm_source'   => $utm['source']   ?? null,
            'utm_medium'   => $utm['medium']   ?? null,
            'utm_campaign' => $utm['campaign'] ?? null,
            'utm_content'  => $context,
        ];
        $params = array_filter($params, fn ($v) => $v !== null && $v !== '');

        $separator = str_contains($url, '?') ? '&' : '?';

        return $url.$separator.http_build_query($params);
    }
}
