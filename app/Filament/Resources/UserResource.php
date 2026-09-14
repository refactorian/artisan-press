<?php

namespace App\Filament\Resources;

use App\Filament\Forms\Components\SeoFields;
use App\Filament\Resources\UserResource\Pages;
use App\Models\User;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Repeater;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Actions\BulkActionGroup;
use Filament\Tables\Actions\DeleteBulkAction;
use Filament\Tables\Columns\IconColumn;
use Filament\Tables\Columns\ImageColumn;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Filters\SelectFilter;
use Filament\Tables\Filters\TernaryFilter;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Hash;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users';

    protected static ?string $navigationGroup = 'Users & Access';

    protected static ?int $navigationSort = 1;

    public static function form(Form $form): Form
    {
        return $form->schema([
            Section::make('Account Information')
                ->columns(2)
                ->schema([
                    TextInput::make('name')
                        ->label('Full Name')
                        ->required()
                        ->maxLength(255),

                    TextInput::make('email')
                        ->label('Email Address')
                        ->email()
                        ->required()
                        ->maxLength(255)
                        ->unique(User::class, 'email', ignoreRecord: true),

                    TextInput::make('password')
                        ->label('Password')
                        ->password()
                        ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                        ->dehydrated(fn ($state) => filled($state))
                        ->required(fn (string $context): bool => $context === 'create')
                        ->maxLength(255)
                        ->helperText('Leave blank to keep existing password.'),

                    Select::make('roles')
                        ->label('Role')
                        ->multiple()
                        ->relationship('roles', 'name')
                        ->preload()
                        ->searchable(),

                    Toggle::make('is_active')
                        ->label('Active Account')
                        ->default(true),

                    Toggle::make('is_featured_author')
                        ->label('Featured Author')
                        ->helperText('Spotlight this author on team and author listings.'),
                ]),

            Section::make('Author Profile & Bio')
                ->schema([
                    Grid::make(3)->schema([
                        FileUpload::make('avatar')
                            ->label('Profile Avatar')
                            ->image()
                            ->disk('public')
                            ->directory('avatars')
                            ->imageResizeMode('cover')
                            ->imageCropAspectRatio('1:1')
                            ->imageResizeTargetWidth('256')
                            ->imageResizeTargetHeight('256')
                            ->maxSize(2048),

                        Grid::make(1)->columnSpan(2)->schema([
                            Grid::make(2)->schema([
                                TextInput::make('job_title')
                                    ->label('Role / Job Title')
                                    ->placeholder('e.g. Lead Developer, Technical Writer'),

                                TextInput::make('pronouns')
                                    ->label('Pronouns')
                                    ->placeholder('e.g. they/them, she/her, he/him'),
                            ]),

                            TextInput::make('website_url')
                                ->label('Personal / Portfolio Website')
                                ->url()
                                ->placeholder('https://example.com'),
                        ]),
                    ]),

                    Textarea::make('bio')
                        ->label('Author Biography')
                        ->rows(4)
                        ->maxLength(1000)
                        ->helperText('A short bio shown on articles and author profile pages.'),

                    Repeater::make('social_links')
                        ->label('Author Social Profiles')
                        ->schema([
                            Grid::make(2)->schema([
                                Select::make('platform')
                                    ->options([
                                        'twitter' => 'Twitter / X',
                                        'github' => 'GitHub',
                                        'linkedin' => 'LinkedIn',
                                        'youtube' => 'YouTube',
                                        'facebook' => 'Facebook',
                                        'instagram' => 'Instagram',
                                        'website' => 'Personal Blog',
                                    ])
                                    ->required(),
                                TextInput::make('url')
                                    ->label('Profile URL')
                                    ->url()
                                    ->required(),
                            ]),
                        ])
                        ->collapsible()
                        ->cloneable()
                        ->defaultItems(0),
                ]),

            SeoFields::make('Author Archive SEO & Social Directives'),
        ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                ImageColumn::make('avatar')
                    ->label('')
                    ->disk('public')
                    ->circular()
                    ->defaultImageUrl(fn (User $record) => 'https://ui-avatars.com/api/?name='.urlencode($record->name).'&background=7c3aed&color=fff'),

                TextColumn::make('name')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->description(fn (User $record) => $record->job_title),

                TextColumn::make('email')
                    ->searchable()
                    ->sortable(),

                TextColumn::make('roles.name')
                    ->label('Role')
                    ->badge()
                    ->separator(','),

                IconColumn::make('is_featured_author')
                    ->label('Featured Author')
                    ->boolean()
                    ->trueIcon('heroicon-s-star')
                    ->falseIcon('heroicon-o-minus')
                    ->trueColor('warning')
                    ->alignCenter(),

                TextColumn::make('posts_count')
                    ->label('Posts')
                    ->counts('posts')
                    ->sortable()
                    ->alignEnd(),

                IconColumn::make('is_active')
                    ->label('Active')
                    ->boolean(),

                TextColumn::make('created_at')
                    ->label('Joined')
                    ->dateTime('M j, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->defaultSort('name')
            ->filters([
                SelectFilter::make('roles')
                    ->relationship('roles', 'name')
                    ->multiple()
                    ->preload(),

                TernaryFilter::make('is_featured_author')
                    ->label('Featured Authors'),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                BulkActionGroup::make([
                    DeleteBulkAction::make(),
                ]),
            ])
            ->modifyQueryUsing(fn (Builder $query) => $query->withCount('posts'));
    }

    public static function getRelations(): array
    {
        return [];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }

    public static function canAccess(): bool
    {
        return auth()->user()?->hasAnyRole(['super_admin', 'admin']) ?? false;
    }
}
