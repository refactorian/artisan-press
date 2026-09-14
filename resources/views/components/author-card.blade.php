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

        <!-- Social & Profile Links -->
        <div class="flex flex-wrap items-center gap-3 text-xs">
            @if($author->twitter_handle)
                <a href="https://twitter.com/{{ $author->twitter_handle }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $author->name }} on X / Twitter" class="inline-flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400 hover:text-[#1DA1F2] dark:hover:text-[#1DA1F2] transition-colors font-medium">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M18.244 2.25h3.308l-7.227 8.26 8.502 11.24H16.17l-4.714-6.231-5.401 6.231H2.746l7.73-8.835L1.254 2.25H8.08l4.713 6.231zm-1.161 17.52h1.833L7.084 4.126H5.117z"/></svg>
                    <span>@{{ $author->twitter_handle }}</span>
                </a>
            @endif

            @if($author->github_username)
                <a href="https://github.com/{{ $author->github_username }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $author->name }} on GitHub" class="inline-flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-colors font-medium">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path fill-rule="evenodd" d="M12 2C6.477 2 2 6.484 2 12.017c0 4.425 2.865 8.18 6.839 9.504.5.092.682-.217.682-.483 0-.237-.008-.868-.013-1.703-2.782.605-3.369-1.343-3.369-1.343-.454-1.158-1.11-1.466-1.11-1.466-.908-.62.069-.608.069-.608 1.003.07 1.531 1.032 1.531 1.032.892 1.53 2.341 1.088 2.91.832.092-.647.35-1.088.636-1.338-2.22-.253-4.555-1.113-4.555-4.951 0-1.093.39-1.988 1.029-2.688-.103-.253-.446-1.272.098-2.65 0 0 .84-.27 2.75 1.026A9.564 9.564 0 0 1 12 6.844a9.59 9.59 0 0 1 2.504.337c1.909-1.296 2.747-1.027 2.747-1.027.546 1.379.202 2.398.1 2.651.64.7 1.028 1.595 1.028 2.688 0 3.848-2.339 4.695-4.566 4.943.359.309.678.92.678 1.855 0 1.338-.012 2.419-.012 2.747 0 .268.18.58.688.482A10.02 10.02 0 0 0 22 12.017C22 6.484 17.522 2 12 2z" clip-rule="evenodd"/></svg>
                    <span>{{ $author->github_username }}</span>
                </a>
            @endif

            @if($author->linkedin_url)
                <a href="{{ $author->linkedin_url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $author->name }} on LinkedIn" class="inline-flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400 hover:text-[#0A66C2] dark:hover:text-[#0A66C2] transition-colors font-medium">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24" aria-hidden="true"><path d="M20.447 20.452h-3.554v-5.569c0-1.328-.027-3.037-1.852-3.037-1.853 0-2.136 1.445-2.136 2.939v5.667H9.351V9h3.414v1.561h.046c.477-.9 1.637-1.85 3.37-1.85 3.601 0 4.267 2.37 4.267 5.455v6.286zM5.337 7.433a2.062 2.062 0 0 1-2.063-2.065 2.064 2.064 0 1 1 2.063 2.065zm1.782 13.019H3.555V9h3.564v11.452zM22.225 0H1.771C.792 0 0 .774 0 1.729v20.542C0 23.227.792 24 1.771 24h20.451C23.2 24 24 23.227 24 22.271V1.729C24 .774 23.2 0 22.222 0h.003z"/></svg>
                    <span>LinkedIn</span>
                </a>
            @endif

            @if($author->website_url)
                <a href="{{ $author->website_url }}" target="_blank" rel="noopener noreferrer" aria-label="{{ $author->name }}'s website" class="inline-flex items-center gap-1.5 text-zinc-600 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors font-medium">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true"><path stroke-linecap="round" stroke-linejoin="round" d="M12 21a9.004 9.004 0 0 0 8.716-6.747M12 21a9.004 9.004 0 0 1-8.716-6.747M12 21c2.485 0 4.5-4.03 4.5-9S14.485 3 12 3m0 18c-2.485 0-4.5-4.03-4.5-9S9.515 3 12 3m0 0a8.997 8.997 0 0 1 7.843 4.582M12 3a8.997 8.997 0 0 0-7.843 4.582m15.686 0A11.953 11.953 0 0 1 12 10.5c-2.998 0-5.74-1.1-7.843-2.918m15.686 0A8.959 8.959 0 0 1 21 12c0 .778-.099 1.533-.284 2.253m0 0A17.919 17.919 0 0 1 12 16.5c-3.162 0-6.133-.815-8.716-2.247m0 0A9.015 9.015 0 0 1 3 12c0-1.605.42-3.113 1.157-4.418"/></svg>
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
                <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                </svg>
            </a>
        </div>
    </div>
</div>
