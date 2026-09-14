<?php

namespace App\Filament\Widgets;

use App\Models\User;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class AuthorPerformanceWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Author Performance Leaderboard')
            ->description('Content creators ranked by readership volume.')
            ->query(
                User::where('is_active', true)
                    ->whereHas('posts', fn ($q) => $q->published())
                    ->withCount(['posts' => fn ($q) => $q->published()])
                    ->withSum(['posts' => fn ($q) => $q->published()], 'view_count')
                    ->orderByDesc('posts_view_count_sum')
                    ->limit(5)
            )
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->circular()
                    ->defaultImageUrl(asset('images/avatar-default.png')),

                TextColumn::make('name')
                    ->label('Author')
                    ->weight('semibold'),

                TextColumn::make('posts_count')
                    ->label('Articles')
                    ->alignEnd(),

                TextColumn::make('posts_view_count_sum')
                    ->label('Total Views')
                    ->numeric()
                    ->badge()
                    ->color('primary')
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}
