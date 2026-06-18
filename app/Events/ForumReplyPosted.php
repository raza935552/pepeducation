<?php

namespace App\Events;

use App\Models\ForumPost;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ForumReplyPosted
{
    use Dispatchable, SerializesModels;

    public function __construct(public ForumPost $post)
    {
    }
}
