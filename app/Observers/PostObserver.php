<?php

namespace App\Observers;

use App\Enums\PostStatus;
use App\Models\Post;
use Filament\Notifications\Notification;

class PostObserver
{
    /**
     * Handle the Post "creating" event.
     */
    public function creating(Post $post): void
    {
        // Sluggable handles the slug, or manual override
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

    /**
     * Handle the Post "updating" event.
     * Snapshot revision if content or title changed.
     */
    public function updating(Post $post): void
    {
        if ($post->isDirty(['title', 'excerpt', 'content', 'content_blocks'])) {
            $original = $post->getOriginal();
            $post->revisions()->create([
                'user_id' => auth()->id() ?? $post->user_id,
                'title' => $original['title'] ?? $post->title,
                'slug' => $original['slug'] ?? $post->slug,
                'excerpt' => $original['excerpt'] ?? $post->excerpt,
                'content' => $original['content'] ?? $post->content,
                'content_blocks' => isset($original['content_blocks']) && is_string($original['content_blocks'])
                    ? json_decode($original['content_blocks'], true)
                    : ($original['content_blocks'] ?? null),
                'reason' => 'Auto-snapshot before update',
            ]);
        }
    }

    /**
     * Handle the Post "saved" event.
     * Notify users if a post was published.
     */
    public function saved(Post $post): void
    {
        if ($post->wasChanged('status') && $post->status === PostStatus::Published && auth()->check()) {
            Notification::make()
                ->title('Post Published')
                ->success()
                ->body("The post \"{$post->title}\" is now live.")
                ->sendToDatabase(auth()->user());
        }
    }
}
