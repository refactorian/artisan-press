<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\AuthorResource;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class AuthorController extends Controller
{
    /**
     * Display a listing of active authors.
     */
    public function index(): AnonymousResourceCollection
    {
        $authors = User::where('is_active', true)
            ->whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->get();

        return AuthorResource::collection($authors);
    }

    /**
     * Display the specified author along with their published posts.
     */
    public function show(int $id, Request $request): JsonResponse
    {
        $author = User::where('is_active', true)
            ->where('id', $id)
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->firstOrFail();

        $perPage = min(max($request->integer('per_page', 10), 1), 50);

        $posts = $author->posts()
            ->published()
            ->with(['author', 'categories', 'tags', 'media'])
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'author' => new AuthorResource($author),
            'posts' => PostResource::collection($posts)->response()->getData(true),
        ]);
    }
}
