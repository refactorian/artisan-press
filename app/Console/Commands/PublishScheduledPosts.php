<?php

namespace App\Console\Commands;

use App\Enums\PostStatus;
use App\Models\Post;
use Illuminate\Console\Command;

class PublishScheduledPosts extends Command
{
    protected $signature = 'posts:publish-scheduled';

    protected $description = 'Publish all posts whose scheduled publish time has passed.';

    public function handle(): int
    {
        $posts = Post::query()
            ->where('status', PostStatus::Scheduled)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now())
            ->get();

        if ($posts->isEmpty()) {
            $this->line('No scheduled posts to publish.');

            return self::SUCCESS;
        }

        $count = 0;

        foreach ($posts as $post) {
            $post->update(['status' => PostStatus::Published]);
            $count++;
            $this->info("Published: [{$post->id}] {$post->title}");
        }

        $this->info("Successfully published {$count} post(s).");

        return self::SUCCESS;
    }
}
