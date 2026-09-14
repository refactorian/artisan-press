<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class CommentApprovedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Comment $comment
    ) {}

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $post = $this->comment->post;
        $postUrl = url("/posts/{$post?->slug}");

        return (new MailMessage)
            ->subject("Your comment on \"{$post?->title}\" was approved!")
            ->line('Great news! Your comment has been reviewed and approved by our editorial team.')
            ->line("\"{$this->comment->content}\"")
            ->action('View Article and Comments', $postUrl)
            ->line('Thank you for contributing to the discussion.');
    }
}
