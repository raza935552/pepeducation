<?php

namespace App\Http\Controllers;

use App\Models\BlogPost;
use App\Models\User;

class AuthorController extends Controller
{
    public function show(User $user)
    {
        if (!$user->is_public_author) {
            abort(404);
        }

        $posts = BlogPost::where('created_by', $user->id)
            ->where('status', 'published')
            ->where(function ($q) {
                $q->whereNull('published_at')->orWhere('published_at', '<=', now());
            })
            ->orderByDesc('published_at')
            ->paginate(12);

        return view('authors.show', compact('user', 'posts'));
    }
}
