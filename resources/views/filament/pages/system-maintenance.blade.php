<x-filament-panels::page>
    @php
        $diagnostics = $this->getSystemDiagnostics();
    @endphp

    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Environment</span>
            <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white capitalize">{{ $diagnostics['app_env'] }}</div>
            <span class="text-xs text-gray-500">{{ $diagnostics['debug_mode'] }}</span>
        </div>

        <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">PHP & Framework</span>
            <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white">PHP {{ $diagnostics['php_version'] }}</div>
            <span class="text-xs text-gray-500">Laravel {{ $diagnostics['laravel_version'] }}</span>
        </div>

        <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Database Engine</span>
            <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white capitalize">{{ $diagnostics['database_driver'] }}</div>
            <span class="text-xs text-emerald-600 dark:text-emerald-400 font-medium">{{ $diagnostics['database_status'] }}</span>
        </div>

        <div class="p-4 bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 shadow-sm">
            <span class="text-xs font-semibold uppercase tracking-wider text-gray-500 dark:text-gray-400">Cache & Queue Driver</span>
            <div class="mt-1 text-lg font-bold text-gray-900 dark:text-white capitalize">{{ $diagnostics['cache_driver'] }} Cache</div>
            <span class="text-xs text-gray-500">Queue: {{ $diagnostics['queue_driver'] }}</span>
        </div>
    </div>

    <div class="bg-white dark:bg-gray-800 rounded-xl border border-gray-200 dark:border-gray-700 p-6 shadow-sm">
        <h3 class="text-base font-semibold text-gray-900 dark:text-white mb-2">Production Maintenance Guidelines</h3>
        <ul class="text-sm text-gray-600 dark:text-gray-300 space-y-2 list-disc list-inside">
            <li><strong>Cache Purging:</strong> Flush all pre-calculated trending items, tag clouds, RSS feeds, and XML sitemaps when global settings or templates change.</li>
            <li><strong>Sitemap Regeneration:</strong> Sitemaps update automatically on content publishing, but you can manually warm the cache here.</li>
            <li><strong>Reading Times:</strong> Recalculate reading time across legacy articles if words-per-minute configurations are altered.</li>
            <li><strong>Content Backup:</strong> Download an immediate JSON export of all posts, excerpts, authors, and SEO metadata.</li>
        </ul>
    </div>
</x-filament-panels::page>
