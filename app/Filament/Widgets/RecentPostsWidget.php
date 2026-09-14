<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Str;

class RecentPostsWidget extends BaseWidget
{
    protected static ?int $sort = 1;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 2,
    ];

    protected static ?string $heading = 'Recent Editorial Publications';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()
                    ->with(['author', 'categories', 'media'])
                    ->latest('updated_at')
                    ->limit(6)
            )
            ->headerActions([
                Action::make('all_posts')
                    ->label('View All')
                    ->icon('heroicon-m-arrow-right')
                    ->iconPosition('after')
                    ->color('gray')
                    ->url(fn (): string => PostResource::getUrl('index')),
            ])
            ->columns([
                SpatieMediaLibraryImageColumn::make('featured_image')
                    ->label('')
                    ->collection('featured_image')
                    ->conversion('thumb')
                    ->width(48)
                    ->height(36)
                    ->circular(false)
                    ->extraImgAttributes(['class' => 'rounded-lg object-cover shadow-xs']),

                TextColumn::make('title')
                    ->label('Title')
                    ->weight('bold')
                    ->description(fn (Post $record): ?string => $record->excerpt ? Str::limit($record->excerpt, 65) : null)
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record]))
                    ->wrap(),

                TextColumn::make('categories.name')
                    ->label('Category')
                    ->badge()
                    ->color('primary')
                    ->limitList(1),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('view_count')
                    ->label('Views')
                    ->numeric()
                    ->icon('heroicon-m-eye')
                    ->alignEnd()
                    ->sortable(),

                TextColumn::make('published_at')
                    ->label('Date')
                    ->dateTime('M j, Y')
                    ->placeholder('Draft')
                    ->color('gray')
                    ->alignEnd(),
            ])
            ->actions([
                Action::make('view_live')
                    ->label('')
                    ->icon('heroicon-m-arrow-top-right-on-square')
                    ->tooltip('View live article on public site')
                    ->color('gray')
                    ->url(fn (Post $record): string => route('posts.show', $record))
                    ->openUrlInNewTab()
                    ->visible(fn (Post $record): bool => $record->isPublished()),

                Action::make('edit')
                    ->label('')
                    ->icon('heroicon-m-pencil-square')
                    ->tooltip('Edit article in CMS')
                    ->color('primary')
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record])),
            ])
            ->paginated(false);
    }
}
