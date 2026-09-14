<?php

namespace App\Livewire;

use App\Enums\MessageStatus;
use App\Models\ContactMessage;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class ContactForm extends Component
{
    public string $name = '';

    public string $email = '';

    public string $subject = '';

    public string $message = '';

    public bool $sent = false;

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:2|max:100',
            'email' => 'required|email:filter|max:255',
            'subject' => 'required|string|min:3|max:200',
            'message' => 'required|string|min:10|max:3000',
        ];
    }

    public function submit(): void
    {
        $this->validate();

        ContactMessage::create([
            'name' => trim($this->name),
            'email' => strtolower(trim($this->email)),
            'subject' => trim($this->subject),
            'message' => trim($this->message),
            'status' => MessageStatus::Unread,
            'ip_address' => request()->ip(),
        ]);

        $this->sent = true;
        $this->name = '';
        $this->email = '';
        $this->subject = '';
        $this->message = '';

        $this->dispatch('toast', message: 'Your message has been received! Our editorial team will get back to you shortly.', type: 'success');
    }

    public function render(): View
    {
        return view('livewire.contact-form');
    }
}
