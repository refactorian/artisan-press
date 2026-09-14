<x-layouts.app :metadata="$metadata">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <x-breadcrumbs :items="$breadcrumbs" />

        <!-- Tag Banner -->
        <div class="my-6 p-8 sm:p-10 rounded-3xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800">
            <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-bold uppercase tracking-wider bg-indigo-50 text-indigo-700 dark:bg-indigo-950/80 dark:text-indigo-300 mb-3">
                Topic Tag
            </span>

            <h1 class="text-3xl sm:text-4xl font-black text-zinc-900 dark:text-white tracking-tight mb-3">
                #{{ $tag->name }}
            </h1>

            <div class="mt-4 pt-4 border-t border-zinc-200/80 dark:border-zinc-800 text-xs text-zinc-500 flex items-center gap-4">
                <span><strong>{{ $posts->total() }}</strong> {{ str('article')->plural($posts->total()) }} tagged</span>
                <span>•</span>
                <a href="{{ route('feed.tag', $tag->slug) }}" target="_blank" class="inline-flex items-center gap-1 text-amber-600 dark:text-amber-400 hover:underline">
                    <span>Subscribe to #{{ $tag->name }} RSS</span>
                </a>
            </div>
        </div>

        <!-- Posts List -->
        @if($posts->isNotEmpty())
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
                @foreach($posts as $post)
                    <x-post-card :post="$post" />
                @endforeach
            </div>

            <div class="mt-12">
                {{ $posts->links() }}
            </div>
        @else
            <x-empty-state
                title="No articles tagged with #{{ $tag->name }}"
                description="We haven't published any articles with this tag yet."
                actionLabel="Browse all articles"
                actionUrl="{{ route('posts.index') }}"
            />
        @endif
    </div>
</x-layouts.app>
