<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\SeoService;
use Illuminate\Contracts\View\View;

class AuthorController extends Controller
{
    public function show(User $user, SeoService $seoService): View
    {
        $posts = $user->posts()
            ->published()
            ->with(['author', 'categories', 'media'])
            ->latest('published_at')
            ->paginate(9);

        $metadata = $seoService->generate($user);

        $breadcrumbs = [
            ['label' => 'Articles', 'url' => route('posts.index')],
            ['label' => 'Authors'],
            ['label' => $user->name],
        ];

        return view('pages.authors.show', [
            'author' => $user,
            'posts' => $posts,
            'metadata' => $metadata,
            'breadcrumbs' => $breadcrumbs,
        ]);
    }
}
