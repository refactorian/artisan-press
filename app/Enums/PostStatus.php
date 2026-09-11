<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum PostStatus: string implements HasColor, HasIcon, HasLabel
{
    case Draft = 'draft';
    case Published = 'published';
    case Scheduled = 'scheduled';

    public function getLabel(): string
    {
        return match ($this) {
            PostStatus::Draft => 'Draft',
            PostStatus::Published => 'Published',
            PostStatus::Scheduled => 'Scheduled',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            PostStatus::Draft => 'gray',
            PostStatus::Published => 'success',
            PostStatus::Scheduled => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            PostStatus::Draft => 'heroicon-m-pencil',
            PostStatus::Published => 'heroicon-m-check-circle',
            PostStatus::Scheduled => 'heroicon-m-clock',
        };
    }

    public function isDraft(): bool
    {
        return $this === PostStatus::Draft;
    }

    public function isPublished(): bool
    {
        return $this === PostStatus::Published;
    }

    public function isScheduled(): bool
    {
        return $this === PostStatus::Scheduled;
    }
}
