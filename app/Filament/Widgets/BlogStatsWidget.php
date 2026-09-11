<?php

namespace App\Filament\Widgets;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Enums\SubscriberStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Subscriber;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $publishedPosts = Post::where('status', PostStatus::Published)->count();
        $draftPosts = Post::where('status', PostStatus::Draft)->count();
        $totalViews = Post::sum('view_count');
        $pendingComments = Comment::where('status', CommentStatus::Pending)->count();
        $subscribers = Subscriber::where('status', SubscriberStatus::Subscribed)->count();
        $authors = User::where('is_active', true)->count();

        return [
            Stat::make('Published Posts', $publishedPosts)
                ->description("{$draftPosts} drafts awaiting publication")
                ->descriptionIcon('heroicon-m-document-text')
                ->chart([3, 7, 12, 15, 18, 22, $publishedPosts])
                ->color('primary'),

            Stat::make('Total Post Views', number_format($totalViews))
                ->description('All-time article views')
                ->descriptionIcon('heroicon-m-arrow-trending-up')
                ->color('success'),

            Stat::make('Comments to Moderate', $pendingComments)
                ->description($pendingComments > 0 ? 'Requires attention' : 'Moderation inbox clear')
                ->descriptionIcon('heroicon-m-chat-bubble-left-right')
                ->color($pendingComments > 0 ? 'warning' : 'success'),

            Stat::make('Active Subscribers', number_format($subscribers))
                ->description('Newsletter audience')
                ->descriptionIcon('heroicon-m-envelope-open')
                ->color('info'),

            Stat::make('Active Authors', $authors)
                ->description('Blog team contributors')
                ->descriptionIcon('heroicon-m-users')
                ->color('gray'),
        ];
    }
}
