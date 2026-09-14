<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;

class TagController extends Controller
{
    public function show(Tag $tag, SeoService $seoService): View
    {
        $posts = $tag->posts()
            ->published()
            ->with(['author', 'categories', 'media'])
            ->latest('published_at')
            ->paginate(9);

        $metadata = $seoService->generate($tag);

        $breadcrumbs = [
            ['label' => 'Articles', 'url' => route('posts.index')],
            ['label' => 'Topics'],
            ['label' => '#'.$tag->name],
        ];

        // Related tags: other tags appearing on the same posts
        $relatedTags = Tag::whereHas('posts', function ($q) use ($tag): void {
            $q->published()->whereHas('tags', function ($inner) use ($tag): void {
                $inner->where('tags.id', $tag->id);
            });
        })
            ->where('id', '!=', $tag->id)
            ->withCount(['posts' => function ($q): void {
                $q->published();
            }])
            ->orderByDesc('posts_count')
            ->limit(16)
            ->get();

        return view('pages.tags.show', [
            'tag' => $tag,
            'posts' => $posts,
            'metadata' => $metadata,
            'breadcrumbs' => $breadcrumbs,
            'relatedTags' => $relatedTags,
        ]);
    }
}
