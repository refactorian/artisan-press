<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PostDetailResource;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PostController extends Controller
{
    /**
     * Display a paginated listing of published posts with filtering and sorting.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $query = Post::published()
            ->with(['author', 'categories', 'tags', 'media']);

        // Category filter
        if ($request->filled('category')) {
            $categorySlug = $request->string('category')->toString();
            $query->whereHas('categories', fn ($q) => $q->where('slug', $categorySlug));
        }

        // Tag filter
        if ($request->filled('tag')) {
            $tagSlug = $request->string('tag')->toString();
            $query->whereHas('tags', fn ($q) => $q->where('slug', $tagSlug));
        }

        // Author filter
        if ($request->filled('author')) {
            $authorId = $request->integer('author');
            $query->where('user_id', $authorId);
        }

        // Featured filter
        if ($request->boolean('featured')) {
            $query->featured();
        }

        // Hero filter
        if ($request->boolean('hero')) {
            $query->hero();
        }

        // Series filter
        if ($request->filled('series')) {
            $seriesId = $request->integer('series');
            $query->where('series_id', $seriesId)->orderBy('series_order');
        }

        // Sorting
        $sort = $request->string('sort')->toString();
        match ($sort) {
            'popular' => $query->popular(),
            'trending' => $query->trending(7),
            'oldest' => $query->orderBy('published_at', 'asc'),
            default => $query->orderBy('published_at', 'desc'),
        };

        $perPage = min(max($request->integer('per_page', 10), 1), 50);

        return PostResource::collection($query->paginate($perPage));
    }

    /**
     * Display the specified post.
     */
    public function show(string $slug): PostDetailResource
    {
        $post = Post::published()
            ->where('slug', $slug)
            ->with(['author', 'contributors', 'categories', 'tags', 'series', 'relatedPosts.media', 'media'])
            ->firstOrFail();

        return new PostDetailResource($post);
    }

    /**
     * Display related posts for a given post.
     */
    public function related(string $slug): AnonymousResourceCollection
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        $related = $post->relatedPosts()
            ->published()
            ->with(['author', 'categories', 'tags', 'media'])
            ->limit(4)
            ->get();

        // If manual related posts are fewer than 4, fallback to same category
        if ($related->count() < 4) {
            $categoryIds = $post->categories->pluck('id');
            $fallbacks = Post::published()
                ->where('posts.id', '!=', $post->id)
                ->whereNotIn('posts.id', $related->pluck('id'))
                ->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $categoryIds))
                ->with(['author', 'categories', 'tags', 'media'])
                ->limit(4 - $related->count())
                ->get();

            $related = $related->merge($fallbacks);
        }

        return PostResource::collection($related);
    }

    /**
     * Record a view on a post asynchronously.
     */
    public function recordView(string $slug, Request $request, AnalyticsService $analytics): JsonResponse
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        $recorded = $analytics->recordView($post, $request);

        return response()->json([
            'status' => 'success',
            'recorded' => $recorded,
            'view_count' => $post->view_count + ($recorded ? 1 : 0),
        ]);
    }
}
