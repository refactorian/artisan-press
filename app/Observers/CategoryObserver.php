<?php

namespace App\Observers;

use App\Enums\RedirectType;
use App\Models\Category;
use App\Models\Redirect;
use App\Services\BlogCacheService;

class CategoryObserver
{
    public function updating(Category $category): void
    {
        if ($category->isDirty('slug')) {
            $oldSlug = $category->getOriginal('slug');
            if ($oldSlug && $oldSlug !== $category->slug) {
                Redirect::updateOrCreate(
                    ['source_path' => "/categories/{$oldSlug}"],
                    [
                        'target_path' => "/categories/{$category->slug}",
                        'status_code' => RedirectType::Permanent301,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    public function saved(Category $category): void
    {
        app(BlogCacheService::class)->invalidateTaxonomies();
    }

    public function deleted(Category $category): void
    {
        app(BlogCacheService::class)->invalidateTaxonomies();
    }
}
