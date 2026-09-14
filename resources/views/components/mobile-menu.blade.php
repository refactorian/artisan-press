@php
    $siteName = \App\Models\Setting::get('site_name', config('app.name', 'Laravel Modern Blog'));
    $headerMenu = \App\Models\NavigationMenu::where('location', 'header')
        ->where('is_active', true)
        ->with(['rootItems' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])
        ->first();

    $menuItems = $headerMenu?->rootItems ?? collect([
        (object)['label' => 'Home', 'url' => '/'],
        (object)['label' => 'Articles', 'url' => '/posts'],
        (object)['label' => 'Tutorials', 'url' => '/categories/tutorials'],
        (object)['label' => 'Architecture', 'url' => '/categories/architecture'],
        (object)['label' => 'About', 'url' => '/about'],
        (object)['label' => 'Contact', 'url' => '/contact'],
    ]);
@endphp

<div
    x-show="mobileMenuOpen"
    x-cloak
    class="relative z-50 md:hidden"
    role="dialog"
    aria-modal="true"
>
    <!-- Backdrop -->
    <div
        x-show="mobileMenuOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="mobileMenuOpen = false"
        class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm"
    ></div>

    <div class="fixed inset-0 z-10 flex">
        <div
            x-show="mobileMenuOpen"
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="-translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="-translate-x-full"
            class="relative mr-16 flex w-full max-w-xs flex-1 flex-col bg-white dark:bg-zinc-900 px-6 pb-6 pt-5 shadow-2xl"
        >
            <!-- Header with close button -->
            <div class="flex items-center justify-between pb-5 border-b border-zinc-200/80 dark:border-zinc-800">
                <a href="{{ route('home') }}" @click="mobileMenuOpen = false" class="flex items-center gap-2 font-black text-lg text-zinc-900 dark:text-white">
                    <span class="w-7 h-7 rounded-lg bg-indigo-600 flex items-center justify-center text-white text-xs shadow-md">
                        <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                        </svg>
                    </span>
                    <span>{{ $siteName }}</span>
                </a>

                <button
                    type="button"
                    @click="mobileMenuOpen = false"
                    class="p-1.5 rounded-lg text-zinc-500 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- Quick Search in Mobile Drawer -->
            <div class="mt-4">
                <button
                    type="button"
                    @click="mobileMenuOpen = false; $dispatch('open-quick-search')"
                    class="w-full flex items-center gap-2 px-3 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-850 text-zinc-500 dark:text-zinc-400 text-sm"
                >
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                    </svg>
                    <span>Search articles...</span>
                </button>
            </div>

            <!-- Navigation Links -->
            <nav class="mt-6 flex-1 space-y-1">
                @foreach($menuItems as $item)
                    @php
                        $isActive = request()->is(ltrim($item->url, '/')) || (request()->routeIs('home') && $item->url === '/');
                    @endphp
                    <a
                        href="{{ $item->url }}"
                        @click="mobileMenuOpen = false"
                        class="flex items-center px-3 py-2.5 rounded-xl text-sm font-semibold transition-colors {{ $isActive ? 'bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400' : 'text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800' }}"
                    >
                        {{ $item->label }}
                    </a>
                @endforeach
            </nav>

            <!-- Bottom Theme Switcher -->
            <div class="pt-6 border-t border-zinc-200/80 dark:border-zinc-800">
                <button
                    type="button"
                    @click="toggleTheme()"
                    class="w-full flex items-center justify-between px-3 py-2.5 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-sm font-medium text-zinc-800 dark:text-zinc-200"
                >
                    <span class="flex items-center gap-2">
                        <span x-show="isDark">Dark Theme</span>
                        <span x-show="!isDark">Light Theme</span>
                    </span>
                    <span class="text-xs px-2 py-0.5 rounded bg-white dark:bg-zinc-900 border border-zinc-200 dark:border-zinc-700 text-zinc-500">
                        Switch
                    </span>
                </button>
            </div>
        </div>
    </div>
</div>
