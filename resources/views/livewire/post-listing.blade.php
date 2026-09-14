<div>
    <!-- Control Bar -->
    <div class="mb-8 space-y-4">
        <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
            <!-- Search Input -->
            <div class="relative flex-1 max-w-md">
                <div class="absolute inset-y-0 left-0 pl-3.5 flex items-center pointer-events-none text-zinc-400">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/>
                    </svg>
                </div>
                <input
                    type="text"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Search within articles..."
                    aria-label="Search articles"
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 dark:focus:ring-indigo-400/50 transition-all shadow-sm"
                >
                @if($search)
                    <button wire:click="$set('search', '')" aria-label="Clear search" class="absolute inset-y-0 right-0 pr-3 flex items-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200 cursor-pointer">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
                    </button>
                @endif
            </div>

            <!-- Sort and View Mode Controls -->
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400">
                    <label for="sort-select" class="font-medium hidden sm:inline">Sort by:</label>
                    <select
                        id="sort-select"
                        wire:model.live="sort"
                        class="px-3 py-2 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-xs font-medium text-zinc-700 dark:text-zinc-300 focus:outline-none focus:ring-2 focus:ring-indigo-500/50 shadow-sm cursor-pointer"
                    >
                        <option value="latest">Latest First</option>
                        <option value="popular">Most Popular</option>
                        <option value="trending">Trending (7d)</option>
                        <option value="oldest">Oldest First</option>
                    </select>
                </div>

                <!-- View Mode Toggles -->
                <div class="hidden sm:inline-flex items-center p-1 rounded-xl bg-zinc-100 dark:bg-zinc-800/80 border border-zinc-200/80 dark:border-zinc-700/60" role="group" aria-label="View mode">
                    <button
                        type="button"
                        wire:click="setViewMode('grid')"
                        aria-label="Grid view"
                        class="p-1.5 rounded-lg transition-colors {{ $viewMode === 'grid' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 0 1 6 3.75h2.25A2.25 2.25 0 0 1 10.5 6v2.25a2.25 2.25 0 0 1-2.25 2.25H6a2.25 2.25 0 0 1-2.25-2.25V6ZM3.75 15.75A2.25 2.25 0 0 1 6 13.5h2.25a2.25 2.25 0 0 1 2.25 2.25V18a2.25 2.25 0 0 1-2.25 2.25H6A2.25 2.25 0 0 1 3.75 18v-2.25ZM13.5 6a2.25 2.25 0 0 1 2.25-2.25H18A2.25 2.25 0 0 1 20.25 6v2.25A2.25 2.25 0 0 1 18 10.5h-2.25a2.25 2.25 0 0 1-2.25-2.25V6ZM13.5 15.75a2.25 2.25 0 0 1 2.25-2.25H18a2.25 2.25 0 0 1 2.25 2.25V18A2.25 2.25 0 0 1 18 20.25h-2.25A2.25 2.25 0 0 1 13.5 18v-2.25Z"/></svg>
                    </button>
                    <button
                        type="button"
                        wire:click="setViewMode('list')"
                        aria-label="List view"
                        class="p-1.5 rounded-lg transition-colors {{ $viewMode === 'list' ? 'bg-white dark:bg-zinc-900 text-indigo-600 dark:text-indigo-400 shadow-sm' : 'text-zinc-500 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white' }}"
                    >
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5"/></svg>
                    </button>
                </div>
            </div>
        </div>

        <!-- Category Pills Filter -->
        <div class="flex flex-wrap items-center gap-2 pt-2" role="group" aria-label="Filter by category">
            <button
                type="button"
                wire:click="$set('selectedCategory', '')"
                class="px-3 py-1 rounded-full text-xs font-semibold transition-all cursor-pointer {{ $selectedCategory === '' ? 'bg-indigo-600 text-white shadow-sm' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
            >
                All Topics
            </button>

            @foreach($categories as $category)
                <button
                    type="button"
                    wire:click="$set('selectedCategory', '{{ $category->slug }}')"
                    class="px-3 py-1 rounded-full text-xs font-semibold transition-all cursor-pointer {{ $selectedCategory === $category->slug ? 'bg-indigo-600 text-white shadow-sm' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
                >
                    {{ $category->name }}
                    <span class="ml-1 opacity-70">({{ $category->posts_count }})</span>
                </button>
            @endforeach

            @if($selectedCategory !== '' || $selectedTag !== '' || $search !== '')
                <button
                    type="button"
                    wire:click="clearFilters"
                    class="ml-auto text-xs font-medium text-rose-500 hover:text-rose-600 dark:text-rose-400 underline underline-offset-2 flex items-center gap-1 cursor-pointer"
                >
                    Reset filters
                </button>
            @endif
        </div>
    </div>

    <!-- Active Tag Banner -->
    @if($selectedTag)
        <div class="mb-6 inline-flex items-center gap-2 px-3 py-1.5 rounded-lg bg-indigo-50 dark:bg-indigo-950/60 border border-indigo-200 dark:border-indigo-800 text-xs text-indigo-700 dark:text-indigo-300">
            <span>Filtered by tag: <strong>#{{ $selectedTag }}</strong></span>
            <button wire:click="$set('selectedTag', '')" aria-label="Remove tag filter" class="hover:text-indigo-900 dark:hover:text-white cursor-pointer">
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12"/></svg>
            </button>
        </div>
    @endif

    <!-- Results count -->
    <div class="mb-5 flex items-center justify-between text-xs text-zinc-500 dark:text-zinc-400">
        <span>
            Showing <strong class="text-zinc-700 dark:text-zinc-200">{{ $posts->count() }}</strong>
            of <strong class="text-zinc-700 dark:text-zinc-200">{{ $totalCount }}</strong>
            {{ str('article')->plural($totalCount) }}
        </span>
        <div wire:loading.delay class="flex items-center gap-1.5 text-indigo-600 dark:text-indigo-400">
            <svg class="animate-spin h-3.5 w-3.5" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
            <span>Updating...</span>
        </div>
    </div>

    <!-- Post Grid or List -->
    @if($posts->isNotEmpty())
        @if($viewMode === 'list')
            <div class="space-y-4">
                @foreach($posts as $post)
                    <x-post-card :post="$post" layout="list"/>
                @endforeach
            </div>
        @else
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($posts as $post)
                    <x-post-card :post="$post" layout="grid"/>
                @endforeach
            </div>
        @endif

        <!-- Load More Button -->
        @if($hasMore)
            <div class="mt-12 text-center">
                <button
                    type="button"
                    wire:click="loadMore"
                    wire:loading.attr="disabled"
                    wire:target="loadMore"
                    class="inline-flex items-center justify-center gap-2.5 px-8 py-3.5 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 hover:border-indigo-400 dark:hover:border-indigo-500 text-sm font-semibold text-zinc-700 dark:text-zinc-200 shadow-sm hover:shadow-md transition-all cursor-pointer disabled:opacity-60 disabled:cursor-not-allowed"
                >
                    <span wire:loading.remove wire:target="loadMore">
                        Load more articles
                    </span>
                    <span wire:loading wire:target="loadMore" class="flex items-center gap-2">
                        <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path></svg>
                        Loading...
                    </span>
                    <svg wire:loading.remove wire:target="loadMore" class="w-4 h-4 text-indigo-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15"/>
                    </svg>
                </button>
            </div>
        @else
            <div class="mt-10 text-center text-xs text-zinc-400 dark:text-zinc-600 py-4">
                — You've reached the end —
            </div>
        @endif
    @else
        <x-empty-state
            title="No matching articles found"
            description="We could not find any published articles matching your active search or filter criteria."
        />
        <div class="text-center mt-4">
            <button wire:click="clearFilters" class="text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline cursor-pointer">
                Reset filters and view all articles
            </button>
        </div>
    @endif
</div>
