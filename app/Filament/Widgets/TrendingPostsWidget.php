<?php

namespace App\Filament\Widgets;

use App\Models\Post;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class TrendingPostsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = 'full';

    public function table(Table $table): Table
    {
        return $table
            ->heading('Trending Articles (Past 7 Days)')
            ->description('Posts with the highest reader velocity and discrete views over the last week.')
            ->query(
                Post::trending(7)->limit(10)
            )
            ->columns([
                TextColumn::make('title')
                    ->label('Article Title')
                    ->weight('bold')
                    ->searchable()
                    ->url(fn (Post $record) => url("/admin/posts/{$record->id}/edit")),

                TextColumn::make('author.name')
                    ->label('Author')
                    ->icon('heroicon-m-user')
                    ->color('gray'),

                TextColumn::make('reading_time')
                    ->label('Reading Time')
                    ->suffix(' min')
                    ->alignCenter(),

                TextColumn::make('views_count')
                    ->label('7-Day Views')
                    ->badge()
                    ->color('success')
                    ->alignEnd(),

                TextColumn::make('view_count')
                    ->label('Total Views')
                    ->numeric()
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}
