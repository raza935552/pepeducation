<?php

namespace App\Http\Controllers;

use App\Models\Peptide;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves the education guide PDFs, which are stored PRIVATELY under
 * storage/app/guides (never in the public web root) so no crawler or bot can
 * reach them. Gates only:
 *   - admin library + download (routes behind the auth+admin middleware)
 *   - a valid signed URL (for customer delivery emails; a bot cannot forge it)
 *
 * Guides are SKU-based: one PDF per BioLinx SKU (e.g. retatrutide.pdf for the
 * 10 mg guide, retatrutide-30mg.pdf for the 30 mg guide). The admin library
 * scans the guides folder directly, so a guide shows up whether or not a
 * matching Peptide row exists. A manifest.json (written at deploy) supplies
 * the pretty name / size for each file; anything missing falls back to the
 * filename.
 */
class GuideController extends Controller
{
    private const DIR = 'guides';

    /** Admin guide library — lists every stored guide PDF (SKU-based). */
    public function index(): View
    {
        $dir = storage_path('app/' . self::DIR);
        $manifest = $this->manifest();

        $guides = collect(glob($dir . '/*.pdf') ?: [])
            ->map(function (string $path) use ($manifest) {
                $key = pathinfo($path, PATHINFO_FILENAME);
                $meta = $manifest[$key] ?? [];
                return (object) [
                    'key' => $key,
                    'name' => $meta['name'] ?? $this->prettify($key),
                    'size_label' => $meta['size'] ?? null,
                    'sku' => $meta['sku'] ?? null,
                    'bundle' => $meta['bundle'] ?? null,
                    'bytes' => filesize($path),
                ];
            })
            ->sortBy('name')
            ->values();

        return view('admin.guides.index', compact('guides'));
    }

    /** Admin download by guide key/filename (route behind auth+admin). */
    public function fileDownload(Request $request, string $key): BinaryFileResponse
    {
        return $this->streamFile($key, $request->boolean('dl'));
    }

    /** Customer download via a signed link (route uses the 'signed' middleware). */
    public function download(Request $request, string $key): BinaryFileResponse
    {
        return $this->streamFile($key, false);
    }

    /** Legacy: admin download bound to a Peptide (kept for the peptide edit page). */
    public function adminDownload(Request $request, Peptide $peptide): BinaryFileResponse
    {
        abort_unless($peptide->guide_pdf, 404);
        $key = pathinfo($peptide->guide_pdf, PATHINFO_FILENAME);
        return $this->streamFile($key, $request->boolean('dl'));
    }

    private function streamFile(string $key, bool $forceDownload): BinaryFileResponse
    {
        // Only a plain slug is ever a valid guide key — blocks path traversal.
        abort_unless(preg_match('/^[a-z0-9-]+$/i', $key), 404);
        $path = storage_path('app/' . self::DIR . '/' . $key . '.pdf');
        abort_unless(is_file($path), 404);

        $disposition = $forceDownload ? 'attachment' : 'inline';

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => $disposition . '; filename="' . $key . '-guide.pdf"',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    /**
     * Public SKU → guide map for the store's delivery automation. Names/sizes only,
     * never the PDFs, so it is safe to expose. Keyed by BioLinx product slug.
     */
    public function publicManifest(): \Illuminate\Http\JsonResponse
    {
        $map = [];
        foreach ($this->manifest() as $key => $meta) {
            $entry = ['key' => $key, 'name' => $meta['name'] ?? $key, 'size' => $meta['size'] ?? null];
            // Primary SKU + any alias SKUs (e.g. a GLP-1 sold under both a coded and
            // a named product slug) all resolve to the same guide.
            foreach (array_merge([$meta['sku'] ?? null], $meta['aliases'] ?? []) as $sku) {
                if ($sku) {
                    $map[$sku] = $entry;
                }
            }
        }
        return response()->json($map)->header('Cache-Control', 'public, max-age=1800');
    }

    /**
     * HMAC-gated per-order delivery page: lists the guides matching the ordered SKUs,
     * each with a 30-day signed download link. The store (BioLinx) signs the URL with
     * the shared `guides.delivery_secret`; an empty secret disables this entirely.
     */
    public function deliver(Request $request): \Illuminate\Contracts\View\View
    {
        $secret = trim((string) \App\Models\Setting::getValue('guides', 'delivery_secret', ''));
        abort_if($secret === '', 404);

        $skus = collect(explode(',', (string) $request->query('skus', '')))
            ->map(fn ($s) => trim($s))->filter()->unique()->sort()->values();
        $order = (string) $request->query('order', '');
        $exp = (int) $request->query('exp', 0);
        $sig = (string) $request->query('sig', '');

        abort_if($exp < time(), 410, 'This link has expired.');
        $payload = $skus->implode(',') . '|' . $order . '|' . $exp;
        abort_unless(hash_equals(hash_hmac('sha256', $payload, $secret), $sig), 403);

        $skuList = $skus->all();
        $guides = [];
        foreach ($this->manifest() as $key => $meta) {
            $guideSkus = array_merge([$meta['sku'] ?? null], $meta['aliases'] ?? []);
            if (array_intersect($guideSkus, $skuList) && !isset($guides[$key])) {
                $guides[$key] = [
                    'key' => $key,
                    'name' => $meta['name'] ?? $key,
                    'size' => $meta['size'] ?? null,
                    'url' => \Illuminate\Support\Facades\URL::signedRoute('guide.download', ['key' => $key], now()->addDays(30)),
                ];
            }
        }

        return view('guides.deliver', ['guides' => array_values($guides), 'order' => $order]);
    }

    /** slug => [name, size, sku, bundle], written to storage at deploy time. */
    private function manifest(): array
    {
        $path = storage_path('app/' . self::DIR . '/manifest.json');
        if (!is_file($path)) {
            return [];
        }
        return json_decode(file_get_contents($path), true) ?: [];
    }

    private function prettify(string $key): string
    {
        return ucwords(str_replace('-', ' ', $key));
    }
}
