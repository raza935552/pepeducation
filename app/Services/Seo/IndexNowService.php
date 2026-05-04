<?php

namespace App\Services\Seo;

use App\Models\BlogPost;
use App\Models\Peptide;
use App\Models\Setting;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class IndexNowService
{
    public const ENDPOINTS = [
        'bing'   => 'https://www.bing.com/indexnow',
        'yandex' => 'https://yandex.com/indexnow',
    ];

    public static function isEnabled(): bool
    {
        return (bool) Setting::getValue('seo', 'indexnow_enabled', false)
            && !empty(Setting::getValue('seo', 'indexnow_key'));
    }

    public static function getKey(): ?string
    {
        return Setting::getValue('seo', 'indexnow_key');
    }

    public static function generateKey(): string
    {
        $key = Str::lower(bin2hex(random_bytes(16)));
        Setting::setValue('seo', 'indexnow_key', $key);

        return $key;
    }

    public static function host(): string
    {
        return parse_url(config('app.url'), PHP_URL_HOST) ?: 'professorpeptides.co';
    }

    public static function keyLocation(): string
    {
        $key = self::getKey();

        return rtrim(config('app.url'), '/').'/'.$key.'.txt';
    }

    public static function submit(array $urls): array
    {
        $key = self::getKey();

        if (!$key || empty($urls)) {
            return ['ok' => false, 'reason' => 'IndexNow not configured or empty url list'];
        }

        $urls = array_values(array_unique(array_filter($urls, fn ($u) => is_string($u) && str_starts_with($u, 'http'))));

        if (empty($urls)) {
            return ['ok' => false, 'reason' => 'no valid urls'];
        }

        $payload = [
            'host'        => self::host(),
            'key'         => $key,
            'keyLocation' => self::keyLocation(),
            'urlList'     => array_slice($urls, 0, 10000),
        ];

        $results = [];
        foreach (self::ENDPOINTS as $name => $endpoint) {
            try {
                $response = Http::timeout(8)
                    ->acceptJson()
                    ->asJson()
                    ->post($endpoint, $payload);

                $results[$name] = [
                    'status' => $response->status(),
                    'ok'     => $response->successful() || $response->status() === 202,
                ];
            } catch (\Throwable $e) {
                Log::warning('IndexNow submit failed', ['endpoint' => $name, 'error' => $e->getMessage()]);
                $results[$name] = ['status' => 0, 'ok' => false, 'error' => $e->getMessage()];
            }
        }

        return ['ok' => true, 'submitted' => count($urls), 'endpoints' => $results];
    }

    public static function submitOne(string $url): array
    {
        return self::submit([$url]);
    }

    public static function submitAllPublished(): array
    {
        $urls = [];

        Peptide::published()->select('slug')->chunk(200, function ($peptides) use (&$urls) {
            foreach ($peptides as $p) {
                $urls[] = route('peptides.show', $p->slug);
            }
        });

        BlogPost::published()->select('slug')->chunk(200, function ($posts) use (&$urls) {
            foreach ($posts as $p) {
                $urls[] = route('blog.show', $p->slug);
            }
        });

        $urls[] = route('home');
        $urls[] = route('peptides.index');
        $urls[] = route('blog.index');

        return self::submit($urls);
    }
}
