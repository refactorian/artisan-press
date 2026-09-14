<?php

namespace App\Services;

use App\Enums\CommentStatus;
use App\Enums\SubscriberStatus;
use App\Jobs\ProcessPostViewJob;
use App\Models\Comment;
use App\Models\Post;
use App\Models\PostView;
use App\Models\SearchLog;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class AnalyticsService
{
    /**
     * Common bot user agent substrings.
     *
     * @var list<string>
     */
    protected array $botUserAgents = [
        'bot', 'crawl', 'spider', 'slurp', 'mediapartners', 'lighthouse',
        'headlesschrome', 'curl', 'wget', 'facebookexternalhit', 'pingdom',
    ];

    /**
     * Record a view on a post, deduplicated per IP hash within a 1-hour window.
     */
    public function recordView(Post $post, Request $request): bool
    {
        $userAgent = (string) $request->userAgent();

        // Bot / crawler filtering
        foreach ($this->botUserAgents as $bot) {
            if (stripos($userAgent, $bot) !== false) {
                return false;
            }
        }

        $ip = $request->ip() ?? '127.0.0.1';
        $ipHash = hash('sha256', $ip.config('app.key'));
        $cacheKey = "post_view_dedup:{$post->id}:{$ipHash}";

        // If viewed within the last hour by same IP, do not record a new discrete view
        if (Cache::has($cacheKey)) {
            return false;
        }

        Cache::put($cacheKey, true, now()->addHour());

        ProcessPostViewJob::dispatch(
            $post->id,
            $request->user()?->id,
            $ipHash,
            substr($userAgent, 0, 500),
            substr((string) $request->header('referer'), 0, 500),
            now()->toDateString()
        );

        return true;
    }

    /**
     * Record a search query and results count for search analytics.
     */
    public function recordSearch(string $query, int $resultsCount, ?Request $request = null): void
    {
        $cleanQuery = trim($query);

        if ($cleanQuery === '') {
            return;
        }

        SearchLog::create([
            'query' => str($cleanQuery)->limit(255),
            'results_count' => $resultsCount,
            'user_id' => $request?->user()?->id,
            'ip_address' => $request?->ip(),
        ]);
    }

    /**
     * Get trending posts within the last N days.
     *
     * @return Collection<int, Post>
     */
    public function getTrendingPosts(int $days = 7, int $limit = 5): Collection
    {
        return Post::trending($days)
            ->with(['author', 'categories'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get all-time popular posts.
     *
     * @return Collection<int, Post>
     */
    public function getPopularPosts(int $limit = 5): Collection
    {
        return Post::popular()
            ->with(['author', 'categories'])
            ->limit($limit)
            ->get();
    }

    /**
     * Get high-level overview metrics for the blog.
     *
     * @return array<string, mixed>
     */
    public function getOverviewStats(): array
    {
        $thirtyDaysAgo = now()->subDays(30)->toDateString();

        $totalViews = (int) Post::published()->sum('view_count');
        $thirtyDayViews = (int) PostView::where('viewed_date', '>=', $thirtyDaysAgo)->count();
        $totalPublishedPosts = Post::published()->count();
        $avgReadingTime = (int) round(Post::published()->avg('reading_time') ?: 1);

        return [
            'total_views' => $totalViews,
            'thirty_day_views' => $thirtyDayViews,
            'published_posts' => $totalPublishedPosts,
            'avg_reading_time' => $avgReadingTime,
        ];
    }

    /**
     * Get author performance leaderboard stats.
     *
     * @return Collection<int, User>
     */
    public function getAuthorPerformance(): Collection
    {
        return User::where('is_active', true)
            ->whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->withSum(['posts' => fn ($q) => $q->published()], 'view_count')
            ->orderByDesc('posts_view_count_sum')
            ->get();
    }

    /**
     * Get search analytics summary.
     *
     * @return array{top_searches: array<int, object>, zero_results: array<int, object>, total_queries: int}
     */
    public function getSearchAnalytics(int $days = 30): array
    {
        $since = now()->subDays($days);

        $topSearches = SearchLog::where('created_at', '>=', $since)
            ->select('query', DB::raw('count(*) as searches_count'), DB::raw('avg(results_count) as avg_results'))
            ->groupBy('query')
            ->orderByDesc('searches_count')
            ->limit(10)
            ->get()
            ->all();

        $zeroResults = SearchLog::where('created_at', '>=', $since)
            ->where('results_count', 0)
            ->select('query', DB::raw('count(*) as failures_count'))
            ->groupBy('query')
            ->orderByDesc('failures_count')
            ->limit(10)
            ->get()
            ->all();

        $totalQueries = SearchLog::where('created_at', '>=', $since)->count();

        return [
            'top_searches' => $topSearches,
            'zero_results' => $zeroResults,
            'total_queries' => $totalQueries,
        ];
    }

    /**
     * Get comment and moderation statistics.
     *
     * @return array<string, mixed>
     */
    public function getCommentModerationStats(): array
    {
        $total = Comment::count();
        $approved = Comment::where('status', CommentStatus::Approved)->count();
        $pending = Comment::where('status', CommentStatus::Pending)->count();
        $spam = Comment::where('status', CommentStatus::Spam)->count();
        $approvalRate = $total > 0 ? round(($approved / $total) * 100, 1) : 100.0;

        return [
            'total_comments' => $total,
            'approved_count' => $approved,
            'pending_count' => $pending,
            'spam_count' => $spam,
            'approval_rate' => $approvalRate,
        ];
    }

    /**
     * Get subscriber statistics.
     *
     * @return array<string, mixed>
     */
    public function getSubscriberStats(): array
    {
        $total = Subscriber::count();
        $active = Subscriber::where('status', SubscriberStatus::Subscribed)->count();
        $unsubscribed = Subscriber::where('status', SubscriberStatus::Unsubscribed)->count();
        $recentThirtyDays = Subscriber::where('subscribed_at', '>=', now()->subDays(30))->count();

        return [
            'total_subscribers' => $total,
            'active_subscribers' => $active,
            'unsubscribed' => $unsubscribed,
            'recent_growth' => $recentThirtyDays,
        ];
    }
}
