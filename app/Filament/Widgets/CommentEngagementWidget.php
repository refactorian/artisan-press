<?php

namespace App\Filament\Widgets;

use App\Services\AnalyticsService;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class CommentEngagementWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected function getStats(): array
    {
        $stats = app(AnalyticsService::class)->getCommentModerationStats();

        return [
            Stat::make('Total Comments', number_format($stats['total_comments']))
                ->description('Reader feedback count')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color('primary'),

            Stat::make('Pending Moderation', number_format($stats['pending_count']))
                ->description('Awaiting review')
                ->descriptionIcon('heroicon-m-clock')
                ->color($stats['pending_count'] > 0 ? 'warning' : 'gray'),

            Stat::make('Spam Detected', number_format($stats['spam_count']))
                ->description('Blocked by keyword filters')
                ->descriptionIcon('heroicon-m-shield-exclamation')
                ->color('danger'),

            Stat::make('Approval Rate', "{$stats['approval_rate']}%")
                ->description('Approved community engagement')
                ->descriptionIcon('heroicon-m-check-badge')
                ->color('success'),
        ];
    }
}
