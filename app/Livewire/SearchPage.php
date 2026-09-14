<?php

namespace App\Livewire;

use App\Models\Category;
use App\Models\Post;
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

    public int $perPage = 9;

    public function updatingQuery(): void
    {
        $this->resetPage();
    }

    public function updatingSelectedCategory(): void
    {
        $this->resetPage();
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

            $posts = $builder->orderBy('published_at', 'desc')->paginate($this->perPage);
            $totalResults = $posts->total();

            // Record search query analytics
            app(AnalyticsService::class)->recordSearch($cleanQuery, $totalResults, request());
        }

        $categories = Category::active()
            ->whereHas('posts', function ($q): void {
                $q->published();
            })
            ->withCount(['posts' => function ($q): void {
                $q->published();
            }])
            ->get();

        return view('livewire.search-page', [
            'posts' => $posts,
            'totalResults' => $totalResults,
            'categories' => $categories,
        ]);
    }
}
