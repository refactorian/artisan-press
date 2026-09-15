<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/**
 * @mixin Comment
 */
class CommentResource extends JsonResource
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
            'post_id' => $this->post_id,
            'user_id' => $this->user_id,
            'parent_id' => $this->parent_id,
            'author_name' => $this->author_name,
            'content' => $this->content,
            'created_at' => $this->created_at?->toIso8601String(),
            'replies' => CommentResource::collection($this->whenLoaded('replies')),
        ];
    }
}
