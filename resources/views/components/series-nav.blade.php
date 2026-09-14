@props(['post'])

@if($post->series)
    @php
        $series = $post->series;
        $posts = $series->posts->filter(fn($p) => $p->isPublished())->values();
        $total = $posts->count();
        $currentIndex = $posts->search(fn($p) => $p->id === $post->id);
        $currentPart = $currentIndex !== false ? $currentIndex + 1 : 1;
        $progressPct = $total > 0 ? round(($currentPart / $total) * 100) : 0;
        $nextInSeries = ($currentIndex !== false && $currentIndex + 1 < $total) ? $posts[$currentIndex + 1] : null;
        $prevInSeries = ($currentIndex !== false && $currentIndex > 0) ? $posts[$currentIndex - 1] : null;
    @endphp

    <div
        x-data="{ expanded: false }"
        class="my-8 rounded-2xl bg-purple-50/60 dark:bg-purple-950/30 border border-purple-200/80 dark:border-purple-900/60 overflow-hidden shadow-sm"
    >
        <!-- Header / Summary Bar -->
        <div class="p-5 sm:p-6">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-2">
                    <span class="w-2.5 h-2.5 rounded-full bg-purple-600 animate-pulse"></span>
                    <span class="text-xs font-bold uppercase tracking-wider text-purple-900 dark:text-purple-300">
                        Series Track: <a href="{{ route('series.show', $series) }}" class="underline hover:text-purple-600">{{ $series->name }}</a>
                    </span>
                </div>

                <div class="flex items-center gap-3 text-xs font-semibold text-purple-800 dark:text-purple-300">
                    <span>Part {{ $currentPart }} of {{ $total }}</span>
                    <button
                        type="button"
                        @click="expanded = !expanded"
                        class="px-2.5 py-1 rounded-lg bg-purple-100 dark:bg-purple-900/60 hover:bg-purple-200 text-purple-900 dark:text-purple-200 text-[11px] font-bold transition-colors cursor-pointer"
                    >
                        <span x-text="expanded ? 'Hide Chapters' : 'View All Chapters'"></span>
                    </button>
                </div>
            </div>

            <!-- Progress Bar -->
            <div class="w-full bg-purple-200/70 dark:bg-purple-900/60 h-2 rounded-full overflow-hidden mb-3">
                <div
                    class="bg-purple-600 h-full rounded-full transition-all duration-500 ease-out"
                    style="width: {{ $progressPct }}%"
                ></div>
            </div>

            <!-- Next in Series Quick Link -->
            @if($nextInSeries)
                <div class="flex items-center justify-between text-xs pt-2">
                    <span class="text-zinc-500 dark:text-zinc-400">Up next in this series:</span>
                    <a href="{{ route('posts.show', $nextInSeries) }}" class="font-bold text-purple-700 dark:text-purple-400 hover:underline flex items-center gap-1">
                        <span>{{ $nextInSeries->title }}</span>
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 4.5 21 12m0 0-7.5 7.5M21 12H3" />
                        </svg>
                    </a>
                </div>
            @endif
        </div>

        <!-- Collapsible Chapter List -->
        <div
            x-show="expanded"
            x-collapse
            class="px-5 pb-5 pt-2 border-t border-purple-200/60 dark:border-purple-900/40 bg-white/40 dark:bg-zinc-900/40"
        >
            <ol class="space-y-2 text-xs">
                @foreach($posts as $idx => $p)
                    @php $isCurrent = $p->id === $post->id; @endphp
                    <li class="flex items-center justify-between gap-3 p-2 rounded-lg {{ $isCurrent ? 'bg-purple-100/70 dark:bg-purple-900/50 font-bold text-purple-900 dark:text-purple-200' : 'text-zinc-600 dark:text-zinc-400 hover:bg-white dark:hover:bg-zinc-800' }}">
                        <div class="flex items-center gap-2 truncate">
                            <span class="w-5 h-5 rounded-full flex items-center justify-center text-[10px] {{ $isCurrent ? 'bg-purple-600 text-white' : 'bg-purple-200 dark:bg-purple-900 text-purple-700 dark:text-purple-300' }}">
                                {{ $idx + 1 }}
                            </span>
                            @if($isCurrent)
                                <span class="truncate">{{ $p->title }} <span class="text-[10px] font-normal opacity-80">(You are here)</span></span>
                            @else
                                <a href="{{ route('posts.show', $p) }}" class="truncate hover:underline">
                                    {{ $p->title }}
                                </a>
                            @endif
                        </div>
                        <span class="text-[10px] opacity-70 flex-shrink-0">{{ $p->reading_time ?? 3 }} min</span>
                    </li>
                @endforeach
            </ol>
        </div>
    </div>
@endif
