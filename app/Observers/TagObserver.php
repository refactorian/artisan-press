<?php

namespace App\Observers;

use App\Enums\RedirectType;
use App\Models\Redirect;
use App\Models\Tag;
use App\Services\BlogCacheService;

class TagObserver
{
    public function updating(Tag $tag): void
    {
        if ($tag->isDirty('slug')) {
            $oldSlug = $tag->getOriginal('slug');
            if ($oldSlug && $oldSlug !== $tag->slug) {
                Redirect::updateOrCreate(
                    ['source_path' => "/tags/{$oldSlug}"],
                    [
                        'target_path' => "/tags/{$tag->slug}",
                        'status_code' => RedirectType::Permanent301,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    public function saved(Tag $tag): void
    {
        app(BlogCacheService::class)->invalidateTaxonomies();
    }

    public function deleted(Tag $tag): void
    {
        app(BlogCacheService::class)->invalidateTaxonomies();
    }
}
