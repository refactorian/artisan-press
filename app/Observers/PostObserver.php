<?php

namespace App\Observers;

use App\Enums\PostStatus;
use App\Enums\RedirectType;
use App\Jobs\DispatchWebhookJob;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Setting;
use App\Services\BlogCacheService;
use Filament\Notifications\Notification;

class PostObserver
{
    /**
     * Handle the Post "creating" event.
     */
    public function creating(Post $post): void
    {
        $post->reading_time = $post->calculateReadingTime();
    }

    /**
     * Handle the Post "saving" event.
     * Auto-set published_at when status transitions to published.
     */
    public function saving(Post $post): void
    {
        if ($post->isDirty(['content', 'content_blocks'])) {
            $post->reading_time = $post->calculateReadingTime();
        }

        if ($post->isDirty('status')) {
            if ($post->status === PostStatus::Published && is_null($post->published_at)) {
                $post->published_at = now();
            }
        }
    }

    /**
     * Handle the Post "updating" event.
     * Record 301 redirect if slug changed on a published post.
     */
    public function updating(Post $post): void
    {
        if ($post->isDirty('slug')) {
            $originalSlug = $post->getOriginal('slug');
            if ($originalSlug && $originalSlug !== $post->slug) {
                Redirect::updateOrCreate(
                    ['source_path' => "/posts/{$originalSlug}"],
                    [
                        'target_path' => "/posts/{$post->slug}",
                        'status_code' => RedirectType::Permanent301,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    /**
     * Handle the Post "saved" event.
     */
    public function saved(Post $post): void
    {
        // Invalidate caching
        app(BlogCacheService::class)->invalidatePost($post);

        // Notify user if post just transitioned to published
        if ($post->wasChanged('status') && $post->status === PostStatus::Published && auth()->check()) {
            Notification::make()
                ->title('Post Published')
                ->success()
                ->body("The post \"{$post->title}\" is now live.")
                ->sendToDatabase(auth()->user());
        }

        // Dispatch outbound webhook if configured
        $webhookUrl = Setting::get('webhook_url');
        if ($webhookUrl) {
            $event = $post->wasRecentlyCreated ? 'post.created' : ($post->wasChanged('status') && $post->status === PostStatus::Published ? 'post.published' : 'post.updated');
            DispatchWebhookJob::dispatch(
                $webhookUrl,
                $event,
                [
                    'id' => $post->id,
                    'title' => $post->title,
                    'slug' => $post->slug,
                    'status' => $post->status->value,
                    'published_at' => $post->published_at?->toIso8601String(),
                ],
                Setting::get('webhook_secret')
            );
        }
    }

    /**
     * Handle the Post "deleted" event.
     */
    public function deleted(Post $post): void
    {
        app(BlogCacheService::class)->invalidatePost($post);

        $webhookUrl = Setting::get('webhook_url');
        if ($webhookUrl) {
            DispatchWebhookJob::dispatch(
                $webhookUrl,
                'post.deleted',
                ['id' => $post->id, 'title' => $post->title, 'slug' => $post->slug],
                Setting::get('webhook_secret')
            );
        }
    }
}
