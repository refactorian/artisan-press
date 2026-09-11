<?php

namespace App\Policies;

use App\Models\Post;
use App\Models\User;

class PostPolicy
{
    /**
     * Perform pre-authorization checks.
     */
    public function before(User $user, string $ability): ?bool
    {
        if ($user->hasRole('super_admin')) {
            return true;
        }

        return null;
    }

    /**
     * Determine if the user can view any posts.
     */
    public function viewAny(User $user): bool
    {
        return $user->hasPermissionTo('view posts') || $user->hasAnyRole(['admin', 'editor', 'author']);
    }

    /**
     * Determine if the user can view a specific post.
     */
    public function view(User $user, Post $post): bool
    {
        if ($user->hasAnyRole(['admin', 'editor'])) {
            return true;
        }

        return $user->hasRole('author') && $post->user_id === $user->id;
    }

    /**
     * Determine if the user can create posts.
     */
    public function create(User $user): bool
    {
        return $user->hasPermissionTo('create posts');
    }

    /**
     * Determine if the user can update a post.
     */
    public function update(User $user, Post $post): bool
    {
        if ($user->hasAnyRole(['admin', 'editor']) || $user->hasPermissionTo('edit posts')) {
            return true;
        }

        // Authors can only edit their own posts
        return $user->hasPermissionTo('edit own posts') && $post->user_id === $user->id;
    }

    /**
     * Determine if the user can delete a post.
     */
    public function delete(User $user, Post $post): bool
    {
        if ($user->hasAnyRole(['admin']) || $user->hasPermissionTo('delete posts')) {
            return true;
        }

        // Editors and authors can delete their own posts
        return $user->hasPermissionTo('delete own posts') && $post->user_id === $user->id;
    }

    /**
     * Determine if the user can restore a soft-deleted post.
     */
    public function restore(User $user, Post $post): bool
    {
        return $user->hasPermissionTo('restore posts') || $user->hasRole('admin');
    }

    /**
     * Determine if the user can permanently delete a post.
     */
    public function forceDelete(User $user, Post $post): bool
    {
        return $user->hasRole('super_admin');
    }

    /**
     * Determine if the user can publish posts.
     */
    public function publish(User $user, Post $post): bool
    {
        return $user->hasPermissionTo('publish posts');
    }
}
