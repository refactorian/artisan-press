<x-filament-widgets::widget>
    <div class="relative overflow-hidden rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-sm p-6 sm:p-8 transition-colors">
        <!-- Ambient Background Glows -->
        <div class="pointer-events-none absolute -top-24 -right-24 h-72 w-72 rounded-full bg-gradient-to-br from-indigo-500/10 via-purple-500/10 to-transparent blur-3xl dark:from-indigo-500/20 dark:via-purple-500/10"></div>
        <div class="pointer-events-none absolute -bottom-24 -left-24 h-72 w-72 rounded-full bg-gradient-to-tr from-sky-500/10 via-indigo-500/10 to-transparent blur-3xl dark:from-sky-500/15"></div>

        <div class="relative z-10 flex flex-col gap-6 lg:flex-row lg:items-center lg:justify-between">
            <!-- Left: Greeting, Role & Context -->
            <div class="space-y-3 max-w-2xl">
                <div class="flex flex-wrap items-center gap-2.5">
                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-50 text-indigo-700 dark:bg-indigo-950/80 dark:text-indigo-300 border border-indigo-200/60 dark:border-indigo-800/60 shadow-xs">
                        <span class="h-1.5 w-1.5 rounded-full bg-indigo-600 dark:bg-indigo-400 animate-pulse"></span>
                        {{ $role }}
                    </span>

                    <span class="text-xs font-medium text-zinc-500 dark:text-zinc-400">
                        {{ $todayDate }}
                    </span>

                    <span class="hidden sm:inline-block text-zinc-300 dark:text-zinc-700">•</span>

                    <span class="inline-flex items-center gap-1 text-xs font-medium text-emerald-600 dark:text-emerald-400">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m4.5 12.75 6 6 9-13.5" />
                        </svg>
                        All Systems Operational
                    </span>
                </div>

                <div class="space-y-1">
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight text-zinc-900 dark:text-white">
                        {{ $greeting }}, {{ $user?->name ?? 'Publisher' }}
                    </h1>
                    <p class="text-sm text-zinc-600 dark:text-zinc-400 leading-relaxed">
                        Welcome to the <strong>Laravel Modern Blog</strong> executive command center. You have <span class="font-semibold text-zinc-900 dark:text-white">{{ $publishedCount }} published articles</span> reaching <span class="font-semibold text-zinc-900 dark:text-white">{{ number_format($totalViews) }} readers</span>.
                        @if($pendingComments > 0)
                            <span class="text-amber-600 dark:text-amber-400 font-semibold underline underline-offset-2">
                                {{ $pendingComments }} {{ str('comment')->plural($pendingComments) }} require moderation.
                            </span>
                        @else
                            <span class="text-emerald-600 dark:text-emerald-400">Your moderation inbox is completely clear.</span>
                        @endif
                    </p>
                </div>

                <!-- Editorial Pulse Pills -->
                <div class="flex flex-wrap items-center gap-2 pt-1 text-xs">
                    <span class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700/60">
                        <strong>{{ $draftCount }}</strong> {{ str('Draft')->plural($draftCount) }}
                    </span>
                    @if($scheduledCount > 0)
                        <span class="px-2.5 py-1 rounded-lg bg-amber-50 dark:bg-amber-950/60 text-amber-700 dark:text-amber-300 border border-amber-200/60 dark:border-amber-800/60">
                            <strong>{{ $scheduledCount }}</strong> Scheduled
                        </span>
                    @endif
                    <span class="px-2.5 py-1 rounded-lg bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-300 border border-zinc-200/60 dark:border-zinc-700/60">
                        <strong>{{ number_format($activeSubscribers) }}</strong> Newsletter Subscribers
                    </span>
                </div>
            </div>

            <!-- Right: Quick Navigation & Primary Action -->
            <div class="flex flex-col sm:flex-row lg:flex-col xl:flex-row items-stretch sm:items-center gap-3 shrink-0">
                <a
                    href="{{ $createPostUrl }}"
                    class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-sm font-bold shadow-md shadow-indigo-500/20 hover:shadow-indigo-500/30 transition-all cursor-pointer active:scale-[0.99]"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M12 4.5v15m7.5-7.5h-15" />
                    </svg>
                    <span>Write Article</span>
                </a>

                <a
                    href="{{ $liveSiteUrl }}"
                    target="_blank"
                    rel="noopener noreferrer"
                    class="inline-flex items-center justify-center gap-2 px-4 py-2.5 rounded-xl bg-white dark:bg-zinc-800 hover:bg-zinc-50 dark:hover:bg-zinc-700 text-zinc-700 dark:text-zinc-200 border border-zinc-300 dark:border-zinc-700 text-sm font-semibold transition-all shadow-xs"
                    title="Open live public blog in new tab"
                >
                    <span>View Live Site</span>
                    <svg class="w-3.5 h-3.5 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 6H5.25A2.25 2.25 0 0 0 3 8.25v10.5A2.25 2.25 0 0 0 5.25 21h10.5A2.25 2.25 0 0 0 18 18.75V10.5m-10.5 6L21 3m0 0h-5.25M21 3v5.25" />
                    </svg>
                </a>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
