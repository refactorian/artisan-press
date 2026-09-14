@php
    $siteName = \App\Models\Setting::get('site_name', config('app.name', 'Laravel Modern Blog'));
    $tagline = \App\Models\Setting::get('site_tagline', 'In-depth engineering insights, architecture patterns, and tutorials.');
    $footerMenu = \App\Models\NavigationMenu::where('location', 'footer')
        ->where('is_active', true)
        ->with(['rootItems' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])
        ->first();

    $footerCategories = \App\Models\Category::active()
        ->whereHas('posts', function ($q) {
            $q->published();
        })
        ->withCount(['posts' => function ($q) {
            $q->published();
        }])
        ->orderBy('sort_order')
        ->limit(6)
        ->get();
@endphp

<footer class="border-t border-zinc-200/80 dark:border-zinc-800/80 bg-zinc-50 dark:bg-zinc-950 text-zinc-600 dark:text-zinc-400 transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-8 lg:gap-12">
            <!-- Brand & Tagline & Feeds (2 cols) -->
            <div class="lg:col-span-2 space-y-4">
                <a href="{{ route('home') }}" class="inline-flex items-center gap-2 font-black text-xl text-zinc-900 dark:text-white">
                    <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 to-purple-600 flex items-center justify-center text-white text-sm shadow-md">
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                        </svg>
                    </span>
                    <span>{{ $siteName }}</span>
                </a>

                <p class="text-sm text-zinc-500 dark:text-zinc-400 max-w-sm leading-relaxed">
                    {{ $tagline }}
                </p>

                <!-- Feeds & Syndication links -->
                <div class="flex flex-wrap items-center gap-3 pt-2 text-xs">
                    <a href="{{ route('feed.rss') }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/40 text-amber-700 dark:text-amber-400 font-medium hover:bg-amber-100 dark:hover:bg-amber-900/60 transition-colors">
                        <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M6.18 15.64a2.18 2.18 0 0 1 2.18 2.18C8.36 19 7.38 20 6.18 20C5 20 4 19 4 17.82a2.18 2.18 0 0 1 2.18-2.18M4 4.44A15.56 15.56 0 0 1 19.56 20h-2.83A12.73 12.73 0 0 0 4 7.27V4.44m0 5.66a9.9 9.9 0 0 1 9.9 9.9h-2.83A7.07 7.07 0 0 0 4 12.93v-2.83Z"/>
                        </svg>
                        <span>RSS Feed</span>
                    </a>

                    <a href="{{ route('feed.atom') }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-orange-50 dark:bg-orange-950/40 text-orange-700 dark:text-orange-400 font-medium hover:bg-orange-100 dark:hover:bg-orange-900/60 transition-colors">
                        <span>Atom Feed</span>
                    </a>

                    <a href="{{ route('sitemap') }}" target="_blank" class="inline-flex items-center gap-1.5 px-2.5 py-1 rounded-lg bg-zinc-200/70 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium hover:bg-zinc-300 dark:hover:bg-zinc-700 transition-colors">
                        <span>XML Sitemap</span>
                    </a>
                </div>
            </div>

            <!-- Categories -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white mb-4">
                    Topics & Taxonomy
                </h3>
                <ul class="space-y-2.5 text-sm">
                    @forelse($footerCategories as $cat)
                        <li>
                            <a href="{{ route('categories.show', $cat) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors flex items-center justify-between">
                                <span>{{ $cat->name }}</span>
                                <span class="text-xs text-zinc-400 dark:text-zinc-600">{{ $cat->posts_count }}</span>
                            </a>
                        </li>
                    @empty
                        <li><a href="{{ route('posts.index') }}" class="hover:text-indigo-600">All Articles</a></li>
                    @endforelse
                </ul>
            </div>

            <!-- Navigation Links -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white mb-4">
                    Quick Links
                </h3>
                <ul class="space-y-2.5 text-sm">
                    <li><a href="{{ route('home') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Home</a></li>
                    <li><a href="{{ route('posts.index') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">All Articles</a></li>
                    <li><a href="{{ route('search') }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">Search</a></li>
                    @if($footerMenu)
                        @foreach($footerMenu->rootItems as $item)
                            <li>
                                <a href="{{ $item->url }}" target="{{ $item->target ?? '_self' }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                    {{ $item->label }}
                                </a>
                            </li>
                        @endforeach
                    @endif
                </ul>
            </div>

            <!-- Newsletter Subscription (1 col) -->
            <div>
                <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-900 dark:text-white mb-2">
                    Stay Informed
                </h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mb-4 leading-relaxed">
                    Get new articles and engineering patterns delivered directly to your inbox.
                </p>

                <livewire:newsletter-form source="footer" />
            </div>
        </div>

        <!-- Bottom Copyright -->
        <div class="mt-12 pt-6 border-t border-zinc-200/80 dark:border-zinc-800/80 flex flex-col sm:flex-row items-center justify-between gap-4 text-xs text-zinc-500 dark:text-zinc-500">
            <p>© {{ date('Y') }} {{ $siteName }}. Built with Laravel & Livewire.</p>
            <div class="flex items-center gap-4">
                <a href="{{ route('sitemap') }}" class="hover:underline">Sitemap</a>
                <span>•</span>
                <a href="{{ route('feed.rss') }}" class="hover:underline">RSS</a>
                <span>•</span>
                <a href="#top" class="hover:underline flex items-center gap-1">
                    <span>Back to top</span>
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 15.75 7.5-7.5 7.5 7.5" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</footer>
