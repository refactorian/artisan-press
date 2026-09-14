<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Post;
use App\Models\SearchLog;
use App\Models\Tag;
use App\Services\AnalyticsService;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class SearchPage extends Component
{
    use WithPagination;

    #[Url(as: 'q', except: '')]
    public string $query = '';

    #[Url(as: 'category', except: '')]
    public string $selectedCategory = '';

    #[Url(as: 'tag', except: '')]
    public string $selectedTag = '';

    #[Url(as: 'sort', except: 'latest')]
    public string $sort = 'latest';

    public int $perPage = 9;

    public function updatingQuery(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedTag(): void
    {
        $this->resetPage();
    }

    public function updatingSort(): void
    {
        $this->resetPage();
    }

    public function setQuery(string $text): void
    {
        $this->query = $text;
        $this->resetPage();
    }

    public function clearFilters(): void
    {
        $this->query = '';
        $this->selectedCategory = '';
        $this->selectedTag = '';
        $this->sort = 'latest';
        $this->resetPage();
    }

    public function highlight(?string $text, string $query): string
    {
        if (empty($text)) {
            return '';
        }

        $cleanQuery = trim($query);
        if ($cleanQuery === '') {
            return e($text);
        }

        $escaped = preg_quote($cleanQuery, '/');

        return (string) preg_replace(
            '/('.$escaped.')/i',
            '<mark class="bg-indigo-100 dark:bg-indigo-950/80 text-indigo-700 dark:text-indigo-300 font-bold px-0.5 rounded">$1</mark>',
            e($text)
        );
    }

    public function render(): View
    {
        $cleanQuery = trim($this->query);
        $posts = null;
        $totalResults = 0;

        if ($cleanQuery !== '') {
            $builder = Post::published()
                ->where(function ($q) use ($cleanQuery): void {
                    $q->where('title', 'ilike', "%{$cleanQuery}%")
                        ->orWhere('excerpt', 'ilike', "%{$cleanQuery}%")
                        ->orWhere('content', 'ilike', "%{$cleanQuery}%");
                })
                ->with(['author', 'categories', 'media']);

            if ($this->selectedCategory !== '') {
                $builder->whereHas('categories', function ($q): void {
                    $q->where('slug', $this->selectedCategory);
                });
            }

            if ($this->selectedTag !== '') {
                $builder->whereHas('tags', function ($q): void {
                    $q->where('slug', $this->selectedTag);
                });
            }

            match ($this->sort) {
                'popular' => $builder->orderByDesc('view_count'),
                'oldest' => $builder->orderBy('published_at', 'asc'),
                default => $builder->orderBy('published_at', 'desc'),
            };

            $posts = $builder->paginate($this->perPage);
            $totalResults = $posts->total();

            // Record search query analytics
            app(AnalyticsService::class)->recordSearch($cleanQuery, $totalResults, request());
        }

        $categories = Category::active()
            ->whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->get();

        $tags = Tag::whereHas('posts', fn ($q) => $q->published())
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderByDesc('posts_count')
            ->limit(10)
            ->get();

        // Recent popular search terms from SearchLog
        $popularSearches = SearchLog::select('query')
            ->selectRaw('count(*) as count')
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(6)
            ->pluck('query');

        return view('livewire.search-page', [
            'posts' => $posts,
            'totalResults' => $totalResults,
            'categories' => $categories,
            'tags' => $tags,
            'popularSearches' => $popularSearches,
        ]);
    }
}
