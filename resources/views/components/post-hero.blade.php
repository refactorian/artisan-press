@props(['post'])

@php
    $imageUrl = $post->getFirstMediaUrl('featured_image', 'large') ?: $post->getFirstMediaUrl('featured_image');
    $category = $post->categories->first();
    $author = $post->author;
    $readingTime = $post->reading_time ?: ($post->calculateReadingTime() ?? 4);
@endphp

<article class="relative overflow-hidden rounded-3xl bg-zinc-900 text-white border border-zinc-800 shadow-2xl group">
    <!-- Background Image or Decorative Gradient -->
    <div class="absolute inset-0">
        @if($imageUrl)
            <img src="{{ $imageUrl }}" alt="{{ $post->title }}" class="w-full h-full object-cover opacity-35 group-hover:scale-105 transition-transform duration-700 ease-out">
        @else
            <div class="w-full h-full bg-gradient-to-br from-indigo-950 via-zinc-900 to-slate-900 opacity-90"></div>
        @endif
        <div class="absolute inset-0 bg-gradient-to-t from-zinc-950 via-zinc-950/70 to-transparent"></div>
    </div>

    <!-- Hero Content Overlay -->
    <div class="relative z-10 p-6 sm:p-10 md:p-14 flex flex-col justify-end min-h-[420px] sm:min-h-[500px]">
        <div class="max-w-3xl">
            <!-- Badges -->
            <div class="flex flex-wrap items-center gap-3 mb-4">
                <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-500 text-white shadow-sm">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                    </svg>
                    Hero Story
                </span>

                @if($category)
                    <a href="{{ route('categories.show', $category) }}" class="inline-flex items-center px-3 py-1 rounded-full text-xs font-semibold bg-white/10 hover:bg-white/20 text-zinc-200 backdrop-blur-md transition-colors">
                        {{ $category->name }}
                    </a>
                @endif

                <span class="text-xs text-zinc-400 flex items-center gap-1 backdrop-blur-sm px-2.5 py-1 rounded-full bg-black/20">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                    </svg>
                    {{ $readingTime }} min read
                </span>
            </div>

            <!-- Title -->
            <h1 class="text-2xl sm:text-4xl md:text-5xl font-extrabold tracking-tight text-white leading-[1.15] mb-4 group-hover:text-indigo-200 transition-colors">
                <a href="{{ route('posts.show', $post) }}">
                    {{ $post->title }}
                </a>
            </h1>

            <!-- Excerpt -->
            @if($post->excerpt)
                <p class="text-base sm:text-lg text-zinc-300 line-clamp-2 sm:line-clamp-3 mb-6 max-w-2xl font-normal leading-relaxed">
                    {{ $post->excerpt }}
                </p>
            @endif

            <!-- Author & Date -->
            <div class="flex flex-wrap items-center gap-4 text-sm text-zinc-300 pt-2 border-t border-white/10">
                @if($author)
                    <a href="{{ route('authors.show', $author) }}" class="flex items-center gap-2.5 hover:text-white transition-colors">
                        @if($author->getFilamentAvatarUrl())
                            <img src="{{ $author->getFilamentAvatarUrl() }}" alt="{{ $author->name }}" class="w-9 h-9 rounded-full object-cover ring-2 ring-indigo-500/50">
                        @else
                            <div class="w-9 h-9 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-sm ring-2 ring-indigo-400/30">
                                {{ substr($author->name, 0, 1) }}
                            </div>
                        @endif
                        <div>
                            <span class="font-medium text-white block leading-tight">{{ $author->name }}</span>
                            <span class="text-xs text-zinc-400 block">{{ $author->job_title ?? 'Author' }}</span>
                        </div>
                    </a>
                @endif

                <span class="text-zinc-600 dark:text-zinc-500">•</span>

                <time datetime="{{ $post->published_at?->toISOString() }}" class="text-xs text-zinc-400">
                    Published {{ $post->published_at?->format('M j, Y') ?? 'Recently' }}
                </time>

                <a href="{{ route('posts.show', $post) }}" class="ml-auto inline-flex items-center gap-1.5 text-xs sm:text-sm font-semibold text-indigo-400 hover:text-indigo-300 transition-colors group/link">
                    <span>Read Article</span>
                    <svg class="w-4 h-4 transition-transform group-hover/link:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</article>
