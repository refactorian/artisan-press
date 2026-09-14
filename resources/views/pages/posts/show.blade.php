<x-layouts.app :metadata="$metadata">
    @php
        $author = $post->author;
        $category = $post->categories->first();
        $imageUrl = $post->getFirstMediaUrl('featured_image', 'large') ?: $post->getFirstMediaUrl('featured_image');
        $readingTime = $post->reading_time ?: ($post->calculateReadingTime() ?? 3);
    @endphp

    <div
        x-data="{
            progressPercent: 0,
            lightboxOpen: false,
            lightboxSrc: '',
            headings: [],
            activeHeading: '',
            saved: false,
            recentlyViewedList: [],
            init() {
                // Reading progress tracker
                const updateProgress = () => {
                    const winScroll = window.scrollY || document.documentElement.scrollTop;
                    const height = document.documentElement.scrollHeight - document.documentElement.clientHeight;
                    this.progressPercent = height > 0 ? (winScroll / height) * 100 : 0;
                };
                window.addEventListener('scroll', updateProgress, { passive: true });

                // Check bookmark status
                this.saved = window.$bookmarks ? window.$bookmarks.isSaved('{{ $post->slug }}') : false;
                window.addEventListener('bookmarks-updated', () => {
                    this.saved = window.$bookmarks ? window.$bookmarks.isSaved('{{ $post->slug }}') : false;
                });

                // Track recently viewed post in localStorage
                if (window.$recentlyViewed) {
                    window.$recentlyViewed.add({
                        title: @js($post->title),
                        slug: @js($post->slug),
                        url: @js(route('posts.show', $post)),
                        category: @js($category?->name ?? ''),
                        reading_time: {{ $readingTime }},
                        image_url: @js($imageUrl)
                    });
                    this.recentlyViewedList = window.$recentlyViewed.get().filter(p => p.slug !== @js($post->slug)).slice(0, 3);
                }

                // Extract Table of Contents from .prose headings
                this.$nextTick(() => {
                    const hElements = document.querySelectorAll('.prose h2, .prose h3');
                    hElements.forEach((el, index) => {
                        if (!el.id) {
                            el.id = 'heading-' + index;
                        }
                        this.headings.push({
                            id: el.id,
                            text: el.innerText,
                            level: el.tagName.toLowerCase()
                        });
                    });

                    // Scrollspy active heading
                    window.addEventListener('scroll', () => {
                        hElements.forEach(el => {
                            const rect = el.getBoundingClientRect();
                            if (rect.top <= 160 && rect.bottom >= 0) {
                                this.activeHeading = el.id;
                            }
                        });
                    }, { passive: true });

                    // Lightbox on article images
                    document.querySelectorAll('.prose img').forEach(img => {
                        img.classList.add('cursor-zoom-in', 'transition-transform', 'hover:opacity-95');
                        img.addEventListener('click', () => {
                            this.lightboxSrc = img.src;
                            this.lightboxOpen = true;
                        });
                    });
                });
            },
            toggleBookmark() {
                if (window.$bookmarks) {
                    window.$bookmarks.toggle({
                        title: @js($post->title),
                        slug: @js($post->slug),
                        url: @js(route('posts.show', $post)),
                        category: @js($category?->name ?? ''),
                        reading_time: {{ $readingTime }}
                    });
                }
            }
        }"
        class="relative"
    >
        <!-- Top Sticky Reading Progress Bar -->
        <div class="fixed top-0 left-0 right-0 z-50 h-1 bg-transparent pointer-events-none">
            <div
                class="h-full bg-gradient-to-r from-indigo-500 via-purple-500 to-pink-500 transition-all duration-75"
                :style="`width: ${progressPercent}%`"
            ></div>
        </div>

        <!-- Image Lightbox Modal -->
        <div
            x-show="lightboxOpen"
            x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-zinc-950/90 backdrop-blur-md"
            @click="lightboxOpen = false"
            @keydown.escape.window="lightboxOpen = false"
        >
            <button
                type="button"
                @click="lightboxOpen = false"
                class="absolute top-5 right-5 p-2 rounded-full bg-white/10 text-white hover:bg-white/20 transition-colors cursor-pointer"
                aria-label="Close image lightbox"
            >
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>

            <img
                :src="lightboxSrc"
                alt="Enlarged view"
                class="max-w-full max-h-[90vh] rounded-2xl shadow-2xl object-contain"
                @click.stop
            >
        </div>

        <!-- Main Outer Layout (Grid with Sticky Sidebar on xl screens) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
            <div class="grid grid-cols-1 xl:grid-cols-[minmax(0,1fr)_300px] gap-12 items-start">
                
                <!-- Main Article Column -->
                <article class="min-w-0 max-w-4xl mx-auto w-full">
                    <!-- Breadcrumbs -->
                    <x-breadcrumbs :items="$breadcrumbs" />

                    <!-- Post Header -->
                    <header class="mt-6 mb-10">
                        <!-- Taxonomy Badges -->
                        <div class="flex flex-wrap items-center gap-2 mb-4">
                            @if($category)
                                <a href="{{ route('categories.show', $category) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/70 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors">
                                    {{ $category->name }}
                                </a>
                            @endif

                            @if($post->series)
                                <a href="{{ route('series.show', $post->series) }}" class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-semibold bg-purple-50 text-purple-700 dark:bg-purple-950/70 dark:text-purple-300 hover:bg-purple-100">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                    </svg>
                                    Series: {{ $post->series->name }}
                                </a>
                            @endif

                            <span class="text-xs text-zinc-500 dark:text-zinc-400 flex items-center gap-1 ml-auto">
                                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                </svg>
                                {{ $readingTime }} min read
                            </span>
                        </div>

                        <!-- Title -->
                        <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight text-zinc-900 dark:text-white leading-[1.15] mb-6">
                            {{ $post->title }}
                        </h1>

                        <!-- Excerpt / Subheading -->
                        @if($post->excerpt)
                            <p class="text-lg sm:text-xl text-zinc-600 dark:text-zinc-300 leading-relaxed mb-8 font-normal">
                                {{ $post->excerpt }}
                            </p>
                        @endif

                        <!-- Author & Meta Byline Bar -->
                        <div class="flex flex-wrap items-center justify-between gap-4 py-4 border-y border-zinc-200/80 dark:border-zinc-800 text-sm">
                            @if($author)
                                <div class="flex items-center gap-3">
                                    <a href="{{ route('authors.show', $author) }}">
                                        @if($author->getFilamentAvatarUrl())
                                            <img src="{{ $author->getFilamentAvatarUrl() }}" alt="{{ $author->name }}" class="w-11 h-11 rounded-full object-cover ring-2 ring-indigo-500/30">
                                        @else
                                            <div class="w-11 h-11 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-sm">
                                                {{ substr($author->name, 0, 1) }}
                                            </div>
                                        @endif
                                    </a>
                                    <div>
                                        <a href="{{ route('authors.show', $author) }}" class="font-bold text-zinc-900 dark:text-white hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                            {{ $author->name }}
                                        </a>
                                        <div class="flex items-center gap-2 text-xs text-zinc-500 dark:text-zinc-400 mt-0.5">
                                            <time datetime="{{ $post->published_at?->toISOString() }}">
                                                {{ $post->published_at?->format('F j, Y') ?? 'Recently' }}
                                            </time>
                                            <span>•</span>
                                            <span>{{ number_format($post->view_count) }} views</span>
                                        </div>
                                    </div>
                                </div>
                            @endif

                            <!-- Action Controls: Bookmark, Share Popover -->
                            <div class="flex items-center gap-2">
                                <!-- Bookmark Toggle Button -->
                                <button
                                    type="button"
                                    @click="toggleBookmark()"
                                    class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-850 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition-all cursor-pointer shadow-sm"
                                    :title="saved ? 'Remove from saved' : 'Save article'"
                                >
                                    <svg class="w-4 h-4" :class="{ 'text-indigo-600 dark:text-indigo-400': saved }" :fill="saved ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                                    </svg>
                                    <span x-text="saved ? 'Saved' : 'Save'"></span>
                                </button>

                                <!-- Share Popover / Web Share -->
                                <div class="relative" x-data="{ shareOpen: false }" @click.away="shareOpen = false">
                                    <button
                                        type="button"
                                        @click="if (navigator.share) { navigator.share({ title: @js($post->title), url: window.location.href }).catch(() => {}); } else { shareOpen = !shareOpen; }"
                                        class="flex items-center gap-1.5 px-3 py-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-850 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 text-xs font-semibold transition-all cursor-pointer shadow-sm"
                                        aria-label="Share article"
                                    >
                                        <svg class="w-4 h-4 text-zinc-500" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                        </svg>
                                        <span>Share</span>
                                    </button>

                                    <!-- Dropdown Menu -->
                                    <div
                                        x-show="shareOpen"
                                        x-cloak
                                        x-transition:enter="transition ease-out duration-100"
                                        x-transition:enter-start="opacity-0 scale-95"
                                        x-transition:enter-end="opacity-100 scale-100"
                                        x-transition:leave="transition ease-in duration-75"
                                        x-transition:leave-start="opacity-100 scale-100"
                                        x-transition:leave-end="opacity-0 scale-95"
                                        class="absolute right-0 mt-2 w-48 p-1.5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-800 shadow-xl z-30 text-xs space-y-1"
                                    >
                                        <a href="{{ $shareLinks['twitter'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-5.214-6.817L4.99 21.75H1.68l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                                            <span>Share on X</span>
                                        </a>
                                        <a href="{{ $shareLinks['linkedin'] }}" target="_blank" rel="noopener noreferrer" class="flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium">
                                            <svg class="w-3.5 h-3.5" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3a2 2 0 0 1 2 2v14a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h14m-.5 15.5v-5.3a3.26 3.26 0 0 0-3.26-3.26c-.85 0-1.84.52-2.28 1.3v-1.11h-2.79v8.37h2.79v-4.93c0-.77.62-1.4 1.39-1.4a1.4 1.4 0 0 1 1.4 1.4v4.93h2.75M6.88 8.56a1.68 1.68 0 0 0 1.68-1.68c0-.93-.75-1.69-1.68-1.69a1.69 1.69 0 0 0-1.69 1.69c0 .93.76 1.68 1.69 1.68m1.39 9.94v-8.37H5.5v8.37h2.77Z"/></svg>
                                            <span>Share on LinkedIn</span>
                                        </a>
                                        <button
                                            type="button"
                                            @click="navigator.clipboard.writeText('{{ url()->current() }}'); shareOpen = false; $dispatch('toast', { message: 'Article link copied to clipboard!', type: 'success' })"
                                            class="w-full flex items-center gap-2.5 px-3 py-2 rounded-xl hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 font-medium text-left cursor-pointer"
                                        >
                                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M13.19 8.688a4.5 4.5 0 0 1 1.242 7.244l-4.5 4.5a4.5 4.5 0 0 1-6.364-6.364l1.757-1.757m13.35-.622 1.757-1.757a4.5 4.5 0 0 0-6.364-6.364l-4.5 4.5a4.5 4.5 0 0 0 1.242 7.244" /></svg>
                                            <span>Copy Link</span>
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>

                    <!-- Featured Hero Image -->
                    @if($imageUrl)
                        <div class="mb-10 overflow-hidden rounded-3xl shadow-xl border border-zinc-200/80 dark:border-zinc-800">
                            <img
                                src="{{ $imageUrl }}"
                                alt="{{ $post->title }}"
                                loading="eager"
                                fetchpriority="high"
                                decoding="async"
                                class="w-full h-auto max-h-[520px] object-cover cursor-zoom-in"
                                @click="lightboxSrc = '{{ $imageUrl }}'; lightboxOpen = true"
                            >
                        </div>
                    @endif

                    <!-- Series Navigation Progress Bar (if in a series) -->
                    <x-series-nav :post="$post" />

                    <!-- Inline Table of Contents Component (Visible on mobile/tablet, hidden on xl where sticky sidebar is active) -->
                    <div
                        x-show="headings.length > 1"
                        class="xl:hidden my-8 p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800"
                    >
                        <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 mb-3 flex items-center gap-2">
                            <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                            </svg>
                            <span>Table of Contents</span>
                        </h3>
                        <nav>
                            <ul class="space-y-2 text-xs sm:text-sm">
                                <template x-for="heading in headings" :key="heading.id">
                                    <li :class="{ 'pl-4': heading.level === 'h3' }">
                                        <a
                                            :href="`#${heading.id}`"
                                            class="transition-colors line-clamp-1"
                                            :class="activeHeading === heading.id ? 'font-bold text-indigo-600 dark:text-indigo-400' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white'"
                                            x-text="heading.text"
                                        ></a>
                                    </li>
                                </template>
                            </ul>
                        </nav>
                    </div>

                    <!-- Main Article HTML Body -->
                    <div class="prose dark:prose-invert max-w-none">
                        {!! $post->content !!}
                    </div>

                    <!-- Render Structured Content Blocks (Callouts, Code Blocks, Takeaways) -->
                    @if(!empty($post->content_blocks) && is_array($post->content_blocks))
                        <div class="my-10 space-y-8">
                            @foreach($post->content_blocks as $block)
                                @php
                                    $type = $block['type'] ?? '';
                                    $data = $block['data'] ?? [];
                                @endphp

                                @if($type === 'callout')
                                    <div class="p-6 rounded-2xl bg-indigo-50/70 dark:bg-indigo-950/40 border-l-4 border-indigo-600 dark:border-indigo-400 text-zinc-800 dark:text-zinc-200">
                                        @if(!empty($data['title']))
                                            <h4 class="text-base font-bold text-indigo-950 dark:text-indigo-200 mb-2 flex items-center gap-2">
                                                <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                                                </svg>
                                                {{ $data['title'] }}
                                            </h4>
                                        @endif
                                        <p class="text-sm leading-relaxed">{{ $data['content'] ?? '' }}</p>
                                    </div>
                                @elseif($type === 'code')
                                    <div class="rounded-2xl overflow-hidden bg-zinc-950 border border-zinc-800 text-zinc-100 shadow-xl relative">
                                        @if(!empty($data['filename']) || !empty($data['language']))
                                            <div class="px-4 py-2.5 bg-zinc-900 border-b border-zinc-800 flex items-center justify-between text-xs text-zinc-400 font-mono">
                                                <span>{{ $data['filename'] ?? 'snippet' }}</span>
                                                <span class="uppercase font-semibold text-[10px] text-zinc-500">{{ $data['language'] ?? 'code' }}</span>
                                            </div>
                                        @endif
                                        <pre class="p-4 overflow-x-auto text-sm font-mono leading-relaxed m-0 bg-transparent"><code class="language-{{ $data['language'] ?? 'plaintext' }}">{{ $data['code'] ?? '' }}</code></pre>
                                    </div>
                                @elseif($type === 'key_takeaways')
                                    <div class="p-6 rounded-2xl bg-emerald-50/60 dark:bg-emerald-950/30 border border-emerald-200 dark:emerald-900/60">
                                        <h4 class="text-base font-bold text-emerald-950 dark:text-emerald-300 mb-3 flex items-center gap-2">
                                            <svg class="w-5 h-5 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                            </svg>
                                            {{ $data['title'] ?? 'Key Takeaways' }}
                                        </h4>
                                        @if(!empty($data['items']) && is_array($data['items']))
                                            <ul class="space-y-2 text-sm text-zinc-700 dark:text-zinc-300">
                                                @foreach($data['items'] as $item)
                                                    <li class="flex items-start gap-2">
                                                        <span class="text-emerald-600 font-bold">•</span>
                                                        <span>{{ is_array($item) ? ($item['point'] ?? json_encode($item)) : $item }}</span>
                                                    </li>
                                                @endforeach
                                            </ul>
                                        @endif
                                    </div>
                                @endif
                            @endforeach
                        </div>
                    @endif

                    <!-- Tags Bar -->
                    @if($post->tags->isNotEmpty())
                        <div class="mt-12 pt-6 border-t border-zinc-200/80 dark:border-zinc-800">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-3">
                                Tagged with:
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach($post->tags as $tag)
                                    <a href="{{ route('tags.show', $tag) }}" class="inline-flex items-center px-3 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-850 hover:bg-indigo-50 dark:hover:bg-indigo-950 text-xs font-medium text-zinc-700 dark:text-zinc-300 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                                        #{{ $tag->name }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Author Biography Card -->
                    @if($author)
                        <div class="mt-12">
                            <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 dark:text-zinc-500 mb-4">
                                About The Author
                            </h4>
                            <x-author-card :author="$author" />
                        </div>
                    @endif

                    <!-- Chronological Navigation (Next & Previous Articles) -->
                    @if($previousPost || $nextPost)
                        <div class="mt-12 pt-8 border-t border-zinc-200/80 dark:border-zinc-800 grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @if($previousPost)
                                <a href="{{ route('posts.show', $previousPost) }}" class="group p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 hover:border-indigo-500/40 transition-colors">
                                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 flex items-center gap-1 mb-1">
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 19.5 3 12m0 0 7.5-7.5M3 12h18" />
                                        </svg>
                                        Previous Article
                                    </span>
                                    <h5 class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2">
                                        {{ $previousPost->title }}
                                    </h5>
                                </a>
                            @else
                                <div></div>
                            @endif

                            @if($nextPost)
                                <a href="{{ route('posts.show', $nextPost) }}" class="group p-5 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 hover:border-indigo-500/40 transition-colors text-right">
                                    <span class="text-xs font-semibold text-zinc-400 dark:text-zinc-500 flex items-center justify-end gap-1 mb-1">
                                        Next Article
                                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                        </svg>
                                    </span>
                                    <h5 class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2">
                                        {{ $nextPost->title }}
                                    </h5>
                                </a>
                            @endif
                        </div>
                    @endif

                    <!-- Related Articles Section -->
                    @if($relatedPosts && $relatedPosts->isNotEmpty())
                        <section class="mt-16 pt-10 border-t border-zinc-200/80 dark:border-zinc-800">
                            <div class="flex items-center gap-2 mb-8">
                                <span class="w-2.5 h-2.5 rounded-full bg-indigo-600"></span>
                                <h3 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                                    Related Articles
                                </h3>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                                @foreach($relatedPosts as $rPost)
                                    <x-post-card :post="$rPost" />
                                @endforeach
                            </div>
                        </section>
                    @endif

                    <!-- Recently Viewed / Continue Reading (Client-side localStorage) -->
                    <section x-show="recentlyViewedList.length > 0" x-cloak class="mt-16 pt-10 border-t border-zinc-200/80 dark:border-zinc-800">
                        <div class="flex items-center justify-between mb-6">
                            <div class="flex items-center gap-2">
                                <span class="w-2.5 h-2.5 rounded-full bg-purple-600"></span>
                                <h3 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white tracking-tight">
                                    Recently Viewed
                                </h3>
                            </div>
                            <span class="text-xs text-zinc-400 font-medium">Continue where you left off</span>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                            <template x-for="item in recentlyViewedList" :key="item.slug">
                                <a :href="item.url" class="group flex flex-col p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 hover:border-indigo-500/40 transition-all shadow-sm">
                                    <template x-if="item.image_url">
                                        <img :src="item.image_url" :alt="item.title" class="w-full h-32 object-cover rounded-xl mb-3 group-hover:scale-[1.02] transition-transform" />
                                    </template>
                                    <span class="text-[10px] font-bold uppercase tracking-wider text-indigo-600 dark:text-indigo-400 mb-1" x-text="item.category || 'Article'"></span>
                                    <h4 class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 leading-snug" x-text="item.title"></h4>
                                    <div class="mt-auto pt-3 text-[11px] text-zinc-400 flex items-center justify-between">
                                        <span x-text="`${item.reading_time || 3} min read`"></span>
                                        <span class="text-indigo-600 dark:text-indigo-400 font-semibold group-hover:translate-x-0.5 transition-transform">Read →</span>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </section>

                    <!-- Interactive Threaded Comments Section (Lazy Loaded) -->
                    <section class="mt-16 pt-10 border-t border-zinc-200/80 dark:border-zinc-800">
                        <livewire:post-comments :post="$post" lazy />
                    </section>
                </article>

                <!-- Sticky Sidebar on XL Screens (ToC + Quick Actions) -->
                <aside class="hidden xl:block sticky top-24 space-y-6">
                    <!-- Table of Contents Widget -->
                    <div
                        x-show="headings.length > 1"
                        class="p-5 rounded-2xl bg-zinc-50/90 dark:bg-zinc-900/90 backdrop-blur-sm border border-zinc-200/80 dark:border-zinc-800 shadow-sm"
                    >
                        <div class="flex items-center justify-between mb-3 pb-2 border-b border-zinc-200/80 dark:border-zinc-800">
                            <h3 class="text-xs font-bold uppercase tracking-wider text-zinc-500 dark:text-zinc-400 flex items-center gap-1.5">
                                <svg class="w-4 h-4 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 6.75h12M8.25 12h12m-12 5.25h12M3.75 6.75h.007v.008H3.75V6.75Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0ZM3.75 12h.007v.008H3.75V12Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Zm-.375 5.25h.007v.008H3.75v-.008Zm.375 0a.375.375 0 1 1-.75 0 .375.375 0 0 1 .75 0Z" />
                                </svg>
                                <span>On This Page</span>
                            </h3>
                            <span class="text-[10px] text-zinc-400 font-mono" x-text="`${Math.round(progressPercent)}%`"></span>
                        </div>

                        <nav class="max-h-[calc(100vh-16rem)] overflow-y-auto pr-1 text-xs space-y-1">
                            <template x-for="heading in headings" :key="heading.id">
                                <a
                                    :href="`#${heading.id}`"
                                    class="block py-1 px-2 rounded-lg transition-colors leading-snug line-clamp-2"
                                    :class="{
                                        'pl-4 text-[11px]': heading.level === 'h3',
                                        'bg-indigo-50 text-indigo-700 font-semibold dark:bg-indigo-950/60 dark:text-indigo-300': activeHeading === heading.id,
                                        'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800': activeHeading !== heading.id
                                    }"
                                    x-text="heading.text"
                                ></a>
                            </template>
                        </nav>
                    </div>

                    <!-- Quick Article Controls Widget -->
                    <div class="p-4 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 space-y-3 text-xs">
                        <div class="text-zinc-500 dark:text-zinc-400 font-semibold uppercase tracking-wider text-[10px]">
                            Quick Actions
                        </div>
                        <div class="flex items-center gap-2">
                            <button
                                type="button"
                                @click="toggleBookmark()"
                                class="flex-1 inline-flex items-center justify-center gap-1.5 py-2 px-3 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-850 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-800 dark:text-zinc-200 font-semibold transition-all cursor-pointer shadow-sm"
                            >
                                <svg class="w-4 h-4" :class="{ 'text-indigo-600 dark:text-indigo-400': saved }" :fill="saved ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                                </svg>
                                <span x-text="saved ? 'Saved' : 'Bookmark'"></span>
                            </button>
                            <button
                                type="button"
                                @click="if (navigator.share) { navigator.share({ title: @js($post->title), url: window.location.href }).catch(() => {}); } else { navigator.clipboard.writeText(window.location.href); $dispatch('toast', { message: 'Link copied!', type: 'success' }); }"
                                class="inline-flex items-center justify-center p-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-white dark:bg-zinc-850 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-700 dark:text-zinc-300 transition-all cursor-pointer shadow-sm"
                                title="Share or Copy Link"
                            >
                                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M7.217 10.907a2.25 2.25 0 1 0 0 2.186m0-2.186c.18.324.283.696.283 1.093s-.103.77-.283 1.093m0-2.186 9.566-5.314m-9.566 7.5 9.566 5.314m0 0a2.25 2.25 0 1 0 3.935 2.186 2.25 2.25 0 0 0-3.935-2.186Zm0-12.814a2.25 2.25 0 1 0 3.933-2.185 2.25 2.25 0 0 0-3.933 2.185Z" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </aside>

            </div>
        </div>
    </div>
</x-layouts.app>
