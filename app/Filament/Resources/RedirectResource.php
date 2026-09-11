<?php

namespace App\Filament\Resources;

use App\Enums\RedirectType;
use App\Filament\Resources\RedirectResource\Pages;
use App\Models\Redirect;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Table;

class RedirectResource extends Resource
{
    protected static ?string $model = Redirect::class;

    protected static ?string $navigationIcon = 'heroicon-o-arrow-top-right-on-square';

    protected static ?string $navigationGroup = 'Navigation & Structure';

    protected static ?int $navigationSort = 2;

    protected static ?string $recordTitleAttribute = 'source_path';

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Redirect Rule')
                ->schema([
                    TextInput::make('source_path')
                        ->label('Source Path (Old URL)')
                        ->prefix('/')
                        ->required()
                        ->maxLength(255)
                        ->unique(Redirect::class, 'source_path', ignoreRecord: true)
                        ->helperText('Relative path without domain, e.g. "old-blog-post"'),

                    TextInput::make('target_path')
                        ->label('Target Path / URL (New Location)')
                        ->required()
                        ->maxLength(255)
                        ->helperText('Relative path e.g. "/posts/new-slug" or full external URL "https://..."'),

                    Select::make('status_code')
                        ->label('HTTP Status Code')
                        ->options(RedirectType::class)
                        ->default(RedirectType::Permanent301->value)
                        ->required(),

                    Toggle::make('is_active')
                        ->label('Active')
                        ->default(true),
                ])->columns(2),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('source_path')
                    ->label('From (Source)')
                    ->searchable()
                    ->sortable()
                    ->prefix('/')
                    ->weight('bold'),

                TextColumn::make('target_path')
                    ->label('To (Destination)')
                    ->searchable()
                    ->color('info')
                    ->limit(45),

                TextColumn::make('status_code')
                    ->label('Type')
                    ->badge()
                    ->sortable(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('hit_count')
                    ->label('Hits')
                    ->numeric()
                    ->sortable()
                    ->alignEnd(),

                TextColumn::make('last_accessed_at')
                    ->label('Last Triggered')
                    ->dateTime('M j, Y H:i')
                    ->placeholder('Never')
                    ->sortable(),
            ])
            ->defaultSort('hit_count', 'desc')
            ->filters([
                SelectFilter::make('status_code')
                    ->options(RedirectType::class),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListRedirects::route('/'),
            'create' => Pages\CreateRedirect::route('/create'),
            'edit' => Pages\EditRedirect::route('/{record}/edit'),
        ];
    }
}
