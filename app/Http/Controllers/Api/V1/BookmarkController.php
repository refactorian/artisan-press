<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class BookmarkController extends Controller
{
    /**
     * Display a listing of the user's bookmarked posts.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $perPage = min(max($request->integer('per_page', 10), 1), 50);

        $posts = $request->user()
            ->bookmarkedPosts()
            ->published()
            ->with(['author', 'categories', 'tags', 'media'])
            ->orderBy('post_bookmarks.created_at', 'desc')
            ->paginate($perPage);

        return PostResource::collection($posts);
    }

    /**
     * Bookmark a post.
     */
    public function store(Request $request, string $slug): JsonResponse
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $user = $request->user();

        $user->bookmarkedPosts()->syncWithoutDetaching([$post->id]);

        return response()->json([
            'message' => 'Post bookmarked successfully',
            'is_bookmarked' => true,
        ]);
    }

    /**
     * Remove a post from bookmarks.
     */
    public function destroy(Request $request, string $slug): JsonResponse
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $user = $request->user();

        $user->bookmarkedPosts()->detach($post->id);

        return response()->json([
            'message' => 'Post removed from bookmarks',
            'is_bookmarked' => false,
        ]);
    }
}
