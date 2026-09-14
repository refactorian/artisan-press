<div
    x-data="{
        isOpen: false,
        items: [],
        init() {
            this.load();
            window.addEventListener('bookmarks-updated', () => this.load());
            window.addEventListener('open-saved-articles', () => this.isOpen = true);
        },
        load() {
            try {
                this.items = JSON.parse(localStorage.getItem('saved_articles') || '[]');
            } catch(e) {
                this.items = [];
            }
        },
        remove(slug) {
            this.items = this.items.filter(i => i.slug !== slug);
            localStorage.setItem('saved_articles', JSON.stringify(this.items));
            window.dispatchEvent(new CustomEvent('bookmarks-updated'));
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Article removed from saved reading list', type: 'info' } }));
        },
        clearAll() {
            this.items = [];
            localStorage.setItem('saved_articles', JSON.stringify([]));
            window.dispatchEvent(new CustomEvent('bookmarks-updated'));
            window.dispatchEvent(new CustomEvent('toast', { detail: { message: 'Saved reading list cleared', type: 'info' } }));
        }
    }"
    x-show="isOpen"
    x-cloak
    class="relative z-50"
    role="dialog"
    aria-modal="true"
>
    <!-- Backdrop -->
    <div
        x-show="isOpen"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click="isOpen = false"
        class="fixed inset-0 bg-zinc-950/60 backdrop-blur-sm"
    ></div>

    <div class="fixed inset-0 z-10 flex justify-end">
        <div
            x-show="isOpen"
            x-transition:enter="transition ease-in-out duration-300 transform"
            x-transition:enter-start="translate-x-full"
            x-transition:enter-end="translate-x-0"
            x-transition:leave="transition ease-in-out duration-300 transform"
            x-transition:leave-start="translate-x-0"
            x-transition:leave-end="translate-x-full"
            class="relative flex w-full max-w-md flex-col bg-white dark:bg-zinc-900 shadow-2xl p-6 overflow-y-auto"
        >
            <!-- Drawer Header -->
            <div class="flex items-center justify-between pb-4 border-b border-zinc-200/80 dark:border-zinc-800">
                <div class="flex items-center gap-2">
                    <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M5 4a2 2 0 0 1 2-2h10a2 2 0 0 1 2 2v18l-7-3.5L5 22V4Z"/>
                    </svg>
                    <h3 class="font-bold text-lg text-zinc-900 dark:text-white">
                        Saved Reading List
                    </h3>
                    <span x-text="`(${items.length})`" class="text-xs text-zinc-400 font-semibold"></span>
                </div>

                <button
                    type="button"
                    @click="isOpen = false"
                    class="p-1.5 rounded-lg text-zinc-400 hover:text-zinc-900 dark:hover:text-white hover:bg-zinc-100 dark:hover:bg-zinc-800 transition-colors"
                >
                    <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>

            <!-- List of Bookmarks -->
            <div class="flex-1 py-4">
                <template x-if="items.length === 0">
                    <div class="py-16 text-center text-zinc-400 dark:text-zinc-500">
                        <svg class="w-12 h-12 mx-auto mb-3 opacity-30" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M17.593 3.322c1.1.128 1.907 1.077 1.907 2.185V21L12 17.25 4.5 21V5.507c0-1.108.806-2.057 1.907-2.185a48.507 48.507 0 0 1 11.186 0Z" />
                        </svg>
                        <p class="text-sm font-medium text-zinc-700 dark:text-zinc-300 mb-1">Your reading list is empty</p>
                        <p class="text-xs">Click the bookmark icon on any article to save it for later.</p>
                    </div>
                </template>

                <template x-if="items.length > 0">
                    <ul class="divide-y divide-zinc-100 dark:divide-zinc-800 space-y-2">
                        <template x-for="item in items" :key="item.slug">
                            <li class="pt-2 group flex items-start justify-between gap-3">
                                <a :href="item.url" @click="isOpen = false" class="flex-1 min-w-0">
                                    <span x-text="item.category || 'Article'" class="text-[10px] font-semibold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block mb-0.5"></span>
                                    <h4 x-text="item.title" class="text-sm font-bold text-zinc-900 dark:text-white group-hover:text-indigo-600 dark:group-hover:text-indigo-400 transition-colors line-clamp-2"></h4>
                                    <span x-text="`${item.reading_time || 3} min read`" class="text-[11px] text-zinc-400 mt-1 block"></span>
                                </a>
                                <button
                                    type="button"
                                    @click="remove(item.slug)"
                                    class="p-1 text-zinc-400 hover:text-rose-500 rounded transition-colors"
                                    title="Remove from saved list"
                                >
                                    <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
                                    </svg>
                                </button>
                            </li>
                        </template>
                    </ul>
                </template>
            </div>

            <!-- Footer Action -->
            <template x-if="items.length > 0">
                <div class="pt-4 border-t border-zinc-200/80 dark:border-zinc-800 flex items-center justify-between">
                    <button
                        type="button"
                        @click="clearAll()"
                        class="text-xs font-semibold text-rose-500 hover:underline cursor-pointer"
                    >
                        Clear reading list
                    </button>
                    <button
                        type="button"
                        @click="isOpen = false"
                        class="px-4 py-2 rounded-xl bg-zinc-900 dark:bg-zinc-800 hover:bg-zinc-800 dark:hover:bg-zinc-700 text-white text-xs font-bold border border-zinc-900 dark:border-zinc-700 transition-colors cursor-pointer"
                    >
                        Done
                    </button>
                </div>
            </template>
        </div>
    </div>
</div>
