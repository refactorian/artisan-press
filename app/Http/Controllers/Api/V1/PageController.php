<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\PageResource;
use App\Models\Page;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class PageController extends Controller
{
    /**
     * Display a listing of published pages.
     */
    public function index(): AnonymousResourceCollection
    {
        $pages = Page::published()
            ->orderBy('sort_order')
            ->get();

        return PageResource::collection($pages);
    }

    /**
     * Display the specified page.
     */
    public function show(string $slug): PageResource
    {
        $page = Page::published()
            ->where('slug', $slug)
            ->firstOrFail();

        return new PageResource($page);
    }
}
