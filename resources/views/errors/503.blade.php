@extends('errors.layout')

@section('title', 'Maintenance Mode')

@section('content')
    <div class="mb-8">
        <div class="inline-flex items-center justify-center w-20 h-20 rounded-3xl bg-gradient-to-tr from-violet-600 to-indigo-600 text-white shadow-xl shadow-violet-500/30 mb-6">
            <svg class="w-9 h-9" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M11.42 15.17 17.25 21A2.652 2.652 0 0 0 21 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 1 1-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 0 0 4.486-6.336l-3.276 3.277a3.004 3.004 0 0 1-2.25-2.25l3.276-3.276a4.5 4.5 0 0 0-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437 1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008Z"/></svg>
        </div>

        <h1 class="text-4xl sm:text-5xl font-black tracking-tight text-zinc-900 dark:text-white mb-4">
            Down for maintenance
        </h1>

        <p class="text-base text-zinc-500 dark:text-zinc-400 leading-relaxed mb-8">
            We're performing scheduled maintenance to improve your experience. We'll be back shortly — thank you for your patience.
        </p>

        @if(isset($exception) && $exception->getMessage())
            <div class="mb-6 p-4 rounded-xl bg-violet-50 dark:bg-violet-950/40 border border-violet-200 dark:border-violet-900 text-sm text-violet-700 dark:text-violet-300">
                {{ $exception->getMessage() }}
            </div>
        @endif

        <div class="flex items-center justify-center gap-3">
            <button onclick="setTimeout(() => location.reload(), 30000); this.textContent = 'Auto-refreshing in 30s...'; this.disabled = true;" class="inline-flex items-center gap-2 px-6 py-3 rounded-xl bg-violet-600 text-white text-sm font-semibold hover:bg-violet-700 transition-colors shadow-lg shadow-violet-500/25 cursor-pointer">
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" d="M16.023 9.348h4.992v-.001M2.985 19.644v-4.992m0 0h4.992m-4.993 0 3.181 3.183a8.25 8.25 0 0 0 13.803-3.7M4.031 9.865a8.25 8.25 0 0 1 13.803-3.7l3.181 3.182m0-4.991v4.99"/></svg>
                Notify Me When Back
            </button>
        </div>
    </div>
    <div class="text-7xl font-black text-zinc-100 dark:text-zinc-900 select-none pointer-events-none">Offline</div>
@endsection
