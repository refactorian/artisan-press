<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Database\Seeder;

class TaxonomySeeder extends Seeder
{
    /**
     * Seed categories and tags for the blog.
     */
    public function run(): void
    {
        // 1. Categories
        $categories = [
            'architecture' => [
                'name' => 'Architecture',
                'description' => 'Scalable system design, distributed systems, clean code paradigms, and modular patterns.',
                'sort_order' => 1,
                'is_active' => true,
                'seo_title' => 'Software Architecture & System Design',
                'seo_description' => 'Deep technical insights into building resilient architectures, modular domain designs, and scalable systems.',
            ],
            'tutorials' => [
                'name' => 'Tutorials',
                'description' => 'Hands-on, step-by-step guides and practical implementation recipes.',
                'sort_order' => 2,
                'is_active' => true,
                'seo_title' => 'Developer Tutorials & Practical Guides',
                'seo_description' => 'Practical code walkthroughs covering Laravel, Livewire, Filament, and modern web tooling.',
            ],
            'performance' => [
                'name' => 'Performance',
                'description' => 'Database query tuning, Redis caching strategies, HTTP benchmarking, and sub-millisecond optimizations.',
                'sort_order' => 3,
                'is_active' => true,
                'seo_title' => 'High-Performance Web Applications & Database Tuning',
                'seo_description' => 'Benchmarks, query profiling, and optimization case studies for high-load systems.',
            ],
            'frontend' => [
                'name' => 'Frontend',
                'description' => 'Reactive UIs with Livewire 3, Alpine.js, Tailwind CSS v4, and modern accessibility.',
                'sort_order' => 4,
                'is_active' => true,
                'seo_title' => 'Modern Frontend Architecture',
                'seo_description' => 'Building sleek, accessible, server-driven reactive user interfaces.',
            ],
            'devops' => [
                'name' => 'DevOps & Cloud',
                'description' => 'Containerization, CI/CD pipelines, Docker, Kubernetes, and automated deployment pipelines.',
                'sort_order' => 5,
                'is_active' => true,
                'seo_title' => 'DevOps, Containers, and Cloud Deployment Strategies',
                'seo_description' => 'Best practices for Docker, Laravel Sail, automated testing, and scalable deployments.',
            ],
            'security' => [
                'name' => 'Security',
                'description' => 'Application hardening, RBAC permissions, encrypted payloads, and vulnerability prevention.',
                'sort_order' => 6,
                'is_active' => true,
                'seo_title' => 'Web Application Security & Defense in Depth',
                'seo_description' => 'Practical guides for securing APIs, protecting sensitive user data, and auditing codebases.',
            ],
        ];

        foreach ($categories as $slug => $data) {
            Category::firstOrCreate(['slug' => $slug], $data);
        }

        // 2. Tags
        $tags = [
            'laravel' => 'Laravel 13',
            'php' => 'PHP 8.5',
            'livewire' => 'Livewire 3',
            'filament' => 'Filament 3',
            'alpine' => 'Alpine.js',
            'tailwind' => 'Tailwind CSS',
            'postgresql' => 'PostgreSQL',
            'redis' => 'Redis',
            'docker' => 'Docker',
            'testing' => 'Testing',
            'api' => 'API Design',
            'performance' => 'Performance',
        ];

        foreach ($tags as $slug => $name) {
            Tag::firstOrCreate(['slug' => $slug], ['name' => $name]);
        }
    }
}
