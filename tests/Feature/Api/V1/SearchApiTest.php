<?php

namespace Tests\Feature\Api\V1;

use App\Models\Post;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SearchApiTest extends TestCase
{
    use RefreshDatabase;

    public function test_search_requires_minimum_characters(): void
    {
        $response = $this->getJson(route('api.v1.search', ['q' => 'a']));

        $response->assertStatus(422)
            ->assertJson([
                'status' => 'error',
            ]);
    }

    public function test_search_finds_matching_posts(): void
    {
        $matching = Post::factory()->create(['title' => 'Flutter State Management Guide']);
        $other = Post::factory()->create(['title' => 'Ruby on Rails Tutorial']);

        $response = $this->getJson(route('api.v1.search', ['q' => 'Flutter']));

        $response->assertStatus(200)
            ->assertJson([
                'query' => 'Flutter',
                'total' => 1,
            ])
            ->assertJsonStructure([
                'query',
                'total',
                'posts' => [
                    'data' => [
                        '*' => ['id', 'title', 'slug'],
                    ],
                    'links',
                    'meta',
                ],
            ]);

        $this->assertEquals($matching->id, $response->json('posts.data.0.id'));
    }

    public function test_search_logs_analytics(): void
    {
        Post::factory()->create(['title' => 'Mobile Development with Flutter']);

        $this->getJson(route('api.v1.search', ['q' => 'Mobile']));

        $this->assertDatabaseHas('search_logs', [
            'query' => 'Mobile',
        ]);
    }
}
