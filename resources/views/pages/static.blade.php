<x-layouts.app :metadata="$metadata">
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8 sm:py-12">
        <x-breadcrumbs :items="$breadcrumbs" />

        <header class="my-8 pb-6 border-b border-zinc-200/80 dark:border-zinc-800">
            <h1 class="text-3xl sm:text-4xl font-black tracking-tight text-zinc-900 dark:text-white">
                {{ $page->title }}
            </h1>
        </header>

        <div class="prose dark:prose-invert max-w-none mb-10">
            {!! $page->content !!}
        </div>

        @if($page->slug === 'contact')
            <div class="mt-8 pt-8 border-t border-zinc-200/80 dark:border-zinc-800">
                <h2 class="text-xl font-black text-zinc-900 dark:text-white mb-6">Send a Direct Message</h2>
                <livewire:contact-form />
            </div>
        @endif
    </div>
</x-layouts.app>
