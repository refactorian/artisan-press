<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Post
 */
class PostResource extends JsonResource
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
            'reading_time' => $this->reading_time ?: $this->calculateReadingTime(),
            'view_count' => (int) $this->view_count,
            'is_featured' => (bool) $this->is_featured,
            'is_hero' => (bool) $this->is_hero,
            'published_at' => $this->published_at?->toIso8601String(),
            'author' => new AuthorResource($this->whenLoaded('author')),
            'categories' => CategoryResource::collection($this->whenLoaded('categories')),
            'tags' => TagResource::collection($this->whenLoaded('tags')),
            'images' => [
                'thumb' => $this->getFirstMediaUrl('featured_image', 'thumb') ?: null,
                'medium' => $this->getFirstMediaUrl('featured_image', 'medium') ?: null,
                'large' => $this->getFirstMediaUrl('featured_image', 'large') ?: null,
            ],
        ];
    }
}
