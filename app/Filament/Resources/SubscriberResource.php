<?php

namespace App\Filament\Resources;

use App\Enums\SubscriberStatus;
use App\Filament\Resources\SubscriberResource\Pages;
use App\Models\Subscriber;
use Filament\Forms\Components\DateTimePicker;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
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

class SubscriberResource extends Resource
{
    protected static ?string $model = Subscriber::class;

    protected static ?string $navigationIcon = 'heroicon-o-envelope-open';

    protected static ?string $navigationGroup = 'Engagement';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'email';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Subscriber Information')
                ->schema([
                    TextInput::make('email')
                        ->email()
                        ->required()
                        ->unique(Subscriber::class, 'email', ignoreRecord: true),

                    TextInput::make('name')
                        ->maxLength(255),

                    Select::make('status')
                        ->options(SubscriberStatus::class)
                        ->default(SubscriberStatus::Subscribed)
                        ->required(),

                    TextInput::make('source')
                        ->placeholder('e.g. blog_footer, popup, manual'),

                    DateTimePicker::make('subscribed_at')
                        ->default(now()),

                    DateTimePicker::make('unsubscribed_at'),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('email')
                    ->searchable()
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('name')
                    ->searchable()
                    ->placeholder('Anonymous')
                    ->color('gray'),

                TextColumn::make('status')
                    ->badge()
                    ->sortable(),

                TextColumn::make('source')
                    ->badge()
                    ->color('info')
                    ->placeholder('Direct'),

                TextColumn::make('subscribed_at')
                    ->label('Subscribed')
                    ->dateTime('M j, Y')
                    ->sortable(),

                TextColumn::make('unsubscribed_at')
                    ->label('Unsubscribed')
                    ->dateTime('M j, Y')
                    ->placeholder('Active')
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('subscribed_at', 'desc')
            ->filters([
                SelectFilter::make('status')
                    ->options(SubscriberStatus::class),
            ])
            ->actions([
                Action::make('unsubscribe')
                    ->label('Unsubscribe')
                    ->icon('heroicon-m-user-minus')
                    ->color('danger')
                    ->visible(fn (Subscriber $record) => $record->status === SubscriberStatus::Subscribed)
                    ->action(function (Subscriber $record) {
                        $record->unsubscribe();
                        Notification::make()->title('Subscriber unsubscribed')->send();
                    }),

                Action::make('resubscribe')
                    ->label('Resubscribe')
                    ->icon('heroicon-m-check-badge')
                    ->color('success')
                    ->visible(fn (Subscriber $record) => $record->status !== SubscriberStatus::Subscribed)
                    ->action(function (Subscriber $record) {
                        $record->resubscribe();
                        Notification::make()->title('Subscriber resubscribed')->send();
                    }),

                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    BulkAction::make('bulk_unsubscribe')
                        ->label('Unsubscribe Selected')
                        ->icon('heroicon-m-user-minus')
                        ->color('danger')
                        ->action(fn (Collection $records) => $records->each->unsubscribe())
                        ->deselectRecordsAfterCompletion(),

                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListSubscribers::route('/'),
            'edit' => Pages\EditSubscriber::route('/{record}/edit'),
        ];
    }
}
