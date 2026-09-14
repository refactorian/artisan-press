<?php

namespace App\Filament\Resources;

use App\Enums\PageStatus;
use App\Enums\PageTemplate;
use App\Filament\Forms\Components\ContentBlocksBuilder;
use App\Filament\Forms\Components\SeoFields;
use App\Filament\Resources\PageResource\Pages;
use App\Models\Page;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Support\Str;

class PageResource extends Resource
{
    protected static ?string $model = Page::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-duplicate';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(['default' => 1, 'lg' => 3])->schema([
                Group::make()->columnSpan(['lg' => 2])->schema([
                    Section::make('Page Content')
                        ->schema([
                            TextInput::make('title')
                                ->required()
                                ->maxLength(255)
                                ->live(debounce: 500)
                                ->afterStateUpdated(function (Get $get, Set $set, ?string $old, ?string $state) {
                                    if (($get('slug') ?? '') !== Str::slug($old ?? '')) {
                                        return;
                                    }
                                    $set('slug', Str::slug($state));
                                }),

                            TextInput::make('slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(Page::class, 'slug', ignoreRecord: true)
                                ->prefix('/'),

                            TiptapEditor::make('content')
                                ->label('Main Page Content')
                                ->profile('default')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Content Components & Blocks')
                        ->collapsible()
                        ->schema([
                            ContentBlocksBuilder::make('content_blocks'),
                        ]),

                    SeoFields::make('Page SEO & Metadata'),
                ]),

                Group::make()->columnSpan(['lg' => 1])->schema([
                    Section::make('Attributes & Status')
                        ->schema([
                            Select::make('status')
                                ->options(PageStatus::class)
                                ->default(PageStatus::Draft)
                                ->required(),

                            Select::make('template')
                                ->options(PageTemplate::class)
                                ->default(PageTemplate::Default)
                                ->required(),

                            DateTimePicker::make('published_at')
                                ->label('Publish Date'),

                            TextInput::make('sort_order')
                                ->numeric()
                                ->default(0),
                        ]),
                ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->reorderable('sort_order')
            ->columns([
                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('slug')
                    ->searchable()
                    ->color('gray')
                    ->prefix('/'),

                TextColumn::make('template')
                    ->badge()
                    ->color('info'),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->dateTime('M j, Y')
                    ->placeholder('Draft')
                    ->sortable(),

                TextColumn::make('updated_at')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('sort_order', 'asc')
            ->filters([
                SelectFilter::make('status')
                    ->options(PageStatus::class),

                SelectFilter::make('template')
                    ->options(PageTemplate::class),

                TrashedFilter::make(),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->modalHeading(fn (Page $record) => "Page Preview: {$record->title}")
                    ->modalWidth('4xl')
                    ->infolist([
                        TextEntry::make('title')
                            ->size(TextEntrySize::Large)
                            ->weight('bold'),
                        TextEntry::make('template')
                            ->badge(),
                        TextEntry::make('content')
                            ->html(),
                    ]),

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
            'index' => Pages\ListPages::route('/'),
            'create' => Pages\CreatePage::route('/create'),
            'edit' => Pages\EditPage::route('/{record}/edit'),
        ];
    }
}
