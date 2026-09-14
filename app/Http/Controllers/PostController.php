<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Setting;
use App\Services\AnalyticsService;
use App\Services\SeoService;
use App\Services\SocialShareService;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;

class PostController extends Controller
{
    /**
     * Browse all articles.
     */
    public function index(SeoService $seoService): View
    {
        $siteName = Setting::get('site_name', config('app.name', 'Laravel Modern Blog'));
        $metadata = [
            'title' => "All Articles & Guides | {$siteName}",
            'meta_title' => "All Articles & Guides | {$siteName}",
            'meta_description' => 'Explore the complete archive of technical articles, engineering tutorials, and architectural deep-dives.',
            'canonical_url' => route('posts.index'),
            'robots' => 'index, follow',
            'og' => [
                'site_name' => $siteName,
                'type' => 'website',
                'title' => "All Articles & Guides | {$siteName}",
                'description' => 'Explore the complete archive of technical articles and guides.',
                'url' => route('posts.index'),
            ],
            'twitter' => [
                'card' => 'summary',
                'title' => "All Articles & Guides | {$siteName}",
                'description' => 'Explore the complete archive of technical articles and guides.',
            ],
        ];

        $breadcrumbs = [
            ['label' => 'Articles', 'url' => route('posts.index')],
        ];

        return view('pages.posts.index', [
            'metadata' => $metadata,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    /**
     * Show single article with content, blocks, series navigation, and author details.
     */
    public function show(
        Post $post,
        Request $request,
        SeoService $seoService,
        AnalyticsService $analyticsService,
        SocialShareService $socialShareService
    ): View {
        // Enforce published status unless authenticated user with access
        if (! $post->isPublished()) {
            if (! auth()->check() || ! auth()->user()->canAccessPanel(filament()->getCurrentPanel() ?? filament()->getPanel('admin'))) {
                abort(404);
            }
        }

        // Eager load relations
        $post->load([
            'author',
            'categories',
            'tags',
            'series.posts' => fn ($q) => $q->published()->orderBy('series_order'),
            'relatedPosts' => fn ($q) => $q->published()->with(['author', 'categories', 'media']),
        ]);

        // Deduplicated view tracking
        $analyticsService->recordView($post, $request);

        // SEO metadata
        $metadata = $seoService->generate($post);

        // Social sharing links
        $shareLinks = $socialShareService->generateShareLinks($post);

        // Related posts fallback if explicit related posts relation is empty
        $relatedPosts = $post->relatedPosts;
        if ($relatedPosts->isEmpty() && $firstCat = $post->categories->first()) {
            $relatedPosts = $firstCat->posts()
                ->published()
                ->where('posts.id', '!=', $post->id)
                ->with(['author', 'categories', 'media'])
                ->limit(3)
                ->get();
        }

        // Previous and next posts in chronological order
        $previousPost = Post::published()
            ->where('published_at', '<', $post->published_at)
            ->orderByDesc('published_at')
            ->first(['id', 'title', 'slug']);

        $nextPost = Post::published()
            ->where('published_at', '>', $post->published_at)
            ->orderBy('published_at')
            ->first(['id', 'title', 'slug']);

        $primaryCategory = $post->categories->first();
        $breadcrumbs = [
            ['label' => 'Articles', 'url' => route('posts.index')],
        ];

        if ($primaryCategory) {
            $breadcrumbs[] = [
                'label' => $primaryCategory->name,
                'url' => route('categories.show', $primaryCategory),
            ];
        }

        $breadcrumbs[] = ['label' => $post->title];

        // Enrich schema with BreadcrumbList
        $breadcrumbSchema = $seoService->generateBreadcrumbSchema($breadcrumbs);
        if (isset($metadata['schema'])) {
            $metadata['schema'] = [$metadata['schema'], $breadcrumbSchema];
        } else {
            $metadata['schema'] = [$breadcrumbSchema];
        }

        return view('pages.posts.show', [
            'post' => $post,
            'metadata' => $metadata,
            'shareLinks' => $shareLinks,
            'relatedPosts' => $relatedPosts,
            'previousPost' => $previousPost,
            'nextPost' => $nextPost,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
