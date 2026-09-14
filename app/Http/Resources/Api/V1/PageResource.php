<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Page;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Page
 */
class PageResource extends JsonResource
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
            'content' => $this->content,
            'content_blocks' => $this->content_blocks,
            'template' => $this->template?->value,
            'published_at' => $this->published_at?->toIso8601String(),
            'seo' => app(SeoService::class)->generate($this->resource),
        ];
    }
}
