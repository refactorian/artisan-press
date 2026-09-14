<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Post;
use App\Services\SeoService;
use App\Services\SocialShareService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostDetailResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'content_blocks' => $this->content_blocks,
            'reading_time' => $this->reading_time ?: $this->calculateReadingTime(),
            'view_count' => (int) $this->view_count,
            'is_featured' => (bool) $this->is_featured,
            'is_hero' => (bool) $this->is_hero,
            'published_at' => $this->published_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
            'author' => new AuthorResource($this->whenLoaded('author')),
            'contributors' => AuthorResource::collection($this->whenLoaded('contributors')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'series' => $this->whenLoaded('series', function () {
                if (! $this->series) {
                    return null;
                }

                return [
                    'id' => $this->series->id,
                    'title' => $this->series->title,
                    'slug' => $this->series->slug,
                    'order' => $this->series_order,
                ];
            }),
            'related_posts' => PostResource::collection($this->whenLoaded('relatedPosts')),
            'images' => [
                'thumb' => $this->getFirstMediaUrl('featured_image', 'thumb') ?: null,
                'medium' => $this->getFirstMediaUrl('featured_image', 'medium') ?: null,
                'large' => $this->getFirstMediaUrl('featured_image', 'large') ?: null,
                'og' => $this->getFirstMediaUrl('featured_image', 'og') ?: null,
            ],
            'share_links' => app(SocialShareService::class)->generateShareLinks($this->resource),
            'seo' => app(SeoService::class)->generate($this->resource),
        ];
    }
}
