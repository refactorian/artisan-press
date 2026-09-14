<div
    x-data="{
        isOpen: @entangle('isOpen'),
        init() {
            window.addEventListener('keydown', (e) => {
                if ((e.metaKey || e.ctrlKey) && e.key.toLowerCase() === 'k') {
                    e.preventDefault();
                    this.isOpen = true;
                    $nextTick(() => $refs.searchInput?.focus());
                }
                if (e.key === 'Escape' && this.isOpen) {
                    this.isOpen = false;
                }
            });
        }
    }"
    x-show="isOpen"
    x-cloak
    class="relative z-50"
    role="dialog"
    aria-modal="true"
>
    <!-- Backdrop -->
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="isOpen = false"
        class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm transition-opacity"
    ></div>

    <!-- Modal Content -->
    <div class="fixed inset-0 z-10 overflow-y-auto p-4 sm:p-6 md:p-20">
        <div
            x-show="isOpen"
            x-transition:enter="ease-out duration-200"
            x-transition:enter-start="opacity-0 scale-95"
            x-transition:enter-end="opacity-100 scale-100"
            x-transition:leave="ease-in duration-150"
            x-transition:leave-start="opacity-100 scale-100"
            x-transition:leave-end="opacity-0 scale-95"
            @click.outside="isOpen = false"
            class="mx-auto max-w-2xl transform divide-y divide-zinc-200 dark:divide-zinc-800 overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 shadow-2xl ring-1 ring-black/5 transition-all border border-zinc-200 dark:border-zinc-800"
        >
            <!-- Search Bar Input -->
            <div class="relative flex items-center px-4">
                <svg class="pointer-events-none h-5 w-5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>

                <input
                    x-ref="searchInput"
                    type="text"
                    wire:model.live.debounce.250ms="query"
                    placeholder="Search articles, guides, topics..."
                    class="h-14 w-full border-0 bg-transparent pl-3 pr-4 text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-0 text-base"
                >

                <div wire:loading class="pr-2">
                    <svg class="animate-spin h-4 w-4 text-indigo-500" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                </div>

                <kbd class="hidden sm:inline-block rounded px-1.5 py-0.5 text-xs font-semibold text-zinc-400 dark:text-zinc-500 bg-zinc-100 dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700">
                    ESC
                </kbd>
            </div>

            <!-- Results -->
            @if(strlen(trim($query)) >= 2)
                <div class="max-h-96 overflow-y-auto p-2">
                    @if($results->isNotEmpty())
                        <ul class="divide-y divide-zinc-100 dark:divide-zinc-800">
                            @foreach($results as $item)
                                <li>
                                    <a
                                        href="{{ route('posts.show', $item) }}"
                                        @click="isOpen = false"
                                        class="group flex items-start gap-3 rounded-xl p-3 hover:bg-zinc-100 dark:hover:bg-zinc-800/70 transition-colors"
                                    >
                                        <div class="mt-1 flex-shrink-0 text-indigo-600 dark:text-indigo-400">
                                            <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                            </svg>
                                        </div>

                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center gap-2 mb-1">
                                                @if($cat = $item->categories->first())
                                                    <span class="inline-block text-[11px] font-medium text-indigo-600 dark:text-indigo-400">
                                                        {{ $cat->name }}
                                                    </span>
                                                    <span class="text-zinc-300 dark:text-zinc-700">•</span>
                                                @endif
                                                <span class="text-[11px] text-zinc-400">
                                                    {{ $item->reading_time ?? $item->calculateReadingTime() }} min read
                                                </span>
                                            </div>

                                            <h4 class="text-sm font-semibold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors truncate">
                                                {{ $item->title }}
                                            </h4>

                                            @if($item->excerpt)
                                                <p class="text-xs text-zinc-500 dark:text-zinc-400 truncate mt-0.5">
                                                    {{ $item->excerpt }}
                                                </p>
                                            @endif
                                        </div>
                                    </a>
                                </li>
                            @endforeach
                        </ul>

                        <div class="p-3 text-center border-t border-zinc-100 dark:border-zinc-800">
                            <a href="{{ route('search', ['q' => $query]) }}" @click="isOpen = false" class="text-xs font-medium text-indigo-600 dark:text-indigo-400 hover:underline">
                                View all results for "{{ $query }}" →
                            </a>
                        </div>
                    @else
                        <div class="p-8 text-center text-sm text-zinc-500 dark:text-zinc-400">
                            No articles found for <span class="font-semibold text-zinc-800 dark:text-zinc-200">"{{ $query }}"</span>
                        </div>
                    @endif
                </div>
            @else
                <div class="p-6 space-y-4">
                    @if($suggestions->isNotEmpty())
                        <div>
                            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 block mb-2">
                                Suggested searches:
                            </span>
                            <div class="flex flex-wrap gap-1.5">
                                @foreach($suggestions as $sug)
                                    <button
                                        type="button"
                                        wire:click="$set('query', '{{ $sug }}')"
                                        class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-indigo-50 dark:hover:bg-indigo-950 text-xs text-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer"
                                    >
                                        {{ $sug }}
                                    </button>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    @if($topCategories->isNotEmpty())
                        <div class="pt-2">
                            <span class="text-[11px] font-bold uppercase tracking-wider text-zinc-400 block mb-2">
                                Browse Topics:
                            </span>
                            <div class="flex flex-wrap gap-2">
                                @foreach($topCategories as $cat)
                                    <a
                                        href="{{ route('categories.show', $cat) }}"
                                        @click="isOpen = false"
                                        class="px-3 py-1 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 text-xs font-semibold text-indigo-700 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors"
                                    >
                                        {{ $cat->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <div class="pt-2 text-center text-xs text-zinc-400 dark:text-zinc-500">
                        Type at least 2 characters to search across all published articles...
                    </div>
                </div>
            @endif
        </div>
    </div>
</div>
