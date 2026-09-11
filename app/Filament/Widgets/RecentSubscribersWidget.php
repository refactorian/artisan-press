<?php

namespace App\Filament\Widgets;

use App\Models\Subscriber;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class RecentSubscribersWidget extends BaseWidget
{
    protected static ?int $sort = 4;

    protected int|string|array $columnSpan = 1;

    protected static ?string $heading = 'Recent Newsletter Subscribers';

    public function table(Table $table): Table
    {
        return $table
            ->query(Subscriber::query()->latest()->limit(5))
            ->columns([
                TextColumn::make('email')
                    ->label('Email')
                    ->weight('bold')
                    ->limit(25),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->since(),
            ])
            ->paginated(false);
    }
}
