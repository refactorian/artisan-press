<?php

namespace Tests\Feature\Api\V1;

use App\Enums\SubscriberStatus;
use App\Models\Subscriber;
use App\Notifications\SubscriberWelcomeNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Tests\TestCase;

class NewsletterApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_subscribe_to_newsletter(): void
    {
        Notification::fake();

        $response = $this->postJson(route('api.v1.newsletter.subscribe'), [
            'email' => 'subscriber@example.com',
            'name' => 'Newsletter Subscriber',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('subscribers', [
            'email' => 'subscriber@example.com',
            'name' => 'Newsletter Subscriber',
            'status' => SubscriberStatus::Subscribed->value,
        ]);

        Notification::assertSentTo(
            Subscriber::where('email', 'subscriber@example.com')->first(),
            SubscriberWelcomeNotification::class
        );
    }

    public function test_subscribing_again_returns_already_subscribed_message(): void
    {
        Subscriber::factory()->create([
            'email' => 'existing@example.com',
            'status' => SubscriberStatus::Subscribed,
        ]);

        $response = $this->postJson(route('api.v1.newsletter.subscribe'), [
            'email' => 'existing@example.com',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'You are already subscribed to our newsletter.',
            ]);
    }

    public function test_unsubscribed_user_can_resubscribe(): void
    {
        $subscriber = Subscriber::factory()->create([
            'email' => 'resub@example.com',
            'status' => SubscriberStatus::Unsubscribed,
            'unsubscribed_at' => now()->subMonth(),
        ]);

        $response = $this->postJson(route('api.v1.newsletter.subscribe'), [
            'email' => 'resub@example.com',
        ]);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ]);

        $subscriber->refresh();
        $this->assertEquals(SubscriberStatus::Subscribed, $subscriber->status);
        $this->assertNull($subscriber->unsubscribed_at);
    }

    public function test_invalid_email_fails_validation(): void
    {
        $response = $this->postJson(route('api.v1.newsletter.subscribe'), [
            'email' => 'not-an-email',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['email']);
    }
}
