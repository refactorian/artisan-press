<?php

namespace App\Filament\Pages;

use App\Filament\Resources\PostResource;
use App\Filament\Widgets\BlogStatsWidget;
use App\Filament\Widgets\ContentActivityWidget;
use App\Filament\Widgets\DashboardHeroWidget;
use App\Filament\Widgets\PendingCommentsWidget;
use App\Filament\Widgets\PostPublishingChartWidget;
use App\Filament\Widgets\RecentPostsWidget;
use App\Filament\Widgets\RecentSubscribersWidget;
use Filament\Actions\Action;
use Filament\Pages\Dashboard as BaseDashboard;

class Dashboard extends BaseDashboard
{
    protected static ?string $navigationIcon = 'heroicon-o-home';

    protected static ?string $title = 'Command Center';

    protected static ?string $navigationLabel = 'Dashboard';

    /**
     * Define the responsive multi-column layout grid.
     *
     * @return array<string, int>|int
     */
    public function getColumns(): int|string|array
    {
        return [
            'default' => 1,
            'md' => 2,
            'xl' => 3,
        ];
    }

    /**
     * Return curated, executive widgets for the primary admin dashboard.
     *
     * @return array<class-string>
     */
    public function getWidgets(): array
    {
        return [
            DashboardHeroWidget::class,
            BlogStatsWidget::class,
            RecentPostsWidget::class,
            PendingCommentsWidget::class,
            PostPublishingChartWidget::class,
            ContentActivityWidget::class,
            RecentSubscribersWidget::class,
        ];
    }

    /**
     * Define top-level dashboard header actions.
     *
     * @return array<Action>
     */
    protected function getHeaderActions(): array
    {
        return [
            Action::make('write_article')
                ->label('Write Article')
                ->icon('heroicon-m-pencil-square')
                ->color('primary')
                ->url(fn (): string => PostResource::getUrl('create')),

            Action::make('view_site')
                ->label('View Public Blog')
                ->icon('heroicon-m-arrow-top-right-on-square')
                ->color('gray')
                ->url(url('/'))
                ->openUrlInNewTab(),
        ];
    }
}
