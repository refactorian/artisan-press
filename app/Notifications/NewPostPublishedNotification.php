<?php

namespace App\Notifications;

use App\Models\Post;
use App\Models\Setting;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewPostPublishedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Post $post
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
        $siteName = Setting::get('site_name', config('app.name', 'Laravel Modern Blog'));
        $postUrl = url("/posts/{$this->post->slug}");

        return (new MailMessage)
            ->subject("New Article: {$this->post->title} - {$siteName}")
            ->greeting('A new story has just been published!')
            ->line("\"{$this->post->title}\"")
            ->line($this->post->excerpt ?: str(strip_tags($this->post->content ?? ''))->limit(160))
            ->line("Reading time: ~{$this->post->reading_time} min")
            ->action('Read Full Article', $postUrl)
            ->line('Enjoy the read!');
    }
}
