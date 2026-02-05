<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use Illuminate\Http\Request;

class SubscriberController extends Controller
{
    public function index(Request $request)
    {
        $query = Subscriber::query();

        // Search
        if ($request->filled('search')) {
            $query->where('email', 'like', '%' . $request->search . '%');
        }

        // Status filter
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Source filter
        if ($request->filled('source')) {
            $query->where('source', $request->source);
        }

        $subscribers = $query->latest()->paginate(20)->withQueryString();

        $stats = [
            'total' => Subscriber::count(),
            'active' => Subscriber::active()->count(),
            'fromPopup' => Subscriber::fromSource('popup')->count(),
            'fromFooter' => Subscriber::fromSource('footer')->count(),
        ];

        return view('admin.subscribers.index', compact('subscribers', 'stats'));
    }

    public function show(Subscriber $subscriber)
    {
        return view('admin.subscribers.show', compact('subscriber'));
    }

    public function profile(Subscriber $subscriber)
    {
        $subscriber->load([
            'sessions' => fn($q) => $q->latest()->limit(5),
            'quizResponses.quiz',
            'outboundClicks.outboundLink',
            'leadMagnetDownloads.leadMagnet',
            'popupInteractions.popup',
        ]);

        return view('admin.subscribers.profile', compact('subscriber'));
    }

    public function destroy(Subscriber $subscriber)
    {
        $subscriber->delete();

        return redirect()->route('admin.subscribers.index')
            ->with('success', 'Subscriber deleted successfully.');
    }

    public function export()
    {
        $subscribers = Subscriber::active()->get();

        $csv = "Email,Name,Source,Subscribed At\n";
        foreach ($subscribers as $sub) {
            $csv .= "{$sub->email},{$sub->name},{$sub->source},{$sub->subscribed_at}\n";
        }

        return response($csv)
            ->header('Content-Type', 'text/csv')
            ->header('Content-Disposition', 'attachment; filename="subscribers.csv"');
    }
}
