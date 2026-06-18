<?php

namespace App\Policies;

use App\Models\ForumPost;
use App\Models\User;

class ForumPostPolicy
{
    /**
     * Admins bypass all checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function update(User $user, ForumPost $post): bool
    {
        return $user->id === $post->user_id
            && ! $user->is_suspended
            && ! ($post->thread?->is_locked);
    }

    public function delete(User $user, ForumPost $post): bool
    {
        return $user->id === $post->user_id && ! $user->is_suspended;
    }
}
