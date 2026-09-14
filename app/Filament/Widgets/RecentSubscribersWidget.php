<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\SubscriberResource;
use App\Models\Subscriber;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentSubscribersWidget extends BaseWidget
{
    protected static ?int $sort = 5;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected static ?string $heading = 'Recent Newsletter Subscribers';

    public function table(Table $table): Table
    {
        return $table
            ->query(Subscriber::query()->latest()->limit(5))
            ->headerActions([
                Action::make('all_subscribers')
                    ->label('View All')
                    ->icon('heroicon-m-arrow-right')
                    ->iconPosition('after')
                    ->color('gray')
                    ->url(fn (): string => SubscriberResource::getUrl('index')),
            ])
            ->emptyStateHeading('No Subscribers Yet')
            ->emptyStateDescription('New newsletter signups will be logged here.')
            ->columns([
                TextColumn::make('email')
                    ->label('Subscriber')
                    ->weight('bold')
                    ->description(fn (Subscriber $record): ?string => $record->name ?: null)
                    ->limit(24),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->since()
                    ->alignEnd(),
            ])
            ->paginated(false);
    }
}
