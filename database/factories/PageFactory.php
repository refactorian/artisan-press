<?php

namespace Database\Factories;

use App\Enums\PageStatus;
use App\Enums\PageTemplate;
use App\Models\Page;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Page>
 */
class PageFactory extends Factory
{
    protected $model = Page::class;

    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => ucfirst($title),
            'slug' => Str::slug($title),
            'content' => fake()->paragraphs(3, true),
            'template' => PageTemplate::Default,
            'status' => PageStatus::Published,
            'published_at' => now(),
            'sort_order' => fake()->numberBetween(0, 50),
        ];
    }
}
