<?php

namespace Tests\Feature\Api\V1;

use App\Models\Post;
use App\Models\Series;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeriesApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_list_active_series_with_post_count(): void
    {
        $series = Series::factory()->count(3)->create();
        Series::factory()->inactive()->create();

        $post = Post::factory()->create(['series_id' => $series->first()->id, 'series_order' => 1]);

        $response = $this->getJson(route('api.v1.series.index'));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => ['id', 'name', 'slug', 'description', 'posts_count', 'seo'],
                ],
            ]);

        $this->assertCount(3, $response->json('data'));
    }

    public function test_can_show_series_with_paginated_posts(): void
    {
        $series = Series::factory()->create();
        Post::factory()->count(2)->create([
            'series_id' => $series->id,
            'series_order' => 1,
        ]);

        $response = $this->getJson(route('api.v1.series.show', $series->slug));

        $response->assertStatus(200)
            ->assertJsonStructure([
                'series' => ['id', 'name', 'slug', 'seo'],
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

    public function test_returns_404_for_nonexistent_series(): void
    {
        $response = $this->getJson(route('api.v1.series.show', 'unknown-series'));

        $response->assertStatus(404);
    }
}
