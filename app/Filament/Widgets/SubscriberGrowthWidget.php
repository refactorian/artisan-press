<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class SubscriberGrowthWidget extends BaseWidget
{
    protected static ?int $sort = 6;

    protected function getStats(): array
    {
        $stats = app(AnalyticsService::class)->getSubscriberStats();

        return [
            Stat::make('Total Subscribers', number_format($stats['total_subscribers']))
                ->description('All registered newsletter subscribers')
                ->descriptionIcon('heroicon-m-user-group')
                ->color('primary'),

            Stat::make('Active Subscribers', number_format($stats['active_subscribers']))
                ->description('Currently subscribed audience')
                ->descriptionIcon('heroicon-m-envelope-open')
                ->color('success'),

            Stat::make('New (Last 30 Days)', '+'.number_format($stats['recent_growth']))
                ->description('Recent audience growth')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('info'),

            Stat::make('Unsubscribed', number_format($stats['unsubscribed']))
                ->description('Readers opted out')
                ->descriptionIcon('heroicon-m-user-minus')
                ->color('gray'),
        ];
    }
}
