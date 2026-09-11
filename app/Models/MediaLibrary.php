<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

/**
 * Singleton owner model for standalone media uploads.
 *
 * Spatie MediaLibrary requires every `media` row to have a polymorphic owner.
 * This model acts as that owner for images uploaded directly to the Media Library
 * (i.e. not attached to any specific Post, Page, etc.). There is always exactly
 * one row (id=1) in the `media_library` table.
 */
class MediaLibrary extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'media_library';

    protected $guarded = [];

    /**
     * Always return the singleton instance (id=1), creating it if needed.
     */
    public static function getInstance(): static
    {
        return static::firstOrCreate(['id' => 1], ['name' => 'Global Media Library']);
    }

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection('images');
        $this->addMediaCollection('documents');
        $this->addMediaCollection('default');
    }
}
