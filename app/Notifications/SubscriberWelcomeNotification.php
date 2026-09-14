<?php

namespace App\Notifications;

use App\Models\Setting;
use App\Models\Subscriber;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SubscriberWelcomeNotification extends Notification implements ShouldQueue
{
    use Queueable;

    public function __construct(
        public Subscriber $subscriber
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

        return (new MailMessage)
            ->subject("Welcome to {$siteName} Newsletter!")
            ->greeting("Hello {$this->subscriber->name}!")
            ->line("Thank you for subscribing to {$siteName}.")
            ->line('You will receive curated articles, engineering stories, and updates right in your inbox.')
            ->action('Browse Recent Articles', url('/'))
            ->line('If you ever wish to unsubscribe, you can do so anytime using the link at the bottom of our emails.');
    }
}
