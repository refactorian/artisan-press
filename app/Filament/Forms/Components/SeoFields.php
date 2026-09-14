<?php

namespace App\Filament\Forms\Components;

use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;

class SeoFields
{
    /**
     * Create a standardized, reusable SEO & Social metadata section for Filament forms.
     */
    public static function make(string $title = 'SEO & Social Metadata', bool $collapsed = true): Section
    {
        return Section::make($title)
            ->description('Search engine optimization, canonical URLs, and indexing controls.')
            ->collapsed($collapsed)
            ->schema([
                TextInput::make('seo_title')
                    ->label('Custom SEO Title')
                    ->maxLength(70)
                    ->helperText('Recommended: 50–70 characters. Falls back to entity title/name when blank.'),

                Textarea::make('seo_description')
                    ->label('SEO Meta Description')
                    ->rows(3)
                    ->maxLength(160)
                    ->helperText('Recommended: 120–160 characters. Falls back to summary or excerpt when blank.'),

                TextInput::make('canonical_url')
                    ->label('Canonical URL')
                    ->url()
                    ->maxLength(2048)
                    ->helperText('Leave empty to default to this resource\'s standard permalink.'),

                Grid::make(2)->schema([
                    Toggle::make('noindex')
                        ->label('Noindex (Hide from search engines)')
                        ->helperText('Instructs search crawlers NOT to index this page in search results.')
                        ->default(false),

                    Toggle::make('nofollow')
                        ->label('Nofollow (Do not track links)')
                        ->helperText('Instructs search crawlers NOT to follow hyperlinks on this page.')
                        ->default(false),
                ]),
            ]);
    }
}
