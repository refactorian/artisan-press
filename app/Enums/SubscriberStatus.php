<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasIcon;
use Filament\Support\Contracts\HasLabel;

enum SubscriberStatus: string implements HasColor, HasIcon, HasLabel
{
    case Subscribed = 'subscribed';
    case Unsubscribed = 'unsubscribed';
    case Pending = 'pending';

    public function getLabel(): string
    {
        return match ($this) {
            self::Subscribed => 'Subscribed',
            self::Unsubscribed => 'Unsubscribed',
            self::Pending => 'Pending Confirmation',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Subscribed => 'success',
            self::Unsubscribed => 'danger',
            self::Pending => 'warning',
        };
    }

    public function getIcon(): ?string
    {
        return match ($this) {
            self::Subscribed => 'heroicon-m-check-badge',
            self::Unsubscribed => 'heroicon-m-user-minus',
            self::Pending => 'heroicon-m-clock',
        };
    }
}
