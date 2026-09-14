<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Post;
use App\Services\AnalyticsService;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;

class HomeController extends Controller
{
    public function __invoke(SeoService $seoService, AnalyticsService $analyticsService): View
    {
        // 1. Hero Post (explicit is_hero or most recent featured)
        $heroPost = Post::published()
            ->hero()
            ->with(['author', 'categories', 'media'])
            ->first()
            ?? Post::published()
                ->featured()
                ->with(['author', 'categories', 'media'])
                ->first()
            ?? Post::published()
                ->latest('published_at')
                ->with(['author', 'categories', 'media'])
                ->first();

        // 2. Featured Posts (excluding hero)
        $heroId = $heroPost?->id;
        $featuredPosts = Post::published()
            ->featured()
            ->when($heroId, fn ($q) => $q->where('id', '!=', $heroId))
            ->with(['author', 'categories', 'media'])
            ->take(3)
            ->get();

        // 3. Latest Posts (excluding hero and featured)
        $excludedIds = array_filter(array_merge([$heroId], $featuredPosts->pluck('id')->all()));
        $latestPosts = Post::published()
            ->when(! empty($excludedIds), fn ($q) => $q->whereNotIn('id', $excludedIds))
            ->with(['author', 'categories', 'media'])
            ->latest('published_at')
            ->take(6)
            ->get();

        // 4. Active Categories with Post counts
        $categories = Category::active()
            ->whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        // 5. Trending Posts
        $trendingPosts = $analyticsService->getTrendingPosts(7, 5);

        // 6. SEO metadata
        $metadata = $seoService->generate();

        return view('pages.home', [
            'heroPost' => $heroPost,
            'featuredPosts' => $featuredPosts,
            'latestPosts' => $latestPosts,
            'categories' => $categories,
            'trendingPosts' => $trendingPosts,
            'metadata' => $metadata,
        ]);
    }
}
