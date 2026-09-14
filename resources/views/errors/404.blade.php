@extends('errors.layout')

@section('title', '404 — Page Not Found')

@section('content')
    <div class="mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-tr from-indigo-600 to-purple-600 text-white text-3xl font-black shadow-xl shadow-indigo-500/30 mb-6">
            404
        </div>

        <h1 class="text-4xl sm:text-5xl font-black tracking-tight text-zinc-900 dark:text-white mb-4">
            Page not found
        </h1>

        <p class="text-base text-zinc-500 dark:text-zinc-400 leading-relaxed mb-8">
            The page you're looking for doesn't exist or has been moved. Let's get you back on track.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-3">
            <a href="{{ url('/') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 text-white text-sm font-semibold hover:bg-indigo-700 transition-colors shadow-lg shadow-indigo-500/25">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25"/></svg>
                Back to Home
            </a>
            <a href="{{ url('/posts') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-zinc-700 dark:text-zinc-200 text-sm font-semibold hover:bg-zinc-200 dark:hover:bg-zinc-700 transition-colors">
                Browse Articles
            </a>
            <a href="{{ url('/search') }}" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl text-zinc-500 dark:text-zinc-400 text-sm font-medium hover:text-zinc-800 dark:hover:text-zinc-200 transition-colors">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="m21 21-5.197-5.197m0 0A7.5 7.5 0 1 0 5.196 5.196a7.5 7.5 0 0 0 10.607 10.607Z"/></svg>
                Search
            </a>
        </div>
    </div>
    <div class="text-8xl font-black text-zinc-100 dark:text-zinc-900 select-none pointer-events-none">404</div>
@endsection
