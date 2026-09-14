<?php

namespace Database\Seeders;

use App\Enums\CommentStatus;
use App\Enums\MessageStatus;
use App\Enums\SubscriberStatus;
use App\Models\Comment;
use App\Models\ContactMessage;
use App\Models\Post;
use App\Models\Subscriber;
use App\Models\User;
use Illuminate\Database\Seeder;

class CommentSeeder extends Seeder
{
    /**
     * Seed realistic comments, replies, newsletter subscribers, and messages.
     */
    public function run(): void
    {
        $admin = User::where('email', 'admin@blog.test')->first() ?? User::first();
        $editor = User::where('email', 'editor@blog.test')->first() ?? $admin;
        $author = User::where('email', 'author@blog.test')->first() ?? $admin;

        // 1. Threaded Comments on Lead Post
        $leadPost = Post::where('slug', 'building-resilient-content-architectures-with-laravel-and-filament')->first()
            ?? Post::first();

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

            // Moderation queue comment
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

        $frontendPost = Post::where('slug', 'architecting-reactive-uis-with-livewire-3-and-alpine')->first();
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

        // 2. Newsletter Subscribers
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

        // 3. Contact Inquiries
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
    }
}
