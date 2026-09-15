<?php

namespace Tests\Feature\Api\V1;

use App\Models\Category;
use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_active_categories_with_post_count(): void
    {
        $categories = Category::factory()->count(3)->create();
        Category::factory()->inactive()->create();

        $post = Post::factory()->create();
        $post->categories()->attach($categories->first());

        $response = $this->getJson(route('api.v1.categories.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'description', 'posts_count', 'seo'],
                ],
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_show_category_with_paginated_posts(): void
    {
        $category = Category::factory()->create();
        $posts = Post::factory()->count(2)->create();
        $category->posts()->attach($posts);

        $response = $this->getJson(route('api.v1.categories.show', $category->slug));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'category' => ['id', 'name', 'slug', 'seo'],
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

    public function test_returns_404_for_nonexistent_category(): void
    {
        $response = $this->getJson(route('api.v1.categories.show', 'non-existent-category'));

        $response->assertStatus(404);
    }
}
