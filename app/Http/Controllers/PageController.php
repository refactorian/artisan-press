<?php

namespace App\Http\Controllers;

use App\Enums\PageStatus;
use App\Models\Page;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;

class PageController extends Controller
{
    public function show(string $slug, SeoService $seoService): View
    {
        $page = Page::where('slug', $slug)
            ->where('status', PageStatus::Published)
            ->firstOrFail();

        $metadata = $seoService->generate($page);

        $breadcrumbs = [
            ['label' => $page->title],
        ];

        return view('pages.static', [
            'page' => $page,
            'metadata' => $metadata,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
