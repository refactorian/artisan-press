<div>
    @if($subscribed)
        <div class="p-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-300 text-sm flex items-center gap-3">
            <svg class="w-5 h-5 flex-shrink-0 text-emerald-600 dark:text-emerald-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
            <div>
                <p class="font-semibold">Thank you for subscribing!</p>
                <p class="text-xs text-emerald-700 dark:text-emerald-400">You will receive the latest engineering stories right in your inbox.</p>
            </div>
        </div>
    @else
        <form wire:submit="subscribe" class="flex flex-col sm:flex-row gap-2.5">
            <div class="relative flex-1">
                <input
                    type="email"
                    wire:model="email"
                    placeholder="Enter your email address"
                    class="w-full px-4 py-3 rounded-xl bg-white dark:bg-zinc-800/80 border border-zinc-300 dark:border-zinc-700 text-zinc-900 dark:text-white placeholder-zinc-400 text-sm focus:outline-none focus:ring-2 focus:ring-indigo-500 dark:focus:ring-indigo-400 focus:border-transparent transition-all"
                    required
                >
                @error('email')
                    <span class="absolute -bottom-5 left-1 text-[11px] text-rose-500">{{ $message }}</span>
                @enderror
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="inline-flex items-center justify-center gap-2 px-6 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 dark:bg-indigo-500 dark:hover:bg-indigo-600 text-white text-sm font-semibold transition-all shadow-md shadow-indigo-500/20 disabled:opacity-60 flex-shrink-0 cursor-pointer"
            >
                <span wire:loading.remove>Subscribe</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Joining...</span>
                </span>
            </button>
        </form>
    @endif
</div>
