<?php

namespace App\Filament\Widgets;

use App\Enums\PostStatus;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Filament\Widgets\StatsOverviewWidget as BaseWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class BlogStatsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        $totalPosts      = Post::withTrashed()->count();
        $publishedPosts  = Post::where('status', PostStatus::Published)->count();
        $draftPosts      = Post::where('status', PostStatus::Draft)->count();
        $scheduledPosts  = Post::where('status', PostStatus::Scheduled)->count();
        $totalCategories = Category::count();
        $totalTags       = Tag::count();
        $totalUsers      = User::count();

        // Posts published this month vs last month
        $thisMonth  = Post::where('status', PostStatus::Published)
            ->whereMonth('published_at', now()->month)
            ->whereYear('published_at', now()->year)
            ->count();
        $lastMonth  = Post::where('status', PostStatus::Published)
            ->whereMonth('published_at', now()->subMonth()->month)
            ->whereYear('published_at', now()->subMonth()->year)
            ->count();
        $trend      = $lastMonth > 0 ? round((($thisMonth - $lastMonth) / $lastMonth) * 100, 1) : 100;
        $trendColor = $trend >= 0 ? 'success' : 'danger';

        return [
            Stat::make('Total Posts', $totalPosts)
                ->description("{$publishedPosts} published · {$draftPosts} draft · {$scheduledPosts} scheduled")
                ->descriptionIcon('heroicon-m-document-text')
                ->color('primary'),

            Stat::make('Published This Month', $thisMonth)
                ->description(($trend >= 0 ? '+' : '').$trend.'% vs last month')
                ->descriptionIcon($trend >= 0 ? 'heroicon-m-arrow-trending-up' : 'heroicon-m-arrow-trending-down')
                ->color($trendColor),

            Stat::make('Categories', $totalCategories)
                ->description('Blog categories')
                ->descriptionIcon('heroicon-m-folder')
                ->color('warning'),

            Stat::make('Tags', $totalTags)
                ->description('Post tags')
                ->descriptionIcon('heroicon-m-tag')
                ->color('info'),

            Stat::make('Authors', $totalUsers)
                ->description('Registered users')
                ->descriptionIcon('heroicon-m-users')
                ->color('success'),
        ];
    }
}
