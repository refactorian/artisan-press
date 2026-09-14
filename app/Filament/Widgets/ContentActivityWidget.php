<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\ActivityLogResource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Spatie\Activitylog\Models\Activity;

class ContentActivityWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected static ?string $heading = 'Recent Audit Activity';

    public function table(Table $table): Table
    {
        return $table
            ->query(Activity::query()->with('causer')->latest()->limit(5))
            ->headerActions([
                Action::make('all_activity')
                    ->label('View Audit Log')
                    ->icon('heroicon-m-arrow-right')
                    ->iconPosition('after')
                    ->color('gray')
                    ->url(fn (): string => ActivityLogResource::getUrl('index')),
            ])
            ->emptyStateHeading('No Audit Logs Recorded')
            ->emptyStateDescription('Editorial actions and model changes will appear here.')
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
                    ->label('Action')
                    ->limit(28),

                TextColumn::make('created_at')
                    ->label('When')
                    ->since()
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}
