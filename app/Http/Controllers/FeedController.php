<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use App\Services\FeedService;
use Illuminate\Http\Response;

class FeedController extends Controller
{
    public function __construct(
        protected FeedService $feedService
    ) {}

    public function rss(): Response
    {
        $xml = $this->feedService->generateRss();

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=21600, s-maxage=21600',
        ]);
    }

    public function atom(): Response
    {
        $xml = $this->feedService->generateAtom();

        return response($xml, 200, [
            'Content-Type' => 'application/atom+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=21600, s-maxage=21600',
        ]);
    }

    public function category(string $slug): Response
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $xml = $this->feedService->generateRss($category);

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=21600, s-maxage=21600',
        ]);
    }

    public function tag(string $slug): Response
    {
        $tag = Tag::where('slug', $slug)->firstOrFail();
        $xml = $this->feedService->generateRss(null, $tag);

        return response($xml, 200, [
            'Content-Type' => 'application/rss+xml; charset=utf-8',
            'Cache-Control' => 'public, max-age=21600, s-maxage=21600',
        ]);
    }
}
