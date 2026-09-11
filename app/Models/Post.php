<?php

namespace App\Models;

use App\Enums\PostStatus;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[ObservedBy([\App\Observers\PostObserver::class])]
#[Fillable([
    'user_id',
    'title',
    'slug',
    'excerpt',
    'content',
    'status',
    'published_at',
    'seo_title',
    'seo_description',
    'canonical_url',
    'noindex',
    'nofollow',
    'view_count',
])]
class Post extends Model implements HasMedia
{
    /** @use HasFactory<PostFactory> */
    use HasFactory, HasSlug, InteractsWithMedia, SoftDeletes;

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'status'       => PostStatus::class,
            'published_at' => 'datetime',
            'noindex'      => 'boolean',
            'nofollow'     => 'boolean',
            'view_count'   => 'integer',
        ];
    }

    /**
     * Configure the slug options.
     */
    public function getSlugOptions(): SlugOptions
    {
        return SlugOptions::create()
            ->generateSlugsFrom('title')
            ->saveSlugsTo('slug')
            ->doNotGenerateSlugsOnUpdate(); // Allow manual override
    }

    /**
     * Get the route key for the model.
     */
    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    /**
     * Register Spatie MediaLibrary collections.
     */
    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('featured_image')
            ->singleFile();
    }

    /**
     * Register media conversions.
     */
    public function registerMediaConversions(?Media $media = null): void
    {
        $this->addMediaConversion('thumb')
            ->fit(Fit::Crop, 400, 300)
            ->nonQueued();

        $this->addMediaConversion('og')
            ->fit(Fit::Crop, 1200, 630)
            ->nonQueued();
    }

    // ─── Relationships ─────────────────────────────────────────────────────────

    public function author(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    // ─── Scopes ────────────────────────────────────────────────────────────────

    public function scopePublished(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Published)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    public function scopeDraft(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Draft);
    }

    public function scopeScheduled(Builder $query): Builder
    {
        return $query->where('status', PostStatus::Scheduled)
            ->whereNotNull('published_at')
            ->where('published_at', '>', now());
    }

    // ─── Helpers ───────────────────────────────────────────────────────────────

    public function isPublished(): bool
    {
        return $this->status->isPublished();
    }

    public function isDraft(): bool
    {
        return $this->status->isDraft();
    }

    public function isScheduled(): bool
    {
        return $this->status->isScheduled();
    }

    public function incrementViewCount(): void
    {
        $this->increment('view_count');
    }
}
