<x-layouts.app :metadata="$metadata">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <x-breadcrumbs :items="$breadcrumbs" />

        <!-- Category Banner -->
        <div class="my-6 p-8 sm:p-10 rounded-3xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 dark:bg-indigo-950/80 dark:text-indigo-300 mb-3">
                Category
            </span>

            <h1 class="text-3xl sm:text-4xl font-black text-zinc-900 dark:text-white tracking-tight mb-3">
                {{ $category->name }}
            </h1>

            @if($category->description)
                <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 max-w-2xl leading-relaxed">
                    {{ $category->description }}
                </p>
            @endif

            <div class="mt-4 pt-4 border-t border-zinc-200/80 dark:border-zinc-800 text-xs text-zinc-500 flex items-center gap-4">
                <span><strong>{{ $posts->total() }}</strong> {{ str('article')->plural($posts->total()) }} published</span>
                <span>•</span>
                <a href="{{ route('feed.category', $category->slug) }}" target="_blank" class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 hover:underline">
                    <span>Subscribe to this topic RSS</span>
                </a>
            </div>
        </div>

        <!-- Posts List -->
        @if($posts->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @else
            <x-empty-state
                title="No articles in this category yet"
                description="We haven't published any articles in this topic yet. Check back soon!"
                actionLabel="Browse all articles"
                actionUrl="{{ route('posts.index') }}"
            />
        @endif

        @if(isset($otherCategories) && $otherCategories->isNotEmpty())
            <div class="mt-16 pt-10 border-t border-zinc-200/80 dark:border-zinc-800">
                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4">
                    Explore Other Topics
                </h3>
                <div class="flex flex-wrap gap-2.5">
                    @foreach($otherCategories as $otherCat)
                        <a
                            href="{{ route('categories.show', $otherCat) }}"
                            class="inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 hover:border-indigo-500/40 text-xs font-semibold text-zinc-800 dark:text-zinc-200 transition-all shadow-sm"
                        >
                            <span>{{ $otherCat->name }}</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-zinc-200 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
                                {{ $otherCat->posts_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</x-layouts.app>
