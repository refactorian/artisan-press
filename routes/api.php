<?php

use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\NewsletterController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\TagController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['throttle:api'])->group(function (): void {
    // Posts
    Route::get('/posts', [PostController::class, 'index'])->name('api.v1.posts.index');
    Route::get('/posts/{slug}', [PostController::class, 'show'])->name('api.v1.posts.show');
    Route::get('/posts/{slug}/related', [PostController::class, 'related'])->name('api.v1.posts.related');
    Route::post('/posts/{slug}/views', [PostController::class, 'recordView'])
        ->middleware('throttle:api-views')
        ->name('api.v1.posts.view');

    // Post Comments
    Route::get('/posts/{slug}/comments', [CommentController::class, 'index'])->name('api.v1.comments.index');
    Route::post('/posts/{slug}/comments', [CommentController::class, 'store'])
        ->middleware('throttle:api-comments')
        ->name('api.v1.comments.store');

    // Categories
    Route::get('/categories', [CategoryController::class, 'index'])->name('api.v1.categories.index');
    Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('api.v1.categories.show');

    // Tags
    Route::get('/tags', [TagController::class, 'index'])->name('api.v1.tags.index');
    Route::get('/tags/{slug}', [TagController::class, 'show'])->name('api.v1.tags.show');

    // Authors
    Route::get('/authors', [AuthorController::class, 'index'])->name('api.v1.authors.index');
    Route::get('/authors/{id}', [AuthorController::class, 'show'])->name('api.v1.authors.show');

    // Pages
    Route::get('/pages', [PageController::class, 'index'])->name('api.v1.pages.index');
    Route::get('/pages/{slug}', [PageController::class, 'show'])->name('api.v1.pages.show');

    // Search
    Route::get('/search', SearchController::class)
        ->middleware('throttle:api-search')
        ->name('api.v1.search');

    // Newsletter & Inquiries
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
        ->middleware('throttle:api-newsletter')
        ->name('api.v1.newsletter.subscribe');

    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:api-contact')
        ->name('api.v1.contact');

    // Public Settings
    Route::get('/settings', SettingController::class)->name('api.v1.settings');
});
