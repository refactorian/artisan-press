<?php

namespace App\Filament\Widgets;

use App\Filament\Resources\CommentResource;
use App\Models\Comment;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;

class PendingCommentsWidget extends BaseWidget
{
    protected static ?int $sort = 2;

    protected int|string|array $columnSpan = [
        'default' => 'full',
        'xl' => 1,
    ];

    protected static ?string $heading = 'Comments Requiring Moderation';

    public function table(Table $table): Table
    {
        return $table
            ->query(Comment::query()->pending()->with('post')->latest())
            ->headerActions([
                Action::make('all_comments')
                    ->label('View All')
                    ->icon('heroicon-m-arrow-right')
                    ->iconPosition('after')
                    ->color('gray')
                    ->url(fn (): string => CommentResource::getUrl('index')),
            ])
            ->emptyStateHeading('Moderation Inbox Zero')
            ->emptyStateDescription('All reader comments and questions have been reviewed.')
            ->emptyStateIcon('heroicon-o-check-badge')
            ->columns([
                TextColumn::make('author_name')
                    ->label('Commenter')
                    ->weight('bold')
                    ->description(fn (Comment $record): string => $record->author_email ?? 'Guest')
                    ->wrap(),

                TextColumn::make('content')
                    ->label('Snippet')
                    ->limit(60)
                    ->wrap()
                    ->description(fn (Comment $record): string => 'On: '.($record->post?->title ?? 'Post')),

                TextColumn::make('created_at')
                    ->label('When')
                    ->since()
                    ->alignEnd(),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check')
                    ->color('success')
                    ->button()
                    ->size('xs')
                    ->action(function (Comment $record) {
                        $record->approve();
                        Notification::make()->title('Comment approved')->success()->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-mark')
                    ->color('danger')
                    ->button()
                    ->size('xs')
                    ->action(function (Comment $record) {
                        $record->reject();
                        Notification::make()->title('Comment rejected')->danger()->send();
                    }),
            ])
            ->paginated(false);
    }
}
