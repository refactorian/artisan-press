<?php

namespace App\Http\Resources\Api\V1;

use App\Models\User;
use App\Services\SeoService;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin User
 */
class AuthorResource extends JsonResource
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
            'avatar' => $this->getFilamentAvatarUrl(),
            'bio' => $this->bio,
            'job_title' => $this->job_title,
            'pronouns' => $this->pronouns,
            'website_url' => $this->website_url,
            'social_links' => $this->social_links,
            'is_featured_author' => (bool) $this->is_featured_author,
            'posts_count' => $this->whenCounted('posts'),
            'seo' => app(SeoService::class)->generate($this->resource),
        ];
    }
}
