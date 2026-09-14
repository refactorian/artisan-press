<?php

namespace App\Console\Commands;

use App\Jobs\PruneAnalyticsLogsJob;
use Illuminate\Console\Command;

class PruneBlogLogsCommand extends Command
{
    protected $signature = 'blog:prune-logs';

    protected $description = 'Prune old analytics view logs and search queries.';

    public function handle(): int
    {
        $this->info('Dispatching analytics log pruning job...');
        PruneAnalyticsLogsJob::dispatchSync();
        $this->info('Analytics log pruning completed.');

        return self::SUCCESS;
    }
}
