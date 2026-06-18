<?php

namespace App\Policies;

use App\Models\ForumThread;
use App\Models\User;

class ForumThreadPolicy
{
    /**
     * Admins bypass all checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        return $user->isAdmin() ? true : null;
    }

    public function create(User $user): bool
    {
        return $user->canParticipateInCommunity();
    }

    public function update(User $user, ForumThread $thread): bool
    {
        return $user->id === $thread->user_id
            && ! $thread->is_locked
            && ! $user->is_suspended;
    }

    public function delete(User $user, ForumThread $thread): bool
    {
        return $user->id === $thread->user_id && ! $user->is_suspended;
    }

    public function reply(User $user, ForumThread $thread): bool
    {
        return $user->canParticipateInCommunity()
            && ! $thread->is_locked
            && $thread->status === 'published';
    }
}
