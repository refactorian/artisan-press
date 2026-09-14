<?php

namespace App\Filament\Widgets;

use App\Models\SearchLog;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Support\Facades\DB;

class SearchTrendsWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    public function table(Table $table): Table
    {
        return $table
            ->heading('Search Queries & Trends')
            ->description('What readers are looking for on your site.')
            ->query(
                SearchLog::query()
                    ->select('query', DB::raw('count(*) as count'), DB::raw('max(results_count) as max_results'), DB::raw('max(created_at) as last_searched_at'))
                    ->groupBy('query')
                    ->orderByDesc('count')
                    ->limit(8)
            )
            ->columns([
                TextColumn::make('query')
                    ->label('Search Keyword')
                    ->weight('semibold'),

                TextColumn::make('count')
                    ->label('Searches')
                    ->badge()
                    ->color('info')
                    ->alignEnd(),

                TextColumn::make('max_results')
                    ->label('Results')
                    ->badge()
                    ->color(fn ($state) => (int) $state === 0 ? 'danger' : 'success')
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}
