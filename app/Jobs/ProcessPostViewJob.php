<?php

namespace App\Jobs;

use App\Models\Post;
use App\Models\PostView;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class ProcessPostViewJob implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $postId,
        public ?int $userId,
        public ?string $ipHash,
        public ?string $userAgent,
        public ?string $referer,
        public string $viewedDate
    ) {}

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $post = Post::find($this->postId);

        if (! $post) {
            return;
        }

        PostView::create([
            'post_id' => $this->postId,
            'user_id' => $this->userId,
            'ip_hash' => $this->ipHash,
            'user_agent' => $this->userAgent,
            'referer' => $this->referer,
            'viewed_date' => $this->viewedDate,
        ]);

        $post->incrementViewCount();
    }
}
