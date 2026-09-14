<div
    x-data="{
        toasts: [],
        add(message, type = 'success') {
            const id = Date.now() + Math.random();
            this.toasts.push({ id, message, type });
            setTimeout(() => this.remove(id), 4000);
        },
        remove(id) {
            this.toasts = this.toasts.filter(t => t.id !== id);
        }
    }"
    @toast.window="add($event.detail.message, $event.detail.type || 'success')"
    class="fixed bottom-5 right-5 z-50 flex flex-col gap-2.5 max-w-sm pointer-events-none"
    aria-live="polite"
>
    <template x-for="toast in toasts" :key="toast.id">
        <div
            x-transition:enter="transition ease-out duration-300 transform"
            x-transition:enter-start="opacity-0 translate-y-3 scale-95"
            x-transition:enter-end="opacity-100 translate-y-0 scale-100"
            x-transition:leave="transition ease-in duration-200 transform"
            x-transition:leave-start="opacity-100 translate-y-0 scale-100"
            x-transition:leave-end="opacity-0 translate-y-2 scale-95"
            class="pointer-events-auto flex items-center gap-3 px-4 py-3 rounded-2xl shadow-xl border text-sm font-medium backdrop-blur-md transition-all"
            :class="{
                'bg-emerald-900/90 text-white border-emerald-700/60': toast.type === 'success',
                'bg-indigo-900/90 text-white border-indigo-700/60': toast.type === 'info',
                'bg-amber-900/90 text-white border-amber-700/60': toast.type === 'warning',
                'bg-rose-900/90 text-white border-rose-700/60': toast.type === 'error'
            }"
        >
            <!-- Success Icon -->
            <template x-if="toast.type === 'success'">
                <svg class="w-5 h-5 text-emerald-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75 11.25 15 15 9.75M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                </svg>
            </template>

            <!-- Info Icon -->
            <template x-if="toast.type === 'info'">
                <svg class="w-5 h-5 text-indigo-400 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="m11.25 11.25.041-.02a.75.75 0 0 1 1.063.852l-.708 2.836a.75.75 0 0 0 1.063.853l.041-.021M21 12a9 9 0 1 1-18 0 9 9 0 0 1 18 0Zm-9-3.75h.008v.008H12V8.25Z" />
                </svg>
            </template>

            <span x-text="toast.message" class="flex-1"></span>

            <button
                type="button"
                @click="remove(toast.id)"
                class="text-white/60 hover:text-white p-1 rounded-lg transition-colors cursor-pointer"
            >
                <svg class="w-4 h-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18 18 6M6 6l12 12" />
                </svg>
            </button>
        </div>
    </template>
</div>
