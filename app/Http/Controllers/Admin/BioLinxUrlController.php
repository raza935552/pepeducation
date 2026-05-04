<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peptide;
use Illuminate\Http\Request;

class BioLinxUrlController extends Controller
{
    public function index()
    {
        $defaultMap = config('biolinx.product_map', []);
        $peptides = Peptide::orderBy('name')->get(['id', 'slug', 'name', 'biolinx_url', 'is_published']);

        $brand   = \App\Services\BioLinxService::name();
        $homeUrl = config('biolinx.home_url');

        return view('admin.biolinx-urls.index', compact('peptides', 'defaultMap', 'brand', 'homeUrl'));
    }

    public function update(Request $request, Peptide $peptide)
    {
        $data = $request->validate([
            'biolinx_url' => 'nullable|url|max:500',
        ]);

        $peptide->update(['biolinx_url' => $data['biolinx_url'] ?: null]);

        return response()->json([
            'success'      => true,
            'biolinx_url'  => $peptide->biolinx_url,
            'effective'    => \App\Services\BioLinxService::urlForPeptide($peptide, 'admin-preview'),
            'has_product'  => \App\Services\BioLinxService::hasProductForPeptide($peptide),
        ]);
    }
}
