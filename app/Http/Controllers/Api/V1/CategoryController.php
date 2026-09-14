<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CategoryResource;
use App\Http\Resources\Api\V1\PostResource;
use App\Models\Category;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CategoryController extends Controller
{
    /**
     * Display a listing of active categories.
     */
    public function index(): AnonymousResourceCollection
    {
        $categories = Category::active()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        return CategoryResource::collection($categories);
    }

    /**
     * Display the specified category along with its paginated posts.
     */
    public function show(string $slug, Request $request): JsonResponse
    {
        $category = Category::active()
            ->where('slug', $slug)
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->firstOrFail();

        $perPage = min(max($request->integer('per_page', 10), 1), 50);

        $posts = $category->posts()
            ->published()
            ->with(['author', 'categories', 'tags', 'media'])
            ->orderBy('published_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'category' => new CategoryResource($category),
            'posts' => PostResource::collection($posts)->response()->getData(true),
        ]);
    }
}
