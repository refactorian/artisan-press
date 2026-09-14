<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Contracts\View\View;

class SearchController extends Controller
{
    public function index(): View
    {
        $siteName = Setting::get('site_name', config('app.name', 'Laravel Modern Blog'));
        $metadata = [
            'title' => "Search Articles | {$siteName}",
            'meta_title' => "Search Articles | {$siteName}",
            'meta_description' => 'Search across all published articles, tutorials, and architectural patterns.',
            'canonical_url' => route('search'),
            'robots' => 'noindex, follow',
        ];

        $breadcrumbs = [
            ['label' => 'Articles', 'url' => route('posts.index')],
            ['label' => 'Search'],
        ];

        return view('pages.search.index', [
            'metadata' => $metadata,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
