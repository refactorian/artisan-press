<?php

namespace App\Filament\Resources;

use App\Enums\MessageStatus;
use App\Filament\Resources\ContactMessageResource\Pages;
use App\Models\ContactMessage;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Notifications\Notification;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\Action;
use Filament\Tables\Actions\BulkAction;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Collection;

class ContactMessageResource extends Resource
{
    protected static ?string $model = ContactMessage::class;

    protected static ?string $navigationIcon = 'heroicon-o-inbox';

    protected static ?string $navigationGroup = 'Engagement';

    protected static ?int $navigationSort = 3;

    protected static ?string $recordTitleAttribute = 'subject';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Grid::make(3)->schema([
                Section::make('Message Details')
                    ->columnSpan(2)
                    ->schema([
                        TextInput::make('name')->disabled(),
                        TextInput::make('email')->email()->disabled(),
                        TextInput::make('subject')->disabled(),
                        Textarea::make('message')->rows(8)->disabled(),
                    ]),

                Section::make('Status & Follow-up')
                    ->columnSpan(1)
                    ->schema([
                        Select::make('status')
                            ->options(MessageStatus::class)
                            ->required(),

                        Textarea::make('reply_notes')
                            ->label('Internal Reply Notes')
                            ->rows(5)
                            ->placeholder('Record reply or follow-up details...'),
                    ]),
            ]),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('name')
                    ->searchable()
                    ->weight('bold')
                    ->description(fn (ContactMessage $record) => $record->email),

                TextColumn::make('subject')
                    ->searchable()
                    ->limit(40),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('created_at')
                    ->label('Received')
                    ->dateTime('M j, Y H:i')
                    ->sortable(),
            ])
            ->defaultSort('created_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(MessageStatus::class)
                    ->default(MessageStatus::Unread->value),
            ])
            ->actions([
                Action::make('read_message')
                    ->label('Read')
                    ->icon('heroicon-m-envelope-open')
                    ->color('info')
                    ->modalHeading(fn (ContactMessage $record) => "From: {$record->name} ({$record->email})")
                    ->infolist([
                        TextEntry::make('subject')
                            ->weight('bold')
                            ->size(TextEntrySize::Large),
                        TextEntry::make('message')
                            ->label('Message Content'),
                        TextEntry::make('created_at')
                            ->label('Sent Date')
                            ->dateTime('F j, Y g:i A'),
                    ])
                    ->action(function (ContactMessage $record) {
                        $record->markAsRead();
                    }),

                Action::make('reply_done')
                    ->label('Record Reply')
                    ->icon('heroicon-m-arrow-uturn-left')
                    ->color('success')
                    ->form([
                        Textarea::make('reply_notes')
                            ->label('Notes on reply sent')
                            ->required(),
                    ])
                    ->action(function (array $data, ContactMessage $record) {
                        $record->markAsReplied($data['reply_notes']);
                        Notification::make()->title('Message marked as replied')->success()->send();
                    }),

                Action::make('archive')
                    ->label('Archive')
                    ->icon('heroicon-m-archive-box')
                    ->color('gray')
                    ->action(function (ContactMessage $record) {
                        $record->archive();
                        Notification::make()->title('Message archived')->send();
                    }),

                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('bulk_read')
                        ->label('Mark as Read')
                        ->icon('heroicon-m-envelope-open')
                        ->action(fn (Collection $records) => $records->each->markAsRead())
                        ->deselectRecordsAfterCompletion(),

                    BulkAction::make('bulk_archive')
                        ->label('Archive Selected')
                        ->icon('heroicon-m-archive-box')
                        ->action(fn (Collection $records) => $records->each->archive())
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListContactMessages::route('/'),
            'edit' => Pages\EditContactMessage::route('/{record}/edit'),
        ];
    }

    public static function getNavigationBadge(): ?string
    {
        $count = static::getModel()::where('status', MessageStatus::Unread)->count();

        return $count > 0 ? (string) $count : null;
    }

    public static function getNavigationBadgeColor(): string|array|null
    {
        return 'warning';
    }
}
