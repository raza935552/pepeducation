<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\BugReport;
use Illuminate\Http\Request;

class BugReportController extends Controller
{
    public function index(Request $request)
    {
        $query = BugReport::with('user')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('priority')) {
            $query->where('priority', $request->priority);
        }

        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $bugs = $query->paginate(20)->withQueryString();
        $counts = array_merge(
            ['reported' => 0, 'in_progress' => 0, 'fixed' => 0, 'closed' => 0],
            BugReport::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status')->toArray()
        );

        return view('admin.bugs.index', compact('bugs', 'counts'));
    }

    public function create()
    {
        return view('admin.bugs.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            'page_url' => 'nullable|string|max:255',
            'priority' => 'required|in:low,medium,high,critical',
        ]);

        BugReport::create([
            'user_id' => auth()->id(),
            'title' => $request->title,
            'description' => $request->description,
            'page_url' => $request->page_url,
            'priority' => $request->priority,
        ]);

        return redirect()->route('admin.bugs.index')
            ->with('success', 'Bug report submitted.');
    }

    public function show(BugReport $bugReport)
    {
        $bugReport->load('user');

        return view('admin.bugs.show', compact('bugReport'));
    }

    public function updateStatus(Request $request, BugReport $bugReport)
    {
        $request->validate([
            'status' => 'required|in:reported,in_progress,fixed,closed',
            'admin_notes' => 'nullable|string',
        ]);

        $data = ['status' => $request->status, 'admin_notes' => $request->admin_notes];
        if ($request->status === 'fixed') {
            $data['resolved_at'] = now();
        }

        $bugReport->update($data);

        return redirect()->route('admin.bugs.show', $bugReport)
            ->with('success', 'Bug report status updated.');
    }

    public function destroy(BugReport $bugReport)
    {
        $bugReport->delete();
        return redirect()->route('admin.bugs.index')
            ->with('success', 'Bug report deleted.');
    }
}
