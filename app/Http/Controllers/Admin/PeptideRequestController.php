<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\PeptideRequest;
use Illuminate\Http\Request;

class PeptideRequestController extends Controller
{
    public function index(Request $request)
    {
        $query = PeptideRequest::with('user')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where('peptide_name', 'like', "%{$search}%");
        }

        $requests = $query->paginate(20)->withQueryString();
        $counts = array_merge(
            ['pending' => 0, 'in_progress' => 0, 'published' => 0, 'rejected' => 0],
            PeptideRequest::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status')->toArray()
        );

        return view('admin.requests.index', compact('requests', 'counts'));
    }

    public function show(PeptideRequest $peptideRequest)
    {
        $peptideRequest->load(['user', 'publishedPeptide', 'processor']);
        return view('admin.requests.show', compact('peptideRequest'));
    }

    public function updateStatus(Request $request, PeptideRequest $peptideRequest)
    {
        $request->validate([
            'status' => 'required|in:pending,in_progress,published,rejected',
            'rejection_reason' => 'required_if:status,rejected|nullable|string',
        ]);

        $data = [
            'status' => $request->status,
            'processed_by' => auth()->id(),
            'processed_at' => now(),
        ];

        if ($request->status === 'rejected') {
            $data['rejection_reason'] = $request->rejection_reason;
        }

        $peptideRequest->update($data);

        return redirect()->route('admin.requests.show', $peptideRequest)
            ->with('success', 'Request status updated.');
    }

    public function destroy(PeptideRequest $peptideRequest)
    {
        $peptideRequest->delete();
        return redirect()->route('admin.requests.index')
            ->with('success', 'Request deleted.');
    }
}
