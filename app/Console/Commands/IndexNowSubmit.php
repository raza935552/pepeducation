<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;

/**
 * Submit Professor Peptides' indexable URLs to IndexNow (Bing, Yandex, etc.).
 *
 *   php artisan indexnow:submit          # submit every URL in the sitemap
 *   php artisan indexnow:submit --dry    # list URLs without submitting
 *
 * Reads the site's own sitemap.xml as the source of truth (so it always matches
 * what we actually want indexed — the noindex /lp/* bridge landers aren't in the
 * sitemap and are therefore never submitted). Cloudflare "Crawler Hints" handles
 * ongoing automatic submission; run this for an immediate bulk push.
 */
class IndexNowSubmit extends Command
{
    protected $signature = 'indexnow:submit {--dry : List URLs without submitting}';
    protected $description = 'Submit sitemap URLs to IndexNow (Bing/Yandex)';

    private const KEY = '8f0c53dbe1524f6993fb7dbdfb183b89';

    public function handle(): int
    {
        $base = rtrim(config('app.url'), '/');
        $host = parse_url($base, PHP_URL_HOST);

        $urls = $this->urlsFromSitemap($base . '/sitemap.xml');

        if (empty($urls)) {
            $this->error('No URLs found in sitemap.');
            return self::FAILURE;
        }

        $this->info(count($urls) . ' URLs gathered from sitemap for ' . $host);

        if ($this->option('dry')) {
            foreach ($urls as $u) {
                $this->line('  ' . $u);
            }

            return self::SUCCESS;
        }

        // IndexNow accepts up to 10,000 URLs per request.
        $res = Http::asJson()->post('https://api.indexnow.org/IndexNow', [
            'host' => $host,
            'key' => self::KEY,
            'keyLocation' => $base . '/' . self::KEY . '.txt',
            'urlList' => array_values($urls),
        ]);

        $this->line('IndexNow responded: ' . $res->status());
        // 200/202 = accepted; 403 = key mismatch; 422 = host/url mismatch.
        return $res->successful() || $res->status() === 202 ? self::SUCCESS : self::FAILURE;
    }

    /** Collect page URLs from a sitemap, following a sitemap-index one level deep. */
    private function urlsFromSitemap(string $sitemapUrl): array
    {
        $xml = Http::get($sitemapUrl)->body();
        if ($xml === '') {
            return [];
        }

        $locs = $this->extractLocs($xml);

        // Sitemap index → each <loc> is a child sitemap; fetch and flatten.
        if (str_contains($xml, '<sitemapindex')) {
            $pages = [];
            foreach ($locs as $child) {
                $pages = array_merge($pages, $this->extractLocs(Http::get($child)->body()));
            }
            $locs = $pages;
        }

        return array_values(array_unique($locs));
    }

    /** @return string[] all <loc> values in an XML string. */
    private function extractLocs(string $xml): array
    {
        preg_match_all('/<loc>\s*([^<\s]+)\s*<\/loc>/i', $xml, $m);

        return $m[1] ?? [];
    }
}
