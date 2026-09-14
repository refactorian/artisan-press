<?php

namespace App\Livewire;

use App\Models\Post;
use App\Services\AnalyticsService;
use Illuminate\Contracts\View\View;
use Illuminate\Database\Eloquent\Collection;
use Livewire\Attributes\On;
use Livewire\Component;

class QuickSearch extends Component
{
    public string $query = '';

    public bool $isOpen = false;

    #[On('open-quick-search')]
    public function open(): void
    {
        $this->isOpen = true;
    }

    public function close(): void
    {
        $this->isOpen = false;
        $this->query = '';
    }

    public function render(): View
    {
        $results = new Collection;

        $trimmed = trim($this->query);
        if (strlen($trimmed) >= 2) {
            $results = Post::published()
                ->where(function ($q) use ($trimmed): void {
                    $q->where('title', 'ilike', "%{$trimmed}%")
                        ->orWhere('excerpt', 'ilike', "%{$trimmed}%");
                })
                ->with(['categories', 'author'])
                ->limit(6)
                ->get();

            if ($results->isNotEmpty()) {
                app(AnalyticsService::class)->recordSearch($trimmed, $results->count(), request());
            }
        }

        $suggestions = \App\Models\SearchLog::select('query')
            ->selectRaw('count(*) as count')
            ->groupBy('query')
            ->orderByDesc('count')
            ->limit(4)
            ->pluck('query');

        $topCategories = \App\Models\Category::active()
            ->whereHas('posts', fn ($q) => $q->published())
            ->orderBy('sort_order')
            ->limit(4)
            ->get();

        return view('livewire.quick-search', [
            'results' => $results,
            'suggestions' => $suggestions,
            'topCategories' => $topCategories,
        ]);
    }
}
