<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;

class PostListing extends Component
{
    #[Url(as: 'q', except: '')]
    public string $search = '';

    #[Url(as: 'category', except: '')]
    public string $selectedCategory = '';

    #[Url(as: 'tag', except: '')]
    public string $selectedTag = '';

    #[Url(as: 'sort', except: 'latest')]
    public string $sort = 'latest';

    public string $viewMode = 'grid';

    public int $perPage = 9;

    public int $page = 1;

    public bool $hasMore = false;

    /** @var array<int, mixed> */
    public array $loadedIds = [];

    public function updatingSearch(): void
    {
        $this->resetListing();
    }

    public function updatingSelectedCategory(): void
    {
        $this->resetListing();
    }

    public function updatingSelectedTag(): void
    {
        $this->resetListing();
    }

    public function updatingSort(): void
    {
        $this->resetListing();
    }

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = '';
        $this->selectedTag = '';
        $this->sort = 'latest';
        $this->resetListing();
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['grid', 'list'], true)) {
            $this->viewMode = $mode;
        }
    }

    public function loadMore(): void
    {
        $this->page++;
    }

    private function resetListing(): void
    {
        $this->page = 1;
        $this->loadedIds = [];
        $this->hasMore = false;
    }

    public function render(): View
    {
        $query = Post::published()
            ->with(['author', 'categories', 'media']);

        // Search Filter
        $trimmed = trim($this->search);
        if ($trimmed !== '') {
            $query->where(function ($q) use ($trimmed): void {
                $q->where('title', 'ilike', "%{$trimmed}%")
                    ->orWhere('excerpt', 'ilike', "%{$trimmed}%")
                    ->orWhere('content', 'ilike', "%{$trimmed}%");
            });
        }

        // Category Filter
        if ($this->selectedCategory !== '') {
            $query->whereHas('categories', function ($q): void {
                $q->where('slug', $this->selectedCategory);
            });
        }

        // Tag Filter
        if ($this->selectedTag !== '') {
            $query->whereHas('tags', function ($q): void {
                $q->where('slug', $this->selectedTag);
            });
        }

        // Sorting
        match ($this->sort) {
            'popular' => $query->orderByDesc('view_count'),
            'trending' => $query->withCount(['views' => function ($q): void {
                $q->where('viewed_date', '>=', now()->subDays(7)->toDateString());
            }])->orderByDesc('views_count')->orderByDesc('view_count'),
            'oldest' => $query->orderBy('published_at', 'asc'),
            default => $query->orderBy('published_at', 'desc'),
        };

        $totalCount = $query->count();
        $posts = $query->paginate($this->perPage * $this->page);
        $this->hasMore = $posts->hasMorePages();

        $categories = Category::active()
            ->whereHas('posts', function ($q): void {
                $q->published();
            })
            ->withCount(['posts' => function ($q): void {
                $q->published();
            }])
            ->orderBy('sort_order')
            ->get();

        $tags = Tag::whereHas('posts', function ($q): void {
            $q->published();
        })
            ->withCount(['posts' => function ($q): void {
                $q->published();
            }])
            ->orderByDesc('posts_count')
            ->limit(15)
            ->get();

        return view('livewire.post-listing', [
            'posts' => $posts,
            'totalCount' => $totalCount,
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
}
