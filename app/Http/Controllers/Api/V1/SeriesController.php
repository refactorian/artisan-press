<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PostResource;
use App\Http\Resources\Api\V1\SeriesResource;
use App\Models\Series;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class SeriesController extends Controller
{
    /**
     * Display a listing of active series.
     */
    public function index(): AnonymousResourceCollection
    {
        $series = Series::active()
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        return SeriesResource::collection($series);
    }

    /**
     * Display the specified series along with its paginated posts.
     */
    public function show(string $slug, Request $request): JsonResponse
    {
        $series = Series::active()
            ->where('slug', $slug)
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->firstOrFail();

        $perPage = min(max($request->integer('per_page', 10), 1), 50);

        $posts = $series->posts()
            ->published()
            ->with(['author', 'categories', 'tags', 'media'])
            ->orderBy('series_order', 'asc')
            ->paginate($perPage);

        return response()->json([
            'series' => new SeriesResource($series),
            'posts' => PostResource::collection($posts)->response()->getData(true),
        ]);
    }
}
