<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Peptide;
use App\Models\Subscriber;
use App\Models\User;
use App\Models\UserEvent;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'peptides' => Peptide::count(),
            'users' => User::count(),
            'subscribers' => Subscriber::where('status', 'active')->count(),
            'pageViews' => class_exists(UserEvent::class)
                ? UserEvent::where('event_type', 'page_view')->count()
                : 0,
        ];

        return view('admin.dashboard', compact('stats'));
    }
}
