<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class AuthorSeeder extends Seeder
{
    /**
     * Seed realistic editorial authors and team members.
     */
    public function run(): void
    {
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
    }
}
