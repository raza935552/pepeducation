<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Defence-in-depth scraper block for the private community.
 *
 * The forum is already behind auth + verified (so anonymous crawlers can never
 * reach content), but this rejects known bots/AI scrapers outright and stamps a
 * noindex header — so the community is never crawled, cached, or trained on.
 */
class BlockForumScrapers
{
    /** Known crawlers / AI scrapers / generic HTTP clients to hard-block. */
    private const BLOCKED_AGENTS = [
        // Search + SEO crawlers
        'googlebot', 'bingbot', 'slurp', 'duckduckbot', 'baiduspider', 'yandex',
        'sogou', 'exabot', 'facebot', 'ia_archiver', 'semrushbot', 'ahrefsbot',
        'mj12bot', 'dotbot', 'petalbot', 'bytespider', 'dataforseo',
        // AI / LLM scrapers
        'gptbot', 'oai-searchbot', 'chatgpt-user', 'ccbot', 'claudebot', 'claude-web',
        'anthropic-ai', 'perplexitybot', 'perplexity-user', 'google-extended',
        'applebot-extended', 'meta-externalagent', 'meta-externalfetcher',
        'facebookbot', 'amazonbot', 'youbot', 'diffbot', 'imagesiftbot', 'omgili',
        'timpibot', 'cohere-ai', 'img2dataset', 'webzio',
        // Generic clients / libraries
        'python-requests', 'python-urllib', 'scrapy', 'curl/', 'wget', 'libwww-perl',
        'httpclient', 'okhttp', 'go-http-client', 'java/', 'aiohttp', 'node-fetch',
        'axios/', 'headlesschrome', 'phantomjs', 'puppeteer', 'playwright',
        'httrack', 'wpbot', 'crawler', 'spider', 'scraper',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        $ua = strtolower((string) $request->userAgent());

        // Empty UA or any known bot signature => blocked.
        if ($ua === '') {
            abort(403);
        }

        foreach (self::BLOCKED_AGENTS as $needle) {
            if (str_contains($ua, $needle)) {
                abort(403);
            }
        }

        /** @var Response $response */
        $response = $next($request);
        $response->headers->set('X-Robots-Tag', 'noindex, nofollow, noarchive, nosnippet');

        return $response;
    }
}
