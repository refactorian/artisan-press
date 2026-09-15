<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Post;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PostLikeController extends Controller
{
    /**
     * Like a post.
     */
    public function store(Request $request, string $slug): JsonResponse
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $user = $request->user();

        $post->likes()->syncWithoutDetaching([$user->id]);

        return response()->json([
            'message' => 'Post liked successfully',
            'is_liked' => true,
            'likes_count' => $post->likes()->count(),
        ]);
    }

    /**
     * Unlike a post.
     */
    public function destroy(Request $request, string $slug): JsonResponse
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();
        $user = $request->user();

        $post->likes()->detach($user->id);

        return response()->json([
            'message' => 'Post unliked successfully',
            'is_liked' => false,
            'likes_count' => $post->likes()->count(),
        ]);
    }
}
