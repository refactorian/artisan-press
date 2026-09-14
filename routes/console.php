<?php

use App\Console\Commands\GenerateSitemapCommand;
use App\Console\Commands\PruneBlogLogsCommand;
use App\Console\Commands\PublishScheduledPosts;
use Illuminate\Foundation\Inspiring;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schedule;

Artisan::command('inspire', function () {
    $this->comment(Inspiring::quote());
})->purpose('Display an inspiring quote');

// Publish scheduled posts
Schedule::command(PublishScheduledPosts::class)
    ->everyMinute()
    ->withoutOverlapping()
    ->runInBackground();

// Pre-warm and refresh XML sitemap
Schedule::command(GenerateSitemapCommand::class)
    ->dailyAt('00:00')
    ->runInBackground();

// Prune old analytics and search logs
Schedule::command(PruneBlogLogsCommand::class)
    ->weeklyOn(0, '02:00')
    ->runInBackground();

// Clean activity logs (Spatie)
Schedule::command('activitylog:clean')
    ->dailyAt('03:00')
    ->runInBackground();
