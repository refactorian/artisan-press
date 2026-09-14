<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Actions;
use Filament\Infolists\Components\TextEntry;
use Filament\Infolists\Components\TextEntry\TextEntrySize;
use Filament\Resources\Pages\EditRecord;

class EditPost extends EditRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\Action::make('preview')
                ->label('Preview Post')
                ->icon('heroicon-m-eye')
                ->color('info')
                ->modalHeading(fn (Post $record) => "Preview: {$record->title}")
                ->modalWidth('4xl')
                ->infolist([
                    TextEntry::make('title')
                        ->size(TextEntrySize::Large)
                        ->weight('bold'),
                    TextEntry::make('author.name')
                        ->label('Byline')
                        ->formatStateUsing(fn ($state, Post $record) => "By {$state} • Status: {$record->status->getLabel()} • Views: {$record->view_count}"),
                    TextEntry::make('excerpt')
                        ->label('Excerpt')
                        ->color('gray')
                        ->visible(fn (Post $record) => ! empty($record->excerpt)),
                    TextEntry::make('content')
                        ->label('Content Body')
                        ->html(),
                ]),

            Actions\DeleteAction::make(),
            Actions\ForceDeleteAction::make(),
            Actions\RestoreAction::make(),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }
}
