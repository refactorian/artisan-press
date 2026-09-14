<x-layouts.app :metadata="$metadata">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12 space-y-16">
        <!-- 1. Hero Post Section -->
        @if($heroPost)
            <section aria-label="Lead Story">
                <x-post-hero :post="$heroPost" />
            </section>
        @endif

        <!-- 2. Categories / Topics Ribbon -->
        @if($categories->isNotEmpty())
            <section aria-label="Explore Topics" class="py-2">
                <div class="flex items-center justify-between mb-4">
                    <h2 class="text-xs font-bold uppercase tracking-widest text-zinc-500 dark:text-zinc-400">
                        Explore Topics
                    </h2>
                    <a href="{{ route('posts.index') }}" class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        View all topics →
                    </a>
                </div>

                <div class="flex flex-wrap gap-2.5">
                    @foreach($categories as $category)
                        <a
                            href="{{ route('categories.show', $category) }}"
                            class="group inline-flex items-center gap-2 px-4 py-2 rounded-xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 hover:border-indigo-500/40 dark:hover:border-indigo-400/40 hover:bg-white dark:hover:bg-zinc-850 transition-all text-xs font-semibold text-zinc-800 dark:text-zinc-200 shadow-sm"
                        >
                            <span>{{ $category->name }}</span>
                            <span class="px-1.5 py-0.5 rounded-full text-[10px] bg-zinc-200/70 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400 group-hover:bg-indigo-100 dark:group-hover:bg-indigo-950 group-hover:text-indigo-600 dark:group-hover:text-indigo-300 transition-colors">
                                {{ $category->posts_count }}
                            </span>
                        </a>
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 3. Featured Stories Section -->
        @if($featuredPosts->isNotEmpty())
            <section aria-label="Featured Articles">
                <div class="flex items-center justify-between mb-8 pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
                    <div class="flex items-center gap-2.5">
                        <span class="w-2.5 h-2.5 rounded-full bg-amber-500 ring-4 ring-amber-500/20"></span>
                        <h2 class="text-xl sm:text-2xl font-black tracking-tight text-zinc-900 dark:text-white">
                            Featured Stories
                        </h2>
                    </div>
                    <a href="{{ route('posts.index') }}" class="text-xs sm:text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                        Browse all articles →
                    </a>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                    @foreach($featuredPosts as $post)
                        <x-post-card :post="$post" />
                    @endforeach
                </div>
            </section>
        @endif

        <!-- 4. Latest Articles + Trending Sidebar -->
        <section aria-label="Latest Publications" class="pt-4">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-10 lg:gap-12">
                <!-- Main Column: Latest Articles (2 cols) -->
                <div class="lg:col-span-2">
                    <div class="flex items-center justify-between mb-8 pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
                        <div class="flex items-center gap-2.5">
                            <span class="w-2.5 h-2.5 rounded-full bg-indigo-600 ring-4 ring-indigo-500/20"></span>
                            <h2 class="text-xl sm:text-2xl font-black tracking-tight text-zinc-900 dark:text-white">
                                Latest Publications
                            </h2>
                        </div>
                        <a href="{{ route('posts.index') }}" class="text-xs sm:text-sm font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                            View all →
                        </a>
                    </div>

                    @if($latestPosts->isNotEmpty())
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @foreach($latestPosts as $post)
                                <x-post-card :post="$post" />
                            @endforeach
                        </div>

                        <div class="mt-10 text-center">
                            <a
                                href="{{ route('posts.index') }}"
                                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-zinc-900 dark:bg-zinc-100 text-white dark:text-zinc-900 text-sm font-semibold hover:bg-zinc-800 dark:hover:bg-white transition-all shadow-md"
                            >
                                <span>Browse complete archive</span>
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    @else
                        <x-empty-state
                            title="No more recent articles"
                            description="Check back soon as we publish fresh editorial guides and analyses."
                        />
                    @endif
                </div>

                <!-- Sidebar: Trending Articles & Newsletter (1 col) -->
                <aside class="space-y-8">
                    <!-- Trending Posts Widget -->
                    @if($trendingPosts->isNotEmpty())
                        <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800">
                            <div class="flex items-center gap-2 mb-6 pb-3 border-b border-zinc-200/80 dark:border-zinc-800">
                                <svg class="w-4 h-4 text-amber-500" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M12.334 3.018a.75.75 0 0 1 .744.154 8.75 8.75 0 0 1 2.922 6.578c0 3.09-1.528 5.677-3.834 6.945a.75.75 0 0 1-1.077-.73 6.002 6.002 0 0 0-.251-2.148c-.282-1.002-.823-1.92-1.573-2.67a8.775 8.775 0 0 1-2.222-4.14.75.75 0 0 1 1.05-.838 6.006 6.006 0 0 0 2.215.82 8.756 8.756 0 0 1 2.026-4.17Zm-3.79 7.64a7.25 7.25 0 0 0 1.558 2.25c.577.577.994 1.282 1.21 2.052.128.455.19.927.185 1.401a7.25 7.25 0 0 0 3.753-5.782 7.251 7.251 0 0 0-2.203-5.187 7.25 7.25 0 0 0-1.284 3.197.75.75 0 0 1-1.228.411 7.265 7.265 0 0 1-1.99-3.342Z" clip-rule="evenodd"/>
                                </svg>
                                <h3 class="font-bold text-sm uppercase tracking-wider text-zinc-900 dark:text-white">
                                    Trending This Week
                                </h3>
                            </div>

                            <ol class="space-y-4">
                                @foreach($trendingPosts as $index => $tPost)
                                    <li class="flex items-start gap-3 group">
                                        <span class="text-2xl font-black text-zinc-300 dark:text-zinc-700 leading-none group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors w-6">
                                            0{{ $index + 1 }}
                                        </span>
                                        <div class="flex-1 min-w-0">
                                            @if($tCat = $tPost->categories->first())
                                                <span class="text-[11px] font-semibold text-indigo-600 dark:text-indigo-400 block mb-0.5">
                                                    {{ $tCat->name }}
                                                </span>
                                            @endif
                                            <h4 class="text-xs sm:text-sm font-bold text-zinc-800 dark:text-zinc-200 group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 leading-snug">
                                                <a href="{{ route('posts.show', $tPost) }}">
                                                    {{ $tPost->title }}
                                                </a>
                                            </h4>
                                            <span class="text-[11px] text-zinc-400 mt-1 block">
                                                {{ $tPost->reading_time ?? $tPost->calculateReadingTime() }} min read
                                            </span>
                                        </div>
                                    </li>
                                @endforeach
                            </ol>
                        </div>
                    @endif

                    <!-- Newsletter Card -->
                    <div class="p-6 rounded-2xl bg-gradient-to-br from-indigo-900 to-purple-900 text-white shadow-xl relative overflow-hidden">
                        <div class="absolute -right-8 -bottom-8 w-36 h-36 bg-white/10 rounded-full blur-2xl"></div>
                        <div class="relative z-10 space-y-3">
                            <span class="inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-white/20 text-white backdrop-blur-md">
                                Weekly Dispatch
                            </span>
                            <h3 class="text-lg font-bold">
                                Modern Architectural Insights
                            </h3>
                            <p class="text-xs text-indigo-200 leading-relaxed">
                                Join over thousands of developers getting high-signal engineering patterns.
                            </p>
                            <div class="pt-2">
                                <livewire:newsletter-form source="sidebar" lazy />
                            </div>
                        </div>
                    </div>
                </aside>
            </div>
        </section>
    </div>
</x-layouts.app>
