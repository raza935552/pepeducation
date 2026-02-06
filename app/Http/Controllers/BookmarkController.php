<?php

namespace App\Http\Controllers;

use App\Models\Peptide;
use Illuminate\Support\Facades\Auth;

class BookmarkController extends Controller
{
    public function toggle(Peptide $peptide)
    {
        if (!$peptide->is_published) {
            abort(403, 'Cannot bookmark unpublished peptide.');
        }

        $user = Auth::user();
        $bookmark = $user->bookmarks()->where('peptide_id', $peptide->id)->first();

        if ($bookmark) {
            $bookmark->delete();
            $message = 'Removed from bookmarks';
            $bookmarked = false;
        } else {
            $user->bookmarks()->create(['peptide_id' => $peptide->id]);
            $message = 'Added to bookmarks';
            $bookmarked = true;
        }

        if (request()->wantsJson()) {
            return response()->json([
                'bookmarked' => $bookmarked,
                'message' => $message,
            ]);
        }

        return back()->with('success', $message);
    }
}
