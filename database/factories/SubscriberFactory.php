<?php

namespace Database\Factories;

use App\Enums\SubscriberStatus;
use App\Models\Subscriber;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Subscriber>
 */
class SubscriberFactory extends Factory
{
    protected $model = Subscriber::class;

    public function definition(): array
    {
        return [
            'email' => fake()->unique()->safeEmail(),
            'name' => fake()->name(),
            'status' => SubscriberStatus::Subscribed,
            'source' => 'api',
            'subscribed_at' => now(),
        ];
    }
}
