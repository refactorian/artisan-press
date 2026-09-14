<?php

namespace Database\Seeders;

use App\Enums\PageStatus;
use App\Enums\PageTemplate;
use App\Enums\RedirectType;
use App\Models\NavigationMenu;
use App\Models\Page;
use App\Models\Redirect;
use Illuminate\Database\Seeder;

class PageAndNavigationSeeder extends Seeder
{
    /**
     * Seed static editorial pages, navigation menus, and redirect rules.
     */
    public function run(): void
    {
        // 1. Static Pages
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
                'seo_title' => 'About Our Publication',
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
                'seo_title' => 'Contact Us',
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
                'seo_title' => 'Privacy Policy',
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
                'seo_title' => 'Terms of Service',
                'seo_description' => 'Terms of service and content usage guidelines.',
                'sort_order' => 4,
            ]
        );

        // 2. Navigation Menus
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

        // 3. Redirect Rules
        Redirect::firstOrCreate(
            ['source_path' => 'old-laravel-guide'],
            [
                'target_path' => '/posts/building-resilient-content-architectures-with-laravel-and-filament',
                'status_code' => RedirectType::Permanent301,
                'is_active' => true,
                'hit_count' => 84,
            ]
        );
    }
}
