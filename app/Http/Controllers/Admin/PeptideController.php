<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peptide;
use Illuminate\Http\Request;

class PeptideController extends Controller
{
    public function index(Request $request)
    {
        $query = Peptide::with('categories');

        // Search
        if ($search = $request->get('search')) {
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('full_name', 'like', "%{$search}%")
                  ->orWhere('abbreviation', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($status = $request->get('status')) {
            $query->where('is_published', $status === 'published');
        }

        // Filter by research status
        if ($research = $request->get('research')) {
            $query->where('research_status', $research);
        }

        $peptides = $query->orderBy('name')->paginate(15);

        return view('admin.peptides.index', compact('peptides'));
    }

    public function create()
    {
        return view('admin.peptides.create');
    }

    public function store(Request $request)
    {
        $validated = $this->validatePeptide($request);
        $peptide = Peptide::create($validated);
        $peptide->categories()->sync($request->get('categories', []));

        return redirect()->route('admin.peptides.index')
            ->with('success', 'Peptide created successfully.');
    }

    public function edit(Peptide $peptide)
    {
        $peptide->load('categories');
        return view('admin.peptides.edit', compact('peptide'));
    }

    public function update(Request $request, Peptide $peptide)
    {
        $validated = $this->validatePeptide($request);
        $peptide->update($validated);
        $peptide->categories()->sync($request->get('categories', []));

        return redirect()->route('admin.peptides.index')
            ->with('success', 'Peptide updated successfully.');
    }

    public function destroy(Peptide $peptide)
    {
        $peptide->delete();

        return redirect()->route('admin.peptides.index')
            ->with('success', 'Peptide deleted successfully.');
    }

    public function togglePublish(Peptide $peptide)
    {
        $peptide->update(['is_published' => !$peptide->is_published]);

        return back()->with('success', 'Peptide status updated.');
    }

    protected function validatePeptide(Request $request): array
    {
        return $request->validate([
            'name' => 'required|string|max:255',
            'full_name' => 'nullable|string|max:255',
            'abbreviation' => 'nullable|string|max:20',
            'type' => 'nullable|string|max:255',
            'typical_dose' => 'nullable|string|max:255',
            'dose_frequency' => 'nullable|string|max:255',
            'route' => 'nullable|string|max:255',
            'injection_sites' => 'nullable|array',
            'cycle' => 'nullable|string|max:255',
            'storage' => 'nullable|string|max:255',
            'research_status' => 'required|in:extensive,well,emerging,limited',
            'is_published' => 'boolean',
            'overview' => 'nullable|string',
            'key_benefits' => 'nullable|array',
            'mechanism_of_action' => 'nullable|string',
            'what_to_expect' => 'nullable|array',
            'safety_warnings' => 'nullable|array',
            'molecular_weight' => 'nullable|numeric',
            'amino_acid_length' => 'nullable|integer',
            'peak_time' => 'nullable|string|max:255',
            'half_life' => 'nullable|string|max:255',
            'clearance_time' => 'nullable|string|max:255',
        ]);
    }
}
