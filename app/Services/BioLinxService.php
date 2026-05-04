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
        $map = config('biolinx.product_map', []);
        $base = $slug && isset($map[$slug]) ? $map[$slug] : config('biolinx.home_url');

        return self::withUtm($base, $context);
    }

    public static function hasProductFor(?string $slug): bool
    {
        if (!$slug) {
            return false;
        }

        return array_key_exists($slug, config('biolinx.product_map', []));
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
