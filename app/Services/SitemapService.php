<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Support\Facades\Cache;

class SitemapService
{
    /**
     * Generate complete XML sitemap content.
     */
    public function generateXml(): string
    {
        return Cache::remember(BlogCacheService::CACHE_KEY_SITEMAP, now()->addHours(24), function () {
            $urls = [];

            // 1. Home
            $urls[] = [
                'loc' => url('/'),
                'lastmod' => now()->toAtomString(),
                'changefreq' => 'daily',
                'priority' => '1.0',
            ];

            // 2. Published Posts
            $posts = Post::published()
                ->where('noindex', false)
                ->orderBy('updated_at', 'desc')
                ->get(['slug', 'updated_at', 'published_at']);

            foreach ($posts as $post) {
                $urls[] = [
                    'loc' => url("/posts/{$post->slug}"),
                    'lastmod' => ($post->updated_at ?? $post->published_at ?? now())->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.8',
                ];
            }

            // 3. Published Pages
            $pages = Page::published()
                ->where('noindex', false)
                ->orderBy('updated_at', 'desc')
                ->get(['slug', 'updated_at', 'published_at']);

            foreach ($pages as $page) {
                $urls[] = [
                    'loc' => url("/{$page->slug}"),
                    'lastmod' => ($page->updated_at ?? $page->published_at ?? now())->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.6',
                ];
            }

            // 4. Categories
            $categories = Category::active()
                ->where('noindex', false)
                ->get(['slug', 'updated_at']);

            foreach ($categories as $category) {
                $urls[] = [
                    'loc' => url("/categories/{$category->slug}"),
                    'lastmod' => ($category->updated_at ?? now())->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.5',
                ];
            }

            // 5. Tags
            $tags = Tag::where('noindex', false)
                ->get(['slug', 'updated_at']);

            foreach ($tags as $tag) {
                $urls[] = [
                    'loc' => url("/tags/{$tag->slug}"),
                    'lastmod' => ($tag->updated_at ?? now())->toAtomString(),
                    'changefreq' => 'weekly',
                    'priority' => '0.4',
                ];
            }

            // 6. Authors
            $authors = User::where('is_active', true)
                ->where('noindex', false)
                ->whereHas('posts', fn ($q) => $q->published())
                ->get(['id', 'updated_at']);

            foreach ($authors as $author) {
                $urls[] = [
                    'loc' => url("/authors/{$author->id}"),
                    'lastmod' => ($author->updated_at ?? now())->toAtomString(),
                    'changefreq' => 'monthly',
                    'priority' => '0.5',
                ];
            }

            // Build XML
            $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $xml .= '<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">'."\n";

            foreach ($urls as $url) {
                $xml .= "    <url>\n";
                $xml .= '        <loc>'.htmlspecialchars($url['loc'], ENT_XML1, 'UTF-8')."</loc>\n";
                $xml .= "        <lastmod>{$url['lastmod']}</lastmod>\n";
                $xml .= "        <changefreq>{$url['changefreq']}</changefreq>\n";
                $xml .= "        <priority>{$url['priority']}</priority>\n";
                $xml .= "    </url>\n";
            }

            $xml .= '</urlset>';

            return $xml;
        });
    }
}
