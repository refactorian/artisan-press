<?php

namespace Tests\Feature\Api\V1;

use App\Models\Post;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TagApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_tags_with_post_count(): void
    {
        $tags = Tag::factory()->count(3)->create();
        $post = Post::factory()->create();
        $post->tags()->attach($tags);

        $response = $this->getJson(route('api.v1.tags.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'posts_count', 'seo'],
                ],
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_show_tag_with_paginated_posts(): void
    {
        $tag = Tag::factory()->create();
        $posts = Post::factory()->count(2)->create();
        $tag->posts()->attach($posts);

        $response = $this->getJson(route('api.v1.tags.show', $tag->slug));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'tag' => ['id', 'name', 'slug', 'seo'],
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

    public function test_returns_404_for_nonexistent_tag(): void
    {
        $response = $this->getJson(route('api.v1.tags.show', 'unknown-tag'));

        $response->assertStatus(404);
    }
}
