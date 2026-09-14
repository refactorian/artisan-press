<?php

namespace Database\Seeders;

use App\Models\Series;
use Illuminate\Database\Seeder;

class SeriesSeeder extends Seeder
{
    /**
     * Seed article series and learning paths.
     */
    public function run(): void
    {
        $series = [
            [
                'slug' => 'modern-laravel-mastery',
                'name' => 'Modern Laravel Mastery',
                'description' => 'A comprehensive 4-part deep dive into modern Laravel architectures, Eloquent performance, and production workflows.',
                'sort_order' => 1,
                'is_active' => true,
            ],
            [
                'slug' => 'filament-admin-deep-dive',
                'name' => 'Filament Admin Deep Dive',
                'description' => 'Building enterprise-grade administrative panels and full content management systems with Filament 3.',
                'sort_order' => 2,
                'is_active' => true,
            ],
            [
                'slug' => 'high-performance-databases',
                'name' => 'High-Performance PostgreSQL & Redis',
                'description' => 'Maximizing database throughput, indexing strategies, and caching layers in modern applications.',
                'sort_order' => 3,
                'is_active' => true,
            ],
        ];

        foreach ($series as $data) {
            Series::firstOrCreate(['slug' => $data['slug']], $data);
        }
    }
}
