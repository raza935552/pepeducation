<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;

/**
 * Lightweight IP → location resolver for the Visitor Log. Uses the free ip-api.com
 * batch endpoint, caches each IP for 7 days, and is called lazily (only when an
 * admin views the log) so it never touches a visitor's request. Also returns
 * proxy/hosting flags — a datacenter/VPN signal that catches bots which spoof a
 * real browser user-agent (which UA-based detection misses).
 *
 * Best-effort: any failure returns empty geo, never throws.
 */
class IpGeo
{
    /** Resolve many IPs at once. Returns [ip => ['country','country_code','region','city','proxy','hosting']]. */
    public static function resolveMany(array $ips): array
    {
        $out = [];
        $need = [];

        foreach (array_unique(array_filter($ips)) as $ip) {
            if (! filter_var($ip, FILTER_VALIDATE_IP) || self::isPrivate($ip)) {
                continue;
            }
            $cached = Cache::get("ipgeo:{$ip}");
            if ($cached !== null) {
                $out[$ip] = $cached;
            } else {
                $need[] = $ip;
            }
        }

        if (! empty($need)) {
            try {
                $resp = Http::timeout(5)->post(
                    'http://ip-api.com/batch?fields=status,country,countryCode,regionName,city,proxy,hosting,query',
                    array_map(fn ($ip) => ['query' => $ip], $need)
                );
                if ($resp->ok()) {
                    foreach ($resp->json() ?: [] as $row) {
                        $ip = $row['query'] ?? null;
                        if (! $ip) {
                            continue;
                        }
                        $geo = ($row['status'] ?? '') === 'success' ? [
                            'country'      => $row['country'] ?? null,
                            'country_code' => $row['countryCode'] ?? null,
                            'region'       => $row['regionName'] ?? null,
                            'city'         => $row['city'] ?? null,
                            'proxy'        => (bool) ($row['proxy'] ?? false),
                            'hosting'      => (bool) ($row['hosting'] ?? false),
                        ] : self::empty();
                        Cache::put("ipgeo:{$ip}", $geo, now()->addDays(7));
                        $out[$ip] = $geo;
                    }
                }
            } catch (\Throwable $e) {
                // best-effort — leave unresolved IPs out
            }
        }

        return $out;
    }

    public static function empty(): array
    {
        return ['country' => null, 'country_code' => null, 'region' => null, 'city' => null, 'proxy' => false, 'hosting' => false];
    }

    private static function isPrivate(string $ip): bool
    {
        return ! filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_NO_PRIV_RANGE | FILTER_FLAG_NO_RES_RANGE);
    }
}
