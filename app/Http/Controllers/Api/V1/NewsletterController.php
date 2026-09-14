<?php

namespace App\Http\Controllers\Api\V1;

use App\Enums\SubscriberStatus;
use App\Http\Controllers\Controller;
use App\Models\Subscriber;
use App\Notifications\SubscriberWelcomeNotification;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NewsletterController extends Controller
{
    /**
     * Subscribe an email to the newsletter.
     */
    public function subscribe(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'email' => ['required', 'email', 'max:255'],
            'name' => ['nullable', 'string', 'max:100'],
            'source' => ['nullable', 'string', 'max:50'],
        ]);

        $subscriber = Subscriber::where('email', $validated['email'])->first();

        if ($subscriber) {
            if ($subscriber->status === SubscriberStatus::Subscribed) {
                return response()->json([
                    'status' => 'success',
                    'message' => 'You are already subscribed to our newsletter.',
                ]);
            }

            // Resubscribe
            $subscriber->update([
                'status' => SubscriberStatus::Subscribed,
                'subscribed_at' => now(),
                'unsubscribed_at' => null,
                'name' => $validated['name'] ?? $subscriber->name,
            ]);
        } else {
            /** @var Subscriber $subscriber */
            $subscriber = Subscriber::create([
                'email' => $validated['email'],
                'name' => $validated['name'] ?? null,
                'source' => $validated['source'] ?? 'api',
                'status' => SubscriberStatus::Subscribed,
                'subscribed_at' => now(),
            ]);

            $subscriber->notify(new SubscriberWelcomeNotification($subscriber));
        }

        return response()->json([
            'status' => 'success',
            'message' => 'Thank you for subscribing! Please check your email for confirmation.',
        ], 201);
    }
}
