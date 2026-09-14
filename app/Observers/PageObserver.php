<?php

namespace App\Observers;

use App\Enums\RedirectType;
use App\Models\Page;
use App\Models\Redirect;
use App\Services\BlogCacheService;

class PageObserver
{
    public function updating(Page $page): void
    {
        if ($page->isDirty('slug')) {
            $oldSlug = $page->getOriginal('slug');
            if ($oldSlug && $oldSlug !== $page->slug) {
                Redirect::updateOrCreate(
                    ['source_path' => "/{$oldSlug}"],
                    [
                        'target_path' => "/{$page->slug}",
                        'status_code' => RedirectType::Permanent301,
                        'is_active' => true,
                    ]
                );
            }
        }
    }

    public function saved(Page $page): void
    {
        app(BlogCacheService::class)->invalidateSitemap();
    }

    public function deleted(Page $page): void
    {
        app(BlogCacheService::class)->invalidateSitemap();
    }
}
