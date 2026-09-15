<?php

namespace Database\Factories;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Comment>
 */
class CommentFactory extends Factory
{
    protected $model = Comment::class;

    public function definition(): array
    {
        return [
            'post_id' => Post::factory(),
            'user_id' => null,
            'guest_name' => fake()->name(),
            'guest_email' => fake()->safeEmail(),
            'content' => fake()->paragraph(),
            'status' => CommentStatus::Approved,
            'ip_address' => fake()->ipv4(),
            'user_agent' => fake()->userAgent(),
        ];
    }

    public function pending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CommentStatus::Pending,
        ]);
    }

    public function spam(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => CommentStatus::Spam,
        ]);
    }
}
