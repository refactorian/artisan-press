<div>
    @if($sent)
        <div class="p-8 rounded-3xl bg-emerald-50 dark:bg-emerald-950/40 border border-emerald-200 dark:border-emerald-800 text-center space-y-3">
            <div class="w-12 h-12 rounded-2xl bg-emerald-100 dark:bg-emerald-900/60 text-emerald-600 dark:text-emerald-400 mx-auto flex items-center justify-center">
                <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </div>
            <h3 class="text-lg font-bold text-emerald-900 dark:text-emerald-200">Message Delivered</h3>
            <p class="text-sm text-emerald-700 dark:text-emerald-400 max-w-md mx-auto">
                Thank you for reaching out. A confirmation has been logged, and an editor will review your inquiry shortly.
            </p>
            <button
                type="button"
                wire:click="$set('sent', false)"
                class="mt-4 px-4 py-2 rounded-xl bg-white dark:bg-zinc-800 text-xs font-bold text-zinc-700 dark:text-zinc-300 hover:bg-zinc-100 transition-colors border border-zinc-200 dark:border-zinc-700"
            >
                Send another message
            </button>
        </div>
    @else
        <form wire:submit="submit" class="space-y-4">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Your Full Name *</label>
                    <input
                        type="text"
                        wire:model="name"
                        placeholder="Ada Lovelace"
                        class="w-full px-4 py-3 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                    @error('name') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Email Address *</label>
                    <input
                        type="email"
                        wire:model="email"
                        placeholder="ada@example.com"
                        class="w-full px-4 py-3 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                    >
                    @error('email') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Subject / Inquiry Topic *</label>
                <input
                    type="text"
                    wire:model="subject"
                    placeholder="e.g. Editorial proposal or technical correction"
                    class="w-full px-4 py-3 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-indigo-500"
                >
                @error('subject') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <div>
                <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Message *</label>
                <textarea
                    wire:model="message"
                    rows="5"
                    placeholder="Tell us about your inquiry, proposal, or feedback..."
                    class="w-full px-4 py-3 rounded-xl bg-white dark:bg-zinc-900 border border-zinc-300 dark:border-zinc-700 text-sm text-zinc-900 dark:text-white placeholder-zinc-400 focus:outline-none focus:ring-2 focus:ring-indigo-500 leading-relaxed resize-y"
                ></textarea>
                @error('message') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
            </div>

            <button
                type="submit"
                wire:loading.attr="disabled"
                class="w-full sm:w-auto inline-flex items-center justify-center gap-2 px-8 py-3.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-white text-sm font-bold transition-all shadow-md shadow-indigo-600/20 cursor-pointer disabled:opacity-50"
            >
                <span wire:loading.remove>Send Message</span>
                <span wire:loading class="flex items-center gap-2">
                    <svg class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                    </svg>
                    <span>Transmitting...</span>
                </span>
            </button>
        </form>
    @endif
</div>
