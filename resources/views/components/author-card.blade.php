@props(['author'])

<div class="p-6 sm:p-8 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 flex flex-col sm:flex-row gap-6 items-start sm:items-center">
    <!-- Avatar -->
    <a href="{{ route('authors.show', $author) }}" class="flex-shrink-0">
        @if($author->getFilamentAvatarUrl())
            <img src="{{ $author->getFilamentAvatarUrl() }}" alt="{{ $author->name }}" class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl object-cover ring-2 ring-indigo-500/30">
        @else
            <div class="w-20 h-20 sm:w-24 sm:h-24 rounded-2xl bg-gradient-to-br from-indigo-500 to-purple-600 text-white font-extrabold flex items-center justify-center text-3xl shadow-md">
                {{ substr($author->name, 0, 1) }}
            </div>
        @endif
    </a>

    <!-- Details -->
    <div class="flex-1">
        <div class="flex flex-wrap items-center gap-2 mb-1.5">
            <h3 class="text-xl font-bold text-zinc-900 dark:text-white">
                <a href="{{ route('authors.show', $author) }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    {{ $author->name }}
                </a>
            </h3>

            @if($author->pronouns)
                <span class="text-xs text-zinc-500 dark:text-zinc-400 px-2 py-0.5 rounded-md bg-zinc-200/60 dark:bg-zinc-800">
                    {{ $author->pronouns }}
                </span>
            @endif

            @if($author->is_featured_author)
                <span class="text-xs font-medium text-indigo-700 dark:text-indigo-300 px-2 py-0.5 rounded-md bg-indigo-50 dark:bg-indigo-950/60">
                    Staff Writer
                </span>
            @endif
        </div>

        @if($author->job_title)
            <p class="text-sm font-medium text-zinc-600 dark:text-zinc-400 mb-3">
                {{ $author->job_title }}
            </p>
        @endif

        @if($author->bio)
            <p class="text-sm text-zinc-600 dark:text-zinc-300 leading-relaxed mb-4">
                {{ $author->bio }}
            </p>
        @endif

        <!-- Links -->
        <div class="flex flex-wrap items-center gap-3 text-xs">
            @if($author->website_url)
                <a href="{{ $author->website_url }}" target="_blank" rel="noopener noreferrer" class="inline-flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418" />
                    </svg>
                    <span>Website</span>
                </a>
            @endif

            @if(!empty($author->social_links) && is_array($author->social_links))
                @foreach($author->social_links as $link)
                    @if(!empty($link['url']))
                        <a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer" class="capitalize inline-flex items-center gap-1 text-zinc-600 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors font-medium">
                            <span>{{ $link['platform'] ?? 'Social' }}</span>
                        </a>
                    @endif
                @endforeach
            @endif

            <a href="{{ route('authors.show', $author) }}" class="ml-auto inline-flex items-center gap-1 font-semibold text-indigo-600 dark:text-indigo-400 hover:underline">
                View all articles
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>
    </div>
</div>
