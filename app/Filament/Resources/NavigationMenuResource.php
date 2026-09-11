<?php

namespace App\Filament\Resources;

use App\Filament\Resources\NavigationMenuResource\Pages;
use App\Models\Category;
use App\Models\NavigationMenu;
use App\Models\Page;
use App\Models\Post;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class NavigationMenuResource extends Resource
{
    protected static ?string $model = NavigationMenu::class;

    protected static ?string $navigationIcon = 'heroicon-o-bars-3';

    protected static ?string $navigationGroup = 'Navigation & Structure';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'name';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Menu Definition')
                ->schema([
                    TextInput::make('name')
                        ->required()
                        ->placeholder('Main Header Navigation'),

                    Select::make('location')
                        ->options([
                            'header' => 'Header Navigation',
                            'footer' => 'Footer Navigation',
                            'sidebar' => 'Sidebar Navigation',
                            'mobile' => 'Mobile Drawer Menu',
                        ])
                        ->required()
                        ->unique(NavigationMenu::class, 'location', ignoreRecord: true),

                    Toggle::make('is_active')
                        ->default(true)
                        ->label('Active'),
                ])->columns(3),

            Section::make('Navigation Links & Items')
                ->schema([
                    Repeater::make('items')
                        ->relationship('items')
                        ->orderColumn('sort_order')
                        ->collapsible()
                        ->cloneable()
                        ->itemLabel(fn (array $state): ?string => $state['label'] ?? null)
                        ->schema([
                            Grid::make(4)->schema([
                                TextInput::make('label')
                                    ->required()
                                    ->placeholder('e.g. Home, Tutorials, About'),

                                Select::make('type')
                                    ->options([
                                        'url' => 'Custom External/Internal URL',
                                        'post' => 'Blog Post',
                                        'category' => 'Post Category',
                                        'page' => 'Static Page',
                                    ])
                                    ->default('url')
                                    ->live()
                                    ->required(),

                                TextInput::make('url')
                                    ->label('URL / Path')
                                    ->visible(fn (Get $get) => $get('type') === 'url')
                                    ->required(fn (Get $get) => $get('type') === 'url')
                                    ->placeholder('https://... or /contact'),

                                Select::make('url')
                                    ->label('Select Post')
                                    ->options(fn () => Post::published()->pluck('title', 'slug')->mapWithKeys(fn ($t, $s) => ["/posts/{$s}" => $t]))
                                    ->searchable()
                                    ->visible(fn (Get $get) => $get('type') === 'post'),

                                Select::make('url')
                                    ->label('Select Category')
                                    ->options(fn () => Category::active()->pluck('name', 'slug')->mapWithKeys(fn ($n, $s) => ["/categories/{$s}" => $n]))
                                    ->searchable()
                                    ->visible(fn (Get $get) => $get('type') === 'category'),

                                Select::make('url')
                                    ->label('Select Page')
                                    ->options(fn () => Page::published()->pluck('title', 'slug')->mapWithKeys(fn ($t, $s) => ["/{$s}" => $t]))
                                    ->searchable()
                                    ->visible(fn (Get $get) => $get('type') === 'page'),

                                Select::make('target')
                                    ->options([
                                        '_self' => 'Same Tab (_self)',
                                        '_blank' => 'New Tab (_blank)',
                                    ])
                                    ->default('_self'),
                            ]),

                            Toggle::make('is_active')
                                ->label('Active Item')
                                ->default(true),
                        ]),
                ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('location')
                    ->badge()
                    ->color('info')
                    ->sortable(),

                TextColumn::make('items_count')
                    ->counts('items')
                    ->label('Links Count')
                    ->badge(),

                IconColumn::make('is_active')
                    ->boolean(),

                TextColumn::make('updated_at')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListNavigationMenus::route('/'),
            'create' => Pages\CreateNavigationMenu::route('/create'),
            'edit' => Pages\EditNavigationMenu::route('/{record}/edit'),
        ];
    }
}
