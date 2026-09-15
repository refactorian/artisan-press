<?php

namespace Tests\Feature\Api\V1;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LikeApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_like_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.v1.posts.like', $post->slug));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post liked successfully',
                'is_liked' => true,
                'likes_count' => 1,
            ]);

        $this->assertDatabaseHas('post_likes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_like_is_idempotent(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $post->likes()->attach($user->id);

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.v1.posts.like', $post->slug));

        $response->assertStatus(200)
            ->assertJson([
                'is_liked' => true,
                'likes_count' => 1,
            ]);
    }

    public function test_authenticated_user_can_unlike_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $post->likes()->attach($user->id);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson(route('api.v1.posts.unlike', $post->slug));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post unliked successfully',
                'is_liked' => false,
                'likes_count' => 0,
            ]);

        $this->assertDatabaseMissing('post_likes', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_unauthenticated_user_cannot_like_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->postJson(route('api.v1.posts.like', $post->slug));

        $response->assertStatus(401);
    }

    public function test_unauthenticated_user_cannot_unlike_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->deleteJson(route('api.v1.posts.unlike', $post->slug));

        $response->assertStatus(401);
    }
}
