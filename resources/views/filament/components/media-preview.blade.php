@php
    $record = $getRecord();
    $url    = $record?->getUrl();
    $mime   = $record?->mime_type ?? '';
    $isImage = str_starts_with($mime, 'image/');
@endphp

@if ($record && $url)
    <div class="rounded-xl overflow-hidden border border-gray-200 dark:border-gray-700 bg-gray-50 dark:bg-gray-900">
        @if ($isImage)
            <div class="relative group">
                <img
                    src="{{ $url }}"
                    alt="{{ $record->getCustomProperty('alt_text') ?? $record->name }}"
                    class="w-full object-contain max-h-72 mx-auto block"
                    loading="lazy"
                />
                {{-- Hover overlay with "Open full size" link --}}
                <a
                    href="{{ $url }}"
                    target="_blank"
                    rel="noopener"
                    class="absolute inset-0 bg-black/0 group-hover:bg-black/30 transition-colors flex items-center justify-center opacity-0 group-hover:opacity-100"
                >
                    <span class="inline-flex items-center gap-1.5 rounded-full bg-white/90 px-3 py-1.5 text-xs font-semibold text-gray-800 shadow">
                        <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" />
                        Open full size
                    </span>
                </a>
            </div>

            {{-- File info strip --}}
            <div class="px-3 py-2 flex items-center justify-between text-xs text-gray-500 dark:text-gray-400 border-t border-gray-200 dark:border-gray-700">
                <span class="truncate max-w-[70%]">{{ $record->file_name }}</span>
                <span class="shrink-0 font-medium">{{ $record->formatted_size }}</span>
            </div>
        @else
            {{-- Non-image file (PDF, doc, etc.) --}}
            <div class="p-8 text-center text-gray-500 dark:text-gray-400">
                <x-heroicon-o-document class="w-16 h-16 mx-auto mb-3 text-gray-300 dark:text-gray-600" />
                <p class="text-sm font-medium truncate">{{ $record->file_name }}</p>
                <p class="text-xs mt-1">{{ $mime }}</p>
                <a
                    href="{{ $url }}"
                    target="_blank"
                    rel="noopener"
                    class="mt-3 inline-flex items-center gap-1.5 text-xs text-primary-600 hover:underline"
                >
                    <x-heroicon-o-arrow-top-right-on-square class="w-3.5 h-3.5" />
                    Open file
                </a>
            </div>
        @endif
    </div>
@endif
