<?php

namespace App\Filament\Resources\CommentResource\Pages;

use App\Enums\CommentStatus;
use App\Filament\Resources\CommentResource;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListComments extends ListRecords
{
    protected static string $resource = CommentResource::class;

    protected function getHeaderActions(): array
    {
        return [];
    }

    public function getTabs(): array
    {
        return [
            'pending' => Tab::make('Pending Review')
                ->badge(fn () => static::getResource()::getModel()::where('status', CommentStatus::Pending)->count())
                ->badgeColor('warning')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CommentStatus::Pending)),

            'approved' => Tab::make('Approved')
                ->badge(fn () => static::getResource()::getModel()::where('status', CommentStatus::Approved)->count())
                ->badgeColor('success')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CommentStatus::Approved)),

            'rejected' => Tab::make('Rejected')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CommentStatus::Rejected)),

            'spam' => Tab::make('Spam')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', CommentStatus::Spam)),

            'all' => Tab::make('All Comments'),
        ];
    }
}
