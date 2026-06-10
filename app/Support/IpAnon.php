<?php

namespace App\Support;

/**
 * Anonymises an IP address GA-style so a raw, fully-identifying IP is never stored:
 *   IPv4  1.2.3.4            -> 1.2.3.0       (zero the last octet)
 *   IPv6  2a01:abcd:...:1234 -> 2a01:abcd::   (keep the /48, zero the rest)
 * Geo lookups still resolve to ~the same region/city from the masked address.
 */
class IpAnon
{
    public static function mask(?string $ip): ?string
    {
        if (! $ip) {
            return null;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) {
            $parts = explode('.', $ip);
            if (count($parts) === 4) {
                $parts[3] = '0';
                return implode('.', $parts);
            }
            return $ip;
        }

        if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV6)) {
            // Keep the first 3 hextets (/48), zero the rest.
            $packed = inet_pton($ip);
            if ($packed !== false) {
                // 16 bytes; keep first 6 bytes (48 bits), zero remaining 10.
                $masked = substr($packed, 0, 6) . str_repeat("\0", 10);
                $out = inet_ntop($masked);
                return $out !== false ? $out : $ip;
            }
            return $ip;
        }

        return $ip; // not an IP — leave as-is
    }
}
