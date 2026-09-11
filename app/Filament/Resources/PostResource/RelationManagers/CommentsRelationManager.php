<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use App\Enums\CommentStatus;
use App\Models\Comment;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CommentsRelationManager extends RelationManager
{
    protected static string $relationship = 'comments';

    protected static ?string $recordTitleAttribute = 'content';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\Textarea::make('content')
                ->required()
                ->rows(4)
                ->columnSpanFull(),

            Forms\Components\Select::make('status')
                ->options(CommentStatus::class)
                ->default(CommentStatus::Pending)
                ->required(),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author_name')
                    ->label('Author')
                    ->weight('bold')
                    ->description(fn (Comment $record) => $record->author_email),

                TextColumn::make('content')
                    ->label('Comment')
                    ->limit(60)
                    ->wrap(),

                TextColumn::make('status')
                    ->badge(),

                TextColumn::make('parent.author_name')
                    ->label('Replying To')
                    ->placeholder('Top-level')
                    ->badge()
                    ->color('gray'),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(CommentStatus::class),
            ])
            ->actions([
                Action::make('approve')
                    ->label('Approve')
                    ->icon('heroicon-m-check-circle')
                    ->color('success')
                    ->visible(fn (Comment $record) => $record->status !== CommentStatus::Approved)
                    ->action(function (Comment $record) {
                        $record->approve();
                        Notification::make()->title('Comment approved')->success()->send();
                    }),

                Action::make('reject')
                    ->label('Reject')
                    ->icon('heroicon-m-x-circle')
                    ->color('danger')
                    ->visible(fn (Comment $record) => $record->status !== CommentStatus::Rejected)
                    ->action(function (Comment $record) {
                        $record->reject();
                        Notification::make()->title('Comment rejected')->danger()->send();
                    }),

                Action::make('spam')
                    ->label('Spam')
                    ->icon('heroicon-m-no-symbol')
                    ->color('gray')
                    ->visible(fn (Comment $record) => $record->status !== CommentStatus::Spam)
                    ->action(function (Comment $record) {
                        $record->markAsSpam();
                        Notification::make()->title('Marked as spam')->send();
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('bulk_approve')
                        ->label('Approve Selected')
                        ->icon('heroicon-m-check-circle')
                        ->color('success')
                        ->action(fn (Collection $records) => $records->each->approve())
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('bulk_reject')
                        ->label('Reject Selected')
                        ->icon('heroicon-m-x-circle')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->reject())
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
