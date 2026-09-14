<x-layouts.app :metadata="$metadata">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <!-- Breadcrumbs -->
        <x-breadcrumbs :items="$breadcrumbs" />

        <!-- Header -->
        <div class="my-6 pb-6 border-b border-zinc-200/80 dark:border-zinc-800">
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-zinc-900 dark:text-white mb-2">
                All Articles & Guides
            </h1>
            <p class="text-sm sm:text-base text-zinc-600 dark:text-zinc-400 max-w-2xl">
                Browse our complete collection of deep-dive articles, design patterns, and engineering practices.
            </p>
        </div>

        <!-- Livewire Interactive Filterable Post Listing -->
        <livewire:post-listing />
    </div>
</x-layouts.app>
