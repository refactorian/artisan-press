<?php

namespace App\Livewire;

use App\Enums\CommentStatus;
use App\Models\Comment;
use App\Models\Post;
use App\Models\Setting;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class PostComments extends Component
{
    public Post $post;

    public string $content = '';

    public ?int $replyToId = null;

    public string $guestName = '';

    public string $guestEmail = '';

    public string $guestWebsite = '';

    public int $loadedCount = 5;

    public function rules(): array
    {
        if (auth()->check()) {
            return [
                'content' => 'required|string|min:3|max:2000',
            ];
        }

        return [
            'guestName' => 'required|string|min:2|max:100',
            'guestEmail' => 'required|email:filter|max:255',
            'guestWebsite' => 'nullable|url|max:255',
            'content' => 'required|string|min:3|max:2000',
        ];
    }

    public function messages(): array
    {
        return [
            'guestName.required' => 'Please enter your name.',
            'guestEmail.required' => 'Please provide an email address.',
            'content.required' => 'Please write a comment before submitting.',
        ];
    }

    public function setReplyTo(?int $commentId): void
    {
        $this->replyToId = $commentId;
    }

    public function cancelReply(): void
    {
        $this->replyToId = null;
    }

    public function loadMore(): void
    {
        $this->loadedCount += 5;
    }

    public function submitComment(): void
    {
        if (! Setting::get('enable_comments', true)) {
            $this->dispatch('toast', message: 'Comments are closed for this article.', type: 'error');

            return;
        }

        $this->validate();

        $autoApprove = (bool) Setting::get('auto_approve_comments', false);
        $status = $autoApprove ? CommentStatus::Approved : CommentStatus::Pending;

        Comment::create([
            'post_id' => $this->post->id,
            'user_id' => auth()->id(),
            'parent_id' => $this->replyToId,
            'guest_name' => auth()->check() ? null : trim($this->guestName),
            'guest_email' => auth()->check() ? null : strtolower(trim($this->guestEmail)),
            'guest_website' => auth()->check() ? null : trim($this->guestWebsite),
            'content' => trim($this->content),
            'status' => $status,
            'ip_address' => request()->ip(),
            'user_agent' => substr((string) request()->userAgent(), 0, 500),
        ]);

        $this->content = '';
        $this->replyToId = null;

        $msg = $autoApprove
            ? 'Your comment has been published!'
            : 'Your comment has been submitted and is awaiting moderation.';

        $this->dispatch('toast', message: $msg, type: 'success');
    }

    public function render(): View
    {
        $commentsEnabled = (bool) Setting::get('enable_comments', true);

        // Fetch top-level approved comments
        $commentsQuery = Comment::where('post_id', $this->post->id)
            ->whereNull('parent_id')
            ->where(function ($q): void {
                $q->approved();
                if (auth()->check()) {
                    $q->orWhere(fn ($sub) => $sub->where('user_id', auth()->id())->where('status', CommentStatus::Pending));
                }
            })
            ->with([
                'user',
                'replies' => function ($q): void {
                    $q->where(function ($subQ): void {
                        $subQ->approved();
                        if (auth()->check()) {
                            $subQ->orWhere(fn ($inner) => $inner->where('user_id', auth()->id())->where('status', CommentStatus::Pending));
                        }
                    })->with('user')->orderBy('created_at', 'asc');
                },
            ])
            ->orderBy('created_at', 'desc');

        $totalRootComments = $commentsQuery->count();
        $comments = $commentsQuery->take($this->loadedCount)->get();

        return view('livewire.post-comments', [
            'comments' => $comments,
            'totalRootComments' => $totalRootComments,
            'commentsEnabled' => $commentsEnabled,
        ]);
    }
}
