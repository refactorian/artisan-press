<?php

namespace App\Filament\Widgets;

use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Activitylog\Models\Activity;

class ContentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Recent Content & Audit Activity';

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->latest()->limit(5))
            ->columns([
                TextColumn::make('causer.name')
                    ->label('User')
                    ->placeholder('System')
                    ->weight('bold'),

                TextColumn::make('event')
                    ->badge()
                    ->color(fn (string $state): string => match ($state) {
                        'created' => 'success',
                        'updated' => 'info',
                        'deleted' => 'danger',
                        default => 'gray',
                    }),

                TextColumn::make('description')
                    ->label('Activity')
                    ->limit(30),

                TextColumn::make('created_at')
                    ->label('When')
                    ->since(),
            ])
            ->paginated(false);
    }
}
