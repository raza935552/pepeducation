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
