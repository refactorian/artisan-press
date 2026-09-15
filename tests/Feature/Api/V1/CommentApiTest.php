<?php

namespace Tests\Feature\Api\V1;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Setting;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_approved_comments_for_post(): void
    {
        $post = Post::factory()->create();

        Comment::factory()->count(3)->create([
            'post_id' => $post->id,
            'status' => CommentStatus::Approved,
        ]);

        Comment::factory()->pending()->create([
            'post_id' => $post->id,
        ]);

        $response = $this->getJson(route('api.v1.comments.index', $post->slug));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'post_id', 'author_name', 'content', 'created_at'],
                ],
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_guest_can_post_comment_with_required_fields(): void
    {
        $post = Post::factory()->create();

        $payload = [
            'guest_name' => 'Alice Guest',
            'guest_email' => 'alice@example.com',
            'content' => 'This is a fantastic and insightful article!',
        ];

        $response = $this->postJson(route('api.v1.comments.store', $post->slug), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'guest_name' => 'Alice Guest',
            'guest_email' => 'alice@example.com',
        ]);
    }

    public function test_guest_cannot_post_comment_without_name_and_email(): void
    {
        $post = Post::factory()->create();

        $response = $this->postJson(route('api.v1.comments.store', $post->slug), [
            'content' => 'Missing author fields',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['guest_name', 'guest_email']);
    }

    public function test_authenticated_user_can_post_comment_without_guest_fields(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $payload = [
            'content' => 'Authenticated comment from mobile app!',
        ];

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.v1.comments.store', $post->slug), $payload);

        $response->assertStatus(201)
            ->assertJson([
                'status' => 'success',
            ]);

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'user_id' => $user->id,
            'guest_name' => $user->name,
            'guest_email' => $user->email,
        ]);
    }

    public function test_spam_keyword_flags_comment_as_spam(): void
    {
        Setting::set('spam_keywords', 'crypto,viagra,casino');

        $post = Post::factory()->create();

        $response = $this->postJson(route('api.v1.comments.store', $post->slug), [
            'guest_name' => 'Spammer',
            'guest_email' => 'spam@example.com',
            'content' => 'Buy cheap crypto right now!',
        ]);

        $response->assertStatus(201);

        $this->assertDatabaseHas('comments', [
            'post_id' => $post->id,
            'status' => CommentStatus::Spam->value,
        ]);
    }

    public function test_comment_fails_when_comments_disabled(): void
    {
        Setting::set('enable_comments', false);

        $post = Post::factory()->create();

        $response = $this->postJson(route('api.v1.comments.store', $post->slug), [
            'guest_name' => 'Guest',
            'guest_email' => 'guest@example.com',
            'content' => 'Trying to post when disabled',
        ]);

        $response->assertStatus(403);
    }

    public function test_authenticated_user_can_delete_own_comment(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson(route('api.v1.comments.destroy', ['slug' => $post->slug, 'id' => $comment->id]));

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'message' => 'Comment deleted successfully.',
            ]);

        $this->assertSoftDeleted('comments', [
            'id' => $comment->id,
        ]);
    }

    public function test_user_cannot_delete_another_users_comment(): void
    {
        $user1 = User::factory()->create();
        $user2 = User::factory()->create();
        $post = Post::factory()->create();

        $comment = Comment::factory()->create([
            'post_id' => $post->id,
            'user_id' => $user1->id,
        ]);

        $response = $this->actingAs($user2, 'sanctum')
            ->deleteJson(route('api.v1.comments.destroy', ['slug' => $post->slug, 'id' => $comment->id]));

        $response->assertStatus(403);
    }
}
