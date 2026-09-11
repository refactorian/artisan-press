<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Enums\PostStatus;
use App\Filament\Resources\PostResource;
use App\Models\Post;
use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        return [
            'all' => Tab::make('All'),
            'published' => Tab::make('Published')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PostStatus::Published))
                ->badge(Post::where('status', PostStatus::Published)->count())
                ->badgeColor('success'),
            'draft' => Tab::make('Drafts')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PostStatus::Draft))
                ->badge(Post::where('status', PostStatus::Draft)->count())
                ->badgeColor('warning'),
            'scheduled' => Tab::make('Scheduled')
                ->modifyQueryUsing(fn (Builder $query) => $query->where('status', PostStatus::Scheduled))
                ->badge(Post::where('status', PostStatus::Scheduled)->count())
                ->badgeColor('info'),
            'trashed' => Tab::make('Trash')
                ->modifyQueryUsing(fn (Builder $query) => $query->onlyTrashed())
                ->badge(Post::onlyTrashed()->count())
                ->badgeColor('danger'),
        ];
    }
}
