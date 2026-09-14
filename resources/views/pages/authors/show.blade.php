<x-layouts.app :metadata="$metadata">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <x-breadcrumbs :items="$breadcrumbs" />

        <!-- Author Profile Banner -->
        <div class="my-6">
            <x-author-card :author="$author" />
        </div>

        <!-- Section Title -->
        <div class="mt-12 mb-6 pb-4 border-b border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
            <h2 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white">
                Articles by {{ $author->name }}
            </h2>
            <span class="text-xs text-zinc-500">
                {{ $posts->total() }} {{ str('article')->plural($posts->total()) }}
            </span>
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
                title="No published articles yet"
                description="{{ $author->name }} has not published any articles yet. Please check back later."
                actionLabel="Browse all articles"
                actionUrl="{{ route('posts.index') }}"
            />
        @endif
    </div>
</x-layouts.app>
