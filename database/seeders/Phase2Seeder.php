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
use Illuminate\Support\Facades\Hash;

class Phase2Seeder extends Seeder
{
    public function run(): void
    {
        // 1. Authors & Team Profiles
        $admin = User::firstOrCreate(
            ['email' => 'admin@blog.test'],
            [
                'name' => 'Alex Sterling',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $admin->update([
            'name' => 'Alex Sterling',
            'job_title' => 'Head of Engineering & Chief Editor',
            'pronouns' => 'they/them',
            'website_url' => 'https://laravel.com',
            'bio' => 'Distributed systems architect, open-source maintainer, and editorial director exploring high-throughput cloud patterns and developer tooling.',
            'is_featured_author' => true,
            'social_links' => [
                ['platform' => 'twitter', 'url' => 'https://x.com/laravelphp'],
                ['platform' => 'github', 'url' => 'https://github.com/laravel'],
                ['platform' => 'linkedin', 'url' => 'https://linkedin.com/company/laravel'],
            ],
        ]);
        if (! $admin->hasRole('super_admin')) {
            $admin->assignRole('super_admin');
        }

        $editor = User::firstOrCreate(
            ['email' => 'editor@blog.test'],
            [
                'name' => 'Sarah Lin',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $editor->update([
            'name' => 'Sarah Lin',
            'job_title' => 'Principal Cloud Architect',
            'pronouns' => 'she/her',
            'website_url' => 'https://blog.test',
            'bio' => 'Specializing in PostgreSQL scaling, database reliability engineering, and resilient microservices architectures.',
            'is_featured_author' => true,
            'social_links' => [
                ['platform' => 'twitter', 'url' => 'https://x.com/tech_writer'],
                ['platform' => 'github', 'url' => 'https://github.com'],
                ['platform' => 'linkedin', 'url' => 'https://linkedin.com'],
            ],
        ]);
        if (! $editor->hasRole('editor')) {
            $editor->assignRole('editor');
        }

        $author = User::firstOrCreate(
            ['email' => 'author@blog.test'],
            [
                'name' => 'Marcus Brody',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $author->update([
            'name' => 'Marcus Brody',
            'job_title' => 'Senior Full-Stack Developer',
            'pronouns' => 'he/him',
            'website_url' => 'https://blog.test',
            'bio' => 'Crafting intuitive user interfaces, reactive Livewire components, and accessible design systems for enterprise web apps.',
            'is_featured_author' => true,
            'social_links' => [
                ['platform' => 'twitter', 'url' => 'https://x.com/marcus_dev'],
                ['platform' => 'github', 'url' => 'https://github.com'],
            ],
        ]);
        if (! $author->hasRole('author')) {
            $author->assignRole('author');
        }

        $contributor = User::firstOrCreate(
            ['email' => 'elena@blog.test'],
            [
                'name' => 'Elena Rostova',
                'password' => Hash::make('password'),
                'email_verified_at' => now(),
                'is_active' => true,
            ]
        );
        $contributor->update([
            'name' => 'Elena Rostova',
            'job_title' => 'Security & Systems Researcher',
            'pronouns' => 'she/her',
            'website_url' => 'https://blog.test',
            'bio' => 'Focusing on zero-trust architectures, web application hardening, and performance observability.',
            'is_featured_author' => false,
            'social_links' => [
                ['platform' => 'github', 'url' => 'https://github.com'],
                ['platform' => 'linkedin', 'url' => 'https://linkedin.com'],
            ],
        ]);
        if (! $contributor->hasRole('author')) {
            $contributor->assignRole('author');
        }

        // 2. Categories
        $categories = [
            'architecture' => Category::firstOrCreate(
                ['slug' => 'architecture'],
                [
                    'name' => 'Architecture',
                    'description' => 'Scalable system design, distributed systems, clean code paradigms, and modular patterns.',
                    'sort_order' => 1,
                    'is_active' => true,
                    'seo_title' => 'Software Architecture & System Design - Modern Blog',
                    'seo_description' => 'Deep technical insights into building resilient architectures, modular domain designs, and scalable systems.',
                ]
            ),
            'tutorials' => Category::firstOrCreate(
                ['slug' => 'tutorials'],
                [
                    'name' => 'Tutorials',
                    'description' => 'Hands-on, step-by-step guides and practical implementation recipes.',
                    'sort_order' => 2,
                    'is_active' => true,
                    'seo_title' => 'In-Depth Developer Tutorials & Practical Guides',
                    'seo_description' => 'Practical code walkthroughs covering Laravel, Livewire, Filament, and modern web tooling.',
                ]
            ),
            'performance' => Category::firstOrCreate(
                ['slug' => 'performance'],
                [
                    'name' => 'Performance',
                    'description' => 'Database query tuning, Redis caching strategies, HTTP benchmarking, and sub-millisecond optimizations.',
                    'sort_order' => 3,
                    'is_active' => true,
                    'seo_title' => 'High-Performance Web Applications & Database Tuning',
                    'seo_description' => 'Benchmarks, query profiling, and optimization case studies for high-load systems.',
                ]
            ),
            'frontend' => Category::firstOrCreate(
                ['slug' => 'frontend'],
                [
                    'name' => 'Frontend',
                    'description' => 'Reactive UIs with Livewire 3, Alpine.js, Tailwind CSS v4, and modern accessibility.',
                    'sort_order' => 4,
                    'is_active' => true,
                    'seo_title' => 'Modern Frontend Architecture: Livewire, Alpine & Tailwind',
                    'seo_description' => 'Building sleek, accessible, server-driven reactive user interfaces.',
                ]
            ),
            'devops' => Category::firstOrCreate(
                ['slug' => 'devops'],
                [
                    'name' => 'DevOps & Cloud',
                    'description' => 'Containerization, CI/CD pipelines, Docker, Kubernetes, and automated deployment pipelines.',
                    'sort_order' => 5,
                    'is_active' => true,
                    'seo_title' => 'DevOps, Containers, and Cloud Deployment Strategies',
                    'seo_description' => 'Best practices for Docker, Laravel Sail, automated testing, and scalable deployments.',
                ]
            ),
            'security' => Category::firstOrCreate(
                ['slug' => 'security'],
                [
                    'name' => 'Security',
                    'description' => 'Application hardening, RBAC permissions, encrypted payloads, and vulnerability prevention.',
                    'sort_order' => 6,
                    'is_active' => true,
                    'seo_title' => 'Web Application Security & Defense in Depth',
                    'seo_description' => 'Practical guides for securing APIs, protecting sensitive user data, and auditing codebases.',
                ]
            ),
        ];

        // 3. Tags
        $tags = [
            'laravel' => Tag::firstOrCreate(['slug' => 'laravel'], ['name' => 'Laravel 13']),
            'php' => Tag::firstOrCreate(['slug' => 'php'], ['name' => 'PHP 8.5']),
            'livewire' => Tag::firstOrCreate(['slug' => 'livewire'], ['name' => 'Livewire 3']),
            'filament' => Tag::firstOrCreate(['slug' => 'filament'], ['name' => 'Filament 3']),
            'alpine' => Tag::firstOrCreate(['slug' => 'alpine'], ['name' => 'Alpine.js']),
            'tailwind' => Tag::firstOrCreate(['slug' => 'tailwind'], ['name' => 'Tailwind CSS']),
            'postgresql' => Tag::firstOrCreate(['slug' => 'postgresql'], ['name' => 'PostgreSQL']),
            'redis' => Tag::firstOrCreate(['slug' => 'redis'], ['name' => 'Redis']),
            'docker' => Tag::firstOrCreate(['slug' => 'docker'], ['name' => 'Docker']),
            'testing' => Tag::firstOrCreate(['slug' => 'testing'], ['name' => 'Testing']),
            'api' => Tag::firstOrCreate(['slug' => 'api'], ['name' => 'API Design']),
            'performance' => Tag::firstOrCreate(['slug' => 'performance'], ['name' => 'Performance']),
        ];

        // 4. Series
        $series1 = Series::firstOrCreate(
            ['slug' => 'modern-laravel-mastery'],
            [
                'name' => 'Modern Laravel Mastery',
                'description' => 'A comprehensive 4-part deep dive into modern Laravel architectures, Eloquent performance, and production workflows.',
                'sort_order' => 1,
                'is_active' => true,
            ]
        );

        $series2 = Series::firstOrCreate(
            ['slug' => 'filament-admin-deep-dive'],
            [
                'name' => 'Filament Admin Deep Dive',
                'description' => 'Building enterprise-grade administrative panels and full content management systems with Filament 3.',
                'sort_order' => 2,
                'is_active' => true,
            ]
        );

        $series3 = Series::firstOrCreate(
            ['slug' => 'high-performance-databases'],
            [
                'name' => 'High-Performance PostgreSQL & Redis',
                'description' => 'Maximizing database throughput, indexing strategies, and caching layers in modern applications.',
                'sort_order' => 3,
                'is_active' => true,
            ]
        );

        // 5. Posts Data
        $postsData = [
            // Series 1, Part 1 (Hero Lead Story)
            [
                'slug' => 'building-resilient-content-architectures-with-laravel-and-filament',
                'user_id' => $admin->id,
                'series_id' => $series1->id,
                'series_order' => 1,
                'title' => 'Building Resilient Content Architectures with Laravel and Filament',
                'excerpt' => 'How we crafted a modern, high-performance publishing platform leveraging Laravel 13, Filament 3, and Livewire server-driven components.',
                'content' => '<p>Modern content management demands far more than simple database storage. It requires decoupled publishing workflows, modular layout builders, instant full-text discoverability, and rapid editorial feedback loops.</p>
<h2>Architectural Foundation</h2>
<p>When engineering high-throughput content platforms, the separation of administrative ingestion from public delivery is paramount. By decoupling the administrative interface using Filament from the public-facing Livewire frontend, we achieve maximum operational resilience.</p>
<blockquote>A truly resilient architecture ensures that editorial workflows and heavy indexing operations never compromise the sub-50ms latency of public reader requests.</blockquote>
<h3>Key Components of the Pipeline</h3>
<p>The core pipeline consists of three distinct layers:</p>
<ul>
<li><strong>Domain Data Models:</strong> Strongly typed Eloquent entities backed by PostgreSQL <code>jsonb</code> documents for schema flexibility.</li>
<li><strong>Administrative Command Center:</strong> Resource-driven Filament panels with automated audit trails and real-time validation.</li>
<li><strong>Reactive Presentation Layer:</strong> Livewire components delivering client-side instant responsiveness without the overhead of heavy SPA bundles.</li>
</ul>',
                'content_blocks' => [
                    [
                        'type' => 'callout',
                        'data' => [
                            'type' => 'info',
                            'title' => 'Architectural Insight',
                            'content' => 'Server-driven UI frameworks allow complete encapsulation of business rules on the backend, reducing client bundle size by over 70% compared to traditional client SPAs.',
                        ],
                    ],
                    [
                        'type' => 'code',
                        'data' => [
                            'language' => 'php',
                            'filename' => 'app/Services/PublishingEngine.php',
                            'code' => "namespace App\Services;

use App\Models\Post;
use App\Enums\PostStatus;
use Illuminate\Support\Facades\DB;

class PublishingEngine
{
    public function publish(Post \$post): bool
    {
        return DB::transaction(function () use (\$post) {
            \$post->update([
                'status' => PostStatus::Published,
                'published_at' => now(),
            ]);

            event(new PostPublishedEvent(\$post));

            return true;
        });
    }
}",
                        ],
                    ],
                    [
                        'type' => 'key_takeaways',
                        'data' => [
                            'title' => 'Key Architectural Pillars',
                            'items' => [
                                ['point' => 'Zero-downtime schema evolution via non-blocking migrations'],
                                ['point' => 'Threaded comment moderation with automated spam heuristics'],
                                ['point' => 'Atomic cache invalidation targeting tags and categories'],
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
                'view_count' => 14200,
                'category' => 'architecture',
                'tags' => ['laravel', 'filament', 'php', 'architecture'],
            ],

            // Series 1, Part 2
            [
                'slug' => 'advanced-eloquent-indexing-jsonb-and-high-throughput-queries',
                'user_id' => $editor->id,
                'series_id' => $series1->id,
                'series_order' => 2,
                'title' => 'Advanced Eloquent Indexing, JSONB, and High-Throughput Queries',
                'excerpt' => 'Unlock database performance secrets using PostgreSQL GIN indexes, expression indexes, and eager loading strategies in Laravel Eloquent.',
                'content' => '<p>As application traffic scales, database query latency frequently becomes the primary bottleneck. Eloquent makes writing queries effortless, but understanding how PostgreSQL executes them is essential for high-throughput systems.</p>
<h2>Mastering JSONB Columns</h2>
<p>PostgreSQL offers two JSON data types: <code>json</code> and <code>jsonb</code>. In production Laravel applications, always prefer <code>jsonb</code> for its indexed binary representation and equality comparisons.</p>
<pre><code class="language-sql">CREATE INDEX idx_posts_content_blocks_gin ON posts USING GIN (content_blocks);</code></pre>
<p>With GIN indexing enabled, queries searching for specific block types inside structured content execute in under 2 milliseconds across millions of records.</p>',
                'content_blocks' => [
                    [
                        'type' => 'callout',
                        'data' => [
                            'type' => 'success',
                            'title' => 'Performance Tip',
                            'content' => 'Always verify execution plans using EXPLAIN ANALYZE before deploying complex join queries to production.',
                        ],
                    ],
                ],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => true,
                'featured_order' => 2,
                'sort_order' => 2,
                'published_at' => now()->subDays(5),
                'view_count' => 8420,
                'category' => 'performance',
                'tags' => ['postgresql', 'laravel', 'performance', 'php'],
            ],

            // Series 1, Part 3
            [
                'slug' => 'mastering-asynchronous-queues-webhooks-and-event-pipelines',
                'user_id' => $admin->id,
                'series_id' => $series1->id,
                'series_order' => 3,
                'title' => 'Mastering Asynchronous Queues, Webhooks, and Event Pipelines',
                'excerpt' => 'Decouple long-running tasks, dispatch secure webhook payloads with HMAC signatures, and handle retries gracefully with Redis queues.',
                'content' => '<p>Real-time notifications, webhook syndication, and search indexing should never occur synchronously during the HTTP request cycle. Offloading work to Redis queues guarantees user requests return in milliseconds.</p>
<h2>Idempotency & HMAC Verification</h2>
<p>Outbound webhooks must guarantee both integrity and authenticity. Generating a SHA256 signature using a shared secret allows consumers to verify payload authenticity before processing.</p>',
                'content_blocks' => [
                    [
                        'type' => 'code',
                        'data' => [
                            'language' => 'php',
                            'filename' => 'app/Jobs/DispatchWebhookJob.php',
                            'code' => "\$signature = hash_hmac('sha256', json_encode(\$payload), \$secret);
Http::withHeaders([
    'X-Signature-SHA256' => \$signature,
    'User-Agent' => 'Laravel-Webhook-Engine/1.0',
])->post(\$webhookUrl, \$payload);",
                        ],
                    ],
                ],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => false,
                'featured_order' => null,
                'sort_order' => 3,
                'published_at' => now()->subDays(8),
                'view_count' => 5910,
                'category' => 'architecture',
                'tags' => ['redis', 'laravel', 'api'],
            ],

            // Series 1, Part 4
            [
                'slug' => 'production-hardening-security-policies-and-observability',
                'user_id' => $contributor->id,
                'series_id' => $series1->id,
                'series_order' => 4,
                'title' => 'Production Hardening: Security Policies, Auditing, and Observability',
                'excerpt' => 'Implement defence-in-depth security with Spatie Activitylog, strict authorization policies, and rate-limited endpoints.',
                'content' => '<p>A robust production application requires comprehensive visibility into every administrative action. By combining Spatie Activitylog with fine-grained Filament permission policies, teams can maintain strict audit readiness.</p>
<h2>Audit Trail Best Practices</h2>
<p>Every administrative update should automatically capture the causer ID, IP address, and an exact diff of modified attributes without exposing sensitive credentials.</p>',
                'content_blocks' => [
                    [
                        'type' => 'key_takeaways',
                        'data' => [
                            'title' => 'Production Checklist',
                            'items' => [
                                ['point' => 'Configure strict Content-Security-Policy headers in middleware'],
                                ['point' => 'Store sanitized IP addresses and user agents for audit logs'],
                                ['point' => 'Enforce granular role-based policies for all CRUD mutations'],
                            ],
                        ],
                    ],
                ],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => false,
                'featured_order' => null,
                'sort_order' => 4,
                'published_at' => now()->subDays(12),
                'view_count' => 4210,
                'category' => 'security',
                'tags' => ['security', 'laravel', 'testing'],
            ],

            // Series 2, Part 1
            [
                'slug' => 'mastering-filament-form-builders-and-custom-fields',
                'user_id' => $author->id,
                'series_id' => $series2->id,
                'series_order' => 1,
                'title' => 'Mastering Filament Form Builders and Custom Fields',
                'excerpt' => 'Unlocking the power of flexible content blocks, JSON persistence, and dynamic repeater schemas in Filament 3.',
                'content' => '<p>Filament form builders provide unprecedented control over rich editorial workflows without sacrificing code maintainability. With structured schemas, developers define interfaces entirely in expressive PHP.</p>
<h2>Building Reusable Schema Components</h2>
<p>Rather than duplicating form configurations across multiple resources, Filament allows packaging complex form fields into reusable components.</p>',
                'content_blocks' => [
                    [
                        'type' => 'callout',
                        'data' => [
                            'type' => 'info',
                            'title' => 'Livewire Hydration Tip',
                            'content' => 'Always debounce search inputs and use eager relationship constraints inside Filament table query builders.',
                        ],
                    ],
                ],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => true,
                'featured_order' => 3,
                'sort_order' => 5,
                'published_at' => now()->subDays(3),
                'view_count' => 9870,
                'category' => 'tutorials',
                'tags' => ['filament', 'php', 'laravel'],
            ],

            // Series 2, Part 2
            [
                'slug' => 'extending-filament-tables-with-advanced-filters-and-actions',
                'user_id' => $author->id,
                'series_id' => $series2->id,
                'series_order' => 2,
                'title' => 'Extending Filament Tables with Advanced Filters and Batch Actions',
                'excerpt' => 'Implement multi-column filtering, date range pickers, inline badges, and custom modal bulk actions for rapid editorial management.',
                'content' => '<p>Filament tables are the workhorse of any content platform. Learn how to configure custom query scopes, action modals, and bulk approval queues with zero boilerplate code.</p>
<h2>Custom Action Modals</h2>
<p>Actions encapsulate both the interactive UI trigger and the underlying business logic, enabling complex workflows like batch approvals with single-click simplicity.</p>',
                'content_blocks' => [],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => false,
                'featured_order' => null,
                'sort_order' => 6,
                'published_at' => now()->subDays(6),
                'view_count' => 6120,
                'category' => 'tutorials',
                'tags' => ['filament', 'laravel'],
            ],

            // Series 2, Part 3
            [
                'slug' => 'custom-filament-dashboards-widgets-and-chart-analytics',
                'user_id' => $author->id,
                'series_id' => $series2->id,
                'series_order' => 3,
                'title' => 'Custom Filament Dashboards, Widgets, and Chart Analytics',
                'excerpt' => 'Build high-impact visual dashboards displaying real-time readership metrics, comment moderation throughput, and search trends.',
                'content' => '<p>A modern admin dashboard should tell a story at first glance. By combining Filament Chart Widgets with database aggregation queries, editors gain actionable insights into platform growth.</p>',
                'content_blocks' => [],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => false,
                'featured_order' => null,
                'sort_order' => 7,
                'published_at' => now()->subDays(9),
                'view_count' => 5340,
                'category' => 'tutorials',
                'tags' => ['filament', 'php', 'performance'],
            ],

            // Series 3, Part 1
            [
                'slug' => 'harnessing-postgresql-jsonb-and-gin-indexes-in-laravel',
                'user_id' => $editor->id,
                'series_id' => $series3->id,
                'series_order' => 1,
                'title' => 'Harnessing PostgreSQL JSONB and GIN Indexes in Laravel',
                'excerpt' => 'How to achieve schema flexibility without sacrificing query performance by pairing Laravel Eloquent with PostgreSQL JSONB types.',
                'content' => '<p>Relational databases and unstructured document storage have converged in PostgreSQL. By leveraging JSONB columns alongside proper Generalized Inverted Indexes (GIN), Laravel applications can store complex document shapes with sub-millisecond query performance.</p>
<h2>Why Standard JSON Falls Short</h2>
<p>PostgreSQL text-based JSON requires full re-parsing on every read and lacks indexing operators. JSONB, in contrast, stores decomposed binary data with direct index support.</p>',
                'content_blocks' => [
                    [
                        'type' => 'callout',
                        'data' => [
                            'type' => 'warning',
                            'title' => 'Migration Warning',
                            'content' => 'Always specify column conversions carefully when migrating existing tables to jsonb in PostgreSQL.',
                        ],
                    ],
                ],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => true,
                'featured_order' => 4,
                'sort_order' => 8,
                'published_at' => now()->subDays(4),
                'view_count' => 11200,
                'category' => 'performance',
                'tags' => ['postgresql', 'laravel', 'performance'],
            ],

            // Series 3, Part 2
            [
                'slug' => 'optimizing-full-text-search-and-trigram-similarity',
                'user_id' => $editor->id,
                'series_id' => $series3->id,
                'series_order' => 2,
                'title' => 'Optimizing Full-Text Search and Trigram Similarity in Laravel',
                'excerpt' => 'Replace external search clusters for moderate workloads using PostgreSQL pg_trgm extension and tsvector document matching.',
                'content' => '<p>Before introducing external search infrastructure like Elasticsearch or Meilisearch, evaluate whether PostgreSQL built-in full-text search meets your needs. With trigram indexing, typos and partial matches execute blazingly fast.</p>',
                'content_blocks' => [],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => false,
                'featured_order' => null,
                'sort_order' => 9,
                'published_at' => now()->subDays(7),
                'view_count' => 7890,
                'category' => 'performance',
                'tags' => ['postgresql', 'performance', 'api'],
            ],

            // Series 3, Part 3
            [
                'slug' => 'zero-downtime-schema-migrations-in-high-volume-apps',
                'user_id' => $editor->id,
                'series_id' => $series3->id,
                'series_order' => 3,
                'title' => 'Zero-Downtime Schema Migrations in High-Volume Applications',
                'excerpt' => 'Techniques for adding columns, dropping tables, and rebuilding indexes on active production databases without locking tables.',
                'content' => '<p>Executing an <code>ALTER TABLE</code> command on a table with millions of rows can lock writes and cause catastrophic cascading timeouts. Learn how to perform expand-and-contract migrations safely.</p>',
                'content_blocks' => [],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => false,
                'featured_order' => null,
                'sort_order' => 10,
                'published_at' => now()->subDays(11),
                'view_count' => 6400,
                'category' => 'performance',
                'tags' => ['postgresql', 'devops', 'architecture'],
            ],

            // Standalone Post 1 (Frontend & Livewire)
            [
                'slug' => 'architecting-reactive-uis-with-livewire-3-and-alpine',
                'user_id' => $author->id,
                'series_id' => null,
                'series_order' => null,
                'title' => 'Architecting Reactive UIs with Livewire 3 and Alpine.js',
                'excerpt' => 'Combine Livewire 3 lazy loading, URL query string binding, and Alpine client-side micro-interactions for an extraordinary reader experience.',
                'content' => '<p>Modern web readers expect instant feedback, debounced searches, and seamless transitions without full page reloads. Livewire 3 bridge server state and DOM updates with remarkable elegance.</p>
<h2>Client-Side Enhancements with Alpine.js</h2>
<p>For operations that do not require server state—such as image lightboxes, reading progress bars, and Table of Contents active-section tracking—Alpine.js handles DOM updates client-side with zero network latency.</p>',
                'content_blocks' => [
                    [
                        'type' => 'callout',
                        'data' => [
                            'type' => 'info',
                            'title' => 'UX Design Tip',
                            'content' => 'Always provide skeleton placeholder loaders during lazy component hydration to eliminate layout shifts (CLS).',
                        ],
                    ],
                ],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => false,
                'featured_order' => null,
                'sort_order' => 11,
                'published_at' => now()->subDays(1),
                'view_count' => 12500,
                'category' => 'frontend',
                'tags' => ['livewire', 'alpine', 'tailwind'],
            ],

            // Standalone Post 2 (DevOps)
            [
                'slug' => 'dockerizing-modern-laravel-for-production-with-sail',
                'user_id' => $admin->id,
                'series_id' => null,
                'series_order' => null,
                'title' => 'Dockerizing Modern Laravel for Development and Production',
                'excerpt' => 'Best practices for multi-stage Docker builds, Alpine Linux images, and consistent local environments with Laravel Sail.',
                'content' => '<p>Inconsistent developer environments waste countless engineering hours. By establishing a unified Docker architecture, developers and CI/CD pipelines run on identical runtime dependencies.</p>',
                'content_blocks' => [],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => false,
                'featured_order' => null,
                'sort_order' => 12,
                'published_at' => now()->subDays(10),
                'view_count' => 8930,
                'category' => 'devops',
                'tags' => ['docker', 'laravel', 'devops'],
            ],

            // Standalone Post 3 (PHP 8.5)
            [
                'slug' => 'deep-dive-into-php-85-performance-and-modern-syntax',
                'user_id' => $admin->id,
                'series_id' => null,
                'series_order' => null,
                'title' => 'Deep Dive into PHP 8.5: Performance Innovations and Modern Syntax',
                'excerpt' => 'Exploring the newest features in PHP 8.5: JIT compiler enhancements, pipe operators, pattern matching, and memory profile gains.',
                'content' => '<p>PHP continues its relentless pace of innovation. In this article, we analyze the performance characteristics of PHP 8.5 running real-world Laravel workloads, observing over 15% reduction in request latency.</p>',
                'content_blocks' => [],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => false,
                'featured_order' => null,
                'sort_order' => 13,
                'published_at' => now()->subDays(14),
                'view_count' => 10400,
                'category' => 'architecture',
                'tags' => ['php', 'performance', 'laravel'],
            ],

            // Standalone Post 4 (Security)
            [
                'slug' => 'securing-laravel-apis-with-rate-limiting-and-signed-routes',
                'user_id' => $contributor->id,
                'series_id' => null,
                'series_order' => null,
                'title' => 'Securing Laravel APIs with Rate Limiting and Signed Routes',
                'excerpt' => 'Protect your application against automated abuse, scraping, and brute force attacks using Redis-backed rate limiters and temporary signed URLs.',
                'content' => '<p>Public APIs and RSS syndication endpoints require proportional protection against automated scrapers. Learn how to configure dynamic rate limiting based on authenticated tier and IP reputation.</p>',
                'content_blocks' => [],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => false,
                'featured_order' => null,
                'sort_order' => 14,
                'published_at' => now()->subDays(16),
                'view_count' => 4820,
                'category' => 'security',
                'tags' => ['security', 'api', 'redis'],
            ],

            // Standalone Post 5 (Frontend)
            [
                'slug' => 'crafting-accessible-dark-mode-first-ui-with-tailwind-css',
                'user_id' => $author->id,
                'series_id' => null,
                'series_order' => null,
                'title' => 'Crafting Accessible, Dark-Mode First UI with Tailwind CSS v4',
                'excerpt' => 'Implementing WCAG AAA color contrast, keyboard focus rings, and seamless flash-free dark mode toggles with Tailwind CSS v4.',
                'content' => '<p>Accessibility is not a feature you bolt on after launch; it is the foundation of professional web craftsmanship. Discover how to leverage semantic HTML elements, high-contrast HSL color tokens, and aria attributes.</p>',
                'content_blocks' => [],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => false,
                'featured_order' => null,
                'sort_order' => 15,
                'published_at' => now()->subDays(18),
                'view_count' => 7310,
                'category' => 'frontend',
                'tags' => ['tailwind', 'alpine', 'frontend'],
            ],

            // Standalone Post 6 (Tutorials)
            [
                'slug' => 'practical-guide-to-structuring-seo-metadata-and-json-ld',
                'user_id' => $admin->id,
                'series_id' => null,
                'series_order' => null,
                'title' => 'A Practical Guide to Structuring SEO Metadata and JSON-LD',
                'excerpt' => 'Boost discoverability with Schema.org Article, BreadcrumbList, and Person schemas, Open Graph tags, and dynamic XML sitemaps.',
                'content' => '<p>Modern search engines favor structured data that provides contextual meaning beyond raw HTML text. In this guide, we break down how to dynamically generate comprehensive JSON-LD schemas from Eloquent models.</p>',
                'content_blocks' => [],
                'status' => PostStatus::Published,
                'is_hero' => false,
                'is_featured' => false,
                'featured_order' => null,
                'sort_order' => 16,
                'published_at' => now()->subDays(20),
                'view_count' => 6950,
                'category' => 'tutorials',
                'tags' => ['laravel', 'api', 'tutorials'],
            ],
        ];

        $createdPosts = [];

        foreach ($postsData as $pData) {
            $catKey = $pData['category'];
            $tagKeys = $pData['tags'];

            unset($pData['category'], $pData['tags']);

            $post = Post::firstOrCreate(
                ['slug' => $pData['slug']],
                $pData
            );

            // Sync category
            if (isset($categories[$catKey])) {
                $post->categories()->sync([$categories[$catKey]->id]);
            }

            // Sync tags
            $tagIds = [];
            foreach ($tagKeys as $tKey) {
                if (isset($tags[$tKey])) {
                    $tagIds[] = $tags[$tKey]->id;
                }
            }
            if (! empty($tagIds)) {
                $post->tags()->sync($tagIds);
            }

            $createdPosts[$pData['slug']] = $post;
        }

        // 6. Cross-link Related Posts
        if (isset($createdPosts['building-resilient-content-architectures-with-laravel-and-filament'], $createdPosts['advanced-eloquent-indexing-jsonb-and-high-throughput-queries'])) {
            $createdPosts['building-resilient-content-architectures-with-laravel-and-filament']->relatedPosts()->syncWithoutDetaching([
                $createdPosts['advanced-eloquent-indexing-jsonb-and-high-throughput-queries']->id => ['sort_order' => 1],
            ]);
            $createdPosts['advanced-eloquent-indexing-jsonb-and-high-throughput-queries']->relatedPosts()->syncWithoutDetaching([
                $createdPosts['building-resilient-content-architectures-with-laravel-and-filament']->id => ['sort_order' => 1],
            ]);
        }

        if (isset($createdPosts['harnessing-postgresql-jsonb-and-gin-indexes-in-laravel'], $createdPosts['optimizing-full-text-search-and-trigram-similarity'])) {
            $createdPosts['harnessing-postgresql-jsonb-and-gin-indexes-in-laravel']->relatedPosts()->syncWithoutDetaching([
                $createdPosts['optimizing-full-text-search-and-trigram-similarity']->id => ['sort_order' => 1],
            ]);
        }

        if (isset($createdPosts['architecting-reactive-uis-with-livewire-3-and-alpine'], $createdPosts['crafting-accessible-dark-mode-first-ui-with-tailwind-css'])) {
            $createdPosts['architecting-reactive-uis-with-livewire-3-and-alpine']->relatedPosts()->syncWithoutDetaching([
                $createdPosts['crafting-accessible-dark-mode-first-ui-with-tailwind-css']->id => ['sort_order' => 1],
            ]);
        }

        // 7. Seed Realistic Threaded Comments
        $leadPost = $createdPosts['building-resilient-content-architectures-with-laravel-and-filament'] ?? null;
        if ($leadPost) {
            $c1 = Comment::firstOrCreate(
                ['content' => 'Outstanding architectural overview! The content blocks pattern makes editorial customization remarkably versatile.'],
                [
                    'post_id' => $leadPost->id,
                    'user_id' => $author->id,
                    'status' => CommentStatus::Approved,
                    'moderated_by' => $admin->id,
                    'moderated_at' => now()->subDay(),
                    'created_at' => now()->subDays(2),
                ]
            );

            Comment::firstOrCreate(
                ['content' => 'Thank you Marcus! Decoupling the block schemas from database tables was the biggest breakthrough.'],
                [
                    'post_id' => $leadPost->id,
                    'user_id' => $admin->id,
                    'parent_id' => $c1->id,
                    'status' => CommentStatus::Approved,
                    'moderated_by' => $admin->id,
                    'moderated_at' => now()->subHours(18),
                    'created_at' => now()->subHours(18),
                ]
            );

            Comment::firstOrCreate(
                ['content' => 'How do you handle Redis cache invalidation when a nested block is updated?'],
                [
                    'post_id' => $leadPost->id,
                    'guest_name' => 'David Miller',
                    'guest_email' => 'david@techscale.io',
                    'guest_website' => 'https://techscale.io',
                    'status' => CommentStatus::Approved,
                    'moderated_by' => $admin->id,
                    'moderated_at' => now()->subHours(10),
                    'created_at' => now()->subHours(12),
                ]
            );

            Comment::firstOrCreate(
                ['content' => 'We use model observers on the Post entity that fire cache tag flushes targeting both the specific slug and category listings.'],
                [
                    'post_id' => $leadPost->id,
                    'user_id' => $editor->id,
                    'parent_id' => null,
                    'status' => CommentStatus::Approved,
                    'moderated_by' => $admin->id,
                    'moderated_at' => now()->subHours(8),
                    'created_at' => now()->subHours(9),
                ]
            );

            // Moderation queue comments
            Comment::firstOrCreate(
                ['content' => 'Does this architecture support Octane and RoadRunner out of the box?'],
                [
                    'post_id' => $leadPost->id,
                    'guest_name' => 'Samantha Ray',
                    'guest_email' => 'samantha@devops.co',
                    'status' => CommentStatus::Pending,
                    'ip_address' => '192.168.1.120',
                    'created_at' => now()->subHours(2),
                ]
            );
        }

        $frontendPost = $createdPosts['architecting-reactive-uis-with-livewire-3-and-alpine'] ?? null;
        if ($frontendPost) {
            Comment::firstOrCreate(
                ['content' => 'The Table of Contents scrollspy with Alpine.js is smooth! Loved the zero-dependency approach.'],
                [
                    'post_id' => $frontendPost->id,
                    'guest_name' => 'Chris Vance',
                    'guest_email' => 'chris@frontend.dev',
                    'status' => CommentStatus::Approved,
                    'moderated_by' => $admin->id,
                    'moderated_at' => now()->subHours(14),
                    'created_at' => now()->subHours(15),
                ]
            );
        }

        // 8. Subscribers
        $subscribers = [
            ['email' => 'sarah.connor@cyber.io', 'name' => 'Sarah Connor', 'status' => SubscriberStatus::Subscribed, 'source' => 'blog_footer', 'days' => 20],
            ['email' => 'alex.ross@cloudscale.net', 'name' => 'Alex Ross', 'status' => SubscriberStatus::Subscribed, 'source' => 'article_cta', 'days' => 14],
            ['email' => 'dev.reader@company.com', 'name' => 'Dave Miller', 'status' => SubscriberStatus::Subscribed, 'source' => 'sidebar', 'days' => 7],
            ['email' => 'elena.tech@systems.org', 'name' => 'Elena Tech', 'status' => SubscriberStatus::Subscribed, 'source' => 'homepage_lead', 'days' => 3],
            ['email' => 'pending.user@test.org', 'name' => 'Pending Confirm', 'status' => SubscriberStatus::Pending, 'source' => 'article_cta', 'days' => 1],
        ];

        foreach ($subscribers as $s) {
            Subscriber::firstOrCreate(
                ['email' => $s['email']],
                [
                    'name' => $s['name'],
                    'status' => $s['status'],
                    'source' => $s['source'],
                    'subscribed_at' => now()->subDays($s['days']),
                ]
            );
        }

        // 9. Contact Messages
        ContactMessage::firstOrCreate(
            ['email' => 'clara@partner.com', 'subject' => 'Guest posting and editorial collaboration inquiry'],
            [
                'name' => 'Clara Oswald',
                'message' => 'Hello editorial team! We have drafted an in-depth technical analysis on asynchronous queue performance with Laravel Octane and would love to contribute.',
                'status' => MessageStatus::Unread,
                'created_at' => now()->subDays(1),
            ]
        );

        ContactMessage::firstOrCreate(
            ['email' => 'marcus@cloud.io', 'subject' => 'Question regarding PostgreSQL JSONB indexing benchmarks'],
            [
                'name' => 'Marcus Aurelius',
                'message' => 'Loved your latest post on PostgreSQL GIN indexing. How often do you suggest running REINDEX on high-write tables?',
                'status' => MessageStatus::Replied,
                'read_at' => now()->subDays(3),
                'replied_at' => now()->subDays(2),
                'reply_notes' => 'Recommended pg_repack for zero-lock online index rebuilds.',
                'created_at' => now()->subDays(4),
            ]
        );

        // 10. Static Pages
        Page::firstOrCreate(
            ['slug' => 'about'],
            [
                'title' => 'About Our Publication',
                'content' => '<p>We are a dedicated collective of software engineers, architects, and technical writers documenting modern web craftsmanship, Laravel ecosystem innovations, and distributed computing.</p>
<h2>Our Editorial Mission</h2>
<p>Our goal is to produce high-signal, production-tested guides and architectural case studies that empower developers to build scalable, resilient, and elegant applications.</p>
<h2>Authors & Contributors</h2>
<p>Every article published on this platform is written and reviewed by experienced engineering practitioners with direct experience scaling high-load production environments.</p>',
                'template' => PageTemplate::About,
                'status' => PageStatus::Published,
                'published_at' => now()->subMonths(2),
                'seo_title' => 'About Our Publication - Laravel Modern Blog',
                'seo_description' => 'Learn about our engineering philosophy, mission, and technical contributors.',
                'sort_order' => 1,
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'contact'],
            [
                'title' => 'Contact Editorial Team',
                'content' => '<p>Have a question, feedback on an article, or a story proposal? Reach out to our team using the form below or email us directly at <strong>editor@blog.test</strong>.</p>',
                'template' => PageTemplate::Contact,
                'status' => PageStatus::Published,
                'published_at' => now()->subMonths(2),
                'seo_title' => 'Contact Us - Laravel Modern Blog',
                'seo_description' => 'Get in touch with our editorial and development team.',
                'sort_order' => 2,
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'privacy'],
            [
                'title' => 'Privacy Policy',
                'content' => '<p>Your privacy is important to us. This publication does not track personally identifiable information across third-party networks. Any email addresses provided for newsletter subscriptions are stored securely and never sold or shared.</p>',
                'template' => PageTemplate::Default,
                'status' => PageStatus::Published,
                'published_at' => now()->subMonths(2),
                'seo_title' => 'Privacy Policy - Laravel Modern Blog',
                'seo_description' => 'Our commitment to data privacy and reader security.',
                'sort_order' => 3,
            ]
        );

        Page::firstOrCreate(
            ['slug' => 'terms'],
            [
                'title' => 'Terms of Service',
                'content' => '<p>All technical articles, code snippets, and architecture diagrams published on this site are open for educational use under standard open-source attribution guidelines.</p>',
                'template' => PageTemplate::Default,
                'status' => PageStatus::Published,
                'published_at' => now()->subMonths(2),
                'seo_title' => 'Terms of Service - Laravel Modern Blog',
                'seo_description' => 'Terms of service and content usage guidelines.',
                'sort_order' => 4,
            ]
        );

        // 11. Navigation Menus
        $headerMenu = NavigationMenu::firstOrCreate(
            ['location' => 'header'],
            ['name' => 'Main Header Navigation', 'is_active' => true]
        );

        $headerMenu->items()->delete();
        $headerMenu->items()->create(['label' => 'Home', 'url' => '/', 'sort_order' => 1, 'type' => 'url']);
        $headerMenu->items()->create(['label' => 'Articles', 'url' => '/posts', 'sort_order' => 2, 'type' => 'url']);
        $headerMenu->items()->create(['label' => 'Series', 'url' => '/series', 'sort_order' => 3, 'type' => 'url']);
        $headerMenu->items()->create(['label' => 'Architecture', 'url' => '/categories/architecture', 'sort_order' => 4, 'type' => 'category']);
        $headerMenu->items()->create(['label' => 'Tutorials', 'url' => '/categories/tutorials', 'sort_order' => 5, 'type' => 'category']);
        $headerMenu->items()->create(['label' => 'About', 'url' => '/about', 'sort_order' => 6, 'type' => 'page']);

        $footerMenu = NavigationMenu::firstOrCreate(
            ['location' => 'footer'],
            ['name' => 'Footer Links', 'is_active' => true]
        );
        $footerMenu->items()->delete();
        $footerMenu->items()->create(['label' => 'Articles Archive', 'url' => '/posts', 'sort_order' => 1, 'type' => 'url']);
        $footerMenu->items()->create(['label' => 'Learning Paths', 'url' => '/series', 'sort_order' => 2, 'type' => 'url']);
        $footerMenu->items()->create(['label' => 'About Us', 'url' => '/about', 'sort_order' => 3, 'type' => 'page']);
        $footerMenu->items()->create(['label' => 'Contact', 'url' => '/contact', 'sort_order' => 4, 'type' => 'page']);
        $footerMenu->items()->create(['label' => 'Privacy Policy', 'url' => '/privacy', 'sort_order' => 5, 'type' => 'page']);
        $footerMenu->items()->create(['label' => 'Terms of Service', 'url' => '/terms', 'sort_order' => 6, 'type' => 'page']);

        // 12. Redirect Rules
        Redirect::firstOrCreate(
            ['source_path' => 'old-laravel-guide'],
            [
                'target_path' => '/posts/building-resilient-content-architectures-with-laravel-and-filament',
                'status_code' => RedirectType::Permanent301,
                'is_active' => true,
                'hit_count' => 84,
            ]
        );

        // 13. System Settings
        Setting::set('site_name', 'Laravel Modern Blog & Engineering Dispatch', 'general');
        Setting::set('site_tagline', 'Deep-dive architectural patterns, performance guides, and modern web craftsmanship.', 'general');
        Setting::set('contact_email', 'editor@blog.test', 'general');
        Setting::set('default_seo_title', 'Laravel Modern Blog - Engineering Architecture & Tutorials', 'seo');
        Setting::set('default_seo_desc', 'In-depth engineering articles on Laravel 13, Filament 3, Livewire, PostgreSQL, and modern cloud architectures.', 'seo');
        Setting::set('twitter_handle', '@laravelphp', 'seo');
        Setting::set('enable_comments', true, 'comments', 'boolean');
        Setting::set('auto_approve_comments', false, 'comments', 'boolean');
        Setting::set('search_driver', 'database', 'search');
    }
}
