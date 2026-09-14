@php
    $siteName = \App\Models\Setting::get('site_name', config('app.name', 'Laravel Modern Blog'));
    $headerMenu = \App\Models\NavigationMenu::where('location', 'header')
        ->where('is_active', true)
        ->with(['rootItems.children' => function ($q) {
            $q->where('is_active', true)->orderBy('sort_order');
        }])
        ->first();

    $menuItems = $headerMenu?->rootItems ?? collect([
        (object)['label' => 'Home', 'url' => '/', 'type' => 'url'],
        (object)['label' => 'Articles', 'url' => '/posts', 'type' => 'url'],
        (object)['label' => 'Tutorials', 'url' => '/categories/tutorials', 'type' => 'category'],
        (object)['label' => 'Architecture', 'url' => '/categories/architecture', 'type' => 'category'],
        (object)['label' => 'About', 'url' => '/about', 'type' => 'page'],
    ]);
@endphp

<header class="sticky top-0 z-40 w-full border-b border-zinc-200/80 dark:border-zinc-800/80 bg-white/80 dark:bg-zinc-950/80 backdrop-blur-md transition-colors">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-16 flex items-center justify-between gap-4">
        <!-- Logo / Brand -->
        <div class="flex items-center gap-6">
            <a href="{{ route('home') }}" class="flex items-center gap-2.5 font-black text-xl tracking-tight text-zinc-900 dark:text-white group">
                <span class="w-8 h-8 rounded-xl bg-gradient-to-tr from-indigo-600 via-indigo-500 to-purple-500 flex items-center justify-center text-white text-base shadow-md shadow-indigo-500/25 group-hover:scale-105 transition-transform">
                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m3.75 13.5 10.5-11.25L12 10.5h8.25L9.75 21.75 12 13.5H3.75Z" />
                    </svg>
                </span>
                <span class="group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors">
                    {{ $siteName }}
                </span>
            </a>

            <!-- Desktop Navigation -->
            <nav class="hidden md:flex items-center gap-1">
                @foreach($menuItems as $item)
                    @php
                        $hasChildren = !empty($item->children) && count($item->children) > 0;
                        $isActive = request()->is(ltrim($item->url, '/')) || (request()->routeIs('home') && $item->url === '/');
                    @endphp

                    @if($hasChildren)
                        <div class="relative" x-data="{ open: false }" @click.outside="open = false">
                            <button
                                type="button"
                                @click="open = !open"
                                class="flex items-center gap-1 px-3 py-1.5 rounded-lg text-sm font-medium transition-colors text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-850 cursor-pointer"
                            >
                                <span>{{ $item->label }}</span>
                                <svg class="w-3.5 h-3.5 transition-transform" :class="{ 'rotate-180': open }" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="m19.5 8.25-7.5 7.5-7.5-7.5" />
                                </svg>
                            </button>

                            <div
                                x-show="open"
                                x-transition:enter="ease-out duration-150"
                                x-transition:enter-start="opacity-0 translate-y-1"
                                x-transition:enter-end="opacity-100 translate-y-0"
                                x-transition:leave="ease-in duration-100"
                                x-transition:leave-start="opacity-100 translate-y-0"
                                x-transition:leave-end="opacity-0 translate-y-1"
                                class="absolute left-0 mt-1 w-48 rounded-xl bg-white dark:bg-zinc-900 shadow-xl border border-zinc-200/80 dark:border-zinc-800 py-1.5 z-50"
                            >
                                @foreach($item->children as $child)
                                    <a
                                        href="{{ $child->url }}"
                                        class="block px-3.5 py-1.5 text-xs font-medium text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 dark:hover:bg-zinc-800 hover:text-indigo-600 dark:hover:text-indigo-400"
                                    >
                                        {{ $child->label }}
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @else
                        <a
                            href="{{ $item->url }}"
                            class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors {{ $isActive ? 'text-indigo-600 dark:text-indigo-400 bg-indigo-50/70 dark:bg-indigo-950/50' : 'text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-850' }}"
                        >
                            {{ $item->label }}
                        </a>
                    @endif
                @endforeach
            </nav>
        </div>

        <!-- Header Actions: Search, Saved Bookmarks, Theme Toggle, Mobile Toggle -->
        <div class="flex items-center gap-2.5">
            <!-- Search Trigger Button -->
            <button
                type="button"
                @click="$dispatch('open-quick-search')"
                class="flex items-center gap-2 px-3 py-1.5 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-500 dark:text-zinc-400 hover:border-zinc-300 dark:hover:border-zinc-700 hover:text-zinc-800 dark:hover:text-zinc-200 transition-all text-xs cursor-pointer"
                title="Search (⌘K)"
            >
                <svg class="w-4 h-4 text-zinc-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z" />
                </svg>
                <span class="hidden sm:inline">Search...</span>
                <kbd class="hidden sm:inline-block rounded px-1 py-0.5 text-[10px] font-semibold text-zinc-400 bg-zinc-200/60 dark:bg-zinc-800">
                    ⌘K
                </kbd>
            </button>

            <!-- Saved Articles Reading List Button -->
            <button
                type="button"
                x-data="{
                    count: 0,
                    init() {
                        this.update();
                        window.addEventListener('bookmarks-updated', () => this.update());
                    },
                    update() {
                        try {
                            this.count = JSON.parse(localStorage.getItem('saved_articles') || '[]').length;
                        } catch(e) {
                            this.count = 0;
                        }
                    }
                }"
                @click="$dispatch('open-saved-articles')"
                class="relative p-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:border-zinc-300 dark:hover:border-zinc-700 transition-all cursor-pointer"
                title="Saved Reading List"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                </svg>
                <span x-show="count > 0" x-text="count" class="absolute -top-1 -right-1 w-4 h-4 rounded-full bg-indigo-600 text-white text-[10px] font-bold flex items-center justify-center"></span>
            </button>

            <!-- Dark / Light Mode Switcher -->
            <button
                type="button"
                @click="toggleTheme()"
                class="p-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:border-zinc-300 dark:hover:border-zinc-700 transition-all cursor-pointer"
                title="Toggle color theme"
                aria-label="Toggle color theme"
            >
                <!-- Sun icon (shows in dark mode) -->
                <svg x-show="isDark" class="w-4 h-4 text-amber-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 3v2.25m6.364.386-1.591 1.591M21 12h-2.25m-.386 6.364-1.591-1.591M12 18.75V21m-4.773-4.227-1.591 1.591M5.25 12H3m4.227-4.773L5.636 5.636M15.75 12a3.75 3.75 0 1 1-7.5 0 3.75 3.75 0 0 1 7.5 0Z" />
                </svg>
                <!-- Moon icon (shows in light mode) -->
                <svg x-show="!isDark" class="w-4 h-4 text-zinc-700" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.752 15.002A9.72 9.72 0 0 1 18 15.75c-5.385 0-9.75-4.365-9.75-9.75 0-1.33.266-2.597.748-3.752A9.753 9.753 0 0 0 3 11.25C3 16.635 7.365 21 12.75 21a9.753 9.753 0 0 0 9.002-5.998Z" />
                </svg>
            </button>

            <!-- Mobile Menu Toggle Button -->
            <button
                type="button"
                @click="mobileMenuOpen = !mobileMenuOpen"
                class="md:hidden p-2 rounded-xl border border-zinc-200 dark:border-zinc-800 bg-zinc-50 dark:bg-zinc-900 text-zinc-600 dark:text-zinc-400 hover:text-zinc-900 dark:hover:text-white transition-all cursor-pointer"
                aria-label="Open mobile navigation"
            >
                <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6.75h16.5M3.75 12h16.5m-16.5 5.25h16.5" />
                </svg>
            </button>
        </div>
    </div>
</header>
