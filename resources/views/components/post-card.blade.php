@props([
    'post',
    'featured' => false,
    'layout' => 'grid', // 'grid' or 'list'
])

@php
    $imageUrl = $post->getFirstMediaUrl('featured_image', 'medium') ?: $post->getFirstMediaUrl('featured_image');
    $category = $post->categories->first();
    $author = $post->author;
    $readingTime = $post->reading_time ?: ($post->calculateReadingTime() ?? 3);
@endphp

@if($layout === 'list')
    <article class="group relative flex flex-col sm:flex-row items-stretch gap-6 p-4 sm:p-5 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all duration-200 hover:shadow-lg hover:shadow-zinc-200/40 dark:hover:shadow-none">
        <!-- Thumbnail -->
        <a href="{{ route('posts.show', $post) }}" class="relative sm:w-64 sm:h-48 h-52 flex-shrink-0 overflow-hidden rounded-xl bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-pink-500/10 dark:from-indigo-950/40 dark:to-purple-950/40">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
            @else
                <div class="w-full h-full flex flex-col items-center justify-center p-4 text-center bg-gradient-to-tr from-slate-100 via-indigo-50/50 to-slate-200 dark:from-zinc-800 dark:via-indigo-950/30 dark:to-zinc-850">
                    <span class="text-3xl font-extrabold text-indigo-600/30 dark:text-indigo-400/20 uppercase tracking-wider">
                        {{ substr($category?->name ?? 'Blog', 0, 3) }}
                    </span>
                </div>
            @endif

            @if($post->is_featured)
                <span class="absolute top-2.5 left-2.5 inline-flex items-center gap-1 px-2.5 py-0.5 rounded-full text-xs font-semibold bg-amber-500 text-white shadow-sm">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd"/>
                    </svg>
                    Featured
                </span>
            @endif
        </a>

        <!-- Content -->
        <div class="flex flex-col flex-1 justify-between py-1">
            <div>
                <!-- Category & Meta -->
                <div class="flex items-center gap-3 text-xs mb-2.5">
                    @if($category)
                        <a href="{{ route('categories.show', $category) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-md font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-950/60 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors">
                            {{ $category->name }}
                        </a>
                    @endif
                    <span class="text-zinc-400 dark:text-zinc-600">•</span>
                    <span class="text-zinc-500 dark:text-zinc-400 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        {{ $readingTime }} min read
                    </span>
                </div>

                <!-- Title -->
                <h3 class="text-lg sm:text-xl font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors leading-snug">
                    <a href="{{ route('posts.show', $post) }}">
                        {{ $post->title }}
                    </a>
                </h3>

                <!-- Excerpt -->
                @if($post->excerpt)
                    <p class="mt-2 text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2">
                        {{ $post->excerpt }}
                    </p>
                @endif
            </div>

            <!-- Author & Date -->
            <div class="mt-4 pt-3 border-t border-zinc-100 dark:border-zinc-800/80 flex items-center justify-between text-xs">
                @if($author)
                    <a href="{{ route('authors.show', $author) }}" class="flex items-center gap-2 group/author">
                        @if($author->getFilamentAvatarUrl())
                            <img src="{{ $author->getFilamentAvatarUrl() }}" alt="{{ $author->name }}" class="w-6 h-6 rounded-full object-cover">
                        @else
                            <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold flex items-center justify-center text-[10px]">
                                {{ substr($author->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="font-medium text-zinc-700 dark:text-zinc-300 group-hover/author:text-indigo-600 dark:group-hover/author:text-indigo-400 transition-colors">
                            {{ $author->name }}
                        </span>
                    </a>
                @endif

                <div class="flex items-center gap-3">
                    <time datetime="{{ $post->published_at?->toISOString() }}" class="text-zinc-400 dark:text-zinc-500">
                        {{ $post->published_at?->format('M j, Y') ?? 'Recently' }}
                    </time>

                    <button
                        type="button"
                        x-data="{
                            saved: false,
                            check() { this.saved = window.$bookmarks ? window.$bookmarks.isSaved('{{ $post->slug }}') : false; },
                            toggle() {
                                if (window.$bookmarks) {
                                    window.$bookmarks.toggle({
                                        title: '{{ addslashes($post->title) }}',
                                        slug: '{{ $post->slug }}',
                                        url: '{{ route('posts.show', $post) }}',
                                        category: '{{ addslashes($category?->name ?? '') }}',
                                        reading_time: {{ $readingTime }}
                                    });
                                    this.check();
                                }
                            }
                        }"
                        x-init="check(); window.addEventListener('bookmarks-updated', () => check())"
                        @click.prevent.stop="toggle()"
                        class="p-1 rounded-lg text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors cursor-pointer"
                        :title="saved ? 'Remove from saved reading list' : 'Save article for later'"
                    >
                        <svg class="w-4 h-4" :class="{ 'text-indigo-600 dark:text-indigo-400': saved }" :fill="saved ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </article>
@else
    <article class="group relative flex flex-col rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 hover:border-zinc-300 dark:hover:border-zinc-700 overflow-hidden transition-all duration-200 hover:shadow-xl hover:shadow-zinc-200/50 dark:hover:shadow-none hover:-translate-y-0.5">
        <!-- Thumbnail -->
        <a href="{{ route('posts.show', $post) }}" class="relative aspect-[16/10] overflow-hidden bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-pink-500/10 dark:from-indigo-950/40 dark:to-purple-950/40">
            @if($imageUrl)
                <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" loading="lazy">
            @else
                <div class="w-full h-full flex flex-col items-center justify-center p-6 text-center bg-gradient-to-tr from-slate-100 via-indigo-50/60 to-slate-200 dark:from-zinc-800 dark:via-indigo-950/40 dark:to-zinc-850">
                    <span class="text-4xl font-black text-indigo-600/20 dark:text-indigo-400/20 tracking-wider">
                        {{ substr($category?->name ?? 'Blog', 0, 3) }}
                    </span>
                </div>
            @endif

            @if($post->is_featured)
                <span class="absolute top-3 left-3 inline-flex items-center gap-1 px-2.5 py-1 rounded-full text-xs font-semibold bg-amber-500 text-white shadow-sm backdrop-blur-md">
                    <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M10.868 2.884c-.321-.772-1.415-.772-1.736 0l-1.83 4.401-4.753.381c-.833.067-1.171 1.107-.536 1.651l3.62 3.102-1.106 4.637c-.194.813.691 1.456 1.405 1.02L10 15.591l4.069 2.485c.713.436 1.598-.207 1.404-1.02l-1.106-4.637 3.62-3.102c.635-.544.297-1.584-.536-1.65l-4.752-.382-1.831-4.401Z" clip-rule="evenodd"/>
                    </svg>
                    Featured
                </span>
            @endif
        </a>

        <!-- Body -->
        <div class="flex flex-col flex-1 p-5">
            <!-- Category & Reading Time -->
            <div class="flex items-center justify-between text-xs mb-3">
                @if($category)
                    <a href="{{ route('categories.show', $category) }}" class="inline-flex items-center px-2.5 py-0.5 rounded-full font-medium bg-indigo-50 text-indigo-700 dark:bg-indigo-950/70 dark:text-indigo-300 hover:bg-indigo-100 dark:hover:bg-indigo-900 transition-colors">
                        {{ $category->name }}
                    </a>
                @else
                    <span></span>
                @endif

                <span class="text-zinc-500 dark:text-zinc-400 flex items-center gap-1 text-[11px]">
                    <svg class="w-3 h-3" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ $readingTime }} min
                </span>
            </div>

            <!-- Title -->
            <h3 class="text-lg font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2 leading-snug">
                <a href="{{ route('posts.show', $post) }}">
                    {{ $post->title }}
                </a>
            </h3>

            <!-- Excerpt -->
            @if($post->excerpt)
                <p class="mt-2.5 text-sm text-zinc-600 dark:text-zinc-400 line-clamp-2 leading-relaxed flex-1">
                    {{ $post->excerpt }}
                </p>
            @endif

            <!-- Author & Date footer -->
            <div class="mt-5 pt-3 border-t border-zinc-100 dark:border-zinc-800 flex items-center justify-between text-xs">
                @if($author)
                    <a href="{{ route('authors.show', $author) }}" class="flex items-center gap-2 group/author">
                        @if($author->getFilamentAvatarUrl())
                            <img src="{{ $author->getFilamentAvatarUrl() }}" alt="{{ $author->name }}" class="w-6 h-6 rounded-full object-cover">
                        @else
                            <div class="w-6 h-6 rounded-full bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300 font-bold flex items-center justify-center text-[10px]">
                                {{ substr($author->name, 0, 1) }}
                            </div>
                        @endif
                        <span class="font-medium text-zinc-700 dark:text-zinc-300 group-hover/author:text-indigo-600 dark:group-hover/author:text-indigo-400 transition-colors truncate max-w-[120px]">
                            {{ $author->name }}
                        </span>
                    </a>
                @endif

                <div class="flex items-center gap-3">
                    <time datetime="{{ $post->published_at?->toISOString() }}" class="text-zinc-400 dark:text-zinc-500">
                        {{ $post->published_at?->format('M j, Y') ?? 'Recently' }}
                    </time>

                    <button
                        type="button"
                        x-data="{
                            saved: false,
                            check() { this.saved = window.$bookmarks ? window.$bookmarks.isSaved('{{ $post->slug }}') : false; },
                            toggle() {
                                if (window.$bookmarks) {
                                    window.$bookmarks.toggle({
                                        title: '{{ addslashes($post->title) }}',
                                        slug: '{{ $post->slug }}',
                                        url: '{{ route('posts.show', $post) }}',
                                        category: '{{ addslashes($category?->name ?? '') }}',
                                        reading_time: {{ $readingTime }}
                                    });
                                    this.check();
                                }
                            }
                        }"
                        x-init="check(); window.addEventListener('bookmarks-updated', () => check())"
                        @click.prevent.stop="toggle()"
                        class="p-1 rounded-lg text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors cursor-pointer"
                        :title="saved ? 'Remove from saved reading list' : 'Save article for later'"
                    >
                        <svg class="w-4 h-4" :class="{ 'text-indigo-600 dark:text-indigo-400': saved }" :fill="saved ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </article>
@endif
