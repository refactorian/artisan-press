<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PostResource;
use App\Http\Resources\Api\V1\TagResource;
use App\Models\Tag;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class TagController extends Controller
{
    /**
     * Display a listing of tags.
     */
    public function index(): AnonymousResourceCollection
    {
        $tags = Tag::withCount(['posts' => fn ($q) => $q->published()])
            ->having('posts_count', '>', 0)
            ->orderByDesc('posts_count')
            ->get();

        return TagResource::collection($tags);
    }

    /**
     * Display the specified tag along with its paginated posts.
     */
    public function show(string $slug, Request $request): JsonResponse
    {
        $tag = Tag::where('slug', $slug)
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->firstOrFail();

        $perPage = min(max($request->integer('per_page', 10), 1), 50);

        $posts = $tag->posts()
            ->published()
            ->with(['author', 'categories', 'tags', 'media'])
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'tag' => new TagResource($tag),
            'posts' => PostResource::collection($posts)->response()->getData(true),
        ]);
    }
}
