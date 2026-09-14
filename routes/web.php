<?php

use App\Http\Controllers\AuthorController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// Public Blog Frontend Routes
Route::get('/', HomeController::class)->name('home');

Route::get('/posts', [PostController::class, 'index'])->name('posts.index');
Route::get('/posts/{post:slug}', [PostController::class, 'show'])->name('posts.show');

Route::get('/categories/{category:slug}', [CategoryController::class, 'show'])->name('categories.show');
Route::get('/tags/{tag:slug}', [TagController::class, 'show'])->name('tags.show');
Route::get('/authors/{user}', [AuthorController::class, 'show'])->name('authors.show');
Route::get('/search', [SearchController::class, 'index'])->name('search');

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

// Dynamic Pages (About, Contact, Privacy, etc.)
Route::get('/{page:slug}', [PageController::class, 'show'])
    ->where('page', '[a-zA-Z0-9\-_]+')
    ->name('pages.show');
