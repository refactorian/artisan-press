<x-layouts.app :metadata="$metadata">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <x-breadcrumbs :items="$breadcrumbs" />

        <!-- Header -->
        <div class="my-8 pb-6 border-b border-zinc-200/80 dark:border-zinc-800">
            <span class="inline-flex items-center gap-1 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-purple-50 text-purple-700 dark:bg-purple-950/70 dark:text-purple-300 mb-3">
                Learning Paths
            </span>
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-zinc-900 dark:text-white mb-2">
                Multi-Part Engineering Series
            </h1>
            <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 max-w-2xl leading-relaxed">
                Step-by-step masterclasses designed to guide you from foundational design principles through to production architectures.
            </p>
        </div>

        <!-- Series Cards Grid -->
        @if($seriesList->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($seriesList as $series)
                    <article class="p-8 rounded-3xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 hover:border-purple-500/40 dark:hover:border-purple-500/40 transition-all flex flex-col justify-between group shadow-sm">
                        <div>
                            <div class="flex items-center justify-between gap-4 mb-4">
                                <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-100 dark:bg-purple-950 text-purple-700 dark:text-purple-300">
                                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 6.042A8.967 8.967 0 0 0 6 3.75c-1.052 0-2.062.18-3 .512v14.25A8.987 8.987 0 0 1 6 18c2.305 0 4.408.867 6 2.292m0-14.25a8.966 8.966 0 0 1 6-2.292c1.052 0 2.062.18 3 .512v14.25A8.987 8.987 0 0 0 18 18a8.967 8.967 0 0 0-6 2.292m0-14.25v14.25" />
                                    </svg>
                                    {{ $series->posts_count }} {{ str('Part')->plural($series->posts_count) }}
                                </span>

                                <span class="text-xs text-zinc-400 font-medium">Curated Path</span>
                            </div>

                            <h2 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white group-hover:text-purple-600 dark:group-hover:text-purple-400 transition-colors mb-3 leading-snug">
                                <a href="{{ route('series.show', $series) }}">
                                    {{ $series->name }}
                                </a>
                            </h2>

                            @if($series->description)
                                <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed mb-6">
                                    {{ $series->description }}
                                </p>
                            @endif

                            <!-- Part Preview List -->
                            @if($series->posts->isNotEmpty())
                                <div class="mb-6 pt-4 border-t border-zinc-200/60 dark:border-zinc-800/80">
                                    <h4 class="text-xs font-bold uppercase tracking-wider text-zinc-400 mb-2.5">Included Chapters:</h4>
                                    <ul class="space-y-2 text-xs">
                                        @foreach($series->posts->take(3) as $idx => $sPost)
                                            <li class="flex items-center gap-2 text-zinc-600 dark:text-zinc-300">
                                                <span class="w-5 h-5 rounded-full bg-zinc-200 dark:bg-zinc-800 text-[10px] font-bold flex items-center justify-center text-zinc-500">
                                                    {{ $idx + 1 }}
                                                </span>
                                                <span class="truncate">{{ $sPost->title }}</span>
                                            </li>
                                        @endforeach
                                        @if($series->posts_count > 3)
                                            <li class="text-zinc-400 pl-7 text-[11px]">
                                                + {{ $series->posts_count - 3 }} more parts
                                            </li>
                                        @endif
                                    </ul>
                                </div>
                            @endif
                        </div>

                        <div class="pt-4 border-t border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                            <a href="{{ route('series.show', $series) }}" class="inline-flex items-center gap-2 text-sm font-bold text-purple-600 dark:text-purple-400 hover:text-purple-700 transition-colors group/link">
                                <span>Start this series</span>
                                <svg class="w-4 h-4 transition-transform group-hover/link:translate-x-1" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                                </svg>
                            </a>
                        </div>
                    </article>
                @endforeach
            </div>
        @else
            <x-empty-state
                title="No learning paths yet"
                description="We are currently authoring new comprehensive engineering tracks. Check back soon!"
                actionLabel="Browse all articles"
                actionUrl="{{ route('posts.index') }}"
            />
        @endif
    </div>
</x-layouts.app>
