<!DOCTYPE html>
<html lang="en" class="scroll-smooth">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>@yield('title') — {{ config('app.name') }}</title>
    <script>
        if (localStorage.theme === 'dark' || (!('theme' in localStorage) && window.matchMedia('(prefers-color-scheme: dark)').matches)) {
            document.documentElement.classList.add('dark');
        }
    </script>
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="min-h-screen flex flex-col items-center justify-center bg-white dark:bg-zinc-950 text-zinc-900 dark:text-zinc-100 font-sans antialiased px-4">
    <div class="text-center max-w-lg w-full">
        @yield('content')
    </div>

    <footer class="absolute bottom-6 text-xs text-zinc-400 dark:text-zinc-600">
        © {{ date('Y') }} {{ config('app.name') }}
    </footer>
</body>
</html>
