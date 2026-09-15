<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\CommentStatus;
use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\CommentResource;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Setting;
use App\Notifications\NewCommentNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CommentController extends Controller
{
    /**
     * Display approved comments for the specified post.
     */
    public function index(string $slug): AnonymousResourceCollection
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        $comments = $post->comments()
            ->where('status', CommentStatus::Approved)
            ->whereNull('parent_id')
            ->with(['replies' => fn ($q) => $q->where('status', CommentStatus::Approved)->orderBy('created_at')])
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return CommentResource::collection($comments);
    }

    /**
     * Store a new comment on the specified post.
     */
    public function store(string $slug, Request $request): JsonResponse
    {
        // Check if comments are enabled globally
        if (! Setting::get('enable_comments', true)) {
            return response()->json([
                'status' => 'error',
                'message' => 'Comments are currently disabled.',
            ], 403);
        }

        $post = Post::published()->where('slug', $slug)->firstOrFail();

        // Honeypot check
        if ($request->filled('website_hp')) {
            return response()->json([
                'status' => 'success',
                'message' => 'Comment submitted for moderation.',
            ], 201);
        }

        $user = $request->user('sanctum') ?? $request->user();

        $rules = [
            'content' => ['required', 'string', 'min:3', 'max:2000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
            'guest_website' => ['nullable', 'url', 'max:255'],
        ];

        if (! $user) {
            $rules['guest_name'] = ['required', 'string', 'max:100'];
            $rules['guest_email'] = ['required', 'email', 'max:255'];
        } else {
            $rules['guest_name'] = ['nullable', 'string', 'max:100'];
            $rules['guest_email'] = ['nullable', 'email', 'max:255'];
        }

        $validated = $request->validate($rules);

        // Validate parent comment belongs to the same post
        if (! empty($validated['parent_id'])) {
            $parent = Comment::where('id', $validated['parent_id'])
                ->where('post_id', $post->id)
                ->first();

            if (! $parent) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Invalid parent comment.',
                ], 422);
            }
        }

        // Spam detection
        $status = CommentStatus::Pending;
        $spamKeywords = Setting::get('spam_keywords', '');
        if ($spamKeywords) {
            $keywords = array_map('trim', explode(',', strtolower((string) $spamKeywords)));
            $contentLower = strtolower($validated['content']);

            foreach ($keywords as $keyword) {
                if ($keyword !== '' && str_contains($contentLower, $keyword)) {
                    $status = CommentStatus::Spam;
                    break;
                }
            }
        }

        // Auto-approve if enabled and not flagged as spam
        if ($status !== CommentStatus::Spam && Setting::get('auto_approve_comments', false)) {
            $status = CommentStatus::Approved;
        }

        /** @var Comment $comment */
        $comment = $post->comments()->create([
            'parent_id' => $validated['parent_id'] ?? null,
            'user_id' => $user?->id,
            'guest_name' => $user ? ($validated['guest_name'] ?? $user->name) : $validated['guest_name'],
            'guest_email' => $user ? ($validated['guest_email'] ?? $user->email) : $validated['guest_email'],
            'guest_website' => $validated['guest_website'] ?? null,
            'content' => $validated['content'],
            'status' => $status,
            'ip_address' => $request->ip(),
            'user_agent' => substr((string) $request->userAgent(), 0, 500),
        ]);

        // Notify author if pending review
        if ($status === CommentStatus::Pending && $post->author) {
            $post->author->notify(new NewCommentNotification($comment));
        }

        return response()->json([
            'status' => 'success',
            'message' => $status === CommentStatus::Approved
                ? 'Comment posted successfully.'
                : 'Your comment has been submitted and is awaiting moderation.',
            'comment' => new CommentResource($comment),
        ], 201);
    }

    /**
     * Delete the authenticated user's comment.
     */
    public function destroy(string $slug, int $id, Request $request): JsonResponse
    {
        $post = Post::published()->where('slug', $slug)->firstOrFail();

        $comment = Comment::where('post_id', $post->id)
            ->where('id', $id)
            ->firstOrFail();

        $user = $request->user();

        if ($comment->user_id !== $user->id && ! $user->hasAnyRole(['super_admin', 'admin'])) {
            return response()->json([
                'status' => 'error',
                'message' => 'You do not have permission to delete this comment.',
            ], 403);
        }

        $comment->delete();

        return response()->json([
            'status' => 'success',
            'message' => 'Comment deleted successfully.',
        ]);
    }
}
