@props(['items' => []])

@if(!empty($items))
    <nav aria-label="Breadcrumb" class="py-3">
        <ol class="flex flex-wrap items-center gap-1.5 text-xs sm:text-sm text-zinc-500 dark:text-zinc-400">
            <li>
                <a href="{{ route('home') }}" class="flex items-center gap-1 hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m2.25 12 8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                    </svg>
                    <span>Home</span>
                </a>
            </li>

            @foreach($items as $item)
                <li class="flex items-center gap-1.5">
                    <svg class="w-3.5 h-3.5 text-zinc-400 dark:text-zinc-600 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="m8.25 4.5 7.5 7.5-7.5 7.5" />
                    </svg>

                    @if(!empty($item['url']) && !$loop->last)
                        <a href="{{ $item['url'] }}" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors truncate max-w-[180px] sm:max-w-xs">
                            {{ $item['label'] }}
                        </a>
                    @else
                        <span class="font-medium text-zinc-800 dark:text-zinc-200 truncate max-w-[200px] sm:max-w-md" aria-current="page">
                            {{ $item['label'] }}
                        </span>
                    @endif
                </li>
            @endforeach
        </ol>
    </nav>
@endif
