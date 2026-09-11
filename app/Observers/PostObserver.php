<?php

namespace App\Observers;

use App\Enums\PostStatus;
use App\Models\Post;

class PostObserver
{
    /**
     * Handle the Post "creating" event.
     * Ensure slug uniqueness when creating.
     */
    public function creating(Post $post): void
    {
        if (empty($post->slug)) {
            $post->slug = $post->generateUniqueSlug($post->title);
        }
    }

    /**
     * Handle the Post "saving" event.
     * Auto-set published_at when status transitions to published.
     */
    public function saving(Post $post): void
    {
        if ($post->isDirty('status')) {
            if ($post->status === PostStatus::Published && is_null($post->published_at)) {
                $post->published_at = now();
            }
        }
    }
}
