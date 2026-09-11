<?php

namespace App\Filament\Resources;

use App\Enums\CommentStatus;
use App\Filament\Resources\CommentResource\Pages;
use App\Models\Comment;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class CommentResource extends Resource
{
    protected static ?string $model = Comment::class;

    protected static ?string $navigationIcon = 'heroicon-o-chat-bubble-left-right';

    protected static ?string $navigationGroup = 'Engagement';

    protected static ?int $navigationSort = 1;

    protected static ?string $recordTitleAttribute = 'content';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(3)->schema([
                Section::make('Comment Information')
                    ->columnSpan(2)
                    ->schema([
                        Select::make('post_id')
                            ->label('Article / Post')
                            ->relationship('post', 'title')
                            ->searchable()
                            ->preload()
                            ->required(),

                        Select::make('parent_id')
                            ->label('In Reply To (Parent Comment)')
                            ->relationship('parent', 'content')
                            ->searchable()
                            ->placeholder('Top-level comment (no parent)'),

                        Textarea::make('content')
                            ->label('Comment Content')
                            ->rows(6)
                            ->required(),
                    ]),

                Section::make('Author & Moderation Details')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->label('Moderation Status')
                            ->options(CommentStatus::class)
                            ->required()
                            ->native(false),

                        Select::make('user_id')
                            ->label('Registered User')
                            ->relationship('user', 'name')
                            ->searchable()
                            ->placeholder('Guest user (unregistered)'),

                        TextInput::make('guest_name')
                            ->label('Guest Name')
                            ->maxLength(255),

                        TextInput::make('guest_email')
                            ->label('Guest Email')
                            ->email()
                            ->maxLength(255),

                        TextInput::make('guest_website')
                            ->label('Guest Website')
                            ->url()
                            ->maxLength(255),

                        TextInput::make('ip_address')
                            ->label('IP Address')
                            ->disabled(),
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('author_name')
                    ->label('Author')
                    ->searchable(['guest_name', 'guest_email'])
                    ->weight('bold')
                    ->description(fn (Comment $record) => $record->author_email),

                TextColumn::make('content')
                    ->label('Comment')
                    ->searchable()
                    ->limit(70)
                    ->wrap(),

                TextColumn::make('post.title')
                    ->label('Article')
                    ->searchable()
                    ->limit(35)
                    ->color('info'),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('replies_count')
                    ->counts('replies')
                    ->label('Replies')
                    ->badge()
                    ->color('gray')
                    ->alignCenter(),

                TextColumn::make('created_at')
                    ->label('Submitted')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(CommentStatus::class)
                    ->default(CommentStatus::Pending->value),

                SelectFilter::make('post_id')
                    ->label('Post')
                    ->relationship('post', 'title')
                    ->searchable()
                    ->preload(),
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

                Action::make('reply')
                    ->label('Reply')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('info')
                    ->form([
                        Textarea::make('reply_content')
                            ->label('Admin Reply')
                            ->required()
                            ->rows(4),
                    ])
                    ->action(function (array $data, Comment $record) {
                        Comment::create([
                            'post_id' => $record->post_id,
                            'user_id' => auth()->id(),
                            'parent_id' => $record->id,
                            'content' => $data['reply_content'],
                            'status' => CommentStatus::Approved,
                            'moderated_by' => auth()->id(),
                            'moderated_at' => now(),
                        ]);

                        Notification::make()
                            ->title('Reply published successfully')
                            ->success()
                            ->send();
                    }),

                Tables\Actions\EditAction::make(),
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

                    BulkAction::make('bulk_spam')
                        ->label('Mark as Spam')
                        ->icon('heroicon-m-no-symbol')
                        ->color('gray')
                        ->action(fn (Collection $records) => $records->each->markAsSpam())
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListComments::route('/'),
            'edit' => Pages\EditComment::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', CommentStatus::Pending)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }

    public static function getNavigationBadgeTooltip(): ?string
    {
        return 'Comments pending moderation';
    }
}
