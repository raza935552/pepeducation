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
        // Generic store link — route through /go so the click is logged and
        // fbclid/fbp/fbc + session/email are forwarded to Biolinx (closed loop).
        return self::goWrap(null, $context);
    }

    public static function urlForSlug(?string $slug, string $context = 'peptide'): string
    {
        return self::goWrap(self::resolveProductUrl($slug), $context);
    }

    public static function urlForPeptide($peptide, string $context = 'peptide'): string
    {
        $direct = is_object($peptide) ? trim((string) ($peptide->biolinx_url ?? '')) : '';
        if ($direct !== '') {
            return self::goWrap($direct, $context);
        }

        $slug = is_object($peptide) ? ($peptide->slug ?? null) : $peptide;

        return self::urlForSlug($slug, $context);
    }

    /**
     * Wrap a Biolinx destination in the PP /go redirect so the outbound click is
     * tracked and the cross-domain match keys (fbclid/fbp/fbc), real ad UTMs,
     * pp_session and email are forwarded — the same bridge the ad landers use.
     *
     * @param  string|null  $dest  Specific Biolinx product URL, or null for the store home.
     */
    private static function goWrap(?string $dest, string $context = 'peptide'): string
    {
        $slug = config('biolinx.go_slug', 'biolinxlabs');
        $base = url('/go/'.$slug);

        $home = rtrim((string) config('biolinx.home_url'), '/');
        $dest = $dest !== null ? trim($dest) : null;

        // No dest override for the bare store home — the outbound link already
        // points there. Only deep-link via ?dest= for a specific product page.
        // (/go ignores extra query params other than dest; per-CTA context is
        // logged server-side in buy_clicks via ppTrackBuyClick.)
        if (!$dest || rtrim($dest, '/') === $home) {
            return $base;
        }

        return $base.'?dest='.rawurlencode($dest);
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
