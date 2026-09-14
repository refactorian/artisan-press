<?php

namespace App\Http\Controllers;

use App\Models\Series;
use App\Models\Setting;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;

class SeriesController extends Controller
{
    public function index(SeoService $seoService): View
    {
        $siteName = Setting::get('site_name', config('app.name', 'Laravel Modern Blog'));
        $seriesList = Series::active()
            ->with(['posts' => fn ($q) => $q->published()->orderBy('series_order')->with(['author', 'categories'])])
            ->withCount(['posts' => fn ($q) => $q->published()])
            ->orderBy('sort_order')
            ->get();

        $metadata = [
            'title' => "Learning Paths & Series | {$siteName}",
            'meta_title' => "Learning Paths & Series | {$siteName}",
            'meta_description' => 'Explore comprehensive multi-part engineering series and structured learning paths.',
            'canonical_url' => route('series.index'),
            'robots' => 'index, follow',
        ];

        $breadcrumbs = [
            ['label' => 'Learning Paths'],
        ];

        return view('pages.series.index', [
            'seriesList' => $seriesList,
            'metadata' => $metadata,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }

    public function show(Series $series, SeoService $seoService): View
    {
        if (! $series->is_active) {
            abort(404);
        }

        $series->load([
            'posts' => fn ($q) => $q->published()->orderBy('series_order')->with(['author', 'categories', 'media']),
        ]);

        $siteName = Setting::get('site_name', config('app.name', 'Laravel Modern Blog'));
        $metadata = [
            'title' => "{$series->name} (Series) | {$siteName}",
            'meta_title' => $series->name,
            'meta_description' => $series->description ?? "Explore all parts of the {$series->name} series.",
            'canonical_url' => route('series.show', $series),
            'robots' => 'index, follow',
        ];

        $breadcrumbs = [
            ['label' => 'Learning Paths', 'url' => route('series.index')],
            ['label' => $series->name],
        ];

        // Cumulative reading time
        $totalReadingTime = $series->posts->sum(function ($post) {
            return $post->reading_time ?: ($post->calculateReadingTime() ?? 3);
        });

        return view('pages.series.show', [
            'series' => $series,
            'totalReadingTime' => $totalReadingTime,
            'metadata' => $metadata,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
