<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class AnalyticsOverviewWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $analytics = app(AnalyticsService::class);
        $stats = $analytics->getOverviewStats();

        return [
            Stat::make('Total Post Views', number_format($stats['total_views']))
                ->description('All-time discrete page views')
                ->descriptionIcon('heroicon-m-eye')
                ->color('primary'),

            Stat::make('30-Day Views', number_format($stats['thirty_day_views']))
                ->description('Reader traffic past month')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Published Articles', number_format($stats['published_posts']))
                ->description('Active live content')
                ->descriptionIcon('heroicon-m-document-check')
                ->color('info'),

            Stat::make('Avg. Reading Time', "{$stats['avg_reading_time']} min")
                ->description('Estimated engagement per post')
                ->descriptionIcon('heroicon-m-clock')
                ->color('warning'),
        ];
    }
}
