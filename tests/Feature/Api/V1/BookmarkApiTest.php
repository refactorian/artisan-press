<?php

namespace Tests\Feature\Api\V1;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class BookmarkApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_authenticated_user_can_bookmark_post(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->postJson(route('api.v1.bookmarks.store', $post->slug));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post bookmarked successfully',
                'is_bookmarked' => true,
            ]);

        $this->assertDatabaseHas('post_bookmarks', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_remove_bookmark(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $user->bookmarkedPosts()->attach($post->id);

        $response = $this->actingAs($user, 'sanctum')
            ->deleteJson(route('api.v1.bookmarks.destroy', $post->slug));

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Post removed from bookmarks',
                'is_bookmarked' => false,
            ]);

        $this->assertDatabaseMissing('post_bookmarks', [
            'post_id' => $post->id,
            'user_id' => $user->id,
        ]);
    }

    public function test_authenticated_user_can_list_bookmarks(): void
    {
        $user = User::factory()->create();
        $posts = Post::factory()->count(3)->create();

        foreach ($posts as $post) {
            $user->bookmarkedPosts()->attach($post->id);
        }

        $otherPost = Post::factory()->create();

        $response = $this->actingAs($user, 'sanctum')
            ->getJson(route('api.v1.bookmarks.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'slug', 'is_bookmarked'],
                ],
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_unauthenticated_user_cannot_access_bookmarks(): void
    {
        $response = $this->getJson(route('api.v1.bookmarks.index'));

        $response->assertStatus(401);
    }
}
