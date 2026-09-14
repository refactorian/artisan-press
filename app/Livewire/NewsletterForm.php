<?php

namespace App\Livewire;

use App\Enums\SubscriberStatus;
use App\Models\Subscriber;
use Illuminate\Contracts\View\View;
use Livewire\Attributes\Validate;
use Livewire\Component;

class NewsletterForm extends Component
{
    #[Validate('required|email:filter|max:255')]
    public string $email = '';

    public string $source = 'homepage_footer';

    public bool $subscribed = false;

    /**
     * Validation rules.
     *
     * @return array<string, string>
     */
    public function rules(): array
    {
        return [
            'email' => 'required|email:filter|max:255',
        ];
    }

    public function subscribe(): void
    {
        $this->validate();

        $subscriber = Subscriber::firstOrNew(['email' => strtolower(trim($this->email))]);

        if ($subscriber->exists && $subscriber->status === SubscriberStatus::Subscribed) {
            $this->subscribed = true;
            $this->email = '';

            return;
        }

        $subscriber->status = SubscriberStatus::Subscribed;
        $subscriber->source = $this->source;
        $subscriber->subscribed_at = now();
        $subscriber->unsubscribed_at = null;
        $subscriber->save();

        $this->subscribed = true;
        $this->email = '';
    }

    public function render(): View
    {
        return view('livewire.newsletter-form');
    }
}
