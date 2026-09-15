<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PostApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_published_posts(): void
    {
        Post::factory()->count(5)->create();
        Post::factory()->draft()->create();

        $response = $this->getJson(route('api.v1.posts.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'title',
                        'slug',
                        'excerpt',
                        'reading_time',
                        'view_count',
                        'likes_count',
                        'is_liked',
                        'is_bookmarked',
                        'is_featured',
                        'is_hero',
                        'published_at',
                    ],
                ],
                'links',
                'meta',
            ]);

        $this->assertCount(5, $response->json('data'));
    }

    public function test_can_filter_posts_by_category(): void
    {
        $category = Category::factory()->create();
        $matchingPost = Post::factory()->create();
        $matchingPost->categories()->attach($category);

        $otherPost = Post::factory()->create();

        $response = $this->getJson(route('api.v1.posts.index', ['category' => $category->slug]));

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($matchingPost->id, $response->json('data.0.id'));
    }

    public function test_can_filter_posts_by_tag(): void
    {
        $tag = Tag::factory()->create();
        $matchingPost = Post::factory()->create();
        $matchingPost->tags()->attach($tag);

        $otherPost = Post::factory()->create();

        $response = $this->getJson(route('api.v1.posts.index', ['tag' => $tag->slug]));

        $response->assertStatus(200);
        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($matchingPost->id, $response->json('data.0.id'));
    }

    public function test_can_show_single_post(): void
    {
        $post = Post::factory()->create();

        $response = $this->getJson(route('api.v1.posts.show', $post->slug));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'slug',
                    'excerpt',
                    'content',
                    'reading_time',
                    'view_count',
                    'likes_count',
                    'is_liked',
                    'is_bookmarked',
                    'seo',
                    'share_links',
                ],
            ]);
    }

    public function test_returns_404_for_draft_post(): void
    {
        $post = Post::factory()->draft()->create();

        $response = $this->getJson(route('api.v1.posts.show', $post->slug));

        $response->assertStatus(404);
    }

    public function test_authenticated_user_sees_is_liked_and_is_bookmarked_flags(): void
    {
        $user = User::factory()->create();
        $post = Post::factory()->create();

        $post->likes()->attach($user->id);
        $user->bookmarkedPosts()->attach($post->id);

        $response = $this->actingAs($user, 'sanctum')
            ->getJson(route('api.v1.posts.show', $post->slug));

        $response->assertStatus(200)
            ->assertJsonPath('data.is_liked', true)
            ->assertJsonPath('data.is_bookmarked', true)
            ->assertJsonPath('data.likes_count', 1);
    }

    public function test_can_get_related_posts(): void
    {
        $category = Category::factory()->create();

        $mainPost = Post::factory()->create();
        $mainPost->categories()->attach($category);

        $relatedPost = Post::factory()->create();
        $relatedPost->categories()->attach($category);

        $response = $this->getJson(route('api.v1.posts.related', $mainPost->slug));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'title', 'slug'],
                ],
            ]);
    }

    public function test_can_record_post_view(): void
    {
        $post = Post::factory()->create(['view_count' => 0]);

        $response = $this->postJson(route('api.v1.posts.view', $post->slug), [
            'device_type' => 'mobile',
        ]);

        $response->assertStatus(200)
            ->assertJson([
                'status' => 'success',
                'recorded' => true,
            ]);
    }
}
