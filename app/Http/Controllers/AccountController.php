<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AccountController extends Controller
{
    public function profile()
    {
        return view('account.profile', [
            'user' => Auth::user(),
        ]);
    }

    public function updateProfile(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'bio' => 'nullable|string|max:200',
        ]);

        $user->update($validated);

        return back()->with('success', 'Profile updated successfully.');
    }

    public function bookmarks()
    {
        $bookmarks = Auth::user()
            ->bookmarks()
            ->with('peptide')
            ->latest()
            ->paginate(12);

        return view('account.bookmarks', compact('bookmarks'));
    }

    public function preferences()
    {
        $preferences = Auth::user()->getOrCreatePreferences();

        return view('account.preferences', compact('preferences'));
    }

    public function updatePreferences(Request $request)
    {
        $user = Auth::user();
        $preferences = $user->getOrCreatePreferences();

        $preferences->update([
            'notify_edit_status' => $request->boolean('notify_edit_status'),
            'notify_marketing' => $request->boolean('notify_marketing'),
            'notify_weekly_digest' => $request->boolean('notify_weekly_digest'),
            'data_usage_opt_in' => $request->boolean('data_usage_opt_in'),
        ]);

        return back()->with('success', 'Preferences updated successfully.');
    }

    public function contributions()
    {
        $contributions = Auth::user()
            ->contributions()
            ->with('peptide:id,name,slug')
            ->latest()
            ->paginate(10);

        return view('account.contributions', compact('contributions'));
    }
}
