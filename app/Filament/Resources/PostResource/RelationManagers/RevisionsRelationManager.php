<?php

namespace App\Filament\Resources\PostResource\RelationManagers;

use App\Models\Post;
use App\Models\PostRevision;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Infolists\Components\TextEntry;
use Filament\Notifications\Notification;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Actions\Action;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;

class RevisionsRelationManager extends RelationManager
{
    protected static string $relationship = 'revisions';

    protected static ?string $recordTitleAttribute = 'created_at';

    public function form(Form $form): Form
    {
        return $form->schema([
            Forms\Components\TextInput::make('title')->disabled(),
            Forms\Components\Textarea::make('excerpt')->disabled(),
            Forms\Components\Textarea::make('content')->disabled()->rows(6),
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('created_at')
                    ->label('Timestamp')
                    ->dateTime('M j, Y H:i:s')
                    ->sortable()
                    ->weight('bold'),

                TextColumn::make('user.name')
                    ->label('Author')
                    ->placeholder('System')
                    ->badge(),

                TextColumn::make('title')
                    ->label('Revision Title')
                    ->limit(40),

                TextColumn::make('reason')
                    ->label('Summary / Note')
                    ->placeholder('Automatic snapshot')
                    ->badge()
                    ->color('gray'),
            ])
            ->defaultSort('created_at', 'desc')
            ->actions([
                Action::make('view_revision')
                    ->label('Inspect')
                    ->icon('heroicon-m-eye')
                    ->modalHeading(fn (PostRevision $record) => "Revision: {$record->created_at->format('M j, Y H:i')}")
                    ->infolist([
                        TextEntry::make('title')
                            ->label('Title'),
                        TextEntry::make('user.name')
                            ->label('Captured By')
                            ->placeholder('System'),
                        TextEntry::make('reason')
                            ->label('Revision Note'),
                        TextEntry::make('excerpt')
                            ->label('Excerpt')
                            ->placeholder('No excerpt'),
                        TextEntry::make('content')
                            ->label('Content')
                            ->html(),
                    ]),

                Action::make('restore')
                    ->label('Restore This Version')
                    ->icon('heroicon-m-arrow-path')
                    ->color('warning')
                    ->requiresConfirmation()
                    ->modalHeading('Restore Post Revision')
                    ->modalDescription('Are you sure you want to restore this post to this saved revision? The current content will be snapshotted first so nothing is lost.')
                    ->action(function (PostRevision $record) {
                        /** @var Post $post */
                        $post = $this->getOwnerRecord();

                        // Snapshot current before restoring
                        $post->createRevision('Snapshot before restoring revision from '.$record->created_at->format('M j, Y H:i'));

                        // Restore
                        $post->update([
                            'title' => $record->title,
                            'slug' => $record->slug,
                            'excerpt' => $record->excerpt,
                            'content' => $record->content,
                            'content_blocks' => $record->content_blocks,
                        ]);

                        Notification::make()
                            ->title('Revision Restored')
                            ->success()
                            ->body("The post has been successfully restored to the version from {$record->created_at->format('M j, Y H:i')}.")
                            ->send();

                        Notification::make()
                            ->title('Post Revision Restored')
                            ->body("Post \"{$post->title}\" was restored to revision from {$record->created_at->format('M j, Y H:i')}.")
                            ->sendToDatabase(auth()->user());
                    }),
            ])
            ->headerActions([
                Action::make('create_snapshot')
                    ->label('Save Snapshot Now')
                    ->icon('heroicon-m-camera')
                    ->form([
                        Forms\Components\TextInput::make('reason')
                            ->label('Snapshot Note / Reason')
                            ->placeholder('e.g. Pre-launch edits, rewrite section 2')
                            ->required(),
                    ])
                    ->action(function (array $data) {
                        /** @var Post $post */
                        $post = $this->getOwnerRecord();
                        $post->createRevision($data['reason'], auth()->id());

                        Notification::make()
                            ->title('Snapshot Saved')
                            ->success()
                            ->send();
                    }),
            ]);
    }
}
