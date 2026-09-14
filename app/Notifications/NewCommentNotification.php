<?php

namespace App\Notifications;

use App\Models\Comment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewCommentNotification extends Notification implements ShouldQueue
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
        return ['mail', 'database'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        $post = $this->comment->post;

        return (new MailMessage)
            ->subject("New Comment on: {$post?->title}")
            ->line("A new comment was submitted by {$this->comment->author_name}.")
            ->line("\"{$this->comment->content}\"")
            ->action('Moderate Comments', url('/admin/comments'))
            ->line('Thank you for managing your blog community!');
    }

    /**
     * Get the array representation of the notification for database storage.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            'comment_id' => $this->comment->id,
            'post_id' => $this->comment->post_id,
            'post_title' => $this->comment->post?->title,
            'author_name' => $this->comment->author_name,
            'message' => "New comment submitted on \"{$this->comment->post?->title}\"",
        ];
    }
}
