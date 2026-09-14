<div>
    <!-- Hero Header / Search Field -->
    <div class="max-w-2xl mx-auto text-center mb-8">
        <h1 class="text-3xl sm:text-4xl font-extrabold text-zinc-900 dark:text-white tracking-tight mb-3">
            Search Our Publications
        </h1>
        <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 mb-6">
            Find technical deep-dives, architectural case studies, and tutorials.
        </p>

        <div class="relative">
            <div class="absolute inset-y-0 left-0 pl-4 flex items-center pointer-events-none text-zinc-400">
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
            </div>

            <input
                type="text"
                wire:model.live.debounce.300ms="query"
                placeholder="Search by keywords, framework, architectural topic..."
                autofocus
                class="w-full pl-12 pr-12 py-3.5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-base text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-md transition-all"
            >

            @if($query)
                <button
                    wire:click="setQuery('')"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        <!-- Search Suggestions / Autocomplete Chips -->
        @if($popularSearches->isNotEmpty() && empty($query))
            <div class="flex flex-wrap items-center justify-center gap-2 mt-4 text-xs text-zinc-500 dark:text-zinc-400">
                <span class="font-medium">Trending searches:</span>
                @foreach($popularSearches as $pQuery)
                    <button
                        type="button"
                        wire:click="setQuery('{{ $pQuery }}')"
                        class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 hover:bg-indigo-50 dark:hover:bg-indigo-950/60 hover:text-indigo-600 text-zinc-700 dark:text-zinc-300 transition-colors cursor-pointer"
                    >
                        {{ $pQuery }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Filter Ribbon & Sorting (when searching) -->
    @if(trim($query) !== '')
        <div class="p-4 mb-8 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 flex flex-wrap items-center justify-between gap-4">
            <!-- Category & Tag Pills -->
            <div class="flex flex-wrap items-center gap-2 text-xs">
                <span class="font-semibold text-zinc-500 mr-1">Filter:</span>
                <button
                    type="button"
                    wire:click="$set('selectedCategory', '')"
                    class="px-3 py-1 rounded-full font-semibold transition-colors cursor-pointer {{ $selectedCategory === '' ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700' }}"
                >
                    All Categories
                </button>
                @foreach($categories as $category)
                    <button
                        type="button"
                        wire:click="$set('selectedCategory', '{{ $category->slug }}')"
                        class="px-3 py-1 rounded-full font-semibold transition-colors cursor-pointer {{ $selectedCategory === $category->slug ? 'bg-indigo-600 text-white' : 'bg-white dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200 dark:border-zinc-700' }}"
                    >
                        {{ $category->name }}
                    </button>
                @endforeach

                @if($tags->isNotEmpty())
                    <span class="text-zinc-300 dark:text-zinc-700 mx-1">|</span>
                    @foreach($tags->take(4) as $tag)
                        <button
                            type="button"
                            wire:click="$set('selectedTag', '{{ $selectedTag === $tag->slug ? '' : $tag->slug }}')"
                            class="px-2.5 py-1 rounded-lg font-medium text-[11px] transition-colors cursor-pointer {{ $selectedTag === $tag->slug ? 'bg-indigo-600 text-white' : 'bg-zinc-200/70 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400' }}"
                        >
                            #{{ $tag->name }}
                        </button>
                    @endforeach
                @endif
            </div>

            <!-- Sort By -->
            <div class="flex items-center gap-2 text-xs text-zinc-500">
                <label for="search-sort" class="font-medium">Sort:</label>
                <select
                    id="search-sort"
                    wire:model.live="sort"
                    class="px-3 py-1.5 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-200 dark:border-zinc-700 text-xs font-semibold text-zinc-800 dark:text-zinc-200 cursor-pointer"
                >
                    <option value="latest">Latest</option>
                    <option value="popular">Most Popular</option>
                    <option value="oldest">Oldest</option>
                </select>
            </div>
        </div>
    @endif

    <!-- Loading State -->
    <div wire:loading class="w-full mb-6 text-center">
        <div class="inline-flex items-center gap-2 text-xs text-indigo-600 dark:text-indigo-400">
            <svg class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24">
                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
            </svg>
            <span>Searching articles...</span>
        </div>
    </div>

    <!-- Results Section -->
    @if(trim($query) !== '')
        <div class="mb-6 flex items-center justify-between text-sm text-zinc-500 dark:text-zinc-400 border-b border-zinc-200/80 dark:border-zinc-800 pb-3">
            <span>
                Found <strong class="text-zinc-900 dark:text-white">{{ $totalResults }}</strong> {{ str('result')->plural($totalResults) }} for <strong class="text-indigo-600 dark:text-indigo-400">"{{ $query }}"</strong>
            </span>

            @if($selectedCategory !== '' || $selectedTag !== '')
                <button
                    type="button"
                    wire:click="clearFilters"
                    class="text-xs text-rose-500 hover:underline cursor-pointer"
                >
                    Clear active filters
                </button>
            @endif
        </div>

        @if($posts && $posts->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($posts as $post)
                    @php
                        $category = $post->categories->first();
                        $author = $post->author;
                        $readingTime = $post->reading_time ?: ($post->calculateReadingTime() ?? 3);
                        $imageUrl = $post->getFirstMediaUrl('featured_image', 'medium') ?: $post->getFirstMediaUrl('featured_image');
                    @endphp

                    <article class="group relative flex flex-col rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 overflow-hidden transition-all duration-200 hover:shadow-xl">
                        <!-- Thumbnail -->
                        <a href="{{ route('posts.show', $post) }}" class="relative aspect-[16/10] overflow-hidden bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-pink-500/10 dark:from-indigo-950/40 dark:to-purple-950/40">
                            @if($imageUrl)
                                <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
                            @else
                                <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center bg-gradient-to-tr from-slate-100 to-indigo-50 dark:from-zinc-800 dark:to-zinc-850">
                                    <span class="text-3xl font-black text-indigo-600/20 dark:text-indigo-400/20">
                                        {{ substr($category?->name ?? 'Article', 0, 3) }}
                                    </span>
                                </div>
                            @endif
                        </a>

                        <div class="flex flex-col flex-1 p-5">
                            <div class="flex items-center justify-between text-xs mb-3">
                                @if($category)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-950/70 dark:text-indigo-300">
                                        {{ $category->name }}
                                    </span>
                                @else
                                    <span></span>
                                @endif
                                <span class="text-[11px] text-zinc-400">{{ $readingTime }} min read</span>
                            </div>

                            <!-- Highlighted Title -->
                            <h3 class="text-base font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 leading-snug">
                                <a href="{{ route('posts.show', $post) }}">
                                    {!! $this->highlight($post->title, $query) !!}
                                </a>
                            </h3>

                            <!-- Highlighted Excerpt -->
                            @if($post->excerpt)
                                <p class="mt-2 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2 flex-1">
                                    {!! $this->highlight($post->excerpt, $query) !!}
                                </p>
                            @endif

                            <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs">
                                <span class="text-zinc-600 dark:text-zinc-300 font-medium">{{ $author?->name ?? 'Editorial Staff' }}</span>
                                <time datetime="{{ $post->published_at?->toISOString() }}" class="text-zinc-400">
                                    {{ $post->published_at?->format('M j, Y') ?? 'Recently' }}
                                </time>
                            </div>
                        </div>
                    </article>
                @endforeach
            </div>

            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @else
            <x-empty-state
                title="No results match your search"
                description="We could not find any articles matching '{{ $query }}'. Try using different keywords or resetting filters."
                actionLabel="Reset Search"
                actionUrl="javascript:void(0)"
            />
            <div class="text-center mt-2">
                <button wire:click="clearFilters" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                    Clear all filters and search query
                </button>
            </div>
        @endif
    @else
        <!-- Initial Prompt State with Categories and Learning Paths -->
        <div class="py-12 text-center text-zinc-400 dark:text-zinc-500 max-w-lg mx-auto">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Enter a query above to explore our publications</p>
            <p class="text-xs text-zinc-500">Or browse curated <a href="{{ route('series.index') }}" class="text-indigo-600 dark:text-indigo-400 underline">Learning Paths</a> and <a href="{{ route('posts.index') }}" class="text-indigo-600 dark:text-indigo-400 underline">Topics</a>.</p>
        </div>
    @endif
</div>
