<?php

namespace App\Filament\Pages;

use App\Console\Commands\GenerateSitemapCommand;
use App\Console\Commands\PublishScheduledPosts;
use App\Console\Commands\RecalculateReadingTimesCommand;
use App\Models\Post;
use App\Services\BlogCacheService;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Symfony\Component\HttpFoundation\StreamedResponse;

class SystemMaintenance extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver';

    protected static ?string $navigationGroup = 'Settings & Audit';

    protected static ?string $navigationLabel = 'System & Maintenance';

    protected static ?string $title = 'System Maintenance & Diagnostics';

    protected static ?int $navigationSort = 3;

    protected static string $view = 'filament.pages.system-maintenance';

    /**
     * Diagnostic system information.
     *
     * @return array<string, string>
     */
    public function getSystemDiagnostics(): array
    {
        try {
            $dbVersion = DB::selectOne('select version() as ver')->ver ?? 'PostgreSQL';
            $dbStatus = 'Connected';
        } catch (\Throwable $e) {
            $dbVersion = 'Error';
            $dbStatus = 'Disconnected: '.$e->getMessage();
        }

        return [
            'php_version' => PHP_VERSION,
            'laravel_version' => app()->version(),
            'database_driver' => config('database.default'),
            'database_version' => str($dbVersion)->limit(50)->toString(),
            'database_status' => $dbStatus,
            'cache_driver' => config('cache.default'),
            'queue_driver' => config('queue.default'),
            'app_env' => config('app.env'),
            'debug_mode' => config('app.debug') ? 'Enabled (Warning in production)' : 'Disabled (Production Safe)',
        ];
    }

    protected function getHeaderActions(): array
    {
        return [
            Action::make('purgeCache')
                ->label('Purge Blog Cache')
                ->icon('heroicon-o-arrow-path')
                ->color('warning')
                ->requiresConfirmation()
                ->modalHeading('Purge All Blog Cache?')
                ->modalDescription('This will invalidate all cached sitemaps, RSS feeds, taxonomy lists, and popular post queries.')
                ->action(function (BlogCacheService $cache): void {
                    $cache->purgeAll();
                    Notification::make()
                        ->title('Cache Purged Successfully')
                        ->body('All blog caches, sitemaps, and feeds have been cleared.')
                        ->success()
                        ->send();
                }),

            Action::make('rebuildSitemap')
                ->label('Rebuild XML Sitemap')
                ->icon('heroicon-o-globe-alt')
                ->color('info')
                ->action(function (): void {
                    Artisan::call(GenerateSitemapCommand::class);
                    Notification::make()
                        ->title('Sitemap Rebuilt')
                        ->body('XML sitemap cache has been regenerated and updated.')
                        ->success()
                        ->send();
                }),

            Action::make('recalculateReadingTimes')
                ->label('Recalculate Reading Times')
                ->icon('heroicon-o-clock')
                ->color('gray')
                ->action(function (): void {
                    Artisan::call(RecalculateReadingTimesCommand::class);
                    Notification::make()
                        ->title('Reading Times Updated')
                        ->body('Calculated reading times updated across all posts.')
                        ->success()
                        ->send();
                }),

            Action::make('publishScheduled')
                ->label('Check Scheduled Posts')
                ->icon('heroicon-o-paper-airplane')
                ->color('success')
                ->action(function (): void {
                    Artisan::call(PublishScheduledPosts::class);
                    Notification::make()
                        ->title('Scheduled Posts Checked')
                        ->body('All overdue scheduled articles have been published.')
                        ->success()
                        ->send();
                }),

            Action::make('exportAllJson')
                ->label('Export All Posts (JSON)')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('primary')
                ->action(function (): StreamedResponse {
                    $posts = Post::with(['author:id,name,email', 'categories:id,name,slug', 'tags:id,name,slug'])
                        ->get()
                        ->map(fn (Post $p) => [
                            'id' => $p->id,
                            'title' => $p->title,
                            'slug' => $p->slug,
                            'excerpt' => $p->excerpt,
                            'content' => $p->content,
                            'content_blocks' => $p->content_blocks,
                            'status' => $p->status->value,
                            'published_at' => $p->published_at?->toIso8601String(),
                            'reading_time' => $p->reading_time,
                            'view_count' => $p->view_count,
                            'seo_title' => $p->seo_title,
                            'seo_description' => $p->seo_description,
                            'canonical_url' => $p->canonical_url,
                            'author' => $p->author?->name,
                            'categories' => $p->categories->pluck('name')->all(),
                            'tags' => $p->tags->pluck('name')->all(),
                        ]);

                    $filename = 'blog-posts-backup-'.now()->format('Y-m-d-His').'.json';

                    return response()->streamDownload(function () use ($posts) {
                        echo json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    }, $filename, ['Content-Type' => 'application/json']);
                }),
        ];
    }
}
