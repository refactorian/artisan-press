<?php

namespace App\Console\Commands;

use App\Services\BlogCacheService;
use App\Services\SitemapService;
use Illuminate\Console\Command;

class GenerateSitemapCommand extends Command
{
    protected $signature = 'blog:generate-sitemap';

    protected $description = 'Generate and pre-warm the XML sitemap cache.';

    public function handle(SitemapService $sitemapService, BlogCacheService $cacheService): int
    {
        $this->info('Regenerating blog XML sitemap...');

        $cacheService->invalidateSitemap();
        $xml = $sitemapService->generateXml();
        $byteSize = strlen($xml);

        $this->info("XML sitemap generated successfully ({$byteSize} bytes).");

        return self::SUCCESS;
    }
}
