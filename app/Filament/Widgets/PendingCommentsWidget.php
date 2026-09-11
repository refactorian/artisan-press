<?php

namespace App\Filament\Widgets;

use App\Models\Comment;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingCommentsWidget extends BaseWidget
{
    protected static ?int $sort = 3;

    protected int|string|array $columnSpan = 'full';

    protected static ?string $heading = 'Comments Requiring Moderation';

    public function table(Table $table): Table
    {
        return $table
            ->query(Comment::query()->pending()->latest())
            ->columns([
                TextColumn::make('author_name')
                    ->label('Author')
                    ->weight('bold')
                    ->description(fn (Comment $record) => $record->author_email),

                TextColumn::make('post.title')
                    ->label('Article')
                    ->limit(35)
                    ->color('info'),

                TextColumn::make('content')
                    ->label('Comment Body')
                    ->limit(90)
                    ->wrap(),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->since(),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->button()
                    ->size('sm')
                    ->action(function (Comment $record) {
                        $record->approve();
                        Notification::make()->title('Comment approved')->success()->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->button()
                    ->size('sm')
                    ->action(function (Comment $record) {
                        $record->reject();
                        Notification::make()->title('Comment rejected')->danger()->send();
                    }),

                Action::make('spam')
                    ->label('Spam')
                    ->icon('heroicon-m-no-symbol')
                    ->color('gray')
                    ->button()
                    ->size('sm')
                    ->action(function (Comment $record) {
                        $record->markAsSpam();
                        Notification::make()->title('Marked as spam')->send();
                    }),
            ])
            ->paginated([5]);
    }
}
