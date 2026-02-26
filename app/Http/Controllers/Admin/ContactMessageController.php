<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ContactMessage;
use Illuminate\Http\Request;

class ContactMessageController extends Controller
{
    public function index(Request $request)
    {
        $query = ContactMessage::with('user')
            ->orderBy('created_at', 'desc');

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('search')) {
            $search = str_replace(['%', '_'], ['\\%', '\\_'], $request->search);
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('subject', 'like', "%{$search}%");
            });
        }

        $messages = $query->paginate(20)->withQueryString();
        $counts = array_merge(
            ['new' => 0, 'in_progress' => 0, 'resolved' => 0],
            ContactMessage::selectRaw('status, COUNT(*) as count')->groupBy('status')->pluck('count', 'status')->toArray()
        );

        return view('admin.messages.index', compact('messages', 'counts'));
    }

    public function show(ContactMessage $message)
    {
        return view('admin.messages.show', compact('message'));
    }

    public function updateStatus(Request $request, ContactMessage $message)
    {
        $request->validate([
            'status' => 'required|in:new,in_progress,resolved',
            'admin_notes' => 'nullable|string',
        ]);

        $data = ['status' => $request->status, 'admin_notes' => $request->admin_notes];
        if ($request->status === 'resolved') {
            $data['resolved_at'] = now();
        }

        $message->update($data);

        return redirect()->route('admin.messages.show', $message)
            ->with('success', 'Message status updated.');
    }

    public function destroy(ContactMessage $message)
    {
        $message->delete();
        return redirect()->route('admin.messages.index')
            ->with('success', 'Message deleted.');
    }
}
