<?php

namespace App\Filament\Widgets;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Enums\SubscriberStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Subscriber;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStatsWidget extends BaseWidget
{
    protected static ?int $sort = -5;

    protected int|string|array $columnSpan = 'full';

    protected function getStats(): array
    {
        $publishedPosts = Post::where('status', PostStatus::Published)->count();
        $draftPosts = Post::where('status', PostStatus::Draft)->count();
        $scheduledPosts = Post::where('status', PostStatus::Scheduled)->count();
        $totalViews = (int) Post::sum('view_count');
        $pendingComments = Comment::where('status', CommentStatus::Pending)->count();
        $subscribers = Subscriber::where('status', SubscriberStatus::Subscribed)->count();

        return [
            Stat::make('Published Articles', $publishedPosts)
                ->description("{$draftPosts} drafts • {$scheduledPosts} scheduled")
                ->descriptionIcon('heroicon-m-document-text')
                ->chart([3, 5, 8, 11, 13, 15, max($publishedPosts, 16)])
                ->color('success'),

            Stat::make('Lifetime Reader Views', number_format($totalViews))
                ->description('Audience reach across all publications')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->chart([12000, 24000, 38000, 52000, 68000, 81000, max($totalViews, 84000)])
                ->color('primary'),

            Stat::make('Active Subscribers', number_format($subscribers))
                ->description('Weekly engineering digest readers')
                ->descriptionIcon('heroicon-m-envelope-open')
                ->chart([1, 2, 3, 4, 5, 5, max($subscribers, 5)])
                ->color('info'),

            Stat::make('Moderation Queue', $pendingComments)
                ->description($pendingComments > 0 ? "Requires attention ({$pendingComments} pending)" : 'Moderation inbox zero')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->chart($pendingComments > 0 ? [1, 2, 1, 3, 2, 1, $pendingComments] : [0, 0, 0, 0, 0, 0, 0])
                ->color($pendingComments > 0 ? 'warning' : 'success'),
        ];
    }
}
