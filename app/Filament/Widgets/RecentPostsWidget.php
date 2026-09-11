<?php

namespace App\Filament\Widgets;

use App\Enums\PostStatus;
use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Tables;
use Filament\Tables\Columns\SpatieMediaLibraryImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Str;

class RecentPostsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Recent Posts';

    public function table(Table $table): Table
    {
        return $table
            ->query(
                Post::query()
                    ->with(['author', 'categories'])
                    ->latest()
                    ->limit(8)
            )
            ->columns([
                SpatieMediaLibraryImageColumn::make('featured_image')
                    ->label('')
                    ->collection('featured_image')
                    ->conversion('thumb')
                    ->width(60)
                    ->height(40),

                TextColumn::make('title')
                    ->weight('semibold')
                    ->description(fn (Post $record): ?string => $record->excerpt ? Str::limit($record->excerpt, 70) : null)
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record])),

                TextColumn::make('author.name')
                    ->label('Author'),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('published_at')
                    ->label('Published')
                    ->dateTime('M j, Y')
                    ->placeholder('—'),

                TextColumn::make('view_count')
                    ->label('Views')
                    ->numeric()
                    ->alignEnd(),
            ])
            ->actions([
                Tables\Actions\Action::make('edit')
                    ->url(fn (Post $record): string => PostResource::getUrl('edit', ['record' => $record]))
                    ->icon('heroicon-m-pencil-square'),
            ])
            ->paginated(false);
    }
}
