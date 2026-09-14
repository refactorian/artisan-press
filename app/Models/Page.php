<?php

namespace App\Models;

use App\Enums\PageStatus;
use App\Enums\PageTemplate;
use App\Observers\PageObserver;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[ObservedBy([PageObserver::class])]
#[Fillable([
    'title',
    'slug',
    'content',
    'content_blocks',
    'template',
    'status',
    'published_at',
    'seo_title',
    'seo_description',
    'canonical_url',
    'noindex',
    'nofollow',
    'sort_order',
])]
class Page extends Model
{
    use HasFactory, HasSlug, LogsActivity, SoftDeletes;

    protected function casts(): array
    {
        return [
            'status' => PageStatus::class,
            'template' => PageTemplate::class,
            'content_blocks' => 'array',
            'published_at' => 'datetime',
            'noindex' => 'boolean',
            'nofollow' => 'boolean',
            'sort_order' => 'integer',
        ];
    }

    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug');
    }

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'status', 'template'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
    }

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PageStatus::Published)
            ->where(function (Builder $q) {
                $q->whereNull('published_at')
                    ->orWhere('published_at', '<=', now());
            });
    }
}
