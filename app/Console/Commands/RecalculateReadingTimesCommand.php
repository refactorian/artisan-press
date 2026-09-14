<?php

namespace App\Console\Commands;

use App\Models\Post;
use Illuminate\Console\Command;

class RecalculateReadingTimesCommand extends Command
{
    protected $signature = 'blog:recalculate-reading-times';

    protected $description = 'Recalculate estimated reading time for all posts.';

    public function handle(): int
    {
        $this->info('Recalculating reading times for all posts...');

        $count = 0;
        Post::chunk(100, function ($posts) use (&$count) {
            foreach ($posts as $post) {
                $time = $post->calculateReadingTime();
                $post->updateQuietly(['reading_time' => $time]);
                $count++;
            }
        });

        $this->info("Successfully updated reading times for {$count} post(s).");

        return self::SUCCESS;
    }
}
