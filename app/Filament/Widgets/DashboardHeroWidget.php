<?php

namespace App\Filament\Widgets;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Enums\SubscriberStatus;
use App\Filament\Pages\AnalyticsDashboard;
use App\Filament\Pages\ManageSiteSettings;
use App\Filament\Resources\CategoryResource;
use App\Filament\Resources\CommentResource;
use App\Filament\Resources\PostResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Subscriber;
use Filament\Widgets\Widget;

class DashboardHeroWidget extends Widget
{
    protected static string $view = 'filament.widgets.dashboard-hero-widget';

    protected int|string|array $columnSpan = 'full';

    protected static ?int $sort = -10;

    /**
     * Get view data for the executive dashboard hero.
     *
     * @return array<string, mixed>
     */
    protected function getViewData(): array
    {
        $user = auth()->user();
        $roleName = $user?->getRoleNames()->first() ?? 'Administrator';
        $formattedRole = ucwords(str_replace('_', ' ', $roleName));

        $publishedCount = Post::where('status', PostStatus::Published)->count();
        $draftCount = Post::where('status', PostStatus::Draft)->count();
        $scheduledCount = Post::where('status', PostStatus::Scheduled)->count();
        $pendingComments = Comment::where('status', CommentStatus::Pending)->count();
        $activeSubscribers = Subscriber::where('status', SubscriberStatus::Subscribed)->count();
        $totalViews = Post::sum('view_count');

        // Greeting based on time of day
        $hour = (int) now()->format('H');
        $greeting = match (true) {
            $hour < 12 => 'Good morning',
            $hour < 17 => 'Good afternoon',
            default => 'Good evening',
        };

        return [
            'user' => $user,
            'role' => $formattedRole,
            'greeting' => $greeting,
            'publishedCount' => $publishedCount,
            'draftCount' => $draftCount,
            'scheduledCount' => $scheduledCount,
            'pendingComments' => $pendingComments,
            'activeSubscribers' => $activeSubscribers,
            'totalViews' => $totalViews,
            'todayDate' => now()->format('l, F j, Y'),
            'createPostUrl' => PostResource::getUrl('create'),
            'managePostsUrl' => PostResource::getUrl('index'),
            'categoriesUrl' => CategoryResource::getUrl('index'),
            'commentsUrl' => CommentResource::getUrl('index'),
            'settingsUrl' => ManageSiteSettings::getUrl(),
            'analyticsUrl' => AnalyticsDashboard::getUrl(),
            'liveSiteUrl' => url('/'),
        ];
    }
}
