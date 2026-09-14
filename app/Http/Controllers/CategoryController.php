<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;

class CategoryController extends Controller
{
    public function show(Category $category, SeoService $seoService): View
    {
        if (! $category->is_active) {
            abort(404);
        }

        $posts = $category->posts()
            ->published()
            ->with(['author', 'categories', 'media'])
            ->latest('published_at')
            ->paginate(9);

        $metadata = $seoService->generate($category);

        $breadcrumbs = [
            ['label' => 'Articles', 'url' => route('posts.index')],
            ['label' => 'Categories'],
            ['label' => $category->name],
        ];

        return view('pages.categories.show', [
            'category' => $category,
            'posts' => $posts,
            'metadata' => $metadata,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
