<?php

namespace App\Models;

use App\Enums\CommentStatus;
use App\Enums\PostStatus;
use App\Observers\PostObserver;
use Database\Factories\PostFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Spatie\Activitylog\Models\Concerns\LogsActivity;
use Spatie\Activitylog\Support\LogOptions;
use Spatie\Image\Enums\Fit;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;
use Spatie\Sluggable\HasSlug;
use Spatie\Sluggable\SlugOptions;

#[ObservedBy([PostObserver::class])]
#[Fillable([
    'user_id',
    'series_id',
    'series_order',
    'title',
    'slug',
    'excerpt',
    'content',
    'content_blocks',
    'status',
    'is_featured',
    'is_hero',
    'featured_order',
    'sort_order',
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
    use HasFactory, HasSlug, InteractsWithMedia, LogsActivity, SoftDeletes;

    /**
     * The attributes that should be cast.
     */
    protected function casts(): array
    {
        return [
            'status' => PostStatus::class,
            'published_at' => 'datetime',
            'noindex' => 'boolean',
            'nofollow' => 'boolean',
            'view_count' => 'integer',
            'is_featured' => 'boolean',
            'is_hero' => 'boolean',
            'featured_order' => 'integer',
            'sort_order' => 'integer',
            'series_order' => 'integer',
            'content_blocks' => 'array',
        ];
    }

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->logOnly(['title', 'slug', 'status', 'is_featured', 'is_hero'])
            ->logOnlyDirty()
            ->dontLogEmptyChanges();
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

    public function series(): BelongsTo
    {
        return $this->belongsTo(Series::class);
    }

    public function contributors(): BelongsToMany
    {
        return $this->belongsToMany(User::class, 'post_user')
            ->withPivot('role')
            ->withTimestamps();
    }

    public function relatedPosts(): BelongsToMany
    {
        return $this->belongsToMany(Post::class, 'post_related', 'post_id', 'related_post_id')
            ->withPivot('sort_order')
            ->orderBy('post_related.sort_order');
    }

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function tags(): BelongsToMany
    {
        return $this->belongsToMany(Tag::class);
    }

    public function revisions(): HasMany
    {
        return $this->hasMany(PostRevision::class)->orderBy('created_at', 'desc');
    }

    public function comments(): HasMany
    {
        return $this->hasMany(Comment::class)->orderBy('created_at', 'desc');
    }

    public function approvedComments(): HasMany
    {
        return $this->hasMany(Comment::class)
            ->where('status', CommentStatus::Approved)
            ->whereNull('parent_id')
            ->orderBy('created_at', 'desc');
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

    public function scopeFeatured(Builder $query): Builder
    {
        return $query->where('is_featured', true)->orderBy('featured_order');
    }

    public function scopeHero(Builder $query): Builder
    {
        return $query->where('is_hero', true);
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

    public function createRevision(?string $reason = null, ?int $userId = null): PostRevision
    {
        return $this->revisions()->create([
            'user_id' => $userId ?? auth()->id() ?? $this->user_id,
            'title' => $this->title,
            'slug' => $this->slug,
            'excerpt' => $this->excerpt,
            'content' => $this->content,
            'content_blocks' => $this->content_blocks,
            'reason' => $reason,
        ]);
    }
}
