<?php

namespace App\Jobs;

use App\Models\PostView;
use App\Models\SearchLog;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Support\Facades\Log;

class PruneAnalyticsLogsJob implements ShouldQueue
{
    use Queueable;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        $viewThreshold = now()->subDays(90)->toDateString();
        $viewsPruned = PostView::where('viewed_date', '<', $viewThreshold)->delete();

        $searchThreshold = now()->subDays(180);
        $searchesPruned = SearchLog::where('created_at', '<', $searchThreshold)->delete();

        Log::info("Analytics logs pruned: {$viewsPruned} view records, {$searchesPruned} search records.");
    }
}
