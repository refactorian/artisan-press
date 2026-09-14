<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Cache;

class FeedService
{
    /**
     * Generate RSS 2.0 Feed XML.
     */
    public function generateRss(?Category $category = null, ?Tag $tag = null): string
    {
        $cacheKey = match (true) {
            $category !== null => "blog:feed:category:{$category->slug}",
            $tag !== null => "blog:feed:tag:{$tag->slug}",
            default => BlogCacheService::CACHE_KEY_FEED_RSS,
        };

        return Cache::remember($cacheKey, now()->addHours(6), function () use ($category, $tag) {
            $siteName = Setting::get('site_name', config('app.name', 'Laravel Modern Blog'));
            $siteDesc = Setting::get('default_seo_desc', 'Insights, engineering, and architecture stories.');
            $feedTitle = $siteName;

            $query = Post::published()
                ->with(['author', 'categories', 'tags', 'media'])
                ->orderBy('published_at', 'desc')
                ->limit(25);

            if ($category) {
                $query->whereHas('categories', fn ($q) => $q->where('categories.id', $category->id));
                $feedTitle .= " - Category: {$category->name}";
            }

            if ($tag) {
                $query->whereHas('tags', fn ($q) => $q->where('tags.id', $tag->id));
                $feedTitle .= " - Tag: #{$tag->name}";
            }

            /** @var Collection<int, Post> $posts */
            $posts = $query->get();
            $lastBuildDate = $posts->first()?->published_at?->toRssString() ?? now()->toRssString();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $xml .= '<rss version="2.0" xmlns:atom="http://www.w3.org/2005/Atom" xmlns:content="http://purl.org/rss/1.0/modules/content/" xmlns:dc="http://purl.org/dc/elements/1.1/">'."\n";
            $xml .= "    <channel>\n";
            $xml .= '        <title>'.htmlspecialchars($feedTitle, ENT_XML1, 'UTF-8')."</title>\n";
            $xml .= '        <link>'.htmlspecialchars(url('/'), ENT_XML1, 'UTF-8')."</link>\n";
            $xml .= '        <description>'.htmlspecialchars($siteDesc, ENT_XML1, 'UTF-8')."</description>\n";
            $xml .= "        <language>en-us</language>\n";
            $xml .= "        <lastBuildDate>{$lastBuildDate}</lastBuildDate>\n";
            $xml .= '        <atom:link href="'.htmlspecialchars(url()->current(), ENT_XML1, 'UTF-8').'" rel="self" type="application/rss+xml" />'."\n";

            foreach ($posts as $post) {
                $postUrl = url("/posts/{$post->slug}");
                $pubDate = $post->published_at?->toRssString() ?? $post->created_at->toRssString();
                $authorName = $post->author?->name ?? 'Editorial Team';
                $imageUrl = $post->getFirstMediaUrl('featured_image', 'large');

                $xml .= "        <item>\n";
                $xml .= '            <title>'.htmlspecialchars($post->title, ENT_XML1, 'UTF-8')."</title>\n";
                $xml .= '            <link>'.htmlspecialchars($postUrl, ENT_XML1, 'UTF-8')."</link>\n";
                $xml .= '            <guid isPermaLink="true">'.htmlspecialchars($postUrl, ENT_XML1, 'UTF-8')."</guid>\n";
                $xml .= "            <pubDate>{$pubDate}</pubDate>\n";
                $xml .= '            <dc:creator>'.htmlspecialchars($authorName, ENT_XML1, 'UTF-8')."</dc:creator>\n";
                $xml .= '            <description><![CDATA['.($post->excerpt ?? '')."]]></description>\n";
                $xml .= '            <content:encoded><![CDATA['.($post->content ?? '')."]]></content:encoded>\n";

                if ($imageUrl) {
                    $xml .= '            <enclosure url="'.htmlspecialchars($imageUrl, ENT_XML1, 'UTF-8').'" type="image/webp" />'."\n";
                }

                foreach ($post->categories as $cat) {
                    $xml .= '            <category>'.htmlspecialchars($cat->name, ENT_XML1, 'UTF-8')."</category>\n";
                }

                $xml .= "        </item>\n";
            }

            $xml .= "    </channel>\n";
            $xml .= '</rss>';

            return $xml;
        });
    }

    /**
     * Generate Atom 1.0 Feed XML.
     */
    public function generateAtom(): string
    {
        return Cache::remember(BlogCacheService::CACHE_KEY_FEED_ATOM, now()->addHours(6), function () {
            $siteName = Setting::get('site_name', config('app.name', 'Laravel Modern Blog'));
            $siteDesc = Setting::get('default_seo_desc', 'Insights, engineering, and architecture stories.');

            /** @var Collection<int, Post> $posts */
            $posts = Post::published()
                ->with(['author', 'categories', 'tags'])
                ->orderBy('published_at', 'desc')
                ->limit(25)
                ->get();

            $updated = $posts->first()?->updated_at?->toAtomString() ?? now()->toAtomString();

            $xml = '<?xml version="1.0" encoding="UTF-8"?>'."\n";
            $xml .= '<feed xmlns="http://www.w3.org/2005/Atom">'."\n";
            $xml .= '    <title>'.htmlspecialchars($siteName, ENT_XML1, 'UTF-8')."</title>\n";
            $xml .= '    <subtitle>'.htmlspecialchars($siteDesc, ENT_XML1, 'UTF-8')."</subtitle>\n";
            $xml .= '    <link href="'.htmlspecialchars(url('/'), ENT_XML1, 'UTF-8')."\" />\n";
            $xml .= '    <link href="'.htmlspecialchars(url('/feed/atom'), ENT_XML1, 'UTF-8')."\" rel=\"self\" />\n";
            $xml .= "    <updated>{$updated}</updated>\n";
            $xml .= '    <id>'.htmlspecialchars(url('/'), ENT_XML1, 'UTF-8')."</id>\n";

            foreach ($posts as $post) {
                $postUrl = url("/posts/{$post->slug}");
                $published = $post->published_at?->toAtomString() ?? $post->created_at->toAtomString();
                $postUpdated = $post->updated_at?->toAtomString() ?? $published;
                $authorName = $post->author?->name ?? 'Editorial Team';

                $xml .= "    <entry>\n";
                $xml .= '        <title>'.htmlspecialchars($post->title, ENT_XML1, 'UTF-8')."</title>\n";
                $xml .= '        <link href="'.htmlspecialchars($postUrl, ENT_XML1, 'UTF-8')."\" />\n";
                $xml .= '        <id>'.htmlspecialchars($postUrl, ENT_XML1, 'UTF-8')."</id>\n";
                $xml .= "        <published>{$published}</published>\n";
                $xml .= "        <updated>{$postUpdated}</updated>\n";
                $xml .= "        <author>\n";
                $xml .= '            <name>'.htmlspecialchars($authorName, ENT_XML1, 'UTF-8')."</name>\n";
                $xml .= "        </author>\n";
                $xml .= '        <summary><![CDATA['.($post->excerpt ?? '')."]]></summary>\n";
                $xml .= '        <content type="html"><![CDATA['.($post->content ?? '')."]]></content>\n";

                foreach ($post->categories as $cat) {
                    $xml .= '        <category term="'.htmlspecialchars($cat->slug, ENT_XML1, 'UTF-8').'" label="'.htmlspecialchars($cat->name, ENT_XML1, 'UTF-8')."\" />\n";
                }

                $xml .= "    </entry>\n";
            }

            $xml .= '</feed>';

            return $xml;
        });
    }
}
