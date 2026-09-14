@props(['metadata' => []])
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Flash-free Theme Initializer -->
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    </script>

    <!-- Dynamic SEO & Social Meta -->
    <x-seo-meta :metadata="$metadata" />

    <!-- Fonts & Assets -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>
<body
    id="top"
    x-data="{
        isDark: document.documentElement.classList.contains('dark'),
        mobileMenuOpen: false,
        toggleTheme() {
            this.isDark = !this.isDark;
            if (this.isDark) {
                document.documentElement.classList.add('dark');
                localStorage.theme = 'dark';
            } else {
                document.documentElement.classList.remove('dark');
                localStorage.theme = 'light';
            }
        }
    }"
    class="min-h-screen flex flex-col bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased selection:bg-indigo-500 selection:text-white transition-colors duration-200"
>
    <!-- Header Navigation -->
    <x-header />

    <!-- Mobile Slideover Navigation -->
    <x-mobile-menu />

    <!-- Main Page Content Slot -->
    <main class="flex-1">
        {{ $slot }}
    </main>

    <!-- Footer -->
    <x-footer />

    <!-- Global Quick Search Dialog Modal -->
    <livewire:quick-search />

    <!-- Saved Reading List Drawer -->
    <x-saved-articles-modal />

    <!-- Global Toast Notification System -->
    <x-toast />

    <!-- Global Helpers Script -->
    <script>
        window.$bookmarks = {
            isSaved(slug) {
                try {
                    const list = JSON.parse(localStorage.getItem('saved_articles') || '[]');
                    return list.some(i => i.slug === slug);
                } catch(e) { return false; }
            },
            toggle(article) {
                try {
                    let list = JSON.parse(localStorage.getItem('saved_articles') || '[]');
                    const exists = list.some(i => i.slug === article.slug);
                    if (exists) {
                        list = list.filter(i => i.slug !== article.slug);
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Article removed from saved reading list', type: 'info' } }));
                    } else {
                        list.unshift(article);
                        window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Article saved to reading list', type: 'success' } }));
                    }
                    localStorage.setItem('saved_articles', JSON.stringify(list));
                    window.dispatchEvent(new CustomEvent('bookmarks-updated'));
                } catch(e) {}
            }
        };
    </script>

    @livewireScripts
</body>
</html>
