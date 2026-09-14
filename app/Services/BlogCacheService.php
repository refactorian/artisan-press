<?php

namespace App\Services;

use App\Models\Post;
use Illuminate\Support\Facades\Cache;

class BlogCacheService
{
    public const CACHE_KEY_SITEMAP = 'blog:sitemap';

    public const CACHE_KEY_FEED_RSS = 'blog:feed:rss';

    public const CACHE_KEY_FEED_ATOM = 'blog:feed:atom';

    public const CACHE_KEY_POPULAR_POSTS = 'blog:posts:popular';

    public const CACHE_KEY_TRENDING_POSTS = 'blog:posts:trending';

    public const CACHE_KEY_CATEGORIES = 'blog:categories:all';

    public const CACHE_KEY_TAGS = 'blog:tags:cloud';

    public const CACHE_KEY_SETTINGS = 'blog:settings:public';

    /**
     * Invalidate all cached data related to posts, sitemaps, and feeds.
     */
    public function invalidatePost(Post $post): void
    {
        Cache::forget(self::CACHE_KEY_SITEMAP);
        Cache::forget(self::CACHE_KEY_FEED_RSS);
        Cache::forget(self::CACHE_KEY_FEED_ATOM);
        Cache::forget(self::CACHE_KEY_POPULAR_POSTS);
        Cache::forget(self::CACHE_KEY_TRENDING_POSTS);
        Cache::forget("blog:post:{$post->id}");
        Cache::forget("blog:post:{$post->slug}");

        // Clear taxonomy-specific feeds
        foreach ($post->categories as $category) {
            Cache::forget("blog:feed:category:{$category->slug}");
        }
        foreach ($post->tags as $tag) {
            Cache::forget("blog:feed:tag:{$tag->slug}");
        }
    }

    /**
     * Invalidate taxonomies cache.
     */
    public function invalidateTaxonomies(): void
    {
        Cache::forget(self::CACHE_KEY_CATEGORIES);
        Cache::forget(self::CACHE_KEY_TAGS);
        Cache::forget(self::CACHE_KEY_SITEMAP);
    }

    /**
     * Invalidate sitemap cache.
     */
    public function invalidateSitemap(): void
    {
        Cache::forget(self::CACHE_KEY_SITEMAP);
    }

    /**
     * Invalidate feeds cache.
     */
    public function invalidateFeeds(): void
    {
        Cache::forget(self::CACHE_KEY_FEED_RSS);
        Cache::forget(self::CACHE_KEY_FEED_ATOM);
    }

    /**
     * Purge all blog cache entries.
     */
    public function purgeAll(): void
    {
        Cache::forget(self::CACHE_KEY_SITEMAP);
        Cache::forget(self::CACHE_KEY_FEED_RSS);
        Cache::forget(self::CACHE_KEY_FEED_ATOM);
        Cache::forget(self::CACHE_KEY_POPULAR_POSTS);
        Cache::forget(self::CACHE_KEY_TRENDING_POSTS);
        Cache::forget(self::CACHE_KEY_CATEGORIES);
        Cache::forget(self::CACHE_KEY_TAGS);
        Cache::forget(self::CACHE_KEY_SETTINGS);
    }
}
