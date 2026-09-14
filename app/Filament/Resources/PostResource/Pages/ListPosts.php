<?php

namespace App\Filament\Resources\PostResource\Pages;

use App\Enums\PostStatus;
use App\Filament\Resources\PostResource;
use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use App\Models\User;
use Carbon\Carbon;
use Filament\Actions;
use Filament\Forms\Components\FileUpload;
use Filament\Notifications\Notification;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class ListPosts extends ListRecords
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),

            Actions\Action::make('importJson')
                ->label('Import JSON')
                ->icon('heroicon-o-arrow-up-tray')
                ->color('gray')
                ->form([
                    FileUpload::make('json_file')
                        ->label('Posts JSON File')
                        ->acceptedFileTypes(['application/json', 'text/json', 'text/plain'])
                        ->disk('local')
                        ->directory('imports')
                        ->required(),
                ])
                ->action(function (array $data): void {
                    $path = storage_path('app/'.$data['json_file']);
                    if (! file_exists($path)) {
                        Notification::make()
                            ->title('Import Failed')
                            ->body('Uploaded file could not be located.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $content = file_get_contents($path);
                    $records = json_decode($content, true);

                    if (! is_array($records)) {
                        Notification::make()
                            ->title('Invalid Format')
                            ->body('The file does not contain valid JSON array data.')
                            ->danger()
                            ->send();

                        return;
                    }

                    $importedCount = 0;
                    $fallbackUserId = auth()->id() ?? User::first()?->id ?? 1;

                    foreach ($records as $item) {
                        if (empty($item['title'])) {
                            continue;
                        }

                        $slug = ! empty($item['slug']) ? Str::slug($item['slug']) : Str::slug($item['title']);

                        $post = Post::updateOrCreate(
                            ['slug' => $slug],
                            [
                                'user_id' => $fallbackUserId,
                                'title' => $item['title'],
                                'excerpt' => $item['excerpt'] ?? null,
                                'content' => $item['content'] ?? null,
                                'status' => PostStatus::tryFrom($item['status'] ?? '') ?? PostStatus::Draft,
                                'published_at' => ! empty($item['published_at']) ? Carbon::parse($item['published_at']) : null,
                                'seo_title' => $item['seo_title'] ?? null,
                                'seo_description' => $item['seo_description'] ?? null,
                                'canonical_url' => $item['canonical_url'] ?? null,
                            ]
                        );

                        // Attach categories if present
                        if (! empty($item['categories']) && is_array($item['categories'])) {
                            $categoryIds = [];
                            foreach ($item['categories'] as $catName) {
                                if (is_string($catName)) {
                                    $cat = Category::firstOrCreate(
                                        ['slug' => Str::slug($catName)],
                                        ['name' => $catName]
                                    );
                                    $categoryIds[] = $cat->id;
                                }
                            }
                            $post->categories()->syncWithoutDetaching($categoryIds);
                        }

                        // Attach tags if present
                        if (! empty($item['tags']) && is_array($item['tags'])) {
                            $tagIds = [];
                            foreach ($item['tags'] as $tagName) {
                                if (is_string($tagName)) {
                                    $tag = Tag::firstOrCreate(
                                        ['slug' => Str::slug($tagName)],
                                        ['name' => $tagName]
                                    );
                                    $tagIds[] = $tag->id;
                                }
                            }
                            $post->tags()->syncWithoutDetaching($tagIds);
                        }

                        $importedCount++;
                    }

                    @unlink($path);

                    Notification::make()
                        ->title('Import Successful')
                        ->body("Successfully imported or updated {$importedCount} posts.")
                        ->success()
                        ->send();
                }),

            Actions\Action::make('exportJson')
                ->label('Export JSON')
                ->icon('heroicon-o-arrow-down-tray')
                ->color('gray')
                ->action(function () {
                    $posts = Post::with(['author:id,name', 'categories:id,name', 'tags:id,name'])
                        ->get()
                        ->map(fn (Post $p) => [
                            'id' => $p->id,
                            'title' => $p->title,
                            'slug' => $p->slug,
                            'excerpt' => $p->excerpt,
                            'content' => $p->content,
                            'status' => $p->status->value,
                            'published_at' => $p->published_at?->toIso8601String(),
                            'reading_time' => $p->reading_time,
                            'view_count' => $p->view_count,
                            'seo_title' => $p->seo_title,
                            'seo_description' => $p->seo_description,
                            'canonical_url' => $p->canonical_url,
                            'author' => $p->author?->name,
                            'categories' => $p->categories->pluck('name')->all(),
                            'tags' => $p->tags->pluck('name')->all(),
                        ]);

                    $filename = 'posts-export-'.now()->format('Y-m-d-His').'.json';

                    return response()->streamDownload(function () use ($posts) {
                        echo json_encode($posts, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
                    }, $filename, ['Content-Type' => 'application/json']);
                }),
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
