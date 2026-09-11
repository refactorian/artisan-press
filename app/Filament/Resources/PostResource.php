<?php

namespace App\Filament\Resources;

use App\Enums\PostStatus;
use App\Filament\Forms\Components\ContentBlocksBuilder;
use App\Filament\Resources\PostResource\Pages;
use App\Filament\Resources\PostResource\RelationManagers\CommentsRelationManager;
use App\Filament\Resources\PostResource\RelationManagers\RevisionsRelationManager;
use App\Models\Post;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Forms\Get;
use Filament\Forms\Set;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Actions\EditAction;
use Filament\Tables\Actions\ForceDeleteBulkAction;
use Filament\Tables\Actions\RestoreBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\Filter;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Filters\TrashedFilter;
use Filament\Tables\Table;
use FilamentTiptapEditor\TiptapEditor;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Illuminate\Support\Str;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-o-document-text';

    protected static ?string $navigationGroup = 'Content';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'title';

    public static function form(Form $form): Form
    {
        return $form->schema([
            // ── Left/Main Column ───────────────────────────────────────────────
            Grid::make(['default' => 1, 'lg' => 3])->schema([

                Group::make()->columnSpan(['lg' => 2])->schema([

                    Section::make('Post Content')
                        ->schema([
                            TextInput::make('title')
                                ->label('Title')
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
                                ->label('Slug')
                                ->required()
                                ->maxLength(255)
                                ->unique(Post::class, 'slug', ignoreRecord: true)
                                ->helperText('Auto-generated from title. You can override it manually.')
                                ->prefix('/'),

                            Textarea::make('excerpt')
                                ->label('Excerpt')
                                ->rows(3)
                                ->maxLength(500)
                                ->helperText('A short summary shown in post listings.'),

                            TiptapEditor::make('content')
                                ->label('Main Article Body')
                                ->profile('default')
                                ->columnSpanFull(),
                        ]),

                    Section::make('Dynamic Content Blocks')
                        ->description('Add modular callouts, quotes, code snippets, galleries, video embeds, and CTAs.')
                        ->collapsible()
                        ->schema([
                            ContentBlocksBuilder::make('content_blocks'),
                        ]),

                    Section::make('Featured Image')
                        ->schema([
                            SpatieMediaLibraryFileUpload::make('featured_image')
                                ->label('Featured Image')
                                ->collection('featured_image')
                                ->image()
                                ->imageResizeMode('cover')
                                ->imageCropAspectRatio('16:9')
                                ->imageResizeTargetWidth('1200')
                                ->imageResizeTargetHeight('675')
                                ->maxSize(5120)
                                ->helperText('Recommended: 1200×675px (16:9). Max 5MB.'),
                        ]),

                    Section::make('SEO & Social Sharing')
                        ->collapsed()
                        ->schema([
                            TextInput::make('seo_title')
                                ->label('SEO Title')
                                ->maxLength(70)
                                ->helperText('Leave blank to use the post title. Recommended: 50–70 characters.'),

                            Textarea::make('seo_description')
                                ->label('SEO Description')
                                ->rows(3)
                                ->maxLength(160)
                                ->helperText('Recommended: 120–160 characters.'),

                            TextInput::make('canonical_url')
                                ->label('Canonical URL')
                                ->url()
                                ->maxLength(2048)
                                ->helperText('Leave blank to use the default post URL.'),

                            Grid::make(2)->schema([
                                Toggle::make('noindex')
                                    ->label('Noindex')
                                    ->helperText('Exclude from search engine results.'),

                                Toggle::make('nofollow')
                                    ->label('Nofollow')
                                    ->helperText('Do not follow links in this post.'),
                            ]),
                        ]),
                ]),

                // ── Right Sidebar ──────────────────────────────────────────────
                Group::make()->columnSpan(['lg' => 1])->schema([

                    Section::make('Publishing & Status')
                        ->schema([
                            Select::make('status')
                                ->label('Status')
                                ->options(PostStatus::class)
                                ->default(PostStatus::Draft)
                                ->required()
                                ->live()
                                ->native(false),

                            DateTimePicker::make('published_at')
                                ->label('Publish Date')
                                ->helperText('Required for scheduled posts. Leave blank to publish immediately.')
                                ->visible(fn (Get $get) => $get('status') === PostStatus::Scheduled->value || $get('status') === PostStatus::Published->value)
                                ->native(false),

                            Select::make('user_id')
                                ->label('Primary Author')
                                ->relationship('author', 'name')
                                ->searchable()
                                ->preload()
                                ->required()
                                ->default(fn () => auth()->id()),

                            Select::make('contributors')
                                ->label('Co-Authors & Contributors')
                                ->relationship('contributors', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->helperText('Optional contributors collaborating on this post.'),
                        ]),

                    Section::make('Featured & Hero Display')
                        ->schema([
                            Toggle::make('is_hero')
                                ->label('Mark as Hero Post')
                                ->helperText('Displays in the primary hero slot on the blog homepage.'),

                            Toggle::make('is_featured')
                                ->label('Featured Post')
                                ->helperText('Pin in featured section and highlight badge.'),

                            TextInput::make('featured_order')
                                ->label('Featured Sort Order')
                                ->numeric()
                                ->default(0)
                                ->helperText('Lower numbers appear first.'),

                            TextInput::make('sort_order')
                                ->label('General Sort Order')
                                ->numeric()
                                ->default(0),
                        ]),

                    Section::make('Series & Collections')
                        ->schema([
                            Select::make('series_id')
                                ->label('Part of Series')
                                ->relationship('series', 'name')
                                ->searchable()
                                ->preload()
                                ->placeholder('Select a series (optional)'),

                            TextInput::make('series_order')
                                ->label('Order in Series')
                                ->numeric()
                                ->placeholder('e.g. 1, 2, 3')
                                ->helperText('Defines position within the series collection.'),
                        ]),

                    Section::make('Taxonomy')
                        ->schema([
                            Select::make('categories')
                                ->label('Categories')
                                ->relationship('categories', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')->required()->maxLength(255),
                                    TextInput::make('slug')->maxLength(255),
                                ]),

                            Select::make('tags')
                                ->label('Tags')
                                ->relationship('tags', 'name')
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->createOptionForm([
                                    TextInput::make('name')->required()->maxLength(255),
                                    TextInput::make('slug')->maxLength(255),
                                ]),
                        ]),

                    Section::make('Related Posts')
                        ->schema([
                            Select::make('relatedPosts')
                                ->label('Curated Related Posts')
                                ->relationship('relatedPosts', 'title', fn (Builder $query, ?Post $record) => $record ? $query->where('id', '!=', $record->id) : $query)
                                ->multiple()
                                ->searchable()
                                ->preload()
                                ->helperText('Manually associate related articles.'),
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
                SpatieMediaLibraryImageColumn::make('featured_image')
                    ->label('')
                    ->collection('featured_image')
                    ->conversion('thumb')
                    ->circular(false)
                    ->width(80)
                    ->height(50),

                TextColumn::make('title')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->wrap()
                    ->description(fn (Post $record): ?string => $record->excerpt ? Str::limit($record->excerpt, 80) : null),

                TextColumn::make('author.name')
                    ->label('Author')
                    ->sortable()
                    ->searchable(),

                TextColumn::make('series.name')
                    ->label('Series')
                    ->badge()
                    ->color('info')
                    ->placeholder('None')
                    ->toggleable(),

                IconColumn::make('is_hero')
                    ->label('Hero')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->alignCenter(),

                IconColumn::make('is_featured')
                    ->label('Featured')
                    ->boolean()
                    ->trueIcon('heroicon-s-sparkles')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('success')
                    ->alignCenter(),

                TextColumn::make('categories.name')
                    ->label('Categories')
                    ->badge()
                    ->separator(','),

                TextColumn::make('status')
                    ->label('Status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->placeholder('Not published'),

                TextColumn::make('view_count')
                    ->label('Views')
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('comments_count')
                    ->counts('comments')
                    ->label('Comments')
                    ->sortable()
                    ->alignEnd(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(PostStatus::class),

                TernaryFilter::make('is_featured')
                    ->label('Featured Posts'),

                TernaryFilter::make('is_hero')
                    ->label('Hero Posts'),

                SelectFilter::make('series_id')
                    ->label('Series')
                    ->relationship('series', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('user_id')
                    ->label('Author')
                    ->relationship('author', 'name')
                    ->searchable()
                    ->preload(),

                SelectFilter::make('categories')
                    ->relationship('categories', 'name')
                    ->multiple()
                    ->preload(),

                Filter::make('published_at')
                    ->label('Published This Month')
                    ->query(fn (Builder $query) => $query->whereMonth('published_at', now()->month)),

                TrashedFilter::make(),
            ])
            ->actions([
                Action::make('preview')
                    ->label('Preview')
                    ->icon('heroicon-m-eye')
                    ->color('info')
                    ->modalHeading(fn (Post $record) => "Preview: {$record->title}")
                    ->modalWidth('4xl')
                    ->infolist([
                        TextEntry::make('title')
                            ->size(TextEntrySize::Large)
                            ->weight('bold'),
                        TextEntry::make('author.name')
                            ->label('Byline')
                            ->formatStateUsing(fn ($state, Post $record) => "By {$state} • Status: {$record->status->getLabel()} • Views: {$record->view_count}"),
                        TextEntry::make('excerpt')
                            ->label('Excerpt')
                            ->color('gray')
                            ->visible(fn (Post $record) => ! empty($record->excerpt)),
                        TextEntry::make('content')
                            ->label('Content Body')
                            ->html(),
                    ]),

                EditAction::make(),
                Tables\Actions\DeleteAction::make(),
                Tables\Actions\RestoreAction::make(),
                Tables\Actions\ForceDeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    BulkAction::make('publish')
                        ->label('Publish Selected')
                        ->icon('heroicon-o-check-circle')
                        ->color('success')
                        ->action(function (Collection $records): void {
                            $records->each(fn (Post $post) => $post->update([
                                'status' => PostStatus::Published,
                                'published_at' => $post->published_at ?? now(),
                            ]));
                        })
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('toggle_featured')
                        ->label('Mark as Featured')
                        ->icon('heroicon-o-sparkles')
                        ->color('warning')
                        ->action(fn (Collection $records) => $records->each->update(['is_featured' => true]))
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('draft')
                        ->label('Move to Draft')
                        ->icon('heroicon-o-pencil')
                        ->color('gray')
                        ->action(fn (Collection $records) => $records->each->update(['status' => PostStatus::Draft]))
                        ->requiresConfirmation()
                        ->deselectRecordsAfterCompletion(),

                    DeleteBulkAction::make(),
                    RestoreBulkAction::make(),
                    ForceDeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]));
    }

    public static function getRelations(): array
    {
        return [
            RevisionsRelationManager::class,
            CommentsRelationManager::class,
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        return parent::getEloquentQuery()
            ->withoutGlobalScopes([
                SoftDeletingScope::class,
            ]);
    }

    public static function getNavigationBadge(): ?string
    {
        return (string) static::getModel()::where('status', PostStatus::Draft)->count();
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Draft posts';
    }
}
