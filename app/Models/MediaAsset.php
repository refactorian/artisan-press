<?php

namespace App\Models;

use Spatie\MediaLibrary\MediaCollections\Models\Media as BaseMedia;

class MediaAsset extends BaseMedia
{
    protected $table = 'media';

    public function getAltTextAttribute(): ?string
    {
        return $this->getCustomProperty('alt_text');
    }

    public function setAltTextAttribute(?string $value): void
    {
        $this->setCustomProperty('alt_text', $value);
    }

    public function getCaptionAttribute(): ?string
    {
        return $this->getCustomProperty('caption');
    }

    public function setCaptionAttribute(?string $value): void
    {
        $this->setCustomProperty('caption', $value);
    }

    public function getCreditsAttribute(): ?string
    {
        return $this->getCustomProperty('credits');
    }

    public function setCreditsAttribute(?string $value): void
    {
        $this->setCustomProperty('credits', $value);
    }

    public function getTitleTextAttribute(): ?string
    {
        return $this->getCustomProperty('title') ?? $this->name;
    }

    public function setTitleTextAttribute(?string $value): void
    {
        $this->setCustomProperty('title', $value);
    }

    public function getFormattedSizeAttribute(): string
    {
        $bytes = $this->size;
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        for ($i = 0; $bytes > 1024; $i++) {
            $bytes /= 1024;
        }

        return round($bytes, 2).' '.$units[$i];
    }
}
