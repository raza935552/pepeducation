<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Contribution;
use Illuminate\Http\Request;

class ContributionController extends Controller
{
    public function index(Request $request)
    {
        $query = Contribution::with(['user', 'peptide'])
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('user', fn($q) => $q->where('name', 'like', "%{$search}%"))
                  ->orWhereHas('peptide', fn($q) => $q->where('name', 'like', "%{$search}%"));
            });
        }

        $contributions = $query->paginate(20)->withQueryString();
        $counts = [
            'pending' => Contribution::where('status', 'pending')->count(),
            'under_review' => Contribution::where('status', 'under_review')->count(),
            'approved' => Contribution::where('status', 'approved')->count(),
            'rejected' => Contribution::where('status', 'rejected')->count(),
        ];

        return view('admin.contributions.index', compact('contributions', 'counts'));
    }

    public function show(Contribution $contribution)
    {
        $contribution->load(['user', 'peptide', 'reviewer']);
        return view('admin.contributions.show', compact('contribution'));
    }

    public function approve(Request $request, Contribution $contribution)
    {
        $contribution->update([
            'status' => 'approved',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'reviewer_notes' => $request->reviewer_notes,
            'published_at' => now(),
        ]);

        return redirect()->route('admin.contributions.index')
            ->with('success', 'Contribution approved successfully.');
    }

    public function reject(Request $request, Contribution $contribution)
    {
        $request->validate(['reviewer_notes' => 'required|string']);

        $contribution->update([
            'status' => 'rejected',
            'reviewed_by' => auth()->id(),
            'reviewed_at' => now(),
            'reviewer_notes' => $request->reviewer_notes,
        ]);

        return redirect()->route('admin.contributions.index')
            ->with('success', 'Contribution rejected.');
    }
}
