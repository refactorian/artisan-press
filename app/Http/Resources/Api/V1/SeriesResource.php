<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Series;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Series
 */
class SeriesResource extends JsonResource
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
            'name' => $this->name,
            'slug' => $this->slug,
            'description' => $this->description,
            'posts_count' => $this->whenCounted('posts'),
            'sort_order' => $this->sort_order,
            'seo' => app(SeoService::class)->generate($this->resource),
            'posts' => PostResource::collection($this->whenLoaded('posts')),
        ];
    }
}
