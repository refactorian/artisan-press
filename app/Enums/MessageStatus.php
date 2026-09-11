<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum MessageStatus: string implements HasColor, HasIcon, HasLabel
{
    case Unread = 'unread';
    case Read = 'read';
    case Replied = 'replied';
    case Archived = 'archived';

    public function getLabel(): string
    {
        return match ($this) {
            self::Unread => 'Unread',
            self::Read => 'Read',
            self::Replied => 'Replied',
            self::Archived => 'Archived',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Unread => 'warning',
            self::Read => 'info',
            self::Replied => 'success',
            self::Archived => 'gray',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Unread => 'heroicon-m-envelope',
            self::Read => 'heroicon-m-envelope-open',
            self::Replied => 'heroicon-m-arrow-uturn-left',
            self::Archived => 'heroicon-m-archive-box',
        };
    }
}
