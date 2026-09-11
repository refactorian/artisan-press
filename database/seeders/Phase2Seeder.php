<?php

namespace Database\Seeders;

use App\Enums\CommentStatus;
use App\Enums\MessageStatus;
use App\Enums\PageStatus;
use App\Enums\PageTemplate;
use App\Enums\PostStatus;
use App\Enums\RedirectType;
use App\Enums\SubscriberStatus;
use App\Models\Category;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\NavigationMenu;
use App\Models\Page;
use App\Models\Post;
use App\Models\Redirect;
use App\Models\Series;
use App\Models\Setting;
use App\Models\Subscriber;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Database\Seeder;

class Phase2Seeder extends Seeder
{
    public function run(): void
    {
        $admin = User::where('email', 'admin@blog.test')->first() ?? User::first();
        $editor = User::where('email', 'editor@blog.test')->first() ?? $admin;
        $author = User::where('email', 'author@blog.test')->first() ?? $admin;

        // 1. Update Author Profiles
        if ($admin) {
            $admin->update([
                'job_title' => 'Chief Editor & Architect',
                'pronouns' => 'they/them',
                'website_url' => 'https://laravel.com',
                'bio' => 'Seasoned full-stack engineer and editorial director covering modern PHP, cloud architectures, and developer tooling.',
                'is_featured_author' => true,
                'social_links' => [
                    ['platform' => 'twitter', 'url' => 'https://x.com/laravelphp'],
                    ['platform' => 'github', 'url' => 'https://github.com/laravel'],
                    ['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/laravel'],
                ],
            ]);
        }

        if ($author) {
            $author->update([
                'job_title' => 'Senior Technical Writer',
                'pronouns' => 'she/her',
                'website_url' => 'https://blog.test',
                'bio' => 'Writing in-depth tutorials, performance case studies, and practical guides for modern web developers.',
                'is_featured_author' => true,
                'social_links' => [
                    ['platform' => 'twitter', 'url' => 'https://x.com/tech_writer'],
                    ['platform' => 'github', 'url' => 'https://github.com'],
                ],
            ]);
        }

        // 2. Categories & Tags
        $catTech = Category::firstOrCreate(
            ['slug' => 'architecture'],
            ['name' => 'Architecture', 'description' => 'Scalable system design and patterns', 'sort_order' => 1, 'is_active' => true]
        );
        $catTutorials = Category::firstOrCreate(
            ['slug' => 'tutorials'],
            ['name' => 'Tutorials', 'description' => 'Step-by-step guides and walkthroughs', 'sort_order' => 2, 'is_active' => true]
        );

        $tagPhp = Tag::firstOrCreate(['slug' => 'php'], ['name' => 'PHP 8.5']);
        $tagFilament = Tag::firstOrCreate(['slug' => 'filament'], ['name' => 'Filament 3']);
        $tagLaravel = Tag::firstOrCreate(['slug' => 'laravel'], ['name' => 'Laravel 13']);

        // 3. Series
        $series1 = Series::firstOrCreate(
            ['slug' => 'modern-laravel-mastery'],
            [
                'name' => 'Modern Laravel Mastery',
                'description' => 'A comprehensive deep dive into modern Laravel architectures, Eloquent performance, and production workflows.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $series2 = Series::firstOrCreate(
            ['slug' => 'filament-admin-deep-dive'],
            [
                'name' => 'Filament Admin Deep Dive',
                'description' => 'Building enterprise-grade administrative panels and full content management systems with Filament.',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        // 4. Advanced Posts
        $postHero = Post::firstOrCreate(
            ['slug' => 'building-resilient-content-architectures-with-laravel-and-filament'],
            [
                'user_id' => $admin->id,
                'series_id' => $series1->id,
                'series_order' => 1,
                'title' => 'Building Resilient Content Architectures with Laravel and Filament',
                'excerpt' => 'How we crafted a modern, high-performance content management platform leveraging Laravel 13 and Filament 3.',
                'content' => '<p>Modern content management demands more than simple database storage. It requires seamless workflows, versioning, modular content blocks, and instantaneous administrative moderation.</p><p>In this comprehensive architectural overview, we explore the patterns behind version snapshots, threaded discussions, and resilient settings engines.</p>',
                'content_blocks' => [
                    [
                        'type' => 'callout',
                        'data' => [
                            'type' => 'info',
                            'title' => 'Architecture Key Insight',
                            'content' => 'Decoupling publishing workflows from public delivery guarantees maximum operational flexibility.',
                        ],
                    ],
                    [
                        'type' => 'code',
                        'data' => [
                            'language' => 'php',
                            'filename' => 'app/Models/Post.php',
                            'code' => 'public function createRevision(?string $reason = null): PostRevision
{
    return $this->revisions()->create([
        \'title\'   => $this->title,
        \'content\' => $this->content,
        \'reason\'  => $reason,
    ]);
}',
                        ],
                    ],
                    [
                        'type' => 'key_takeaways',
                        'data' => [
                            'title' => 'Key Implementation Pillars',
                            'items' => [
                                ['point' => 'Zero downtime schema evolution via safe migrations'],
                                ['point' => 'Threaded comment moderation with automated spam filtering'],
                                ['point' => 'Automatic post revision snapshots on update'],
                            ],
                        ],
                    ],
                ],
                'status' => PostStatus::Published,
                'is_hero' => true,
                'is_featured' => true,
                'featured_order' => 1,
                'sort_order' => 1,
                'published_at' => now()->subDays(2),
                'view_count' => 1420,
            ]
        );
        $postHero->categories()->sync([$catTech->id]);
        $postHero->tags()->sync([$tagLaravel->id, $tagFilament->id]);
        $postHero->contributors()->syncWithoutDetaching([
            $author->id => ['role' => 'co-author'],
            $editor->id => ['role' => 'editor'],
        ]);

        $post2 = Post::firstOrCreate(
            ['slug' => 'mastering-filament-form-builders-and-custom-fields'],
            [
                'user_id' => $author->id,
                'series_id' => $series2->id,
                'series_order' => 1,
                'title' => 'Mastering Filament Form Builders and Custom Fields',
                'excerpt' => 'Unlocking the power of flexible content blocks, JSON persistence, and dynamic repeater schemas.',
                'content' => '<p>Filament form builders provide unprecedented control over rich editorial workflows without sacrificing code maintainability.</p>',
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => true,
                'featured_order' => 2,
                'sort_order' => 2,
                'published_at' => now()->subDay(),
                'view_count' => 890,
            ]
        );
        $post2->categories()->sync([$catTutorials->id]);
        $post2->tags()->sync([$tagPhp->id, $tagFilament->id]);

        // Related posts
        $postHero->relatedPosts()->syncWithoutDetaching([$post2->id => ['sort_order' => 1]]);
        $post2->relatedPosts()->syncWithoutDetaching([$postHero->id => ['sort_order' => 1]]);

        // 5. Post Revisions
        $postHero->revisions()->firstOrCreate(
            ['slug' => $postHero->slug, 'created_at' => now()->subDays(3)],
            [
                'user_id' => $admin->id,
                'title' => 'Building Resilient Content Architectures with Laravel (Draft v1)',
                'excerpt' => 'Initial outline of the content management platform.',
                'content' => '<p>Initial draft notes on CMS design patterns.</p>',
                'reason' => 'Initial editorial review snapshot',
            ]
        );

        // 6. Comments & Moderation
        $c1 = Comment::firstOrCreate(
            ['content' => 'Outstanding overview! The content blocks pattern makes article editing so versatile.'],
            [
                'post_id' => $postHero->id,
                'user_id' => $author->id,
                'status' => CommentStatus::Approved,
                'moderated_by' => $admin->id,
                'moderated_at' => now()->subDay(),
            ]
        );

        // Nested reply
        Comment::firstOrCreate(
            ['content' => 'Thank you! Glad the block architecture was helpful.'],
            [
                'post_id' => $postHero->id,
                'user_id' => $admin->id,
                'parent_id' => $c1->id,
                'status' => CommentStatus::Approved,
                'moderated_by' => $admin->id,
                'moderated_at' => now()->subHours(12),
            ]
        );

        // Pending comments (for moderation queue)
        Comment::firstOrCreate(
            ['content' => 'Does this architecture support Redis caching for the navigation menus out of the box?'],
            [
                'post_id' => $postHero->id,
                'guest_name' => 'Alex Morgan',
                'guest_email' => 'alex@example.org',
                'status' => CommentStatus::Pending,
                'ip_address' => '192.168.1.50',
            ]
        );

        Comment::firstOrCreate(
            ['content' => 'Great write-up. Are there benchmarks for PostgreSQL JSON querying on the blocks?'],
            [
                'post_id' => $post2->id,
                'guest_name' => 'Sam Taylor',
                'guest_email' => 'sam@dev.io',
                'status' => CommentStatus::Pending,
                'ip_address' => '192.168.1.62',
            ]
        );

        // Spam comment
        Comment::firstOrCreate(
            ['content' => 'Click here for cheap crypto loans and casino bonuses!'],
            [
                'post_id' => $postHero->id,
                'guest_name' => 'Bot Spammer',
                'guest_email' => 'spam@botnet.xyz',
                'status' => CommentStatus::Spam,
                'ip_address' => '10.0.0.99',
            ]
        );

        // 7. Subscribers
        Subscriber::firstOrCreate(
            ['email' => 'sarah.connor@example.com'],
            ['name' => 'Sarah Connor', 'status' => SubscriberStatus::Subscribed, 'source' => 'blog_footer', 'subscribed_at' => now()->subDays(10)]
        );
        Subscriber::firstOrCreate(
            ['email' => 'dev.reader@company.com'],
            ['name' => 'Dave Miller', 'status' => SubscriberStatus::Subscribed, 'source' => 'newsletter_popup', 'subscribed_at' => now()->subDays(3)]
        );
        Subscriber::firstOrCreate(
            ['email' => 'pending.user@test.org'],
            ['name' => 'Pending Confirm', 'status' => SubscriberStatus::Pending, 'source' => 'article_cta', 'subscribed_at' => now()->subHour()]
        );

        // 8. Contact Messages
        ContactMessage::firstOrCreate(
            ['email' => 'clara@partner.com', 'subject' => 'Guest posting and editorial collaboration inquiry'],
            [
                'name' => 'Clara Oswald',
                'message' => 'Hello editorial team! We have a deep technical piece on high-load queuing with Laravel Octane and would love to contribute.',
                'status' => MessageStatus::Unread,
            ]
        );

        ContactMessage::firstOrCreate(
            ['email' => 'marcus@cloud.io', 'subject' => 'Question regarding Meilisearch indexing integration'],
            [
                'name' => 'Marcus Aurelius',
                'message' => 'Loved your latest post on search architecture. How often do you suggest running full reindexes?',
                'status' => MessageStatus::Replied,
                'read_at' => now()->subDays(2),
                'replied_at' => now()->subDay(),
                'reply_notes' => 'Sent recommendations on queue-based Scout chunk syncing.',
            ]
        );

        // 9. Static Pages
        Page::firstOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Our Publication',
                'content' => '<p>We are a dedicated collective of software engineers and architects documenting modern web craftsmanship, Laravel frameworks, and distributed computing.</p>',
                'template' => PageTemplate::About,
                'status' => PageStatus::Published,
                'published_at' => now()->subMonth(),
                'seo_title' => 'About Us - Laravel Modern Blog',
                'seo_description' => 'Learn about our engineering philosophy and technical contributors.',
                'sort_order' => 1,
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact Editorial Team',
                'content' => '<p>Have a question, feedback, or a story proposal? Reach out to our team using the form or email below.</p>',
                'template' => PageTemplate::Contact,
                'status' => PageStatus::Published,
                'published_at' => now()->subMonth(),
                'seo_title' => 'Contact Us - Laravel Modern Blog',
                'seo_description' => 'Get in touch with our editorial and development team.',
                'sort_order' => 2,
            ]
        );

        // 10. Navigation Menus
        $headerMenu = NavigationMenu::firstOrCreate(
            ['location' => 'header'],
            ['name' => 'Main Header Navigation', 'is_active' => true]
        );

        $headerMenu->items()->firstOrCreate(['label' => 'Home', 'url' => '/', 'sort_order' => 1, 'type' => 'url']);
        $headerMenu->items()->firstOrCreate(['label' => 'Tutorials', 'url' => '/categories/tutorials', 'sort_order' => 2, 'type' => 'category']);
        $headerMenu->items()->firstOrCreate(['label' => 'Architecture', 'url' => '/categories/architecture', 'sort_order' => 3, 'type' => 'category']);
        $headerMenu->items()->firstOrCreate(['label' => 'About Us', 'url' => '/about', 'sort_order' => 4, 'type' => 'page']);
        $headerMenu->items()->firstOrCreate(['label' => 'Contact', 'url' => '/contact', 'sort_order' => 5, 'type' => 'page']);

        $footerMenu = NavigationMenu::firstOrCreate(
            ['location' => 'footer'],
            ['name' => 'Footer Links', 'is_active' => true]
        );
        $footerMenu->items()->firstOrCreate(['label' => 'Privacy Policy', 'url' => '/privacy', 'sort_order' => 1, 'type' => 'url']);
        $footerMenu->items()->firstOrCreate(['label' => 'RSS Feed', 'url' => '/feed.xml', 'sort_order' => 2, 'type' => 'url', 'target' => '_blank']);

        // 11. Redirect Rules
        Redirect::firstOrCreate(
            ['source_path' => 'old-laravel-guide'],
            [
                'target_path' => '/posts/building-resilient-content-architectures-with-laravel-and-filament',
                'status_code' => RedirectType::Permanent301,
                'is_active' => true,
                'hit_count' => 45,
            ]
        );

        Redirect::firstOrCreate(
            ['source_path' => 'newsletter'],
            [
                'target_path' => '/#newsletter-section',
                'status_code' => RedirectType::Temporary302,
                'is_active' => true,
                'hit_count' => 12,
            ]
        );

        // 12. Settings
        Setting::set('site_name', 'Laravel Modern CMS & Blog', 'general');
        Setting::set('site_tagline', 'Engineering, architecture, and developer insights.', 'general');
        Setting::set('contact_email', 'editor@blog.test', 'general');
        Setting::set('default_seo_title', 'Laravel Modern Blog - High Performance Content Platform', 'seo');
        Setting::set('default_seo_desc', 'In-depth engineering articles on Laravel 13, Filament 3, and modern cloud architectures.', 'seo');
        Setting::set('twitter_handle', '@laravelphp', 'seo');
        Setting::set('enable_comments', true, 'comments', 'boolean');
        Setting::set('auto_approve_comments', false, 'comments', 'boolean');
        Setting::set('search_driver', 'database', 'search');
    }
}
