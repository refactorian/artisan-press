<?php

use App\Http\Controllers\FeedController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// XML Sitemap
Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

// RSS & Atom Feeds
Route::prefix('feed')->group(function (): void {
    Route::get('/', [FeedController::class, 'rss'])->name('feed.rss');
    Route::get('/rss', [FeedController::class, 'rss'])->name('feed.rss.alias');
    Route::get('/atom', [FeedController::class, 'atom'])->name('feed.atom');
    Route::get('/category/{slug}', [FeedController::class, 'category'])->name('feed.category');
    Route::get('/tag/{slug}', [FeedController::class, 'tag'])->name('feed.tag');
});
