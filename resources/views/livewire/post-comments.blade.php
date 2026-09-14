<div class="space-y-8" id="comments">
    <div class="flex items-center justify-between pb-4 border-b border-zinc-200/80 dark:border-zinc-800">
        <div class="flex items-center gap-2">
            <svg class="w-5 h-5 text-indigo-600 dark:text-indigo-400" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" d="M7.5 8.25h9m-9 3H12m-9.75 1.51c0 1.6 1.123 2.994 2.707 3.227 1.129.166 2.27.293 3.423.379.35.026.67.21.865.501L12 21l2.755-4.133a1.14 1.14 0 0 1 .865-.502 49.188 49.188 0 0 0 3.423-.379c1.584-.233 2.707-1.626 2.707-3.228V6.741c0-1.602-1.123-2.995-2.707-3.228A48.394 48.394 0 0 0 12 3c-2.392 0-4.744.175-7.043.513C3.373 3.746 2.25 5.14 2.25 6.741v6.018Z" />
            </svg>
            <h3 class="text-xl sm:text-2xl font-black text-zinc-900 dark:text-white">
                Discussion & Feedback
            </h3>
        </div>
        <span class="text-xs font-semibold px-2.5 py-1 rounded-full bg-zinc-100 dark:bg-zinc-800 text-zinc-600 dark:text-zinc-400">
            {{ $totalRootComments }} {{ str('Comment')->plural($totalRootComments) }}
        </span>
    </div>

    <!-- Comment Submission Form -->
    @if($commentsEnabled)
        <div class="p-6 rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 shadow-sm">
            <h4 class="text-sm font-bold text-zinc-900 dark:text-white mb-4 flex items-center justify-between">
                <span>{{ $replyToId ? 'Reply to comment' : 'Leave a comment' }}</span>
                @if($replyToId)
                    <button type="button" wire:click="cancelReply" class="text-xs font-semibold text-rose-500 hover:underline">
                        Cancel reply
                    </button>
                @endif
            </h4>

            <form wire:submit="submitComment" class="space-y-4">
                @if(auth()->check())
                    <div class="flex items-center gap-3 p-3 rounded-xl bg-white dark:bg-zinc-800/80 border border-zinc-200 dark:border-zinc-700 text-xs">
                        <div class="w-7 h-7 rounded-full bg-indigo-600 text-white font-bold flex items-center justify-center text-xs">
                            {{ substr(auth()->user()->name, 0, 1) }}
                        </div>
                        <div class="flex-1">
                            <span class="font-bold text-zinc-800 dark:text-zinc-200">{{ auth()->user()->name }}</span>
                            <span class="text-zinc-400">({{ auth()->user()->email }})</span>
                        </div>
                        <span class="text-[10px] uppercase font-bold text-indigo-600 dark:text-indigo-400 px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-950/60">
                            Logged in
                        </span>
                    </div>
                @else
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Your Name *</label>
                            <input
                                type="text"
                                wire:model="guestName"
                                placeholder="e.g. Jane Doe"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                            >
                            @error('guestName') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Your Email (never published) *</label>
                            <input
                                type="email"
                                wire:model="guestEmail"
                                placeholder="jane@example.com"
                                class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-xs text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 transition-colors"
                            >
                            @error('guestEmail') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                @endif

                <div>
                    <label class="block text-xs font-semibold text-zinc-700 dark:text-zinc-300 mb-1">Comment *</label>
                    <textarea
                        wire:model="content"
                        rows="4"
                        placeholder="Write constructive remarks, questions, or architectural suggestions..."
                        class="w-full px-3.5 py-2.5 rounded-xl bg-white dark:bg-zinc-800 border border-zinc-300 dark:border-zinc-700 text-xs sm:text-sm text-zinc-900 dark:text-zinc-100 placeholder-zinc-400 dark:placeholder-zinc-500 focus:outline-none focus:ring-2 focus:ring-indigo-500 leading-relaxed resize-y transition-colors"
                    ></textarea>
                    @error('content') <span class="text-[11px] text-rose-500 mt-1 block">{{ $message }}</span> @enderror
                </div>

                <div class="flex items-center justify-between pt-1">
                    <p class="text-[11px] text-zinc-500 dark:text-zinc-400">Markdown syntax supported. Be kind and constructive.</p>

                    <button
                        type="submit"
                        wire:loading.attr="disabled"
                        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-500 text-white text-xs font-bold transition-all shadow-md shadow-indigo-600/20 cursor-pointer disabled:opacity-50"
                    >
                        <span wire:loading.remove>Post Comment</span>
                        <span wire:loading class="flex items-center gap-2">
                            <svg class="animate-spin h-3.5 w-3.5 text-white" fill="none" viewBox="0 0 24 24">
                                <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                                <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"></path>
                            </svg>
                            <span>Submitting...</span>
                        </span>
                    </button>
                </div>
            </form>
        </div>
    @else
        <div class="p-4 rounded-xl bg-zinc-100 dark:bg-zinc-800 text-xs text-zinc-500 dark:text-zinc-400 text-center">
            Comments are currently disabled for this article.
        </div>
    @endif

    <!-- Comments List -->
    <div class="space-y-6">
        @forelse($comments as $comment)
            <div class="p-5 sm:p-6 rounded-2xl bg-white dark:bg-zinc-900 border border-zinc-200/80 dark:border-zinc-800 space-y-4">
                <!-- Comment Header -->
                <div class="flex items-center justify-between gap-3">
                    <div class="flex items-center gap-3">
                        <div class="w-9 h-9 rounded-full bg-gradient-to-tr from-indigo-500 to-purple-600 text-white font-bold flex items-center justify-center text-xs shadow-sm">
                            {{ substr($comment->author_name, 0, 1) }}
                        </div>
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-bold text-sm text-zinc-900 dark:text-white">
                                    {{ $comment->author_name }}
                                </span>
                                @if($comment->user_id)
                                    <span class="text-[10px] font-semibold px-2 py-0.2 rounded-full bg-indigo-50 text-indigo-700 dark:bg-indigo-950/80 dark:text-indigo-300">
                                        Author
                                    </span>
                                @endif
                                @if($comment->status->value === 'pending')
                                    <span class="text-[10px] font-semibold px-2 py-0.2 rounded-full bg-amber-50 text-amber-700 dark:bg-amber-950/80 dark:text-amber-300">
                                        Pending Review
                                    </span>
                                @endif
                            </div>
                            <time datetime="{{ $comment->created_at->toISOString() }}" class="text-[11px] text-zinc-400">
                                {{ $comment->created_at->diffForHumans() }}
                            </time>
                        </div>
                    </div>

                    @if($commentsEnabled)
                        <button
                            type="button"
                            wire:click="setReplyTo({{ $comment->id }})"
                            class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline flex items-center gap-1 cursor-pointer"
                        >
                            <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M9 15 3 9m0 0 6-6M3 9h12a6 6 0 0 1 0 12h-3" />
                            </svg>
                            <span>Reply</span>
                        </button>
                    @endif
                </div>

                <!-- Comment Content -->
                <div class="text-xs sm:text-sm text-zinc-700 dark:text-zinc-300 leading-relaxed whitespace-pre-line pl-12">
                    {{ $comment->content }}
                </div>

                <!-- Threaded Replies -->
                @if($comment->replies->isNotEmpty())
                    <div class="mt-4 pt-4 border-t border-zinc-100 dark:border-zinc-800/80 pl-6 sm:pl-10 space-y-4">
                        @foreach($comment->replies as $reply)
                            <div class="p-4 rounded-xl bg-zinc-100/70 dark:bg-zinc-800/60 border border-zinc-200/60 dark:border-zinc-700/60 space-y-2">
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center gap-2">
                                        <div class="w-6 h-6 rounded-full bg-purple-600 text-white font-bold flex items-center justify-center text-[10px]">
                                            {{ substr($reply->author_name, 0, 1) }}
                                        </div>
                                        <span class="font-bold text-xs text-zinc-800 dark:text-zinc-200">{{ $reply->author_name }}</span>
                                        @if($reply->user_id)
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-indigo-100 dark:bg-indigo-950 text-indigo-700 dark:text-indigo-300">
                                                Staff
                                            </span>
                                        @endif
                                        @if($reply->status->value === 'pending')
                                            <span class="text-[9px] font-bold px-1.5 py-0.2 rounded bg-amber-100 dark:bg-amber-950 text-amber-700 dark:text-amber-300">
                                                Pending Review
                                            </span>
                                        @endif
                                    </div>
                                    <time datetime="{{ $reply->created_at->toISOString() }}" class="text-[10px] text-zinc-400">
                                        {{ $reply->created_at->diffForHumans() }}
                                    </time>
                                </div>
                                <p class="text-xs text-zinc-700 dark:text-zinc-300 leading-relaxed whitespace-pre-line">
                                    {{ $reply->content }}
                                </p>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        @empty
            <div class="p-8 text-center rounded-2xl bg-zinc-50 dark:bg-zinc-900 border border-dashed border-zinc-300 dark:border-zinc-800 text-zinc-500 text-xs">
                No comments yet. Be the first to share your perspective!
            </div>
        @endforelse

        <!-- Load More Button -->
        @if($totalRootComments > $loadedCount)
            <div class="text-center pt-4">
                <button
                    type="button"
                    wire:click="loadMore"
                    class="px-5 py-2.5 rounded-xl border border-zinc-200 dark:border-zinc-700 bg-white dark:bg-zinc-800 text-xs font-bold text-zinc-800 dark:text-zinc-200 hover:bg-zinc-50 dark:hover:bg-zinc-700 transition-colors cursor-pointer"
                >
                    Load older comments ({{ $totalRootComments - $loadedCount }} remaining)
                </button>
            </div>
        @endif
    </div>
</div>
