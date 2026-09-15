<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\AuthorController;
use App\Http\Controllers\Api\V1\BookmarkController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CommentController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\NewsletterController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\PostController;
use App\Http\Controllers\Api\V1\PostLikeController;
use App\Http\Controllers\Api\V1\SearchController;
use App\Http\Controllers\Api\V1\SeriesController;
use App\Http\Controllers\Api\V1\SettingController;
use App\Http\Controllers\Api\V1\TagController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->middleware(['throttle:api'])->group(function (): void {
    // ─── Authentication ───────────────────────────────────────────────────
    Route::prefix('auth')->group(function (): void {
        Route::post('/register', [AuthController::class, 'register'])->name('api.v1.auth.register');
        Route::post('/login', [AuthController::class, 'login'])->name('api.v1.auth.login');

        Route::middleware('auth:sanctum')->group(function (): void {
            Route::post('/logout', [AuthController::class, 'logout'])->name('api.v1.auth.logout');
            Route::get('/me', [AuthController::class, 'me'])->name('api.v1.auth.me');
            Route::put('/profile', [AuthController::class, 'updateProfile'])->name('api.v1.auth.profile');
            Route::put('/password', [AuthController::class, 'updatePassword'])->name('api.v1.auth.password');
            Route::delete('/account', [AuthController::class, 'deleteAccount'])->name('api.v1.auth.account');
        });
    });

    // ─── Posts ────────────────────────────────────────────────────────────
    Route::get('/posts', [PostController::class, 'index'])->name('api.v1.posts.index');
    Route::get('/posts/{slug}', [PostController::class, 'show'])->name('api.v1.posts.show');
    Route::get('/posts/{slug}/related', [PostController::class, 'related'])->name('api.v1.posts.related');
    Route::post('/posts/{slug}/views', [PostController::class, 'recordView'])
        ->middleware('throttle:api-views')
        ->name('api.v1.posts.view');

    // ─── Post Likes (Authenticated) ───────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::post('/posts/{slug}/like', [PostLikeController::class, 'store'])->name('api.v1.posts.like');
        Route::delete('/posts/{slug}/like', [PostLikeController::class, 'destroy'])->name('api.v1.posts.unlike');
    });

    // ─── Bookmarks (Authenticated) ────────────────────────────────────────
    Route::middleware('auth:sanctum')->group(function (): void {
        Route::get('/bookmarks', [BookmarkController::class, 'index'])->name('api.v1.bookmarks.index');
        Route::post('/posts/{slug}/bookmark', [BookmarkController::class, 'store'])->name('api.v1.bookmarks.store');
        Route::delete('/posts/{slug}/bookmark', [BookmarkController::class, 'destroy'])->name('api.v1.bookmarks.destroy');
    });

    // ─── Post Comments ────────────────────────────────────────────────────
    Route::get('/posts/{slug}/comments', [CommentController::class, 'index'])->name('api.v1.comments.index');
    Route::post('/posts/{slug}/comments', [CommentController::class, 'store'])
        ->middleware('throttle:api-comments')
        ->name('api.v1.comments.store');
    Route::delete('/posts/{slug}/comments/{id}', [CommentController::class, 'destroy'])
        ->middleware('auth:sanctum')
        ->name('api.v1.comments.destroy');

    // ─── Series ───────────────────────────────────────────────────────────
    Route::get('/series', [SeriesController::class, 'index'])->name('api.v1.series.index');
    Route::get('/series/{slug}', [SeriesController::class, 'show'])->name('api.v1.series.show');

    // ─── Categories ───────────────────────────────────────────────────────
    Route::get('/categories', [CategoryController::class, 'index'])->name('api.v1.categories.index');
    Route::get('/categories/{slug}', [CategoryController::class, 'show'])->name('api.v1.categories.show');

    // ─── Tags ─────────────────────────────────────────────────────────────
    Route::get('/tags', [TagController::class, 'index'])->name('api.v1.tags.index');
    Route::get('/tags/{slug}', [TagController::class, 'show'])->name('api.v1.tags.show');

    // ─── Authors ──────────────────────────────────────────────────────────
    Route::get('/authors', [AuthorController::class, 'index'])->name('api.v1.authors.index');
    Route::get('/authors/{id}', [AuthorController::class, 'show'])->name('api.v1.authors.show');

    // ─── Pages ────────────────────────────────────────────────────────────
    Route::get('/pages', [PageController::class, 'index'])->name('api.v1.pages.index');
    Route::get('/pages/{slug}', [PageController::class, 'show'])->name('api.v1.pages.show');

    // ─── Search ───────────────────────────────────────────────────────────
    Route::get('/search', SearchController::class)
        ->middleware('throttle:api-search')
        ->name('api.v1.search');

    // ─── Newsletter & Inquiries ───────────────────────────────────────────
    Route::post('/newsletter/subscribe', [NewsletterController::class, 'subscribe'])
        ->middleware('throttle:api-newsletter')
        ->name('api.v1.newsletter.subscribe');

    Route::post('/contact', [ContactController::class, 'store'])
        ->middleware('throttle:api-contact')
        ->name('api.v1.contact');

    // ─── Public Settings ──────────────────────────────────────────────────
    Route::get('/settings', SettingController::class)->name('api.v1.settings');
});
