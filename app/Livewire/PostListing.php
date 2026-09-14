<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Post;
use App\Models\Tag;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Url;
use Livewire\Component;
use Livewire\WithPagination;

class PostListing extends Component
{
    use WithPagination;

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

    public function updatingSearch(): void
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

    public function clearFilters(): void
    {
        $this->search = '';
        $this->selectedCategory = '';
        $this->selectedTag = '';
        $this->sort = 'latest';
        $this->resetPage();
    }

    public function setViewMode(string $mode): void
    {
        if (in_array($mode, ['grid', 'list'], true)) {
            $this->viewMode = $mode;
        }
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

        $posts = $query->paginate($this->perPage);

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
            'categories' => $categories,
            'tags' => $tags,
        ]);
    }
}
