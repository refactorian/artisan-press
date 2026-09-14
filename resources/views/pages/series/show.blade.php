<x-layouts.app :metadata="$metadata">
    <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <x-breadcrumbs :items="$breadcrumbs" />

        <!-- Series Banner -->
        <header class="my-8 p-8 sm:p-12 rounded-3xl bg-gradient-to-br from-purple-950 via-zinc-900 to-zinc-950 text-white border border-purple-900/50 shadow-2xl relative overflow-hidden">
            <div class="absolute -right-12 -top-12 w-64 h-64 bg-purple-600/20 rounded-full blur-3xl pointer-events-none"></div>

            <div class="relative z-10 max-w-3xl">
                <div class="flex flex-wrap items-center gap-3 mb-4">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-500 text-white shadow-sm">
                        Series Track
                    </span>
                    <span class="text-xs text-purple-200">
                        {{ $series->posts->count() }} Chapters
                    </span>
                    <span class="text-xs text-purple-400">•</span>
                    <span class="text-xs text-purple-200 flex items-center gap-1">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                        </svg>
                        ~{{ $totalReadingTime }} min total reading time
                    </span>
                </div>

                <h1 class="text-3xl sm:text-4xl md:text-5xl font-black tracking-tight leading-tight mb-4">
                    {{ $series->name }}
                </h1>

                @if($series->description)
                    <p class="text-base sm:text-lg text-zinc-300 leading-relaxed mb-6">
                        {{ $series->description }}
                    </p>
                @endif

                @if($firstPost = $series->posts->first())
                    <a
                        href="{{ route('posts.show', $firstPost) }}"
                        class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-purple-600 hover:bg-purple-500 text-white text-sm font-bold shadow-lg transition-all"
                    >
                        <span>Start Chapter 1</span>
                        <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                @endif
            </div>
        </header>

        <!-- Chapter Curriculum -->
        <section class="mt-12">
            <h2 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white mb-6">
                Curriculum & Chapter Checklist
            </h2>

            <div class="space-y-4">
                @foreach($series->posts as $index => $post)
                    @php
                        $rTime = $post->reading_time ?: ($post->calculateReadingTime() ?? 3);
                    @endphp
                    <article class="p-5 sm:p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 hover:border-purple-500/40 dark:hover:border-purple-500/40 transition-all flex flex-col sm:flex-row items-start sm:items-center justify-between gap-4 group">
                        <div class="flex items-start gap-4 flex-1">
                            <span class="w-9 h-9 rounded-xl bg-purple-100 dark:bg-purple-950/80 text-purple-700 dark:text-purple-300 font-extrabold flex items-center justify-center text-sm flex-shrink-0">
                                0{{ $index + 1 }}
                            </span>
                            <div class="flex-1">
                                <h3 class="text-base sm:text-lg font-bold text-zinc-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors">
                                    <a href="{{ route('posts.show', $post) }}">
                                        {{ $post->title }}
                                    </a>
                                </h3>
                                @if($post->excerpt)
                                    <p class="mt-1 text-xs sm:text-sm text-zinc-600 dark:text-zinc-400 line-clamp-1">
                                        {{ $post->excerpt }}
                                    </p>
                                @endif
                                <div class="flex items-center gap-3 text-xs text-zinc-400 mt-2">
                                    <span>Part {{ $index + 1 }}</span>
                                    <span>•</span>
                                    <span>{{ $rTime }} min read</span>
                                    @if($post->author)
                                        <span>•</span>
                                        <span>By {{ $post->author->name }}</span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <a
                            href="{{ route('posts.show', $post) }}"
                            class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white dark:bg-zinc-800 text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:bg-purple-50 hover:text-purple-700 dark:hover:bg-purple-950 dark:hover:text-purple-300 transition-colors border border-zinc-200 dark:border-zinc-700 flex-shrink-0"
                        >
                            <span>Read Part</span>
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                            </svg>
                        </a>
                    </article>
                @endforeach
            </div>
        </section>
    </div>
</x-layouts.app>
