<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AnalyticsOverviewWidget;
use App\Filament\Widgets\AuthorPerformanceWidget;
use App\Filament\Widgets\CommentEngagementWidget;
use App\Filament\Widgets\SearchTrendsWidget;
use App\Filament\Widgets\SubscriberGrowthWidget;
use App\Filament\Widgets\TrendingPostsWidget;
use Filament\Pages\Page;

class AnalyticsDashboard extends Page
{
    protected static ?string $navigationIcon = 'heroicon-o-chart-bar';

    protected static ?string $navigationGroup = 'Analytics & Reports';

    protected static ?string $navigationLabel = 'Analytics & Insights';

    protected static ?string $title = 'Content Analytics & Performance';

    protected static ?int $navigationSort = 1;

    protected static string $view = 'filament.pages.analytics-dashboard';

    protected function getHeaderWidgets(): array
    {
        return [
            AnalyticsOverviewWidget::class,
            TrendingPostsWidget::class,
            AuthorPerformanceWidget::class,
            SearchTrendsWidget::class,
            CommentEngagementWidget::class,
            SubscriberGrowthWidget::class,
        ];
    }
}
