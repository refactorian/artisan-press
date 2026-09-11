<?php

namespace App\Enums;

use Filament\Support\Contracts\HasColor;
use Filament\Support\Contracts\HasLabel;

enum RedirectType: int implements HasColor, HasLabel
{
    case Permanent301 = 301;
    case Temporary302 = 302;
    case Temporary307 = 307;
    case Permanent308 = 308;

    public function getLabel(): string
    {
        return match ($this) {
            self::Permanent301 => '301 - Permanent Redirect',
            self::Temporary302 => '302 - Temporary Redirect (Found)',
            self::Temporary307 => '307 - Temporary Redirect (Preserve Method)',
            self::Permanent308 => '308 - Permanent Redirect (Preserve Method)',
        };
    }

    public function getColor(): string|array|null
    {
        return match ($this) {
            self::Permanent301, self::Permanent308 => 'success',
            self::Temporary302, self::Temporary307 => 'warning',
        };
    }
}
