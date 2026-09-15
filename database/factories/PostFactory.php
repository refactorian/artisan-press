<?php

namespace Database\Factories;

use App\Enums\PostStatus;
use App\Models\Post;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Post>
 */
class PostFactory extends Factory
{
    protected $model = Post::class;

    public function definition(): array
    {
        $title = fake()->unique()->sentence(5);

        return [
            'user_id' => User::factory(),
            'title' => $title,
            'slug' => Str::slug($title),
            'excerpt' => fake()->paragraph(),
            'content' => fake()->paragraphs(3, true),
            'status' => PostStatus::Published,
            'published_at' => now()->subDays(rand(1, 30)),
            'is_featured' => false,
            'is_hero' => false,
            'view_count' => fake()->numberBetween(0, 100),
            'reading_time' => fake()->numberBetween(2, 8),
        ];
    }

    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Draft,
            'published_at' => null,
        ]);
    }

    public function scheduled(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => PostStatus::Scheduled,
            'published_at' => now()->addDays(3),
        ]);
    }

    public function featured(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_featured' => true,
        ]);
    }

    public function hero(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_hero' => true,
        ]);
    }
}
