<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Page;
use App\Models\Post;
use App\Models\Setting;
use App\Models\Tag;
use App\Models\User;

class SeoService
{
    /**
     * Generate complete SEO metadata payload for any supported model or home page.
     *
     * @return array<string, mixed>
     */
    /**
     * Generate a BreadcrumbList schema from a breadcrumbs array.
     *
     * @param  array<int, array{label: string, url?: string}>  $breadcrumbs
     * @return array<string, mixed>
     */
    public function generateBreadcrumbSchema(array $breadcrumbs, string $homeUrl = ''): array
    {
        $items = [];
        $position = 1;

        // Always prepend home
        $items[] = [
            '@type' => 'ListItem',
            'position' => $position++,
            'name' => 'Home',
            'item' => $homeUrl ?: url('/'),
        ];

        foreach ($breadcrumbs as $crumb) {
            if (empty($crumb['label'])) {
                continue;
            }
            $item = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $crumb['label'],
            ];
            if (! empty($crumb['url'])) {
                $item['item'] = $crumb['url'];
            }
            $items[] = $item;
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => $items,
        ];
    }

    public function generate(Post|Page|Category|Tag|User|null $model = null): array
    {
        $siteName = Setting::get('site_name', config('app.name', 'Laravel Modern Blog'));
        $siteUrl = url('/');
        $defaultTitle = Setting::get('default_seo_title', $siteName);
        $defaultDesc = Setting::get('default_seo_desc', 'Articles, insights, and stories on web development.');
        $twitterHandle = Setting::get('twitter_handle', '@laravel');

        if (! $model) {
            return [
                'title' => $defaultTitle,
                'meta_title' => $defaultTitle,
                'meta_description' => $defaultDesc,
                'canonical_url' => $siteUrl,
                'robots' => 'index, follow',
                'og' => [
                    'site_name' => $siteName,
                    'type' => 'website',
                    'title' => $defaultTitle,
                    'description' => $defaultDesc,
                    'url' => $siteUrl,
                    'image' => asset('images/og-default.png'),
                ],
                'twitter' => [
                    'card' => 'summary_large_image',
                    'site' => $twitterHandle,
                    'title' => $defaultTitle,
                    'description' => $defaultDesc,
                    'image' => asset('images/og-default.png'),
                ],
                'schema' => [
                    [
                        '@context' => 'https://schema.org',
                        '@type' => 'WebSite',
                        'name' => $siteName,
                        'url' => $siteUrl,
                        'potentialAction' => [
                            '@type' => 'SearchAction',
                            'target' => [
                                '@type' => 'EntryPoint',
                                'urlTemplate' => url('/search').'?q={search_term_string}',
                            ],
                            'query-input' => 'required name=search_term_string',
                        ],
                    ],
                ],
            ];
        }

        return match (true) {
            $model instanceof Post => $this->generateForPost($model, $siteName, $twitterHandle),
            $model instanceof Page => $this->generateForPage($model, $siteName, $twitterHandle),
            $model instanceof Category => $this->generateForCategory($model, $siteName, $twitterHandle),
            $model instanceof Tag => $this->generateForTag($model, $siteName, $twitterHandle),
            $model instanceof User => $this->generateForAuthor($model, $siteName, $twitterHandle),
            default => [],
        };
    }

    /**
     * Generate SEO metadata for a Post.
     *
     * @return array<string, mixed>
     */
    public function generateForPost(Post $post, string $siteName, string $twitterHandle): array
    {
        $title = $post->seo_title ?: $post->title;
        $description = $post->seo_description ?: ($post->excerpt ?: str(strip_tags($post->content ?? ''))->limit(160));
        $canonicalUrl = $post->canonical_url ?: url("/posts/{$post->slug}");

        $robots = [];
        $robots[] = $post->noindex ? 'noindex' : 'index';
        $robots[] = $post->nofollow ? 'nofollow' : 'follow';
        $robotsString = implode(', ', $robots);

        $imageUrl = $post->getFirstMediaUrl('featured_image', 'og')
            ?: ($post->getFirstMediaUrl('featured_image') ?: asset('images/og-default.png'));

        $wordCount = str_word_count(strip_tags($post->content ?? ''));
        $keywords = $post->tags->pluck('name')->merge($post->categories->pluck('name'))->filter()->implode(', ');

        $authorSchema = [
            '@type' => 'Person',
            'name' => $post->author?->name ?? 'Editorial Staff',
            'url' => $post->author ? url("/authors/{$post->author->id}") : null,
        ];
        if ($post->author?->job_title) {
            $authorSchema['jobTitle'] = $post->author->job_title;
        }
        if ($post->author?->twitter_handle) {
            $authorSchema['sameAs'][] = "https://twitter.com/{$post->author->twitter_handle}";
        }
        if ($post->author?->linkedin_url) {
            $authorSchema['sameAs'][] = $post->author->linkedin_url;
        }

        $articleSchema = [
            '@context' => 'https://schema.org',
            '@type' => 'BlogPosting',
            'mainEntityOfPage' => [
                '@type' => 'WebPage',
                '@id' => $canonicalUrl,
            ],
            'headline' => $title,
            'description' => (string) $description,
            'image' => $imageUrl,
            'datePublished' => $post->published_at?->toIso8601String(),
            'dateModified' => $post->updated_at?->toIso8601String(),
            'wordCount' => $wordCount,
            'keywords' => $keywords ?: null,
            'author' => $authorSchema,
            'publisher' => [
                '@type' => 'Organization',
                'name' => $siteName,
                'url' => url('/'),
            ],
        ];

        $schema = [$articleSchema];

        return [
            'title' => "{$title} | {$siteName}",
            'meta_title' => $title,
            'meta_description' => (string) $description,
            'canonical_url' => $canonicalUrl,
            'robots' => $robotsString,
            'reading_time' => $post->reading_time ?? $post->calculateReadingTime(),
            'og' => [
                'site_name' => $siteName,
                'type' => 'article',
                'title' => $title,
                'description' => (string) $description,
                'url' => $canonicalUrl,
                'image' => $imageUrl,
                'published_time' => $post->published_at?->toIso8601String(),
                'modified_time' => $post->updated_at?->toIso8601String(),
                'author' => $post->author?->name,
                'section' => $post->categories->first()?->name,
                'tags' => $post->tags->pluck('name')->all(),
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'site' => $twitterHandle,
                'creator' => $post->author?->twitter_handle ? '@'.$post->author->twitter_handle : $twitterHandle,
                'title' => $title,
                'description' => (string) $description,
                'image' => $imageUrl,
            ],
            'schema' => $schema,
        ];
    }

    /**
     * Generate SEO metadata for a Page.
     *
     * @return array<string, mixed>
     */
    public function generateForPage(Page $page, string $siteName, string $twitterHandle): array
    {
        $title = $page->seo_title ?: $page->title;
        $description = $page->seo_description ?: str(strip_tags($page->content ?? ''))->limit(160);
        $canonicalUrl = $page->canonical_url ?: url("/{$page->slug}");

        $robots = [];
        $robots[] = $page->noindex ? 'noindex' : 'index';
        $robots[] = $page->nofollow ? 'nofollow' : 'follow';

        return [
            'title' => "{$title} | {$siteName}",
            'meta_title' => $title,
            'meta_description' => (string) $description,
            'canonical_url' => $canonicalUrl,
            'robots' => implode(', ', $robots),
            'og' => [
                'site_name' => $siteName,
                'type' => 'website',
                'title' => $title,
                'description' => (string) $description,
                'url' => $canonicalUrl,
                'image' => asset('images/og-default.png'),
            ],
            'twitter' => [
                'card' => 'summary_large_image',
                'site' => $twitterHandle,
                'title' => $title,
                'description' => (string) $description,
                'image' => asset('images/og-default.png'),
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'WebPage',
                'name' => $title,
                'url' => $canonicalUrl,
                'description' => (string) $description,
            ],
        ];
    }

    /**
     * Generate SEO metadata for a Category.
     *
     * @return array<string, mixed>
     */
    public function generateForCategory(Category $category, string $siteName, string $twitterHandle): array
    {
        $title = $category->seo_title ?: "{$category->name} Articles";
        $description = $category->seo_description ?: ($category->description ?: "Read latest posts in category {$category->name}.");
        $canonicalUrl = $category->canonical_url ?: url("/categories/{$category->slug}");

        $robots = [];
        $robots[] = $category->noindex ? 'noindex' : 'index';
        $robots[] = $category->nofollow ? 'nofollow' : 'follow';

        return [
            'title' => "{$title} | {$siteName}",
            'meta_title' => $title,
            'meta_description' => (string) $description,
            'canonical_url' => $canonicalUrl,
            'robots' => implode(', ', $robots),
            'og' => [
                'site_name' => $siteName,
                'type' => 'website',
                'title' => $title,
                'description' => (string) $description,
                'url' => $canonicalUrl,
                'image' => asset('images/og-default.png'),
            ],
            'twitter' => [
                'card' => 'summary',
                'site' => $twitterHandle,
                'title' => $title,
                'description' => (string) $description,
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => $category->name,
                'url' => $canonicalUrl,
                'description' => (string) $description,
            ],
        ];
    }

    /**
     * Generate SEO metadata for a Tag.
     *
     * @return array<string, mixed>
     */
    public function generateForTag(Tag $tag, string $siteName, string $twitterHandle): array
    {
        $title = $tag->seo_title ?: "Topic: {$tag->name}";
        $description = $tag->seo_description ?: "Explore all articles tagged with #{$tag->name}.";
        $canonicalUrl = $tag->canonical_url ?: url("/tags/{$tag->slug}");

        $robots = [];
        $robots[] = $tag->noindex ? 'noindex' : 'index';
        $robots[] = $tag->nofollow ? 'nofollow' : 'follow';

        return [
            'title' => "{$title} | {$siteName}",
            'meta_title' => $title,
            'meta_description' => (string) $description,
            'canonical_url' => $canonicalUrl,
            'robots' => implode(', ', $robots),
            'og' => [
                'site_name' => $siteName,
                'type' => 'website',
                'title' => $title,
                'description' => (string) $description,
                'url' => $canonicalUrl,
            ],
            'twitter' => [
                'card' => 'summary',
                'site' => $twitterHandle,
                'title' => $title,
                'description' => (string) $description,
            ],
            'schema' => [
                '@context' => 'https://schema.org',
                '@type' => 'CollectionPage',
                'name' => "#{$tag->name}",
                'url' => $canonicalUrl,
            ],
        ];
    }

    /**
     * Generate SEO metadata for an Author / User.
     *
     * @return array<string, mixed>
     */
    public function generateForAuthor(User $author, string $siteName, string $twitterHandle): array
    {
        $title = $author->seo_title ?: "{$author->name} - Author Profile";
        $description = $author->seo_description ?: ($author->bio ?: "Read articles written by {$author->name}.");
        $canonicalUrl = $author->canonical_url ?: url("/authors/{$author->id}");

        $robots = [];
        $robots[] = $author->noindex ? 'noindex' : 'index';
        $robots[] = $author->nofollow ? 'nofollow' : 'follow';

        return [
            'title' => "{$title} | {$siteName}",
            'meta_title' => $title,
            'meta_description' => (string) $description,
            'canonical_url' => $canonicalUrl,
            'robots' => implode(', ', $robots),
            'og' => [
                'site_name' => $siteName,
                'type' => 'profile',
                'title' => $title,
                'description' => (string) $description,
                'url' => $canonicalUrl,
                'image' => $author->getFilamentAvatarUrl() ?: asset('images/avatar-default.png'),
            ],
            'twitter' => [
                'card' => 'summary',
                'site' => $twitterHandle,
                'title' => $title,
                'description' => (string) $description,
            ],
            'schema' => [
                [
                    '@context' => 'https://schema.org',
                    '@type' => 'Person',
                    'name' => $author->name,
                    'url' => $canonicalUrl,
                    'jobTitle' => $author->job_title,
                    'description' => $author->bio,
                    'image' => $author->getFilamentAvatarUrl() ?: null,
                    'sameAs' => array_values(array_filter([
                        $author->twitter_handle ? "https://twitter.com/{$author->twitter_handle}" : null,
                        $author->linkedin_url ?? null,
                        $author->github_username ? "https://github.com/{$author->github_username}" : null,
                        $author->website_url ?? null,
                    ])),
                ],
            ],
        ];
    }
}
