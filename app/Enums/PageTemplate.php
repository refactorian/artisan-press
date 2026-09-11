<?php

namespace App\Enums;

use Filament\Support\Contracts\HasLabel;

enum PageTemplate: string implements HasLabel
{
    case Default = 'default';
    case FullWidth = 'full_width';
    case Contact = 'contact';
    case About = 'about';

    public function getLabel(): string
    {
        return match ($this) {
            self::Default => 'Default Template',
            self::FullWidth => 'Full Width Template',
            self::Contact => 'Contact Page Template',
            self::About => 'About Page Template',
        };
    }
}
