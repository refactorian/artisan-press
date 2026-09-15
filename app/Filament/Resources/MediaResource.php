<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MediaResource\Pages;
use App\Models\MediaAsset;
use App\Models\MediaLibrary;
use App\Models\Post;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\ViewField;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Spatie\MediaLibrary\MediaCollections\Models\Media;

class MediaResource extends Resource
{
    protected static ?string $model = MediaAsset::class;

    protected static ?string $navigationIcon = 'heroicon-o-photo';

    protected static ?string $navigationGroup = 'Content';

    protected static ?string $navigationLabel = 'Media Library';

    protected static ?string $pluralModelLabel = 'Media Assets';

    protected static ?int $navigationSort = 4;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(3)->schema([
                Section::make('Asset Details')
                    ->columnSpan(1)
                    ->schema([
                        ViewField::make('preview')
                            ->label('')
                            ->view('filament.components.media-preview')
                            ->dehydrated(false),

                        TextInput::make('file_name')
                            ->label('File Name')
                            ->disabled(),

                        TextInput::make('mime_type')
                            ->label('MIME Type')
                            ->disabled(),

                        TextInput::make('collection_name')
                            ->label('Collection')
                            ->disabled(),
                    ]),

                Section::make('Image Metadata & Credits')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('name')
                            ->label('Display Title')
                            ->required(),

                        TextInput::make('alt_text')
                            ->label('Alt Text (Accessibility & SEO)')
                            ->helperText('Describes the image for screen readers and search engines.')
                            ->maxLength(255)
                            ->dehydrated(false) // stored in custom_properties via mutator
                            ->afterStateHydrated(function ($component, MediaAsset $record) {
                                $component->state($record->getCustomProperty('alt_text'));
                            }),

                        Textarea::make('caption')
                            ->label('Caption')
                            ->rows(3)
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, MediaAsset $record) {
                                $component->state($record->getCustomProperty('caption'));
                            }),

                        TextInput::make('credits')
                            ->label('Credits / Photographer')
                            ->placeholder('e.g. Photo by John Doe / Unsplash')
                            ->maxLength(255)
                            ->dehydrated(false)
                            ->afterStateHydrated(function ($component, MediaAsset $record) {
                                $component->state($record->getCustomProperty('credits'));
                            }),
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('preview')
                    ->label('Preview')
                    ->state(fn (MediaAsset $record) => $record->getUrl())
                    ->square()
                    ->size(70),

                TextColumn::make('name')
                    ->label('Title / Name')
                    ->searchable()
                    ->sortable()
                    ->weight('bold')
                    ->description(fn (MediaAsset $record) => $record->file_name),

                TextColumn::make('alt_text')
                    ->label('Alt Text')
                    ->getStateUsing(fn (MediaAsset $record) => $record->getCustomProperty('alt_text'))
                    ->limit(30)
                    ->placeholder('None')
                    ->color('gray'),

                TextColumn::make('credits')
                    ->label('Credit')
                    ->getStateUsing(fn (MediaAsset $record) => $record->getCustomProperty('credits'))
                    ->limit(25)
                    ->placeholder('—'),

                TextColumn::make('collection_name')
                    ->label('Collection')
                    ->badge(),

                TextColumn::make('formatted_size')
                    ->label('Size')
                    ->getStateUsing(fn (MediaAsset $record) => $record->formatted_size)
                    ->alignEnd(),

                TextColumn::make('created_at')
                    ->label('Uploaded')
                    ->dateTime('M j, Y')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('collection_name')
                    ->label('Collection')
                    ->options(fn () => Media::query()
                        ->distinct()
                        ->pluck('collection_name', 'collection_name')
                        ->toArray()
                    ),

                SelectFilter::make('source')
                    ->label('Source')
                    ->options([
                        'library' => 'Media Library (standalone)',
                        'posts' => 'Post attachments',
                    ])
                    ->query(function ($query, array $data) {
                        if ($data['value'] === 'library') {
                            $query->where('model_type', MediaLibrary::class);
                        } elseif ($data['value'] === 'posts') {
                            $query->where('model_type', Post::class);
                        }
                    }),
            ])
            ->actions([
                Action::make('copy_url')
                    ->label('Get Embed Code')
                    ->icon('heroicon-m-code-bracket')
                    ->modalHeading('Media Embed Links')
                    ->form([
                        TextInput::make('direct_url')
                            ->label('Direct Image URL')
                            ->default(fn (MediaAsset $record) => $record->getUrl())
                            ->extraInputAttributes(['readonly' => true]),

                        TextInput::make('markdown_code')
                            ->label('Markdown Embed')
                            ->default(fn (MediaAsset $record) => '!['.($record->getCustomProperty('alt_text') ?? '').']('.$record->getUrl().')')
                            ->extraInputAttributes(['readonly' => true]),

                        Textarea::make('html_code')
                            ->label('HTML Image Tag')
                            ->default(fn (MediaAsset $record) => '<img src="'.$record->getUrl().'" alt="'.htmlspecialchars($record->getCustomProperty('alt_text') ?? '').'" loading="lazy" />')
                            ->extraInputAttributes(['readonly' => true]),
                    ]),

                Tables\Actions\EditAction::make()
                    ->using(function (MediaAsset $record, array $data) {
                        $record->name = $data['name'];
                        $record->setCustomProperty('alt_text', $data['alt_text'] ?? null);
                        $record->setCustomProperty('caption', $data['caption'] ?? null);
                        $record->setCustomProperty('credits', $data['credits'] ?? null);
                        $record->save();

                        return $record;
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    /**
     * Show ALL media records across all model types (posts, pages, standalone library).
     */
    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()->orderBy('created_at', 'desc');
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMedia::route('/'),
            'edit' => Pages\EditMedia::route('/{record}/edit'),
        ];
    }
}
