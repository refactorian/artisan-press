<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    /**
     * Search published posts by query string with analytics logging.
     */
    public function __invoke(Request $request, AnalyticsService $analytics): JsonResponse
    {
        $query = trim((string) $request->input('q', ''));
        $minChars = (int) config('blog.search_min_chars', 2);

        if (mb_strlen($query) < $minChars) {
            return response()->json([
                'status' => 'error',
                'message' => "Search query must be at least {$minChars} characters.",
                'query' => $query,
                'data' => [],
                'total' => 0,
            ], 422);
        }

        $postsQuery = Post::published()
            ->where(function ($q) use ($query) {
                $term = "%{$query}%";
                $q->where('title', 'ILIKE', $term)
                    ->orWhere('excerpt', 'ILIKE', $term)
                    ->orWhere('content', 'ILIKE', $term)
                    ->orWhereHas('tags', fn ($t) => $t->where('name', 'ILIKE', $term))
                    ->orWhereHas('categories', fn ($c) => $c->where('name', 'ILIKE', $term));
            })
            ->with(['author', 'categories', 'tags', 'media'])
            ->orderBy('published_at', 'desc');

        $perPage = min(max($request->integer('per_page', 10), 1), 50);
        $paginated = $postsQuery->paginate($perPage);

        // Record search analytics
        $analytics->recordSearch($query, $paginated->total(), $request);

        return response()->json([
            'query' => $query,
            'total' => $paginated->total(),
            'posts' => PostResource::collection($paginated)->response()->getData(true),
        ]);
    }
}
