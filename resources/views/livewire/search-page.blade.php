<div>
    <!-- Hero Header / Search Field -->
    <div class="max-w-2xl mx-auto text-center mb-10">
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
                wire:model.live.debounce.350ms="query"
                placeholder="Search by keywords, framework, topic..."
                autofocus
                class="w-full pl-12 pr-12 py-3.5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 text-base text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 shadow-md transition-all"
            >

            @if($query)
                <button
                    wire:click="$set('query', '')"
                    class="absolute inset-y-0 right-0 pr-4 flex items-center text-zinc-400 hover:text-zinc-600 dark:hover:text-zinc-200"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            @endif
        </div>

        <!-- Category Facets -->
        @if($categories->isNotEmpty())
            <div class="flex flex-wrap items-center justify-center gap-2 mt-4">
                <button
                    wire:click="$set('selectedCategory', '')"
                    class="px-3 py-1 rounded-full text-xs font-semibold cursor-pointer transition-colors {{ $selectedCategory === '' ? 'bg-indigo-600 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
                >
                    All
                </button>
                @foreach($categories as $category)
                    <button
                        wire:click="$set('selectedCategory', '{{ $category->slug }}')"
                        class="px-3 py-1 rounded-full text-xs font-semibold cursor-pointer transition-colors {{ $selectedCategory === $category->slug ? 'bg-indigo-600 text-white' : 'bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 hover:bg-zinc-200 dark:hover:bg-zinc-700' }}"
                    >
                        {{ $category->name }}
                    </button>
                @endforeach
            </div>
        @endif
    </div>

    <!-- Livewire Loading State -->
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
        </div>

        @if($posts && $posts->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>

            <div class="mt-10">
                {{ $posts->links() }}
            </div>
        @else
            <x-empty-state
                title="No results match your search"
                description="We could not find any articles matching '{{ $query }}'. Try searching for broader terms such as 'Laravel', 'Architecture', or 'PHP'."
            />
        @endif
    @else
        <!-- Initial Prompt State -->
        <div class="py-12 text-center text-zinc-400 dark:text-zinc-500">
            <svg class="w-12 h-12 mx-auto mb-3 opacity-40" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
            </svg>
            <p class="text-sm">Enter keywords above to start searching.</p>
        </div>
    @endif
</div>
