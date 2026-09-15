<?php

namespace Tests\Feature\Api\V1;

use App\Models\Post;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AuthorApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_active_authors_with_published_posts(): void
    {
        $author = User::factory()->create(['is_active' => true]);
        Post::factory()->create(['user_id' => $author->id]);

        $inactiveAuthor = User::factory()->create(['is_active' => false]);
        Post::factory()->create(['user_id' => $inactiveAuthor->id]);

        $authorWithoutPosts = User::factory()->create(['is_active' => true]);

        $response = $this->getJson(route('api.v1.authors.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'bio', 'posts_count', 'seo'],
                ],
            ]);

        $this->assertCount(1, $response->json('data'));
        $this->assertEquals($author->id, $response->json('data.0.id'));
    }

    public function test_can_show_author_with_paginated_posts(): void
    {
        $author = User::factory()->create(['is_active' => true]);
        Post::factory()->count(2)->create(['user_id' => $author->id]);

        $response = $this->getJson(route('api.v1.authors.show', $author->id));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'author' => ['id', 'name', 'seo'],
                'posts' => [
                    'data' => [
                        '*' => ['id', 'title', 'slug'],
                    ],
                    'links',
                    'meta',
                ],
            ]);

        $this->assertCount(2, $response->json('posts.data'));
    }

    public function test_returns_404_for_nonexistent_author(): void
    {
        $response = $this->getJson(route('api.v1.authors.show', 999999));

        $response->assertStatus(404);
    }
}
