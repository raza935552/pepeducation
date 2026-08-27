<?php

namespace App\Http\Controllers;

use App\Models\Peptide;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

/**
 * Serves the per-peptide education guide PDFs, which are stored PRIVATELY under
 * storage/app/guides (never in the public web root) so no crawler or bot can
 * reach them. Two gates only:
 *   - admin download (route protected by the auth+admin middleware)
 *   - a valid signed URL (for customer delivery emails; a bot cannot forge it)
 */
class GuideController extends Controller
{
    /** Admin-only download (route sits behind the auth+admin middleware group). */
    public function adminDownload(Peptide $peptide): BinaryFileResponse
    {
        return $this->stream($peptide);
    }

    /** Customer download via a signed link (route uses the 'signed' middleware). */
    public function download(Request $request, Peptide $peptide): BinaryFileResponse
    {
        return $this->stream($peptide);
    }

    private function stream(Peptide $peptide): BinaryFileResponse
    {
        abort_unless($peptide->guide_pdf, 404);
        $path = storage_path('app/' . $peptide->guide_pdf);
        abort_unless(is_file($path), 404);

        return response()->file($path, [
            'Content-Type' => 'application/pdf',
            'Content-Disposition' => 'inline; filename="' . $peptide->slug . '-guide.pdf"',
            'X-Robots-Tag' => 'noindex, nofollow, noarchive',
            'Cache-Control' => 'private, no-store',
        ]);
    }
}
