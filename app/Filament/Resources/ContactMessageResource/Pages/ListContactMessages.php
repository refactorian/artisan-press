<?php

namespace App\Filament\Resources\ContactMessageResource\Pages;

use App\Enums\MessageStatus;
use App\Filament\Resources\ContactMessageResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListContactMessages extends ListRecords
{
    protected static string $resource = ContactMessageResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'unread' => Tab::make('Unread')
                ->badge(fn () => static::getResource()::getModel()::where('status', MessageStatus::Unread)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', MessageStatus::Unread)),

            'all' => Tab::make('All Messages'),

            'archived' => Tab::make('Archived')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', MessageStatus::Archived)),
        ];
    }
}
